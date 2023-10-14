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

