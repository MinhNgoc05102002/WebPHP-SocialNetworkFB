<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Account extends Model
{
    use HasFactory;
    protected $table = 'Account';

    public function getNumRecentAccount(){
        $num = DB::select(' SELECT count(*) as num_new_acc
                            FROM account
                            WHERE datediff(createdAt, now()) < 7; ');
        return $num[0];
    }
}
