<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class PartnerCompanyModel extends Model
{

    protected $table = 'tb_partnercompany';
    public $timestamps = false;
    
    protected $fillable = [
		'companyName',
		'jobType',
		'yaruki',
		'revit',
		'ipd',
		'satelliteExp',
		'satelliteProjName',
		'remark',
		'inchargeName',
		'phone',
		'email'
	];
	
	public function GetAllPartnerCompany(){
	    $query = "SELECT * FROM tb_company where company_type_id = 3";
	    $data = DB::select($query);
        return $data;
	}
	
	public function GetAllPartnerCompany1(){
	    $query = "SELECT * FROM tb_partnercompany";
	    $data = DB::select($query);
        return $data;
	}
	
	public function GetMaxNumberOfIncharge(){
		$query = "SELECT MAX(MaxNum) as numOfIncharge FROM (SELECT company_id, COUNT(company_id) as MaxNum FROM tb_cooperate_company GROUP BY company_id) as result";
		$data = DB::select($query);
        return $data;
	}
	
	public function GetAllPartnerCompanyInfo(){
	    $query = "select cocom.* , company.* from tb_cooperate_company as cocom join tb_company as company on cocom.company_id = company.id";
	    $data = DB::select($query);
        return json_decode(json_encode($data),true);
	}
	
	public function GetCooperateInfoById($companyId){
	    $query = "select * from tb_cooperate_company where company_id= $companyId";
	    $data = DB::select($query);
        return json_decode(json_encode($data),true);
	}
	
	public function GetPartnerCompanyById($id){
	    $query = "select company.* , cooperate.*  from tb_company as company
                    join tb_cooperate_company as cooperate on company.id = cooperate.company_id
                     where company.id = $id";
    	$data = DB::select($query);
    	return json_decode(json_encode($data),true);
	}
	
	// public function GetMaruFirstPartnerCompany($columnName){
	//     $query = "SELECT * FROM tb_partnercompany ORDER BY $columnName IS NULL, FIELD ($columnName, '◎','〇','△','✖')"; 
	//     $data = DB::select($query);
	//     return $data;
	    
	// }
	
	public function GetMaruFirstPartnerCompany($companyName){
		$query = "SELECT cocom.*, com.* FROM tb_cooperate_company as cocom
					JOIN  tb_company as com 
					ON cocom.company_id = com.id 
					ORDER BY  FIELD ($companyName, '◎','〇','△','✖','NULL')";
		$data = DB::select($query);
		return json_decode(json_encode($data),true);
	}
	
	public function DeletePartnerCompanyById($id){
        $data = DB::delete('DELETE FROM tb_cooperate_company WHERE company_id = ?',[$id]);
        return $data;
    }
    
    public function UpdatePartnerCompanyById($id, $data){
    	$companyName = $data['companyName'];
    	$jobType = $data['jobType'];
        $yaruki = $data['yaruki'];
        $revit = $data['revit'];
        $ipd = $data['ipd'];
        $satelliteExp = $data['satelliteExp'];
        $satelliteProjName = $data['satelliteProjName'];
        $remark = $data['remark'];
        $inchargeName = $data['inchargeName'];
        $phone = $data['phone'];
        $email = $data['email'];
    	
    	$data = DB::update('update tb_partnercompany set companyName = ?,
    										jobType =?,
    										yaruki =?,
    										revit =?,
    										ipd  =?,
    										satelliteExp  =?,
    										satelliteProjName  =?,
    										remark  =?,
    										inchargeName  =?,
    										phone  =?,
    										email  =?
    										where id = ?',
    										[$companyName,
    										$jobType ,
    										$yaruki,
    										$revit,
    										$ipd,
    										$satelliteExp,
    										$satelliteProjName,
    										$remark,
    										$inchargeName,
    										$phone,
    										$email,
    										$id]);
    	
    	return $data;
   
    }
    
    //tb_displayornot
    public function HideTableColumnDisplay($columnName,$loginUser){
    	$query1 = "select mail from tb_personal where concat(first_name,last_name) = '$loginUser'";
    	$data1 = DB::select($query1);
    	$email = $data1[0]->mail;
    	
    	$query = "select count(loginUser) as count from tb_displayornot where loginUser = '$email'" ;
    	$data = DB::select($query);
    	
    	if($data[0]->count == 0){
    		DB::insert("insert into tb_displayornot (loginUser, $columnName) values(?,?)",[$email,0]);
    	}else{
    		DB::update("update tb_displayornot set $columnName=? where loginUser=?", [0, $email ]);
    	}
    	
    	// $query1 = "select $columnName from tb_displayornot where loginUser = '$loginUser'" ;
    	// $result = DB::select($query1);
    	// print_r($result);
    	// return $result;
    }
    
    public function ShowTableColumnDisplay($columnName,$loginUser){
    	$query1 = "select mail from tb_personal where concat(first_name,last_name) = '$loginUser'";
    	$data1 = DB::select($query1);
    	$email = $data1[0]->mail;
    	
    	$query = "select count(loginUser) as count from tb_displayornot where loginUser = '$email'" ;
    	$data = DB::select($query);
    	if($data[0]->count == 0){
    		DB::insert("insert into tb_displayornot (loginUser, $columnName) values(?,?)",[$email,1]);
    	}else{
    		DB::update("update tb_displayornot set $columnName=? where loginUser=?", [1, $email ]);
    	}
    	
    	// $query1 = "select $columnName from tb_displayornot where loginUser = '$loginUser'" ;
    	// $result = DB::select($query1);
    	// print_r($result);
    	// return $result;
    }
    
    public function HideOrShowTable($loginUser){
    	$query1 = "select mail from tb_personal where concat(first_name,last_name) = '$loginUser'";
    	$data1 = DB::select($query1);
    	$email = $data1[0]->mail;
    	
    	$query = "select * from tb_displayornot where loginUser = '$email'";
    	$data = DB::select($query);
        return $data;
    }
}