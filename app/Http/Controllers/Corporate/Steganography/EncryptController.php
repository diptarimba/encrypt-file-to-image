<?php

namespace App\Http\Controllers\Corporate\Steganography;

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
        $corporateId = auth()->user()->corporate_id;
        if ($request->ajax()) {

            $crypto = Steganography::whereHas('user', function($query) use ($corporateId){
                $query->where('corporate_id', $corporateId);
            })->orderBy('created_at', 'desc')->select();
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
                    return '<a class="' . self::CLASS_BUTTON_SUCCESS . '" href="' . route('corporate.decrypt.index', $query->id) . '">Decrypt</a>';
                })
                ->rawColumns(['encrypted_image', 'action'])
                ->make(true);
        }

        return view('page.corporate-dashboard.steganography.index');
    }

    public function create()
    {
        $imagesPath = url('/assets-dashboard/images/choose-image');

        // Get all image files with .jpg, .jpeg, .png extensions
        $images = glob(public_path('assets-dashboard/images/choose-image/*_thumbnail.{jpg,jpeg,png,PNG}'), GLOB_BRACE);
        $images = array_map(function($path) use ($imagesPath){
            return str_replace(public_path(), '', $path);
        }, $images);
        $images = array_map(function($path) use ($imagesPath){
            return $imagesPath . '/' . substr($path, strlen('assets-dashboard/images/choose-image') + 2);
        }, $images);

        $data = $this->createMetaPageData(null, 'Encrypt', 'encrypt', 'corporate');
        return view('page.corporate-dashboard.steganography.encrypt', compact('data', 'images'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => ['required_without:imagedefault', 'mimes:png', 'max:2048'],
            'imagedefault' => ['required_without:image'],
            'file' => 'required|mimes:jpg,png,jpeg,pdf,docx,txt,csv,xlsx,zip|max:2048',
            'password' => 'required|min:5',
            'watermark_text' => 'required'
        ], [
            'image.required' => 'Image is required',
            'imagedefault.required' => 'Selected Image is required',
            'image.mimes' => 'Only png files are allowed',
            'image.max' => 'Image size must be less than 2MB',
            'password.min' => 'Password must be at least 5 characters',
            'file.required' => 'File is required',
            'file.mimes' => 'Only jpg, png, jpeg, pdf, docx, txt, csv, xlsx files are allowed',
            'file.max' => 'File size must be less than 2MB',
        ]);

        if($request->file('image')) {
            $imagePath = $request->file('image')->getPathname();
        }else {
            $imageRaw = str_replace(url('/'), '', $request->imagedefault);
            $imageRaw = str_replace('_thumbnail', '', $imageRaw);
            $imagePath = public_path($imageRaw);
        }

        $file = $request->file('file');
        $fileExtension = $file->getClientOriginalExtension();

        // Membaca konten file dan mengubahnya menjadi data string base64
        $fileContent = file_get_contents($file->getPathname());
        $base64Content = base64_encode($fileContent);

        $extensionString = '';
        switch ($fileExtension) {
            case 'jpg':
            case 'jpeg':
                $extensionString = 'jpg';
                break;
            case 'png':
            case 'docx':
            case 'csv':
            case 'xlsx':
            case 'txt':
            case 'pdf':
            case 'zip':
                $extensionString = $fileExtension;
                break;
            default:
                $extensionString = 'unknown';
        }

        // Generate hash bcrypt
        $encryptedPassword = Hash::make($request->password);
        $base64Content = 'password:' . $encryptedPassword . '|filetype:' . $extensionString . '|base64:' . $base64Content;
        // Implementasi rot13
        $base64Content = str_rot13($base64Content);

        // Validasi ukuran gambar untuk memastikan bisa menampung pesan
        $imageSize = getimagesize($imagePath);
        $width = $imageSize[0];
        $height = $imageSize[1];
        $maxMessageSize = ($width * $height * 3) / 8; // Kapasitas dalam byte
        if (strlen($base64Content) > $maxMessageSize) {
            return redirect()->back()->withErrors(['file' => 'Ukuran file terlalu besar untuk dienkripsi dalam gambar ini.']);
        }

        // Membuat resource gambar dari file yang diupload
        $img = imagecreatefrompng($imagePath);

        $this->addWatermarkWithOutline($img, $request->watermark_text, $width, $height);

        $imageWithMessage = $this->embedMessage($img, $base64Content);

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

        return redirect()->route('corporate.encrypt.index')->with('success', 'Steganography created successfully');
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

    private function addWatermark($img, $text, $width, $height)
    {
        $font = 5; // Font size, can be adjusted or replaced with TTF font using imagettftext
        // $textColor = imagecolorallocate($img, 255, 255, 255); // White color for the text
        $textColor = imagecolorallocate($img, 0, 0, 0); // White color for the text

        // Calculate position for the watermark text
        $x = $width - imagefontwidth($font) * strlen($text) - 10;
        $y = $height - imagefontheight($font) - 10;

        // Add the text watermark
        imagestring($img, $font, $x, $y, $text, $textColor);
    }

    private function addWatermarkWithOutline($img, $text, $width, $height)
    {
        $font = 5; // Font size (built-in)
        $textColor = imagecolorallocate($img, 255, 255, 255); // White color for the text
        $outlineColor = imagecolorallocate($img, 0, 0, 0); // Black color for the outline

        // Calculate position for the watermark text
        $textWidth = imagefontwidth($font) * strlen($text);
        $textHeight = imagefontheight($font);
        $x = $width - $textWidth - 10;
        $y = $height - $textHeight - 10;

        // Add the text outline
        imagestring($img, $font, $x - 1, $y - 1, $text, $outlineColor);
        imagestring($img, $font, $x + 1, $y - 1, $text, $outlineColor);
        imagestring($img, $font, $x - 1, $y + 1, $text, $outlineColor);
        imagestring($img, $font, $x + 1, $y + 1, $text, $outlineColor);

        // Add the text
        imagestring($img, $font, $x, $y, $text, $textColor);
    }

    public function create_upload()
    {
        $data = [
            'url' => route('corporate.encrypt.upload_store'),
            'title' => 'Upload Encrypted Image for Decrypt',
            'home' => route('corporate.encrypt.index'),

        ];
        return view('page.corporate-dashboard.steganography.encrypted-upload', compact('data'));
    }

    public function store_upload(Request $request)
    {
        $request->validate([
            'encrypted_image' => 'required|image|mimes:png|max:2048',
        ]);

        if ($request->hasFile('encrypted_image')) {
            $image = $request->file('encrypted_image');
            $image->storeAs('stegano_images', $image->hashName(), 'public');
            $request->merge(['picture' => asset('storage/stegano_images/' . $image->hashName())]);
        }

        $decrypt = Steganography::create([
            'created_by' => auth()->user()->id,
            'encrypted_image' => $request->picture
        ]);
        return redirect()->route('corporate.encrypt.index', $decrypt->id)->with('success', 'Encrypted image uploaded successfully');
    }
}


