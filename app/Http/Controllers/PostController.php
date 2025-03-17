<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Models\Post;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        // Fetch all posts to manage
        $posts = Post::all();
        return view('post.manage', compact('posts'));
    }

    /**
     * Display the list of followers of a specific post.
     *
     * @return Application|Factory|View
     */
    public function followers(): Application|Factory|View
    {
        // Logic for fetching followers can be added here
        return view('post.followers');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        return view('post.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreatePostRequest $request
     * @return RedirectResponse
     */
    public function store(CreatePostRequest $request): RedirectResponse
    {
        // Store the post using the validated request data
        Post::create($request->validated());

        // Redirect to the manage posts page with a success message
        return redirect()->route('post.index')->with('success', 'Post created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return Application|Factory|View
     */
    public function show(Post $post): View|Factory|Application
    {
        return view('post.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Post $post
     * @return Application|Factory|View
     */
    public function edit(Post $post): View|Factory|Application
    {
        return view('post.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Post $post
     * @return RedirectResponse
     */
    public function update(Request $request, Post $post): RedirectResponse
    {
        // Validate and update the post
        $post->update($request->all());

        // Redirect to the posts management page with a success message
        return redirect()->route('post.index')->with('success', 'Post updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Post $post
     * @return RedirectResponse
     */
    public function destroy(Post $post): RedirectResponse
    {
        // Delete the post
        $post->delete();

        // Redirect back with a success message
        return redirect()->route('post.index')->with('success', 'Post deleted successfully!');
    }
}
