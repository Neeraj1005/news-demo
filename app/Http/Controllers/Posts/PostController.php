<?php

namespace App\Http\Controllers\Posts;

use App\Tag;
use App\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\PostStoreRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:list-post')->only(['index', 'savedPosts']);
        $this->middleware('permission:create-post')->only(['create', 'store']);
        $this->middleware('permission:view-post')->only('view');
        $this->middleware('permission:edit-post')->only(['edit', 'update', 'isActive']);
        $this->middleware('permission:delete-post')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd(auth()->user()->hasRole('superadmin'));
        // $query = Post::query();

        // //Refactor code
        // $query->activePost(request('filterByactive'));

        // $query->inactivePost(request('filterByinactive'));

        // $query->draftPost(request('draftPost'));

        // $query->trashPost(request('trashPost'));

        // if(auth()->user()->hasRole('superadmin')) {

        //     $allPosts = $query->orderBy('id','desc')->get();
        // } else {

        //     $allPosts = $query->where('user_id',auth()->user()->id)->latest()->get();
        // }

        // return view('cms.posts.index',compact('allPosts'));
        return view('cms.posts.index');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cms.posts.create',[
            'tags' => Tag::get(['id','name']),
            // 'subcategories' => Subcategory::where('category_id',1)->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostStoreRequest $request)
    {
        // dd(request('postType'));
        $input = $request->validated();

        $input['commentActive'] = $request->has('commentActive') ? true : false;

        // dd($input);
        switch (request('postType')) {
            case 'draft':
                $input['published'] = false;
                $input['isactive'] = false;
                break;

            case 'publish':
                $input['published'] = true;
                break;

            default:
                $input['published'] = true;
                break;
        }

        $posts = auth()->user()->posts()->create($input);

        if (request('tags') != '') {
            $tags = explode(',', request('tags'));
            foreach ($tags as $tag_name) {
                $tag = Tag::firstOrCreate(['name' => $tag_name]);
                $posts->tags()->attach($tag);
            }
        }

        if($request->hasFile('featuredimage')){

            $posts->addMedia($request->featuredimage)
                    ->toMediaCollection('posts');
        }

        return redirect(route('posts.index'))->withMessage('post ???? created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        // if (auth()->user()->hasRole('superadmin')) {
        //     return view('cms.posts.show',compact('post'));
        // } elseif (auth()->user()->hasRole('user')) {
        //     abort_if((Auth::id() != $post->user_id), 403);
        //         return view('cms.posts.show',compact('post'));
        // }
        try{
            // $post->increment('postcount');

            $post->load(['category', 'tags', 'user', 'subcategory']);

            Gate::authorize('view', $post);

        } catch (ModelNotFoundException $exception) {
            return view('errors._model_not_found_exception');

        }
        return view('cms.posts.show',compact('post'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $tags = Tag::get(['id','name']);

        $post->load('tags');

        // if (auth()->user()->hasRole('superadmin')) {
        //     return view('cms.posts.edit',compact('categories','tags','post'));
        // } elseif(auth()->user()->hasRole('user')) {
        //     abort_if((Auth::id() != $post->user_id), 403);
        //     return view('cms.posts.edit',compact('categories','tags','post'));
        // }

        Gate::authorize('update', $post);
        return view('cms.posts.edit',compact('tags','post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(PostStoreRequest $request, Post $post)
    {
        // dd($request->input('featuredimage', false));
        $input = $request->validated();

        $input['commentActive'] = $request->has('commentActive') ? true : false;

        switch (request('postType')) {
            case 'draft':
                $input['published'] = false;
                $input['isactive'] = false;
                break;

            case 'publish':
                $input['published'] = true;
                $input['isactive'] = true;
                break;

            default:
                $input['published'] = true;
                break;
        }
        $post->update($input);

        $tags = [];
        if (request('tags') != '') {
            $tags = explode(',', request('tags'));
            // dd($tags);
            foreach ($tags as $tag_name) {
                $tag = Tag::updateOrCreate(['name' => $tag_name]);
                $tagIds[] = $tag->id;
            }
            // dd($tagIds);
        $post->tags()->sync($tagIds);
        }


        if($request->hasFile('featuredimage')){
            $post->addMedia($request->featuredimage)->toMediaCollection('posts');
        }

        // if ($request->input('featuredimage', false)) {
        //     if (!$post->featured_image || $request->input('featuredimage') !== $post->featured_image->file_name) {
        //         $post->addMedia($request->featuredimage)->toMediaCollection('posts');
        //     }
        // } elseif ($post->featured_image) { // y tab run hoga jab post k pass phele se image hai but upload nhi kiya to delete
        //     $post->featured_image->delete();
        // }

        return redirect(route('posts.index'))->withInfo('Posts updated succesfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);

        $post->delete();

        return redirect(route('posts.index'))->with('message','Posts deleted succesfully');
    }

    public function isActive(Post $isActive)
    {
        // dd($isActive->isactive);
        if($isActive->isactive === 1){
            $isActive->update([
                'isactive' => 0,
            ]);
        } else {
            $isActive->update([
                'isactive' => 1,
            ]);
        }
        return redirect()->back()->withInfo('post status has been changed');
    }

    public function featured(Post $featured)
    {
        // dd($isActive->isactive);
        if($featured->featured === 1){
            $featured->update([
                'featured' => 0,
            ]);
        } else {
            $featured->update([
                'featured' => 1,
            ]);
        }
        return redirect()->back()->withInfo('post featured has been changed');
    }

    public function auth_user_saved_post()
    {
        $savedPosts = Auth::user()->favorites()->get();

        return view('cms.posts.saved-posts', compact('savedPosts'));
    }
}
