<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ResponseHandler
{
   public function sucsessResponse($data =  [])
   {
      return response()->json($data , Response::HTTP_OK);
   }

    public function errorResponse($data =  [])
    {
        return response()->json($data , Response::HTTP_BAD_REQUEST);
    }
}
