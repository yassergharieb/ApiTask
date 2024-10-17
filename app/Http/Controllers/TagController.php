<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
           $tags =  Tag::paginate(5);
           return $this->sucsessResponse(["data" =>  $tags]);
    }


    public function store(CreateTagRequest $request)
    {
         $tag =   Tag::create(['name' => $request->name]);
         if($tag) {
             return $this->sucsessResponse(['msg' => "tag created successfully" , "data" => New TagResource($tag) ]);
         } else {
             return  $this->errorResponse(['msg' => "something went wrong!"]);
         }

    }


    public function show(Tag $tag)
    {
        return $this->sucsessResponse(['data' =>   new TagResource($tag)]);
    }



    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $tag->name = $request->get('name');
        $tag->save();
        return $this->sucsessResponse(['msg' =>  "Tag updated successfully" , "tag" =>  new TagResource($tag)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        $bol = $tag->delete();
        if($bol) {
          return $this->sucsessResponse(["msg" => "tag deleted successfully"]);
        } else {
          return  $this->errorResponse(["msg" => "some thing wen wrong"]);
        }
    }
}
