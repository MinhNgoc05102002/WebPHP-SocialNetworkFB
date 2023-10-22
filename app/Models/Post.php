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
            $post = DB::select("CALL getHomePost(:current_username, :page_index, :page_size)",[
                'current_username' => $username,
                'page_index' => $index_page,
                'page_size' => $count_page,
            ]);
        }

        return $post;
    }

    public function getListPostProfile($pageCount,$pageIndex,$username,$username_profile){
        $count_page = intval($pageCount);
        $index_page = intval($pageIndex);
        $post = [];
        $userProfile = null;
        if($username_profile){
            $userProfile = $username_profile;
        }
        if($count_page && $index_page){

            $post = DB::select("CALL getProfilePost(:profile_username, :current_username, :page_index, :page_size)",[
                'current_username' => $username,
                'profile_username' => $userProfile,
                'page_index' => $index_page,
                'page_size' => $count_page,
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

        // Thực hiện cập nhật bản ghi
        $isUpdate = DB::table('Post')
            ->where('post_id', $data['id_post'])
            ->where('username',$username)
            ->update([
                'content' => $data['content'],
                'audience_type' => $data['audience_type'],
                'media_info' => $mediaJson,
            ]);
        if($isUpdate){
            $updatedPost = DB::table('Post')
            ->where('post_id', $data['id_post'])
            ->first();

            return $updatedPost;
        }
        return null;
        // Truy vấn lại bản ghi vừa cập nhật

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

    public function handleBlockPost($postId) {
        $status = DB::select('SELECT status FROM Post WHERE post_id = ? ;', [$postId]);
        $newStatus = $status[0]->status == 'ACTIVE' ? 'BLOCK' : 'ACTIVE';

        $post = DB::update(
                    'UPDATE Post SET status = ? WHERE post_id = ? ',
                    [
                        $newStatus,
                        $postId,
                    ]);
        return $newStatus;
    }
}

