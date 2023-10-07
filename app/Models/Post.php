<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Post extends Model
{
    use HasFactory;
    protected $table = 'Post';

    public function create($data){
        $post = DB::insert('INSERT INTO Post (username,content) values (?, ?)',[
            $data['username'],
            $data['content'],
        ]);
        return $post;
    }

    // public function update($data){
    //     $post = DB::update('UPDATE post SET content = ?',[
    //         $data['content'],
    //     ]);
    //     return $post;
    // }

    // public function delete($data){
    //     $post = DB::insert('DELETE FROM post WHERE postId = ?',[
    //         $data['id'],
    //     ]);
    //     return $post;
    // }

    public function getNumNewPost(){
        $num = DB::select(' SELECT count(*) as num_new_post
                            FROM Post
                            WHERE datediff(created_at, now()) < 7; ');
        return $num[0];
    }

    public function getResultPost($data){

        $stringSearch = DB::select('
                                SELECT * FROM Post WHERE content LIKE ?',
                                ['%' . $data['string_search'] . '%']
                                );
        // dd($data['string_search']);
        return $stringSearch;
    }
}

