<?php

namespace App\Http\Controllers;

use App\Actions\ContestAction;
use App\DTO\ContestDTO;
use App\Http\Requests\Backend\ContestRequest;
use App\Models\Category;
use App\Models\Contest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class UserContestController extends Controller
{

    public function index()
    {
        $contests = Contest::query()->where('user_id', auth()->id())->parent()->get();
        return view('frontend.user.contest.index', compact('contests'));
    }


    public function create()
    {
        $categories = Category::query()->where('status', true)->orderBy('name', 'asc')->get();
        return view('frontend.user.contest.create', compact('categories'));
    }


    public function store(ContestRequest $request, ContestAction $contestAction)
    {
        try {
            $contestAction->createContest($request, ContestDTO::createFromRequest($request));
            Session::flash('contest_msg', 'Contest Created!!');
        } catch (\Exception $e) {
            Session::flash('contest_msg', $e->getMessage());
        }
        return redirect()->route('user.contests.index');
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $categories = Category::query()->where('status', true)->orderBy('name', 'asc')->get();
        $contest = Contest::query()->where('user_id', auth()->id())->with('contest_gallery', 'categories')->findOrFail($id);
        return view('frontend.user.contest.edit', compact('contest', 'categories'));
    }


    public function update(ContestRequest $request, $id, ContestAction $contestAction)
    {
        $contest = Contest::query()->where('user_id', auth()->id())->findOrFail($id);
        try {
            $contestAction->updateContest($contest, $request, ContestDTO::createFromRequest($request));
            Session::flash('contest_msg', 'Contest Updated!!');
        } catch (\Exception $e) {
            Session::flash('contest_msg', $e->getMessage());
        }
        return back();
    }


    public function destroy($id)
    {
        $contest = Contest::where('user_id', auth()->id())->with('contest_gallery')->findOrFail($id);

        try {
            $disk = Storage::disk('public');

            if (!Contest::query()->where('contest_id', $contest->id)->first()) {
                if ($disk->exists('contest/' . $contest->image)) {
                    $disk->delete('contest/' . $contest->image);
                }
            }

            foreach ($contest->contest_gallery as $gallery_image) {
                if ($disk->exists('contest/gallery/' . $gallery_image->image)) {
                    $disk->delete('contest/gallery/' . $gallery_image->image);
                }
            }

            $contest->delete();

            Session::flash('contest_msg', 'Contest Deleted!!');

        } catch (\Exception $e) {
            Session::flash('contest_msg', $e->getMessage());
        }

        return redirect()->route('user.contests.index');
    }
}
