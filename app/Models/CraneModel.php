<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class CraneModel extends Model
{

    function GetBranch(){
      $query = "SELECT * FROM tb_branch ORDER BY bid";
      $data = DB::select($query);
      return json_decode(json_encode($data),true);
    }
    
    function GetPartner(){
      $query = "SELECT * FROM tb_partner WHERE bid = (SELECT bid FROM tb_branch ORDER BY bid limit 1)";
      $data = DB::select($query);
      return json_decode(json_encode($data),true);
    }
    
}
