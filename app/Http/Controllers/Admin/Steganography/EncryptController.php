<?php

namespace App\Http\Controllers\Admin\Steganography;

use App\Http\Controllers\Controller;
use App\Models\Steganography;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Rfc4122\UuidV4;

class EncryptController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $crypto = Steganography::orderBy('created_at', 'desc')->select();
            return datatables()->of($crypto)
                ->addIndexColumn()
                ->addColumn('created_by', function ($query) {
                    return $query->user->name;
                })
                ->addColumn('created_at', function ($query) {
                    return  Carbon::parse($query->created_at)->format('d-m-Y H:i') . ' (' . Carbon::parse($query->created_at)->diffForHumans() . ')';
                })
                ->addColumn('encrypted_image', function ($query) {
                    return '<a class="' . self::CLASS_BUTTON_PRIMARY . '" href="' . $query->encrypted_image . '" target="_blank">View Image</a>';
                })
                ->addColumn('action', function ($query) {
                    return '<a class="' . self::CLASS_BUTTON_PRIMARY . '" href="' . route('admin.decrypt.index', $query->id) . '">Decrypt</a>';
                })
                ->rawColumns(['encrypted_image', 'action'])
                ->make(true);
        }

        return view('page.admin-dashboard.steganography.index');
    }

    public function create()
    {
        $data = $this->createMetaPageData(null, 'Encrypt', 'encrypt');
        return view('page.admin-dashboard.steganography.encrypt', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|mimes:png',
            'file' => 'required|mimes:png,jpg,pdf',
            'password' => 'required'
        ]);

        $imagePath = $request->file('image')->getPathname();
        $file = $request->file('file');

        $fileExtension = $file->getClientOriginalExtension();

        // Membaca konten file dan mengubahnya menjadi data string base64
        $fileContent = file_get_contents($file->getPathname());
        $base64Content = base64_encode($fileContent);

        $extensionString = '';
        switch ($fileExtension) {
            case 'png':
                $extensionString = 'png';
                break;
            case 'jpg':
                $extensionString = 'jpg';
                break;
            case 'pdf':
                $extensionString = 'pdf';
                break;
            default:
                $extensionString = 'unknow';
        }

        // Generate hash bcrypt
        $ecnryptedPassword = Hash::make($request->password);
        $base64Content = 'password:' . $ecnryptedPassword .'|filetype:' . $extensionString . '|base64:' . base64_encode($fileContent);
        // Implementasi rot13
        $base64Content = str_rot13($base64Content);

        $imageWithMessage = $this->embedMessage(imagecreatefrompng($imagePath), $base64Content);

        $nameFile = UuidV4::uuid4()->toString();
        $namePath = 'stegano_images/' . $nameFile . '.png';
        $outputPath = storage_path('app/public/' . $namePath);
        $directory = dirname($outputPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
        imagepng($imageWithMessage, $outputPath);

        $crypto = new Steganography();
        $crypto->encrypted_image = url('storage/' . $namePath);
        $crypto->created_by = auth()->user()->id;
        $crypto->save();

        imagedestroy($imageWithMessage);

        return redirect()->route('admin.encrypt.index')->with('success', 'Steganography created successfully');
    }

    public function embedMessage($img, $message)
    {
        $width = imagesx($img);
        $height = imagesy($img);
        $newImage = imagecreatetruecolor($width, $height);
        imagecopy($newImage, $img, 0, 0, 0, 0, $width, $height);

        // Konversi pesan menjadi string biner
        $message .= "\0"; // Tambahkan karakter null sebagai penanda akhir
        $binaryMessage = '';
        for ($i = 0; $i < strlen($message); $i++) {
            $binaryMessage .= sprintf("%08b", ord($message[$i]));
        }

        $bitIndex = 0;
        $messageLength = strlen($binaryMessage);
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgb = imagecolorat($img, $x, $y);
                $colors = [
                    ($rgb >> 16) & 0xFF, // R
                    ($rgb >> 8) & 0xFF,  // G
                    $rgb & 0xFF          // B
                ];

                // Modifikasi LSB dari setiap channel R, G, B
                foreach ($colors as $i => &$color) {
                    if ($bitIndex < $messageLength) {
                        $newBit = $binaryMessage[$bitIndex] - '0';
                        $color = ($color & ~1) | $newBit; // Ubah bit terakhir
                        $bitIndex++;
                    }
                }

                $alpha = ($rgb & 0xFF000000) >> 24;
                $newColor = imagecolorallocatealpha($newImage, $colors[0], $colors[1], $colors[2], $alpha);
                imagesetpixel($newImage, $x, $y, $newColor);
            }
        }

        return $newImage;
    }
}
