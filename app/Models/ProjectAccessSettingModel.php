<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class ProjectAccessSettingModel extends Model
{


   function SaveProjectAccessSetting($project_list,$access_user_id){
     
     try{
         $project_list_str = "";
         if(sizeof($project_list) > 0){
            $project_list_str = implode (",", $project_list);//array to string conversion
         }
        

        $query = "INSERT INTO tb_project_access_setting(id,login_user_id,accessable_projects) 
                  SELECT MAX(id) +1,$access_user_id,'$project_list_str' FROM tb_project_access_setting
                  ON DUPLICATE KEY UPDATE accessable_projects='$project_list_str'";
        DB::insert($query);
        return "success";
     }catch(Exception $e){
        return $e->getMessage();
     }
     
   }
   
   function SaveAccessSetDetail($project_list,$set_id){
     
     try{

         $project_list_str = "";
         if(sizeof($project_list) > 0){
            $project_list_str = implode (",", $project_list);//array to string conversion
         }

        $query = "UPDATE tb_allstore_authority_set SET detail='$project_list_str' WHERE id= $set_id";
    
        DB::update($query);
        return "success";
     }catch(Exception $e){
        return $e->getMessage();
     }
     
   }
   
   function SaveModelDataSetDetail($project_list,$authority_set_id){
     
     try{
         $project_list_str = "";
         if(sizeof($project_list) > 0){
            $project_list_str = implode (",", $project_list);//array to string conversion
         }
        

        $query = "UPDATE tb_model_data_authority_set SET detail = '$project_list_str' WHERE id = '$authority_set_id'";
        DB::update($query);
        return "success";
     }catch(Exception $e){
        return $e->getMessage();
     }
     
   }
   
   function SaveItemSetDetail($item_list,$authority_set_id){
     
     try{
         $item_list_str = "";
         if(sizeof($item_list) > 0){
            $item_list_str = implode (",", $item_list);//array to string conversion
         }
        

        $query = "UPDATE tb_allstore_item_authority_set SET detail = '$item_list_str' WHERE id = $authority_set_id";
        DB::update($query);
        return "success";
     }catch(Exception $e){
        return $e->getMessage();
     }
     
   }
   
   
   function SaveNewAllstoreSet($newAuthoritySetName){
     
     try{

        $query = "INSERT INTO tb_allstore_authority_set (id,authority_set_name) 
                  SELECT MAX(id) +1,'$newAuthoritySetName' FROM tb_allstore_authority_set
                  ON DUPLICATE KEY UPDATE authority_set_name='$newAuthoritySetName'";
        DB::insert($query);
        return "success";

     }catch(Exception $e){
        return $e->getMessage();
     }
     
   }
   
   function SaveNewAllstoreItemSet($newAuthoritySetName){
     
     try{
            $query = "INSERT INTO tb_allstore_item_authority_set (id,authority_set_name) 
                      SELECT MAX(id) +1,'$newAuthoritySetName' FROM  tb_allstore_item_authority_set
                      ON DUPLICATE KEY UPDATE authority_set_name='$newAuthoritySetName'";
            DB::insert($query);
            return "success";

     }catch(Exception $e){
        return $e->getMessage();
     }
     
   }
   
   function SaveNewModelDataSet($newAuthoritySetName){
     
     try{

            $query = "INSERT INTO tb_model_data_authority_set (id,authority_set_name) 
                      SELECT MAX(id) +1,'$newAuthoritySetName' FROM tb_model_data_authority_set
                      ON DUPLICATE KEY UPDATE authority_set_name='$newAuthoritySetName'";
            DB::insert($query);
            return "success";

     }catch(Exception $e){
        return $e->getMessage();
     }
     
   }
   
   function SaveAllstoreSetId($access_set_id,$access_user_id){
     
     try{
            
            $query = "INSERT INTO tb_project_access_setting (id,login_user_id,allstore_set_id) 
                      SELECT MAX(id) +1,$access_user_id,$access_set_id FROM tb_project_access_setting
                      ON DUPLICATE KEY UPDATE login_user_id = $access_user_id,allstore_set_id = $access_set_id";
            DB::insert($query);
            return "success";
        

     }catch(Exception $e){
        return $e->getMessage();
     }
     
   }
   
   function SaveAllstoreItemSetId($access_set_id,$access_user_id){
     
     try{
            $query = "INSERT INTO tb_project_access_setting (id,login_user_id,allstore_item_set_id) 
                      SELECT MAX(id) +1,$access_user_id,$access_set_id FROM tb_project_access_setting
                      ON DUPLICATE KEY UPDATE login_user_id = $access_user_id ,allstore_item_set_id = $access_set_id";
            DB::insert($query);
            return "success";
        

     }catch(Exception $e){
        return $e->getMessage();
     }
     
   }
   
    function SaveModelDataSetId($access_set_id,$access_user_id){
     
     try{
            $query = "INSERT INTO tb_project_access_setting (id,login_user_id,model_data_set_id) 
                      SELECT MAX(id) +1,$access_user_id,$access_set_id FROM tb_project_access_setting
                      ON DUPLICATE KEY UPDATE login_user_id = $access_user_id ,model_data_set_id = $access_set_id";
            DB::insert($query);
            return "success";
        

     }catch(Exception $e){
        return $e->getMessage();
     }
     
   }
   
   function SaveUserAccessableInfo($user_id,$pj_code_list,$item_index_list,$model_id_list){
       try{
         $item_list_str = "";
         $project_list_str = "";
         $model_list_str = "";
         if(sizeof($pj_code_list) > 0){
            $project_list_str = implode (",", $pj_code_list);//array to string conversion
         }
         if(sizeof($item_index_list) > 0){
            $item_list_str = implode (",", $item_index_list);//array to string conversion
         }
        if(sizeof($model_id_list) > 0){
            $model_list_str = implode (",", $model_id_list);//array to string conversion
         }

        $query = "INSERT INTO tb_project_access_setting(id,login_user_id,accessable_projects,accessable_items,accessable_models) 
                  SELECT MAX(id) +1,$user_id,'$project_list_str','$item_list_str','$model_list_str' FROM tb_project_access_setting
                  ON DUPLICATE KEY UPDATE accessable_projects='$project_list_str',accessable_items ='$item_list_str',accessable_models='$model_list_str'";
        DB::insert($query);
        return "success";
     }catch(Exception $e){
        return $e->getMessage();
     }
   }

    function GetAccessableProjects($access_user_id){
     
     try{

        $query = "SELECT * FROM tb_project_access_setting WHERE login_user_id = $access_user_id";
        $result = DB::select($query);
        return json_decode(json_encode($result),true);
     }catch(Exception $e){
        return $e->getMessage();
     }
     
   }
   
    function GetAllStoreData($access_user_id){
     
     try{
         $shiten_array = array(2=>"東京",3=>"大阪",4=>"名古屋",5=>"九州",6=>"東北",7=>"札幌",8=>"広島",9=>"四国",10=>"北陸");
         
         $query = "SELECT allstore_set_id FROM tb_project_access_setting WHERE login_user_id = $access_user_id";
         $result = DB::select($query);
         $result = json_decode(json_encode($result),true);
         if($result == null || $result[0]["allstore_set_id"] == 0 || $result[0]["allstore_set_id"] == 1){//custom,access all
             $query = "SELECT * FROM tb_allstore_info ";
         }else if($result[0]["allstore_set_id"] >= 2 && $result[0]["allstore_set_id"] <=10){//get data by shiten
                $authority_set_id = $result[0]["allstore_set_id"];
                $accessable_shiten = $shiten_array[$authority_set_id];
                $query = "SELECT *FROM tb_allstore_info WHERE a_shiten LIKE '$accessable_shiten%'";
        }else{
                $allstore_set_id = $result[0]["allstore_set_id"];
                $query = "SELECT * FROM tb_allstore_info 
                WHERE FIND_IN_SET(a_pj_code,(SELECT detail FROM tb_allstore_authority_set WHERE id = $allstore_set_id))"; 

         }
        
        
        $result = DB::select($query);
        return json_decode(json_encode($result),true);
     }catch(Exception $e){
        return $e->getMessage();
     }
     
   }
   
   function GetAccessSetting($access_user_id){
     
     try{
         //check user selected set_id ,if not selected set なし　set by default
         $query = "SELECT allstore_set_id,allstore_item_set_id,model_data_set_id FROM tb_project_access_setting WHERE login_user_id = $access_user_id";
         $result = DB::select($query);
         $result = json_decode(json_encode($result),true);
         if($result == null || $result[0]["allstore_set_id"] == null || $result[0]["allstore_item_set_id"] == null || $result[0]["model_data_set_id"] == null){
             
             $allstore_item_set_id = 0;
             $allstore_set_id = 0;
             $model_data_set_id = 0;
             if($result != null){
                  $allstore_set_id =  $result[0]["allstore_set_id"] == null ? 0 : $result[0]["allstore_set_id"] ;
                  $allstore_item_set_id = $result[0]["allstore_item_set_id"] == null ? 0 : $result[0]["allstore_item_set_id"];
                  $model_data_set_id = $result[0]["model_data_set_id"] == null ? 0 : $result[0]["model_data_set_id"];
             }
             $query = "INSERT INTO tb_project_access_setting (id,login_user_id,allstore_set_id,allstore_item_set_id,model_data_set_id) 
                      SELECT MAX(id) +1,$access_user_id,$allstore_set_id,$allstore_item_set_id,$model_data_set_id FROM tb_project_access_setting
                      ON DUPLICATE KEY UPDATE allstore_set_id = $allstore_set_id,allstore_item_set_id=$allstore_item_set_id,model_data_set_id=$model_data_set_id ";
             DB::insert($query); 

         }
         
          $query = "SELECT 
                 allstore_set.id as allstore_set_id,
                 allstore_set.authority_set_name as allstore_set_name,
                 allstore_item_set.id as allstore_item_set_id,
                 allstore_item_set.authority_set_name as allstore_item_set_name,
                 model_data.id as model_data_set_id,
                 model_data.authority_set_name as model_data_set_name,
                 IF(setting.allstore_set_id = 0,setting.accessable_projects,allstore_set.detail) as accessable_project,
                 IF(setting.allstore_item_set_id = 0,setting.accessable_items,allstore_item_set.detail) as accessable_item,
                 IF(setting.model_data_set_id = 0,setting.accessable_models,model_data.detail) as accessable_model
             FROM tb_project_access_setting as setting
             LEFT JOIN tb_allstore_authority_set as allstore_set ON allstore_set.id = setting.allstore_set_id
             LEFT JOIN tb_allstore_item_authority_set as allstore_item_set ON allstore_item_set.id = setting.allstore_item_set_id
             LEFT JOIN tb_model_data_authority_set as model_data ON model_data.id = setting.model_data_set_id
             WHERE login_user_id = $access_user_id";//allstore_item_set.detail as accessable_item,
        
         
        
        $result = DB::select($query);
        return json_decode(json_encode($result),true);
     }catch(Exception $e){
        return $e->getMessage();
     }
     
   }

   
   function GetAllstoreItemSetList(){
        try{
            $query = "SELECT * FROM tb_allstore_item_authority_set";
            $result = DB::select($query);
            return json_decode(json_encode($result),true);
        }catch(Exception $e){
        return $e->getMessage();
        }
   }
   
    function GetModelDataSetList(){
     
     try{
        $query = "SELECT * FROM tb_model_data_authority_set";
        $result = DB::select($query);
        return json_decode(json_encode($result),true);
        
     }catch(Exception $e){
        return $e->getMessage();
     }
     
   }
   
    function GetAllStoreDataForAccessSet($authority_set_id){
     
        try{
            $shiten_array = array(2=>"東京",3=>"大阪",4=>"名古屋",5=>"九州",6=>"東北",7=>"札幌",8=>"広島",9=>"四国",10=>"北陸");
            $query = "";
            if($authority_set_id == 1){//all accessable
                $query = "SELECT a_pj_code,1 as accessable,a_pj_name,a_shiten,b_ipd_center_tantou,b_pj_name,b_tmp_pj_name,a_kouji_type FROM tb_allstore_info";
            }else if($authority_set_id >= 2 && $authority_set_id <=10){//get data by shiten
                $accessable_shiten = $shiten_array[$authority_set_id];
                $query = "SELECT a_pj_code,1 as accessable,a_pj_name,a_shiten,b_ipd_center_tantou,b_pj_name,b_tmp_pj_name ,a_kouji_type
                FROM tb_allstore_info
                WHERE a_shiten LIKE '$accessable_shiten%'";
            }else{//get data by custom
                $query = "SELECT a_pj_code,
                IF(FIND_IN_SET(a_pj_code,(SELECT (detail) FROM tb_allstore_authority_set WHERE id='$authority_set_id')) > 0,1,0) as accessable,
                a_pj_name,a_shiten,b_ipd_center_tantou,b_pj_name,b_tmp_pj_name,a_kouji_type FROM tb_allstore_info";
            }
            
            
            
            
            $result = DB::select($query);
            return json_decode(json_encode($result),true);
         }catch(Exception $e){
            return $e->getMessage();
        }
     
    }
    
    function GetModelDataAccessSet($authority_set_id){
     
        try{
            $query = "";
            if($authority_set_id == 1){
                $query = "SELECT id,name,
                           1 as accessable
                           FROM tb_project WHERE auto_save_properties = 1";
            }else{
               $query = "SELECT id,name,
                IF(FIND_IN_SET(id,(SELECT md.detail FROM tb_model_data_authority_set as md WHERE md.id= $authority_set_id)) > 0,1,0) as accessable
                FROM tb_project WHERE auto_save_properties = 1"; 
            }
            
            $result = DB::select($query);
            return json_decode(json_encode($result),true);
         }catch(Exception $e){
            return $e->getMessage();
        }
     
    }
    
    function GetModelData($access_user_id){
     
        try{
            
             $query = "SELECT model_data_set_id FROM tb_project_access_setting WHERE login_user_id = $access_user_id";
             $result = DB::select($query);
             $result = json_decode(json_encode($result),true);
             if($result == null || $result[0]["model_data_set_id"] == null || $result[0]["model_data_set_id"] == 0){
                 $query = "SELECT id,name,
                            0  as model_data_set_id 
                            FROM tb_project WHERE auto_save_properties=1 ";
             }else if($result[0]["model_data_set_id"] == 1){
                  $query = "SELECT id,name,
                            1 as model_data_set_id 
                            FROM tb_project WHERE auto_save_properties=1 ";
             }else{
                $model_data_set_id = $result[0]["model_data_set_id"];
                $query = "SELECT id,name,$model_data_set_id as model_data_set_id FROM tb_project 
                        WHERE FIND_IN_SET(id,(SELECT md.detail FROM tb_model_data_authority_set as md WHERE md.id= $model_data_set_id))"; 
    
             }
            
            
            $result = DB::select($query);
            return json_decode(json_encode($result),true);
         }catch(Exception $e){
            return $e->getMessage();
        }
     
    }
    
    function GetAllstoreItemAccessSet($authority_set_id){
     
        try{
    
            $query = "SELECT * FROM tb_allstore_item_authority_set WHERE id = $authority_set_id";
            $result = DB::select($query);
            return json_decode(json_encode($result),true);
         }catch(Exception $e){
            return $e->getMessage();
        }
     
    }
    
    function GetAllUserWithAccessInfo(){
        try{
    
            $query = "SELECT login.personal_id,CONCAT(per.first_name,per.last_name) as name,per.mail as email,d.name as dept,b.name as branch,
                      (SELECT authority_set_name FROM tb_model_data_authority_set WHERE id = pj_access.model_data_set_id) as model_data_set_name,
                      (SELECT authority_set_name FROM tb_allstore_authority_set WHERE id = pj_access.allstore_set_id) as allstore_set_name,
                      (SELECT authority_set_name FROM tb_allstore_item_authority_set WHERE id = pj_access.allstore_item_set_id) as allstore_item_set_name
                      FROM tb_login as login 
                      LEFT JOIN tb_project_access_setting as pj_access ON login.personal_id = pj_access.login_user_id
                      LEFT JOIN tb_personal  as per ON login.personal_id= per.id
                      LEFT JOIN tb_branch_office as b ON b.id = per.branch_id
                      LEFT JOIN tb_dept as d ON d.branch_id = per.dept_id
                      ORDER BY login.id";
            $result = DB::select($query);
            return json_decode(json_encode($result),true);
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    function GetAllStoreSetList(){
         try{
    
            $query = "SELECT * FROM tb_allstore_authority_set";
            $result = DB::select($query);
            return json_decode(json_encode($result),true);
         }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    function DeleteAllstoreSet($set_id){
        try{

            $updateQuery = "UPDATE tb_project_access_setting SET allstore_set_id = NULL WHERE allstore_set_id = $set_id";
            DB::update($updateQuery);
            
            $query = "DELETE  FROM tb_allstore_authority_set WHERE id=$set_id";
            $result = DB::delete($query);
            return "success";
        
         }catch(Exception $e){
            return $e->getMessage();
         }
    }
    
    function DeleteAllstoreItemSet($set_id){
        try{


            $updateQuery = "UPDATE tb_project_access_setting SET allstore_item_set_id = NULL WHERE allstore_item_set_id = $set_id";
            DB::update($updateQuery);
            
            $query = "DELETE  FROM tb_allstore_item_authority_set WHERE id=$set_id";
            $result = DB::delete($query);
            return "success";
        
         }catch(Exception $e){
            return $e->getMessage();
         }
    }
    
     function DeleteModelDataSet($set_id){
        try{


            $updateQuery = "UPDATE tb_project_access_setting SET model_data_set_id = NULL WHERE model_data_set_id = $set_id";
            DB::update($updateQuery);
            
            $query = "DELETE  FROM tb_model_data_authority_set WHERE id=$set_id";
            $result = DB::delete($query);
            return "success";
        
         }catch(Exception $e){
            return $e->getMessage();
         }
    }
    
    function UpdateAllstoreSetName($set_id,$update_name){
     
     try{
            $query = "UPDATE tb_allstore_authority_set SET authority_set_name = '$update_name' WHERE id=$set_id";
            DB::update($query);
            return "success";

     }catch(Exception $e){
        return $e->getMessage();
     }
     
   }
   
   function UpdateAllstoreItemSetName($set_id,$update_name){
     
     try{
            $query = "UPDATE tb_allstore_item_authority_set SET authority_set_name = '$update_name' WHERE id=$set_id";
            DB::update($query);
            return "success";

     }catch(Exception $e){
        return $e->getMessage();
     }
     
   }
   
   function UpdateModelDataSetName($set_id,$update_name){
     
     try{
            $query = "UPDATE tb_model_data_authority_set SET authority_set_name = '$update_name' WHERE id=$set_id";
            DB::update($query);
            return "success";

     }catch(Exception $e){
        return $e->getMessage();
     }
     
   }

}
