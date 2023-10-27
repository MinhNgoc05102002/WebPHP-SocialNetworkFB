<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use DB;


class Account extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'Account';
    public $timestamps = false;
    protected $primaryKey = 'username';
    public $incrementing = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'fullname',
        'day_of_birth',
        'gender',
        'created_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getInfoAccount($username){
        $num = DB::select(' SELECT username, email, avatar, phone, location
                            FROM Account
                            WHERE username = ? ',[$username]);
        return $num;
    }

    public function getNumNewAccount(){
        $num = DB::select(' SELECT count(*) as num_new_acc
                            FROM Account
                            WHERE datediff(created_at, now()) <= 7; ');
        if(count($num) == 0) return null;
        return $num[0]->num_new_acc;
    }

    public function getNumNewBlock(){
        $num = DB::select(' SELECT count(*) as num_new_block
                            FROM Account
                            WHERE datediff(modified_date, now()) <= 7 AND
                                  status = \'BLOCK\'; ');
        if(count($num) == 0) return null;
        return $num[0]->num_new_block;
    }

    public function getNumAccByAge() {
        $res = DB::select(' SELECT C.code, IFNULL(T.account_count, 0) account_count, C.value
                            FROM Classification C LEFT JOIN (
                                SELECT
                                CASE
                                    WHEN YEAR(NOW()) - YEAR(day_of_birth) >= 0 AND YEAR(NOW()) - YEAR(day_of_birth) <= 12 THEN \'0-12\'
                                    WHEN YEAR(NOW()) - YEAR(day_of_birth) >= 13 AND YEAR(NOW()) - YEAR(day_of_birth) <= 17 THEN \'13-17\'
                                    WHEN YEAR(NOW()) - YEAR(day_of_birth) >= 18 AND YEAR(NOW()) - YEAR(day_of_birth) <= 24 THEN \'18-24\'
                                    WHEN YEAR(NOW()) - YEAR(day_of_birth) >= 25 AND YEAR(NOW()) - YEAR(day_of_birth) <= 34 THEN \'25-34\'
                                    WHEN YEAR(NOW()) - YEAR(day_of_birth) >= 35 AND YEAR(NOW()) - YEAR(day_of_birth) <= 54 THEN \'35-54\'
                                    WHEN YEAR(NOW()) - YEAR(day_of_birth) >= 55 THEN \'55+\'
                                    ELSE \'Không xác định\'
                                END AS AGE_RANGE,
                                COUNT(*) AS ACCOUNT_COUNT
                                FROM Account
                                GROUP BY AGE_RANGE
                            ) T ON C.code = T.AGE_RANGE
                            WHERE C.type = \'AGE_RANGE\'; ');
        return $res;
    }

    //protected $name, $avatar, $numberFriend;

    public function getResultAccount($data){

        $stringSearch = DB::select('
                                SELECT * FROM Account WHERE username LIKE ?',
                                ['%' . $data['string_search'] . '%']
                                );
        // dd($data['string_search']);
        return $stringSearch;
    }

    public function checkDuplicate($username, $email){
        $count = $this->where(function ($query) use ($username, $email) {
            $query->where('username', $username)
                ->orWhere('email', $email);
        })->count();

        return $count > 0;
    }



    public function getNumNewAccountByDate(){
        return DB::select(' SELECT dates.creation_date, COUNT(Account.username) AS account_count
                            FROM (
                                SELECT DATE(DATE_SUB(NOW(), INTERVAL n DAY)) AS creation_date
                                FROM (
                                    SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3
                                    UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7
                                    UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10
                                ) AS days
                                WHERE DATE(DATE_SUB(NOW(), INTERVAL n DAY)) >= DATE(NOW()) - INTERVAL 7 DAY
                            ) AS dates
                            LEFT JOIN Account ON DATE(Account.created_at) = dates.creation_date
                            GROUP BY dates.creation_date
                            ORDER BY dates.creation_date DESC; ');
    }

    public function getListReportedAcc($pageIndex, $pageSize) {
        return DB::select(' SELECT Account.username, email, fullname, about_me, location, gender, day_of_birth, status, modified_date, count(Report.username) as num_report
                            from Post join Report on Post.post_id = Report.post_id
                                    join Account on Post.username = Account.username
                            group by Account.username, email, fullname, about_me, location, gender, day_of_birth, status, modified_date
                            LIMIT ? OFFSET ?;',
                            [
                                $pageSize,
                                $pageSize * $pageIndex
                            ]
                            );
    }

    public function handleBlockAcc($username) {
        $status = DB::select('SELECT status FROM Account WHERE username = ? ;', [$username]);
        $newStatus = $status[0]->status == 'ACTIVE' ? 'BLOCK' : 'ACTIVE';
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $acc = DB::update(
                    'UPDATE Account SET status = ?, modified_date = ? WHERE username = ? ',
                    [
                        $newStatus,
                        date('Y-m-d H:i:s'),
                        $username,
                    ]);
        return $newStatus;
    }

    public function sendWarningAcc($username) {
        $status = DB::select('SELECT status FROM Account WHERE username = ? ;', [$username]);
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        DB::select("call createNoti (:i_noti_type, :i_link, :i_sender_username, :i_username, :i_created_at);",[
            'i_noti_type' => 'ADMIN',
            'i_link' => '',
            'i_sender_username' => 'ADMIN',
            'i_username' => $username,
            'i_created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
