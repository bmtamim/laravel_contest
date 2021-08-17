<?php


namespace App\Actions;


use App\DTO\ContestDTO;
use App\Models\Contest;
use App\Services\ContestService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContestAction
{

    public function createContest(Request $request, ContestDTO $DTO)
    {
        $image = '';
        if ($request->hasFile('image')) {
            $image = (new ContestService())->saveImage($request->file('image'), $DTO->title);
        }
        $DTO->image = $image;

        $gallery_images_names = [];
        if ($request->hasFile('image_gallery')) {
            $gallery_images_names = (new ContestService())->saveGalleryImage($request->file('image_gallery'), $DTO->title);
        }

        DB::transaction(function () use ($DTO, $gallery_images_names) {
            $contest = Contest::create($DTO->toArray());
            $contest->categories()->attach($DTO->categories);
            foreach ($gallery_images_names as $gallery_image) {
                $contest->contest_gallery()->create([
                    'image' => $gallery_image
                ]);
            }

        });
    }

    public function updateContest(Contest $contest, Request $request, ContestDTO $DTO)
    {
        $DTO->user_id = $contest->user_id;
        $DTO->image = $contest->image;
        if ($request->hasFile('image')) {
            $DTO->image = (new ContestService())->updateImage($request->file('image'), $DTO->title, $contest->image);
        }

        $gallery_images_names = [];
        if ($request->hasFile('image_gallery')) {
            $gallery_images_names = (new ContestService())->updateGalleryImage($request->file('image_gallery'), $DTO->title, $contest->contest_gallery);
        }

        DB::transaction(function () use ($contest, $DTO, $gallery_images_names) {
            $contest->update($DTO->toArray());
            $contest->categories()->sync($DTO->categories);
            if (count($gallery_images_names) > 0) {
                $contest->contest_gallery()->delete();
                foreach ($gallery_images_names as $gallery_image) {
                    $contest->contest_gallery()->create([
                        'image' => $gallery_image,
                    ]);
                }
            }
        });
    }
}
