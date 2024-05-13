<?php

namespace App\Http\Controllers\Admin\Steganography;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DecryptController extends Controller
{
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
