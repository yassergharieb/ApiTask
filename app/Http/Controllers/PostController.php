<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\TagResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{

    public function index()
    {
        $user_id = auth()->user()->id;
        $posts   =  Post::userPosts($user_id)
             ->with(['tags' => function ($query) {
                $query->select('tags.id', 'tags.name', 'posts_tags.tag_id');
             }])
            ->orderBy("is_pinned" , "desc")
            ->paginate(10);

        return $this->sucsessResponse(['data' => $posts]);
    }


    public function store(CreatePostRequest $request)
    {
        $data   =  $request->validated();
        $data['cover_image']  =  $request->file('cover_image')->store('cover_images', 'public');
        $data['user_id']      =  auth()->user()->id;
        $post =  Post::create($data);
        $post->tags()->sync($data['tags']);
        $tags =  $post->tags()->pluck('name');
        return $this->sucsessResponse(["msg" => "post created successfully"  , 'data' => $post , "tags" =>  $tags]);
    }

    public function show(Post $post)
    {
        $post = Post::with(['tags' => function ($query) {
                 $query->select('tags.id', 'tags.name', 'posts_tags.tag_id');
        }])->where('id', $post->id)->first();
      return $this->sucsessResponse(["data" => $post]);
    }




    public function update(UpdatePostRequest $request, Post $post)
    {
        $data          =  $request->validated();
        $oldCoverImage =  $post->cover_image;

        if ($oldCoverImage && Storage::exists($oldCoverImage)) {
            Storage::delete($oldCoverImage);
        }
        if ($request->hasFile('cover_image')) {
            $newCoverImage = $request->file('cover_image')->store('cover_images');
            $data['cover_image'] = $newCoverImage;
        }
        $post->update($data);
        return $this->sucsessResponse(['msg' =>  "Tag updated successfully" , 'post' => $post]);
    }

    public function destroy(Post $post)
    {
        $bol = $post->delete();
        if($bol) {
            return $this->sucsessResponse(["msg" => "post deleted successfully"]);
        } else {
            return  $this->errorResponse(["msg" => "some thing wen wrong"]);
        }
    }
}
