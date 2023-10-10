<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Post extends Model
{
    use HasFactory;
    protected $table = 'Post';
    protected $primaryKey = 'post_id';
    public $timestamps = false;

    public function createPost($data){
        $mediaJson = $data['media'];
        $post = DB::insert('INSERT INTO Post (username,content,created_at,audience_type,media_info) values (?, ?, NOW(),?,?)',[
            $data['username'],
            $data['content'],
            $data['audience_type'],
            $mediaJson
        ]);
        return $post;
    }

    

    public function updatePost($data,$post){
        $mediaJson = $data['media'];
        $post = DB::update(
            'UPDATE Post SET content = ? ,audience_type = ?,media_info = ? WHERE post_id = ?',
            [
                $data['content'],
                $data['audience_type'],
                $mediaJson,
                $data['id_post'],
            ]);
        return $post;
    }

    public function deletePost($data){
        $post = DB::update(
            'UPDATE Post SET is_deleted = ? WHERE post_id = ?',
            [
                '1',
                $data['id_post']
            ]);
        return $post;
        return $post;
    }

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

