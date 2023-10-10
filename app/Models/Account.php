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
                            FROM db_lab.Account
                            WHERE username = ? ',[$username]);                    
        return $num;
    }

    public function getNumNewAccount(){
        $num = DB::select(' SELECT count(*) as num_new_acc
                            FROM Account
                            WHERE datediff(created_at, now()) < 7; ');
        return $num[0]->num_new_acc;
    }

    public function getNumNewBlock(){
        $num = DB::select(' SELECT count(*) as num_new_block
                            FROM Account
                            WHERE datediff(modified_date, now()) < 7 AND
                                  status = \'BLOCK\'; ');
        return $num[0];
    }

    public function getNumAccByAge() {
        $res = DB::select(' SELECT C.name, IFNULL(T.account_count, 0) account_count, C.description
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
                            ) T ON C.name = T.AGE_RANGE
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
}
