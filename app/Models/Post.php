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
                            WHERE datediff(created_at, now()) <= 7; ');
        if(count($num) == 0) return null;
        return $num[0]->num_new_post;
    }

    public function getResultPost($data){

        $stringSearch = DB::select('
                                SELECT * FROM Post WHERE content LIKE ?',
                                ['%' . $data['string_search'] . '%']
                                );
        // dd($data['string_search']);
        return $stringSearch;
    }

    public function getNumNewPostByDate(){
        return DB::select(' SELECT dates.creation_date, COUNT(Post.post_id) AS count
                            FROM (
                                SELECT DATE(DATE_SUB(NOW(), INTERVAL n DAY)) AS creation_date
                                FROM (
                                    SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3
                                    UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7
                                    UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10
                                ) AS days
                                WHERE DATE(DATE_SUB(NOW(), INTERVAL n DAY)) >= DATE(NOW()) - INTERVAL 7 DAY
                            ) AS dates
                            LEFT JOIN Post ON DATE(Post.created_at) = dates.creation_date
                            GROUP BY dates.creation_date
                            ORDER BY dates.creation_date DESC; ');
    }
}

