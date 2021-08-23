<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class ProcessTable extends Model
{
    public function GetAllProject()
    {
        $query = "SELECT * FROM tb_process";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
}
