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


    public function getListPostByFilter($pageCount,$pageIndex,$username){
        $count_page = intval($pageCount);
        $index_page = intval($pageIndex);
        $post = [];
        if($count_page && $index_page){
            $post = DB::select("
                SELECT *
                FROM Post WHERE username = :username AND (is_deleted != 1 or is_deleted is null) ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset
            ", [
                'username' => $username,
                'limit' => $pageCount,
                'offset' => ($index_page - 1) * $count_page,
            ]);    
        }
        
        return $post;
    }

    public function createPost($data,$username,$media){
        $mediaJson = $media;
        // $post = DB::insert('INSERT INTO Post (username,content,created_at,audience_type,media_info) values (?, ?, NOW(),?,?)',[
        //     $username,
        //     $data['content'],
        //     $data['audience_type'],
        //     $mediaJson
        // ]);
        $insertedId = DB::table('Post')->insertGetId([
            'username' => $username,
            'content' => $data['content'],
            'created_at' => now(),
            'audience_type' => $data['audience_type'],
            'media_info' => $mediaJson
        ]);
        $post = DB::table('Post')->where('post_id', $insertedId)->first();
        return $post;
    }

    public function updatePost($data,$post,$username,$media){
        $mediaJson = $media;
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

    public function deletePost($data,$username){
        $post = DB::update(
            'UPDATE Post SET is_deleted = ? WHERE post_id = ? AND username = ?',
            [
                1,
                $data['id_post'],
                $username->username
            ]);
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

