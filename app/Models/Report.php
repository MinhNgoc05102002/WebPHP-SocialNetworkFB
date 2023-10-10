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
                            WHERE datediff(created_at, now()) < 7; ');
        return $num[0];
    }
}
