<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{

    public function __construct()
    {
        $this->middleware('api:user');
    }

    /**
     * Reset the logged in user's password
     * @method reset
     *
     * @return   response
     */
    public function reset()
    {
        $this->validate(request(),[
            'password' => 'required|confirmed|min:6'
        ]);

        auth()->user()->update([
            'password' => bcrypt( request('password') )
        ]);

        return response([],202);
    }
}
