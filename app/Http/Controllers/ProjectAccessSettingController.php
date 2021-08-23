<?php

namespace App\Http\Controllers;
use App\Models\ProjectAccessSettingModel;
use App\Models\LoginModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Artisan;
class ProjectAccessSettingController extends Controller
{
    function index()
    {
        $projectAccess = new ProjectAccessSettingModel();
        $users = $projectAccess->GetAllUserWithAccessInfo();
        return view('inspection_authority_setting')->with(["users"=>$users]);
    }
    
    function LoadSettingPage($access_user_id,$access_user_name)
    {
        return view('project_access_setting')->with(["access_user_id"=>$access_user_id,"access_user_name"=>$access_user_name]);
    }
    
    function LoadAuthorityModelDataSetPage($access_user_id,$access_user_name,$set_id=null)
    {
        return view('project_access_modeldata_set')->with(["access_user_id"=>$access_user_id,"access_user_name"=>$access_user_name,"set_id"=>$set_id]);
    }
    
    function LoadAuthoritySetPage($access_user_id=null,$access_user_name=null,$set_id=null)
    {
        return view('project_access_authority_set')->with(["access_user_id"=>$access_user_id,"access_user_name"=>$access_user_name,"set_id"=>$set_id]);
    }

    function LoadAuthorityItemSetPage($access_user_id=null,$access_user_name=null,$set_id=null)
    {
        return view('project_access_authority_item_set')->with(["access_user_id"=>$access_user_id,"access_user_name"=>$access_user_name,"set_id"=>$set_id]);
    }
    
    function SaveData(Request $request){
        $message = $request->get("message");
        if($message == "set_access_project"){
            try{
                $access_user_id = $request->get('access_user_id');
                $project_list = $request->get('project_list');
                //$accessable_item_list = $request->get('')
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->SaveProjectAccessSetting($project_list,$access_user_id);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }
        }else if($message == "save_new_allstore_set"){
            try{
                $new_authority_set_name = $request->get('new_authority_set_name');
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->SaveNewAllstoreSet($new_authority_set_name);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }
        }else if($message == "save_new_allstore_item_set"){
            try{
                $new_authority_set_name = $request->get('new_authority_set_name');
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->SaveNewAllstoreItemSet($new_authority_set_name);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }
        }else if($message == "save_new_model_data_set"){
            try{
                $new_authority_set_name = $request->get('new_authority_set_name');
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->SaveNewModelDataSet($new_authority_set_name);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }
        }else if($message == "save_access_set_detail"){
            try{

                $set_id = $request->get('set_id');
                $project_list = json_decode($request->get('project_list'));
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->SaveAccessSetDetail($project_list,$set_id);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }
        }else if($message == "save_model_data_set_detail"){
            try{
                $authority_set_id = $request->get('authority_set_id');
                $project_list = json_decode($request->get('model_list'));
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->SaveModelDataSetDetail($project_list,$authority_set_id);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }
        }else if($message == "save_item_set_detail"){
           try{
                $authority_set_id = $request->get('authority_set_id');
                $item_list = json_decode($request->get('item_list'));
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->SaveItemSetDetail($item_list,$authority_set_id);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            } 
        }else if($message == "save_allstore_set_id"){
            try{
                $access_set_id = $request->get('access_set_id');
                $access_user_id = $request->get('access_user_id');
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->SaveAllstoreSetId($access_set_id,$access_user_id);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            } 
        }else if($message == "save_allstore_item_set_id"){
            try{
                $access_set_id = $request->get('access_set_id');
                $access_user_id = $request->get('access_user_id');
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->SaveAllstoreItemSetId($access_set_id,$access_user_id);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            } 
        }else if($message == "save_model_data_set_id"){
            try{
                $access_set_id = $request->get('access_set_id');
                $access_user_id = $request->get('access_user_id');
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->SaveModelDataSetId($access_set_id,$access_user_id);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            } 
        }else if($message == "update_allstore_set_name"){
           try{
                $access_set_id = $request->get('access_set_id');
                $access_set_name = $request->get('access_set_name');
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->UpdateAllstoreSetName($access_set_id,$access_set_name);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }  
        }else if($message == "update_allstore_item_set_name"){
           try{
                $access_set_id = $request->get('access_set_id');
                $access_set_name = $request->get('access_set_name');
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->UpdateAllstoreItemSetName($access_set_id,$access_set_name);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }  
        }else if($message == "update_model_data_set_name"){
           try{
                $access_set_id = $request->get('access_set_id');
                $access_set_name = $request->get('access_set_name');
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->UpdateModelDataSetName($access_set_id,$access_set_name);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }  
        }else if($message == "save_user_accessable_info"){
            try{
                $access_user_id = $request->get('access_user_id');
                $project_list = json_decode($request->get('project_list'));
                $item_list = $request->get('item_list');
                $model_list = $request->get('model_list');
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->SaveUserAccessableInfo($access_user_id,$project_list,$item_list,$model_list);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }  
        }
    }
    
    
    function GetData(Request $request){
        $message = $request->get("message");
        if($message == "get_accessable_project"){
            try{
                $access_user_id = $request->get('access_user_id');
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->GetAccessableProjects($access_user_id);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }
        }else if($message == "get_allstore_info"){
            try{
                $access_user_id = $request->get('access_user_id');
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->GetAllStoreData($access_user_id);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }
        }else if($message == "get_authority_set"){
            try{
                $status = $request->get('status');
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->GetAuthoritySet($status);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }
        }else if($message == "get_allstore_for_access_set"){
            try{
                $authority_set_id = $request->get('authority_set_id');
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->GetAllStoreDataForAccessSet($authority_set_id);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }
        }else if($message == "get_modeldata_for_access_set"){
            try{
                $authority_set_id = $request->get('authority_set_id');
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->GetModelDataAccessSet($authority_set_id);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }
        }else if($message == "get_modeldata"){
            try{
                $access_user_id = $request->get('access_user_id');
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->GetModelData($access_user_id);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }
        }else if($message == "get_allstore_item_set_byid"){
            try{
                $set_id = $request->get('set_id');
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->GetAllstoreItemAccessSet($set_id);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }
        }else if($message == "get_access_setting"){
            try{
                $access_user_id = $request->get('access_user_id');
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->GetAccessSetting($access_user_id);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }
        }else if($message == "get_allstore_set_list"){
            try{
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->GetAllStoreSetList();
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }
        }else if($message == "get_allstore_item_set_list"){
            try{
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->GetAllStoreItemSetList();
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }
        }else if($message == "get_model_data_set_list"){
            try{
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->GetModelDataSetList();
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }
        }
    }
    
    function DeleteData(Request $request){
        $message = $request->get("message");
        if($message == "delete_allstore_set"){
            try{
                $set_id = $request->get('access_set_id');
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->DeleteAllstoreSet($set_id);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }
        }else if($message == "delete_allstore_item_set"){
            try{
                $set_id = $request->get('access_set_id');
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->DeleteAllstoreItemSet($set_id);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }
        }else if($message == "delete_model_data_set"){
            try{
                $set_id = $request->get('access_set_id');
                $access_setting = new ProjectAccessSettingModel();
                $result = $access_setting->DeleteModelDataSet($set_id);
                return $result;
            }catch(Exception $e){
                return $e->getMessage();
            }
        }
    }
}