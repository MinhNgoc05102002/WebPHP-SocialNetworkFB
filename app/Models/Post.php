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
        $post = DB::insert('INSERT INTO post (username,content,createdAt,) values (?, ?)',[
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

    public function getNumRecentPost(){
        $num = DB::select(' SELECT count(*) as num_new_post
                            FROM post
                            WHERE datediff(createdAt, now()) < 7; ');
        return $num[0];
    }

    public function getResultPost($data){

        $stringSearch = DB::select('
                                SELECT * FROM post WHERE content LIKE ?',
                                ['%' . $data['string_search'] . '%']
                                );
        // dd($data['string_search']);
        return $stringSearch;
    }
}

