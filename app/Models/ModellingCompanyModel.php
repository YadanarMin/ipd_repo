<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class ModellingCompanyModel extends Model{
    
    protected $table = "tb_partnercompany_contact";
    public $timestamps = false;
    
    protected $fillable = [
            "partnerCompanyName",
            "partnerJobType",
            "partnerCompanyBranch",
            "partnerMailCode",
            "partnerCompanyAddress",
            "partnerInchargeName",
            "partnerPhone",
            "partnerEmail"
        ];
        
    public function GetInfoById($id, $branch){
        if(empty($branch)){
            $query = "select c.id, c.name as companyName, c.industry_type,
                    b.name, b.postal_code, b.address ,
                    m.isPartnerCompany, m.user_id
                    from tb_company as c join tb_branch_office as b on c.id = b.company_id
                    join tb_modelling_company as m on c.id = m.company_id where c.id = $id";
        }else{
            $query = "select c.id, c.name as companyName, c.industry_type,
                    b.name, b.postal_code, b.address ,
                    m.isPartnerCompany, m.user_id
                    from tb_company as c join tb_branch_office as b on c.id = b.company_id
                    join tb_modelling_company as m on c.id = m.company_id where c.id = $id and b.name='$branch' and m.branch ='$branch'";
        }
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
        
    public function GetAllModellingCompany(){
        $query = "select modelling.company_id ,modelling.branch, company.name from tb_modelling_company as modelling join tb_company as company on modelling.company_id = company.id";
	    $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    public function GetModellingBranch($companyId,$branch){
        $query = "select id from tb_branch_office where company_id=$companyId and name ='$branch'";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    public function GetCompanyList(){
        $query = "select c.name, b.name as branch from tb_company as c left join tb_branch_office as b on c.id = b.company_id 
                    where company_type_id = 4";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    public function GetCompanyInfoByNameAndBranch($companyId){
        
        $query = "select c.id, c.industry_type, b.name, b.postal_code, b.address from tb_company as c
                  join tb_branch_office as b on c.id = b.company_id where c.id=$companyId ";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    public function GetModellingCompanyInfo(){
        // $query = "select modelling.* , company.* from tb_modelling_company as modelling join tb_company as company on modelling.company_id = company.id";
	    $query = "select modelling.* , company.name, company.industry_type, b.postal_code, b.address, b.name as branch from tb_modelling_company as modelling 
                    left join tb_company as company on modelling.company_id = company.id
                    left join tb_branch_office as b on modelling.company_id = b.company_id and modelling.branch =b.name";
	    $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    public function GetModellingInfoById($companyId,$branch){
        $b = empty($branch)? "" : $branch;
        $query = "select * from tb_modelling_company where company_id= $companyId and branch='$b'";
	    $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    public function GetPostalAndAddress($id){
        $query = "select * from tb_branch_office where company_id = $id";
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    public function GetModellingCompanyById($id){
	    $query = "select company.* , modelling.*  from tb_company as company
                    join tb_modelling_company as modelling on company.id = modelling.company_id
                     where company.id = $id";
    	$data = DB::select($query);
    	return json_decode(json_encode($data),true);
	}
	
	public function DeleteModellingCompanyById($id){
	    $data = DB::delete('DELETE FROM tb_modelling_company WHERE company_id = ?',[$id]);
        return $data;
	}
    
    public function GetPartnerCompanyContactById($id){
	    $query = "SELECT * FROM tb_partnercompany_contact WHERE id = $id" ;
    	$data = DB::select($query);
    	return $data;
	}
	
	public function UpdatePartnerCompanyContactById($id, $data){
    	$partnerCompanyName = $data['partnerCompanyName'];
    	$partnerJobType = $data['partnerJobType'];
        $partnerCompanyBranch = $data['partnerCompanyBranch'];
        $partnerMailCode = $data['partnerMailCode'];
        $partnerCompanyAddress = $data['partnerCompanyAddress'];
        $partnerInchargeName = $data['partnerInchargeName'];
        $partnerPhone = $data['partnerPhone'];
        $partnerEmail = $data['partnerEmail'];
        
    	
    	$data = DB::update('update tb_partnercompany_contact set partnerCompanyName = ?,
    										partnerJobType =?,
    										partnerCompanyBranch =?,
    										partnerMailCode =?,
    										partnerCompanyAddress  =?,
    										partnerInchargeName  =?,
    										partnerPhone  =?,
    										partnerEmail  =?
    										where id = ?',
    										[$partnerCompanyName,
    										$partnerJobType ,
    										$partnerCompanyBranch,
    										$partnerMailCode,
    										$partnerCompanyAddress,
    										$partnerInchargeName,
    										$partnerPhone,
    										$partnerEmail,
    										$id]);
    	
    	return $data;
   
    }
    
    public function DeletePartnerCompanyContactById($id){
        $data = DB::delete('DELETE FROM tb_partnercompany_contact WHERE id = ?',[$id]);
        return $data;
    }
    
    //Table Column Hide or Show By LoginUser
    //tb_displayornot_contact
    public function HideTableColumnDisplay($columnName,$loginUser){
        $query1 = "select mail from tb_personal where concat(first_name,last_name) = '$loginUser'";
    	$data1 = DB::select($query1);
    	$email = $data1[0]->mail;
        
    	$query = "select count(loginUser) as count from tb_displayornot_contact where loginUser = '$email'" ;
    	$data = DB::select($query);
    	if($data[0]->count == 0){
    		DB::insert("insert into tb_displayornot_contact (loginUser, $columnName) values(?,?)",[$email,0]);
    	}else{
    		DB::update("update tb_displayornot_contact set $columnName=? where loginUser=?", [0, $email ]);
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
        
        
    	$query = "select count(loginUser) as count from tb_displayornot_contact where loginUser = '$email'" ;
    	$data = DB::select($query);
    	if($data[0]->count == 0){
    		DB::insert("insert into tb_displayornot_contact (loginUser, $columnName) values(?,?)",[$email,1]);
    	}else{
    		DB::update("update tb_displayornot_contact set $columnName=? where loginUser=?", [1, $email ]);
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
        
    	$query = "select * from tb_displayornot_contact where loginUser = '$email'";
    	$data = DB::select($query);
        return $data;
    }
    
    public function GetAllPartnerCompanyContact(){
        $query = "select * from tb_partnercompany_contact";
    	$data = DB::select($query);
        return $data;
    }
    
}