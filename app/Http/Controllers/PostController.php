<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $posts = Post::all();
        return view('post-create', compact('posts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:300',
            'image' => 'nullable|mimes:png,jpg,jpeg,webp|max:1024',
            'body' => 'required|string|max:250'
        ]);

        if($request->has('image')){
            $imagename = time().'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('uploads/images/'),$imagename);
            $data['image'] = $imagename;
        }

        Post::create($data);
        return back()->with('message', 'Post has been created...');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        return view('post-edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title'=> 'required|string|max:300',
            'image'=> 'sometimes|file|mimes:png,jpg,webp,jpeg|max:1024',
            'body'=> 'required|string|max:250',
        ]);

        if($request->hasFile('image')){
            $destination = 'uploads/images/'.$post->image;

            if(File::exists($destination)){
                File::delete($destination);
            }

            $imageName = time().'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('uploads/images/'), $imageName);

            $data['image'] = $imageName;
        }

        $post->update($data);
        return redirect()->route('post.create')->with('message', 'Post has been updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if($post->image){
            $destination = 'uploads/images/'.$post->image;
            if(File::exists($destination)){
                File::delete($destination);
            }

            $post->delete();
            return back()->with('message', 'Post has been deleted');
        }
    }
}
