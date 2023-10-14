<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Report extends Model
{
    use HasFactory;
    protected $table = 'Report';

    public function getNumNewReport(){
        $num = DB::select(' SELECT count(*) as num_new_report
                            FROM Report
                            WHERE datediff(created_at, now()) <= 7; ');
        if(count($num) == 0) return null;
        return $num[0]->num_new_report;
    }

    public function getNumReportByDate(){
        return DB::select(' SELECT dates.creation_date, COUNT(Report.username + \'_\' + Report.post_id) AS count
                            FROM (
                                SELECT DATE(DATE_SUB(NOW(), INTERVAL n DAY)) AS creation_date
                                FROM (
                                    SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3
                                    UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7
                                    UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10
                                ) AS days
                                WHERE DATE(DATE_SUB(NOW(), INTERVAL n DAY)) >= DATE(NOW()) - INTERVAL 7 DAY
                            ) AS dates
                            LEFT JOIN Report ON DATE(Report.created_at) = dates.creation_date
                            GROUP BY dates.creation_date
                            ORDER BY dates.creation_date DESC;');
    }

    public function getListReportedPost($pageIndex, $pageCount) {
        return DB::select(' SELECT Post.post_id, Post.created_at, like_count, comment_count, is_deleted, share_count, audience_type, Post.username, count(Report.username) as num_report
                            from Post join Report on Post.post_id = Report.post_id
                            group by Post.post_id, Post.created_at, like_count, comment_count, is_deleted, share_count, audience_type, username
                            LIMIT ? OFFSET ?;',
                            [
                                $pageCount,
                                $pageCount * $pageIndex
                            ]
                            );
    }
}
