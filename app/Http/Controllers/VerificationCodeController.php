<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserVerficationRequest;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerificationCodeController extends Controller
{

    public function verify(UserVerficationRequest $request)
    {
        $current_date = now();
        $code         = VerificationCode::where([
            'code'  => $request->code,
        ])->first();

        if ($code && $code->code_expiration_date > $current_date) {
            try {
                DB::beginTransaction();

                $code->is_used =  1;
                $code->used_at =  now();
                $code->save();

                $user = auth()->user();
                $user->is_verified =  true;
                $user->save();
                DB::commit();

               return   $this->sucsessResponse(['msg' =>  'User verified successfully!']);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Verification failed: ' . $e->getMessage());
                return $this->errorResponse(['msg' => 'Verification failed. Please try again.']);
            }
        }

        return $this->errorResponse(['msg' => 'Invalid or expired verification code.']);
    }


}
