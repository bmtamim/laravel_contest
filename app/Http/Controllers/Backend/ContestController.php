<?php

namespace App\Http\Controllers\Backend;

use App\Actions\ContestAction;
use App\DTO\ContestDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\ContestRequest;
use App\Models\Category;
use App\Models\Contest;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContestController extends Controller
{

    public function index(): View
    {
        $contests = Contest::query()->parent()->latest()->get();
        return view('backend.contest.index', compact('contests'));
    }

    public function create(): View
    {
        $categories = Category::query()->where('status', true)->get();
        return view('backend.contest.create', compact('categories'));
    }


    public function store(ContestRequest $request, ContestAction $contestAction)
    {
        try {
            $contestAction->createContest($request, ContestDTO::createFromRequest($request));
            Session::flash('toast', [
                'type' => 'success',
                'msg'  => 'Success, Contest Added!!',
            ]);
        } catch (\Exception $e) {
            Session::flash('toast', [
                'type' => 'error',
                'msg'  => $e->getMessage(),
            ]);
        }
        return back();

    }


    public function show(Contest $contest)
    {
        //
    }


    public function edit($id)
    {
        $contest = Contest::with('contest_gallery', 'categories')->findOrFail($id);
        $categories = Category::query()->where('status', true)->get();
        return view('backend.contest.edit', compact('contest', 'categories'));
    }


    public function update(ContestRequest $request, Contest $contest, ContestAction $contestAction)
    {
        try {
            $contestAction->updateContest($contest, $request, ContestDTO::createFromRequest($request));
            Session::flash('toast', [
                'type' => 'success',
                'msg'  => 'Success, Contest Updated!!',
            ]);
        } catch (\Exception $e) {
            Session::flash('toast', [
                'type' => 'error',
                'msg'  => $e->getMessage(),
            ]);
        }
        return back();
    }


    public function destroy($id)
    {
        $contest = Contest::with('contest_gallery')->findOrFail($id);
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

            Session::flash('toast', [
                'type' => 'success',
                'msg'  => 'Success, Contest Deleted!!',
            ]);

        } catch (\Exception $e) {
            Session::flash('toast', ['type' => 'error',
                                     'msg'  => $e->getMessage(),]);
        }

        return back();
    }

    public
    function pendingView()
    {
        $contests = Contest::query()->where('user_id', '!=', auth()->id())->where(['status' => false, 'contest_id' => null])->latest()->get();
        return view('backend.contest.pending', compact('contests'));
    }
}
