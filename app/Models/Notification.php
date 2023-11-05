<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'Notification';
    public $timestamps = false;

    public function getById($id){
        $query = "SELECT n.*,a.fullname,c.value as content_noti,a.avatar FROM Notification n
                      INNER JOIN Account a ON n.sender_username = a.username
                      INNER JOIN Classification c ON c.code = n.noti_type
                      WHERE n.noti_id = :id";
        $noti = DB::select($query, ['id' => $id]);

        return $noti;
    }
}
