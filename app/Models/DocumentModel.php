<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class DocumentModel extends Model
{
    /******************/
    /** Common       **/
    /******************/
    function GetAllStoreProjectNamesAndPJID(){
        try{
            $query = "SELECT a_pj_code as pj_code,( CASE 
            WHEN b_tmp_pj_name != '' THEN b_tmp_pj_name 
            WHEN b_pj_name != '' AND b_pj_name NOT LIKE '%と同じ%' THEN  b_pj_name
            ELSE a_pj_name END ) as project_name FROM tb_allstore_info";
            $data = DB::select($query);     
            return json_decode(json_encode($data),true);
        }catch(Exception $e){
            return "query error when retrieving allstore_info data.";
        }
    }

    /*******************/
    /** Excel version **/
    /*******************/
    public function SaveTemplateData($name,$description,$template_type,$item_key,$item_val,$file_name)
    {
        try{
            $login_user_id = "";
            if(session()->has('login_user_id')){
                $login_user_id = session("login_user_id");
                /*$select_user = "SELECT name,organization FROM tb_personal WHERE id = $login_user_id";
                $result = DB::select($select_user);
                if(sizeof($result) > 0){
                    $login_user_name = $result[0]->name;
                    $organization_name = $result[0]->organization;
                }*/
                
            }
            $current_date = date("Y-m-d H:i:s");

            $query = "INSERT INTO tb_document_template 
                        (name,description,file_name,item_key,item_val,type,created_user_id,updated_user_id,created_date,updated_date) 
                        VALUES ('$name','$description','$file_name','$item_key','$item_val',$template_type,$login_user_id,$login_user_id,'$current_date','$current_date')
                     ON DUPLICATE KEY UPDATE
                        description = '$description',
                        file_name = '$file_name',
                        item_key = '$item_key', 
                        item_val = '$item_val',
                        type = $template_type,
                        updated_user_id = $login_user_id,
                        updated_date = '$current_date'";
            $data = DB::insert($query);     
            return "success";
        }catch(Exception $e){
            return "query error when saving template data.";
        }
    }

    function GetAllTemplateList(){
        try{
            $query = "SELECT name FROM tb_document_template ";
            $data = DB::select($query);     
            return json_decode(json_encode($data),true);
        }catch(Exception $e){
            return "query error when saving template data.";
        }
    }
    
    function GetTemplateDataByName($templateName){
        try{
            $query = "SELECT * ,
                            (SELECT first_name FROM tb_personal WHERE  id = created_user_id )as created_user_first_name,
                            (SELECT last_name FROM tb_personal WHERE  id = created_user_id )as created_user_last_name,
                            (SELECT branch_id FROM tb_personal WHERE  id = created_user_id)as created_branch_id,
                            (SELECT dept_id FROM tb_personal WHERE  id = created_user_id)as created_orgainzation_id,
                            (SELECT first_name FROM tb_personal WHERE  id = updated_user_id )as updated_user_first_name,
                            (SELECT last_name FROM tb_personal WHERE  id = updated_user_id )as updated_user_last_name,
                            (SELECT branch_id FROM tb_personal WHERE  id = updated_user_id)as updated_branch_id,
                            (SELECT dept_id FROM tb_personal WHERE  id = updated_user_id)as updated_orgainzation_id
                        FROM tb_document_template
                        WHERE name = '$templateName'";
            $data = DB::select($query);     
            return json_decode(json_encode($data),true);
        }catch(Exception $e){
            return "query error when retrieving template data.";
        }
    }
    
    function GetTemplateDataByFileName($file_name,$templateName){
        try{
            $query = "SELECT name FROM tb_document_template WHERE file_name = '$file_name' AND name != '$templateName'";
            $data = DB::select($query);     
            return json_decode(json_encode($data),true);
        }catch(Exception $e){
            return "query error when retrieving template data.";
        }
    }
    
    function DeleteTemplateByName($templateName){
        try{
            $query = "Delete FROM tb_document_template WHERE name = '$templateName'";
            $data = DB::delete($query);     
            return "success";
        }catch(Exception $e){
            return "query error when deleting template.";
        }
    }

    /******************/
    /** Word version **/
    /******************/
    public function SaveWordTemplateData($name,$description,$item_key,$item_val,$file_name)
    {
        try{
            $login_user_id = "";
            if(session()->has('login_user_id')){
                $login_user_id = session("login_user_id");
            }
            $current_date = date("Y-m-d H:i:s");

            $query = "INSERT INTO tb_doc_template_word 
                        (name,description,file_name,item_key,item_val,created_user_id,updated_user_id,created_date,updated_date) 
                        VALUES ('$name','$description','$file_name','$item_key','$item_val',$login_user_id,$login_user_id,'$current_date','$current_date')
                     ON DUPLICATE KEY UPDATE
                        description = '$description',
                        file_name = '$file_name',
                        item_key = '$item_key', 
                        item_val = '$item_val',
                        updated_user_id = $login_user_id,
                        updated_date = '$current_date'";
            $data = DB::insert($query);     
            return "success";
        }catch(Exception $e){
            return "query error when saving template data.";
        }
    }
    
    function GetAllWordTemplateList(){
        try{
            $query = "SELECT name FROM tb_doc_template_word ";
            $data = DB::select($query);     
            return json_decode(json_encode($data),true);
        }catch(Exception $e){
            return "query error when saving template data.";
        }
    }

    function GetWordTemplateDataByName($templateName){
        try{
            $query = "SELECT * ,
                            (SELECT first_name FROM tb_personal WHERE  id = created_user_id )as created_user_first_name,
                            (SELECT last_name FROM tb_personal WHERE  id = created_user_id )as created_user_last_name,
                            (SELECT branch_id FROM tb_personal WHERE  id = created_user_id)as created_branch_id,
                            (SELECT dept_id FROM tb_personal WHERE  id = created_user_id)as created_orgainzation_id,
                            (SELECT first_name FROM tb_personal WHERE  id = updated_user_id )as updated_user_first_name,
                            (SELECT last_name FROM tb_personal WHERE  id = updated_user_id )as updated_user_last_name,
                            (SELECT branch_id FROM tb_personal WHERE  id = updated_user_id)as updated_branch_id,
                            (SELECT dept_id FROM tb_personal WHERE  id = updated_user_id)as updated_orgainzation_id
                        FROM tb_doc_template_word
                        WHERE name = '$templateName'";
            $data = DB::select($query);     
            return json_decode(json_encode($data),true);
        }catch(Exception $e){
            return "query error when retrieving template data.";
        }
    }
    
    function GetWordTemplateDataByFileName($file_name,$templateName){
        try{
            $query = "SELECT name FROM tb_doc_template_word WHERE file_name = '$file_name' AND name != '$templateName'";
            $data = DB::select($query);     
            return json_decode(json_encode($data),true);
        }catch(Exception $e){
            return "query error when retrieving template data.";
        }
    }
    
    function DeleteWordTemplateByName($templateName){
        try{
            $query = "Delete FROM tb_doc_template_word WHERE name = '$templateName'";
            $data = DB::delete($query);     
            return "success";
        }catch(Exception $e){
            return "query error when deleting template.";
        }
    }

}
