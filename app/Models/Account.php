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

    public function getInfoAccount($email){
        $num = DB::select(' SELECT username, email, avatar, phone, location
                            FROM account
                            WHERE email = ? ',[$email]);
        return $num[0];
    }

    public function getNumRecentAccount(){
        $num = DB::select(' SELECT count(*) as num_new_acc
                            FROM account
                            WHERE datediff(createdAt, now()) < 7; ');
        return $num[0];
    }

    //protected $name, $avatar, $numberFriend;

    public function getResultAccount($data){

        $stringSearch = DB::select('
                                SELECT * FROM account WHERE username LIKE ?',
                                ['%' . $data['string_search'] . '%']
                                );
        // dd($data['string_search']);
        return $stringSearch;
    }
}
