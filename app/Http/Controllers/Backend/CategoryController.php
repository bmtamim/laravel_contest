<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::query()->latest()->get();
        return view('backend.category.index', compact('categories'));
    }


    public function create()
    {
        return view('backend.category.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name'   => ['required', 'string'],
            'image'  => ['required', 'image', 'mimes:png,jpg,jpeg,gif,svg'],
            'status' => ['nullable', 'string', 'max:10'],
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = Str::slug($request->name) . '-' . date('dmyhisa') . '-' . uniqid() . '.' . $file->getClientOriginalExtension();

            $disk = Storage::disk('public');
            if (!$disk->exists('category')) {
                $disk->makeDirectory('category');
            }

            $disk->put('category/' . $fileName, $file->getContent());

            $imageName = $fileName;
        }

        try {
            Category::create([
                'name'   => $request->name,
                'image'  => $imageName,
                'status' => $request->filled('status'),
            ]);

            Session::flash('toast', [
                'type' => 'success',
                'msg'  => 'Success, Category Created!!',
            ]);
        } catch (\Exception $e) {
            Session::flash('toast', [
                'type' => 'error',
                'msg'  => $e->getMessage(),
            ]);
        }

        return back();
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $editCategory = Category::query()->findOrFail($id);

        $categories = Category::query()->latest()->get();

        return view('backend.category.index', compact('categories', 'editCategory'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::query()->findOrFail($id);

        $request->validate([
            'name'   => ['required', 'string'],
            'image'  => ['nullable', 'image', 'mimes:png,jpg,jpeg,gif,svg'],
            'status' => ['nullable', 'string', 'max:10'],
        ]);

        $imageName = $category->image;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = Str::slug($request->name) . '-' . date('dmyhisa') . '-' . uniqid() . '.' . $file->getClientOriginalExtension();

            $disk = Storage::disk('public');
            if (!$disk->exists('category')) {
                $disk->makeDirectory('category');
            }

            $disk->put('category/' . $fileName, $file->getContent());

            $imageName = $fileName;

            if ($disk->exists('category/' . $category->image)) {
                $disk->delete('category/' . $category->image);
            }
        }

        try {
            $category->update([
                'name'   => $request->name,
                'image'  => $imageName,
                'status' => $category->id == 1 ? true : $request->filled('status'),
            ]);

            Session::flash('toast', [
                'type' => 'success',
                'msg'  => 'Success, Category Updated!!',
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
        $category = Category::query()->findOrFail($id);

        try {
            /* DB::table('category_contest')->where('category_id', $category->id)->update(['category_id' => 1]);
            */
            $disk = Storage::disk('public');
            if ($disk->exists('category/' . $category->image)) {
                $disk->delete('category/' . $category->image);
            }

            $category->delete();

            Session::flash('toast', [
                'type' => 'success',
                'msg'  => 'Success, Category Deleted!!',
            ]);
        } catch (\Exception $e) {
            Session::flash('toast', [
                'type' => 'error',
                'msg'  => $e->getMessage(),
            ]);
        }

        return redirect()->route('admin.category.index');
    }
}
