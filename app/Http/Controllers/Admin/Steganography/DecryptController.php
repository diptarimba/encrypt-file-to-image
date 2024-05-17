<?php

namespace App\Http\Controllers\Admin\Steganography;

use App\Http\Controllers\Controller;
use App\Models\Steganography;
use Illuminate\Http\Request;
use Ramsey\Uuid\Rfc4122\UuidV4;

class DecryptController extends Controller
{
    public function edit(Steganography $decrypt)
    {
        $data = [
            'title' => 'Decrypt Image',
            'url' => route('admin.decrypt.store', $decrypt->id),
            'home' => route('admin.encrypt.index')
        ];
        return view('page.admin-dashboard.steganography.decrypt', compact('data'));
    }

    public function decrypt_store(Steganography $decrypt, Request $request)
    {
        $request->validate([
            'password' => 'required'
        ]);

        $password = $request->password;

        $image = $decrypt->encrypted_image;
        $pattern = "/\/storage\/stegano_images\/[^ ]*/";

        preg_match($pattern, $image, $matches);
        $imagePath = public_path($matches[0]);
        $image = imagecreatefromstring(file_get_contents($imagePath));

        $message = $this->extractMessage($image);
        $message = str_rot13($message);

        $patternPassword = "/password:([^\|]*)/";
        $patternFileType = "/filetype:([^\|]*)/";
        $patternFileData = "/base64:([^\|]*)/";

        if (preg_match($patternPassword, $message, $matches)) {
            if (password_verify($password, $matches[1])) {
                preg_match($patternFileType, $message, $matchesType);
                preg_match($patternFileData, $message, $matchesData);
                $name = UuidV4::uuid4();
                switch ($matchesType[1] && $matchesData[1]) {
                    case 'pdf':
                        dd(strlen($matchesData[1]));
                        $file = base64_decode($matchesData[1]);
                        return response()->streamDownload(function () use ($file) {
                            fpassthru($file);
                        }, $name . '.pdf', ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="' . $name . '.pdf"']);
                    case 'jpg':
                        $file = base64_decode($matchesData[1]);
                        return response()->streamDownload(function () use ($file) {
                            fpassthru($file);
                        }, $name . '.jpg', ['Content-Type' => 'image/jpeg']);
                    case 'png':
                        $file = base64_decode($matchesData[1]);
                        return response()->streamDownload(function () use ($file) {
                            fpassthru($file);
                        }, $name . '.png', ['Content-Type' => 'image/png']);
                    case 'txt':
                        $file = base64_decode($matchesData[1]);
                        return response()->streamDownload(function () use ($file) {
                            fpassthru($file);
                        }, $name . '.txt', ['Content-Type' => 'text/plain']);
                    default:
                        echo "File type not supported";
                        return back()->withInput()->withErrors(['password' => 'File type not supported']);
                }

            } else {
                echo "Password salah";
                return back()->withInput()->withErrors(['password' => 'Password salah']);
            }
            echo "Password found: " . $matches[1];
        } else {
            echo "No password format found.";
        }

        dd($message);
    }

    public function extractMessage($img)
    {
        $width = imagesx($img);
        $height = imagesy($img);
        $binaryMessage = '';
        $extractedMessage = '';

        // Menelusuri setiap piksel untuk mengekstrak LSB
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgb = imagecolorat($img, $x, $y);
                $colors = [
                    ($rgb >> 16) & 0xFF, // R
                    ($rgb >> 8) & 0xFF,  // G
                    $rgb & 0xFF          // B
                ];

                // Ekstrak LSB dari setiap channel R, G, B
                foreach ($colors as $color) {
                    $binaryMessage .= $color & 1;
                }
            }
        }

        // Konversi string biner kembali menjadi teks
        for ($i = 0; $i < strlen($binaryMessage) - 8; $i += 8) {
            $byte = substr($binaryMessage, $i, 8);
            $character = chr(bindec($byte));
            if ($character == "\0") {
                break; // Menghentikan pembacaan jika menemukan karakter null (akhir pesan)
            }
            $extractedMessage .= $character;
        }

        return $extractedMessage;
    }
}
