<?php

namespace App\Traits;

// use Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

trait ImageProcessing
{
    public function get_mime($mime)
    {
        if ($mime == 'image/jpeg')
            $extension = '.jpg';
        elseif ($mime == 'image/png')
            $extension = '.png';
        elseif ($mime == 'image/gif')
            $extension = '.gif';
        elseif ($mime == 'image/svg+xml')
            $extension = '.svg';
        elseif ($mime == 'image/tiff')
            $extension = '.tiff';
        elseif ($mime == 'image/webp')
            $extension = '.webp';

        return $extension;
    }


    public function saveImage($image, $folder)
    {
        $img = Image::make($image);
        $extension = $this->get_mime($img->mime());

        $str_random = Str::random(8);
        $imgpath = $str_random . time() . $extension;
        $img->save(storage_path('app/imagesfp/' . $folder) . '/' . $imgpath);

        return  $imgpath;
    }



public function get_mime_e($mime)
{
    $mime_map = [
        // Images
        'image/jpeg' => '.jpg',
        'image/jpg' => '.jpg',
        'image/png' => '.png',
        'image/gif' => '.gif',
        'image/bmp' => '.bmp',
        'image/webp' => '.webp',
        'image/avif' => '.avif',
        'image/svg+xml' => '.svg',
        'image/tiff' => '.tiff',
        'image/jfif' => '.jfif',

        // Videos
        'video/mp4' => '.mp4',
        'video/x-msvideo' => '.avi',
        'video/quicktime' => '.mov',
        'video/x-ms-wmv' => '.wmv',
        'video/x-flv' => '.flv',
        'video/webm' => '.webm',
        'video/mpeg' => '.mpeg',

        // Audio
        'audio/mpeg' => '.mp3',
        'audio/wav' => '.wav',
        'audio/ogg' => '.ogg',
        'audio/x-m4a' => '.m4a',
        'audio/aac' => '.aac',
    ];

    return $mime_map[$mime] ?? '';
}
  /**
     * Save an audio or video file to the specified storage folder
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder
     * @return string
     */
    public function saveFile($file, $folder)
    {
        $mime = $file->getMimeType();
        $extension = $this->get_mime_e($mime);

        // If MIME type not found, use file extension
        if (!$extension) {
            $extension = '.' . $file->getClientOriginalExtension();
        }

        $fileName = Str::random(8) . time() . $extension;
        $storagePath = storage_path("app/public/$folder");

        // Ensure the folder exists
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0770, true);
        }

        // Move the file to the folder
        $file->move($storagePath, $fileName);

        return $fileName;
    }


    public function aspect4resize($image, $width, $height, $folder)
    {
        $img = Image::make($image);
        $extension = $this->get_mime($img->mime());
        $str_random = Str::random(8);

        $img->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        // Compress the image
        $img->encode(null, 75);  // 75% quality compression

        $imgpath = $str_random . time() . $extension;
        $img->save(storage_path('app/imagesfp/' . $folder) . '/' . $imgpath);

        return $imgpath;
    }
    public function aspect4height($image, $width, $height)
    {
        $img = Image::make($image);
        $extension = $this->get_mime($img->mime());
        $str_random = Str::random(8);
        $img->resize(null, $height, function ($constraint) {
            $constraint->aspectRatio();
        });

        if ($img->width() < $width) {
            $img->resize($width, null);
        } else if ($img->width() > $width) {
            $img->crop($width, $height, 0, 0);
        }

        $imgpath = $str_random . time() . $extension;
        $img->save(storage_path('app/imagesfp') . '/' . $imgpath);
        return $imgpath;
    }

    // public function saveImageAndThumbnail($Thefile, $thumb = false)
    // {
    //     $dataX = array();

    //     $dataX['image'] = $this->saveImage($Thefile);

    //     if ($thumb) {

    //         $dataX['thumbnailsm'] = $this->aspect4resize($Thefile, 256, 144);
    //         $dataX['thumbnailmd'] = $this->aspect4resize($Thefile, 426, 240);
    //         $dataX['thumbnailxl'] = $this->aspect4resize($Thefile, 640, 360);
    //     }

    //     return $dataX;
    // }

    public function deleteImage($filePath)
    {
        if ($filePath) {
            if (is_file(Storage::disk('imagesfp')->path($filePath))) {
                if (file_exists(Storage::disk('imagesfp')->path($filePath))) {
                    unlink(Storage::disk('imagesfp')->path($filePath));
                }
            }
        }
    }
}
