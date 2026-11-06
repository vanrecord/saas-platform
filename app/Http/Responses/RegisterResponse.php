<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    protected $home;

    public function toResponse($request)
    {
        // below is the existing response
        // replace this with your own code
        // the user can be located with Auth facade
        $this->home = tenant("id")?config('fortify.home'):"/admin";

        return $request->wantsJson()
                    ? response()->json(['two_factor' => false])
                    : redirect()->intended($this->home);
    }

}