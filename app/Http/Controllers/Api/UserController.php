<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function index()
    {
        return response()->json(
            User::select('id', 'name', 'username')
                ->orderBy('name')
                ->get()
        );  
    }
}
