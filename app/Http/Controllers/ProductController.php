<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Post;

class ProductController extends Controller
{
    public $data = [];
    public function index(){
        $user = Post::all();
        dd($user);
        return view('product', ['user'=>$user]);
        
    }
}
