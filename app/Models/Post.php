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
        $post = DB::select("CALL getHomePost(:current_username, :page_index, :page_size)",[ 
            'current_username' => $username,
            'page_index' => $index_page,
            'page_size' => $count_page,
        ]);

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
        $post = DB::select("CALL getProfilePost(:profile_username, :current_username, :page_index, :page_size)",[
            'current_username' => $username,
            'profile_username' => $userProfile,
            'page_index' => $index_page,
            'page_size' => $count_page,
        ]);

        return $post;
    }

    public function getPostById($id,$username){
        $post = DB::select("SELECT t1.*, t2.fullname,t2.avatar, react_type1, react_type2, react_type3, React.react_type current_react_type  
        FROM Post t1 
        JOIN Account t2 on t2.username = t1.username
        left join (
            SELECT post_id,
                MAX(CASE WHEN react_rank = 1 THEN react_type END) AS react_type1,
                MAX(CASE WHEN react_rank = 2 THEN react_type END) AS react_type2,
                MAX(CASE WHEN react_rank = 3 THEN react_type END) AS react_type3
            FROM (
                SELECT post_id, react_type, ROW_NUMBER() OVER (PARTITION BY post_id ORDER BY COUNT(*) DESC) AS react_rank
                FROM React
                WHERE post_id = :post_id_1
                GROUP BY react_type, post_id
            ) AS subquery
            WHERE subquery.react_rank <= 3 
            group by post_id) top_like_table on t1.post_id = top_like_table.post_id 
            left join React on React.post_id = t1.post_id and React.username = :username
            WHERE t1.post_id = :post_id_2;
            ",[ 
                'post_id_1' => intval($id),
                'post_id_2' => intval($id),
                'username' => $username
            ]);

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
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $insertedId = DB::table('Post')->insertGetId([
            'username' => $username,
            'content' => $data['content'],
            'created_at' => now(),
            'audience_type' => $data['audience_type'],
            'media_info' => $mediaJson
        ]);
        $post = DB::select('SELECT Post.*, Account.fullname,Account.avatar FROM Post JOIN Account on Post.username = Account.username WHERE post_id = :post_id',[
            'post_id' => $insertedId
        ]);
        return $post[0];
    }



    public function updatePost($data,$post,$username,$media){
        $mediaJson = $media;
        date_default_timezone_set('Asia/Ho_Chi_Minh');
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
            $updatedPost = DB::select('SELECT Post.*, Account.fullname,Account.avatar FROM Post JOIN Account on Post.username = Account.username WHERE post_id = :post_id',[
                'post_id' => $data['id_post']
            ]);

            return $updatedPost;
        }
        return null;
        // Truy vấn lại bản ghi vừa cập nhật

    }

    public function deletePost($data,$username){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
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

