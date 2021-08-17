<?php


namespace App\Services;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContestService
{
    public function saveImage($file, $title): string
    {
        $image_name = $image_name = self::imageNameGenerate($title, $file->getClientOriginalExtension());

        $disk = Storage::disk('public');
        if (!$disk->exists('contest')) {
            $disk->makeDirectory('contest');
        }
        $disk->put('contest/' . $image_name, $file->getContent());

        return $image_name;
    }

    public function saveGalleryImage($files, $title): array
    {
        $gallery_images_names = [];
        foreach ($files as $key => $file) {
            $gallery_image_name = Str::slug($title) . '-' . date('dmYhis') . '-' . uniqid() . '.' . $file->getClientOriginalExtension();

            $disk = Storage::disk('public');

            if (!$disk->exists('contest/gallery')) {
                $disk->makeDirectory('contest/gallery');
            }

            $disk->put('contest/gallery/' . $gallery_image_name, $file->getContent());
            $gallery_images_names[] = $gallery_image_name;
        }
        return $gallery_images_names;
    }

    public function updateImage($file, $title, $prevImage): string
    {
        $image_name = self::imageNameGenerate($title, $file->getClientOriginalExtension());

        $disk = Storage::disk('public');
        if (!$disk->exists('contest')) {
            $disk->makeDirectory('contest');
        }
        $disk->put('contest/' . $image_name, $file->getContent());
        if ($disk->exists('contest/' . $prevImage)) {
            $disk->delete('contest/' . $prevImage);
        }
        return $image_name;
    }

    public function updateGalleryImage($files, $title, $contest_gallery): array
    {
        $gallery_images_names = [];
        foreach ($files as $file) {
            $gallery_image_name = self::imageNameGenerate($title, $file->getClientOriginalExtension());
            $disk = Storage::disk('public');
            if (!$disk->exists('contest/gallery')) {
                $disk->makeDirectory('contest/gallery');
            }
            $disk->put('contest/gallery/' . $gallery_image_name, $file->getContent());
            $gallery_images_names[] = $gallery_image_name;
            if ($contest_gallery->count() > 0) {
                foreach ($contest_gallery as $gallery_image) {
                    if ($disk->exists('contest/gallery/' . $gallery_image->image)) {
                        $disk->delete('contest/gallery/' . $gallery_image->image);
                    }
                }
            }
        }
        return $gallery_images_names;
    }

    public static function imageNameGenerate($title, $ext): string
    {
        return Str::slug($title) . '-' . date('dmYhis') . '-' . uniqid() . '.' . $ext;
    }
}
