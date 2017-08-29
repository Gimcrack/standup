<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    
    /**
     * Get an index of the resource
     * @method index
     *
     * @return   response
     */
    public function index()
    {
        return response()->json( User::all(), 200 );
    }

    /**
     * Store the new user
     * @method store
     *
     * @return   response
     */
    public function store()
    {
        User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('password')),
            'api_token' => str_random(60)
        ]);

        return response()->json([], 201);
    }

    /**
     * Promote the user to admin
     * @method promote
     *
     * @return   response
     */
    public function promote(User $user)
    {
        $user->promoteToAdmin();

        return response()->json([],202);
    }

    /**
     * Destroy the specified user
     * @method destroy
     *
     * @return   response
     */
    public function destroy(User $user)
    {
        if ( $user->isAdmin() ) {
            
            return response()->json([
                'errors' => true,
                'message' => 'Cannot delete admin account'
            ], 403);
        }

        $user->delete();

        return response()->json([],202);
    }
}
