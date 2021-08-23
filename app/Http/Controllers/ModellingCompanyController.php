<?php

namespace App\Http\Controllers;
use App\Models\ModellingCompanyModel;
use App\Models\PersonalModel;
use App\Models\CompanyModel;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ModellingCompanyController extends Controller
{
    
    public function index()
    {
        return view('modellingCompanyInfo');
    }
    
    public function ShowList(){
        $loginUser = session("userName");
        $modellingCompany = new ModellingCompanyModel();
        $personal = new PersonalModel();
        $modellingCompanyList = $modellingCompany->GetModellingCompanyInfo();
        // echo "<pre>";
        // print_r($modellingCompanyList);
        // return;
        $hideOrShow  = $modellingCompany->HideOrShowTable($loginUser);
        //*********Testing************//
        $companyIdList = array();
        $resultSet = array();
        if(count($modellingCompanyList)>0){
            
            for($i=0; $i<count($modellingCompanyList); $i++){
                
                $companyId = $modellingCompanyList[$i]['company_id'];
                $branch = $modellingCompanyList[$i]['branch'];
                
                $uniqueTxt = $companyId . $branch;
                if(!in_array($uniqueTxt, $companyIdList)){
                    $companyIdList[]=$uniqueTxt;
                    $cooperateInfo = $modellingCompany->GetModellingInfoById($companyId,$branch);
                
                    if(count($cooperateInfo)>0){
                        $inchargeInfo = array();
                        foreach($cooperateInfo as $c){
                        
                            $personalId = $c['user_id'];
                            if(!empty($personalId)){
                                $innerArray = array();
                                $personalInfo = $personal->GetInchargeInfoById($personalId);
                                if(count($personalInfo)>0){
                                    $innerArray['name'] = $personalInfo[0]['name'];
                                    $innerArray['phone'] = $personalInfo[0]['phone'];
                                    $innerArray['outsideCall'] = $personalInfo[0]['outsideCall'];
                                    $innerArray['mail'] = $personalInfo[0]['mail'];
                                    $inchargeInfo[] = $innerArray;
                                }
                            }else{
                                $inchargeInfo = [];
                            }
                        }
                        $modellingCompanyList[$i]['incharge'] = $inchargeInfo;
                        $resultSet[] = $modellingCompanyList[$i];
                    }
                }else{
                    unset($modellingCompanyList[$i]);
                }
                
            }
            
            
        }
        //**************Testing***************************//
        
        // echo "<pre>";
        // print_r($modellingCompanyList);
        // return;
        
        $data = [
            'modellingCompanyList'  => $modellingCompanyList,
            'hideOrShow'   => $hideOrShow
        ];
        return view ('modellingCompanyInfoList')->with($data);
    }
    
    
    public function SaveData(Request $request){
        
        $message = $request->get("message");
        $data  = $request->get("partnerCompanyContact");
        $partnerCompanyContact = new ModellingCompanyModel();
        $company = new CompanyModel();
        $personal = new PersonalModel();
        if($message == "insertPartnerCompanyContact"){
            try{
                
                $partnerCompanyContact->partnerCompanyName = $data['partnerCompanyName'];
                $partnerCompanyContact->partnerJobType = $data['partnerJobType'];
                $partnerCompanyContact->partnerCompanyBranch = $data['partnerCompanyBranch'];
                $partnerCompanyContact->partnerMailCode = $data['partnerMailCode'];
                $partnerCompanyContact->partnerCompanyAddress = $data['partnerCompanyAddress'];
                $partnerCompanyContact->partnerInchargeName = $data['partnerInchargeName'];
                $partnerCompanyContact->partnerPhone = $data['partnerPhone'];
                $partnerCompanyContact->partnerEmail = $data['partnerEmail'];
               
                $partnerCompanyContact->save();
                return array("isInserted" => true);
                
            }catch(Exception $e){
                return array("isInserted" => false);
            }
        }elseif($message == 'insertModellingCompany'){
            $modellingCompanyInfo =  $request->get("modellingCompanyInfo");
            if(count($modellingCompanyInfo)>0){
                // $companyName = $modellingCompanyInfo['companyName'];
                // $industryType = $modellingCompanyInfo['jobType'];
                // $checkExistingCompany = $company->GetCompanyInfoByNameAndCompanyTypeId($companyName,4);
    
                // if(count($checkExistingCompany)>0){
                //     $modellingCompanyInfo['company_id'] = $checkExistingCompany[0]['id'];
                // }
                $companyId = $modellingCompanyInfo['company_id'];
                $branch = $modellingCompanyInfo['branch'];
                if(!empty($branch)){
                    $branchInfo = $partnerCompanyContact->GetModellingBranch($companyId, $branch);
                    if(count($branchInfo)>0){
                        $modellingCompanyInfo['branch_id'] = $branchInfo[0]['id'];
                    }
                }
                
                
                //************tb-personalに入力する**************//
                $personalInfo = $modellingCompanyInfo['inchargeInfo'];
                if(count($personalInfo)>0){
                    foreach($personalInfo as $p){
                        if(!empty($p['first_name']) && !empty($p['mail'])){
                            $p['company_id'] = $modellingCompanyInfo['company_id'];
                            if(!empty($modellingCompanyInfo['branch_id'])){
                                $p['branch_id'] = $modellingCompanyInfo['branch_id'];
                            }
                            $personalInsert = $personal->InsertPersonalCooperateCompany($p);
                            $inchargeId = $personal->GetInchargeIdByMail($p['mail']);
                            if(count($inchargeId)>0){
                                $modellingCompanyInfo['incharge_id'] = $inchargeId[0]['id'];
                                
                                //************tb-modelling-companyに入力する**************//
                                $cooperateCompanyInsert = $company->InsertModellingCompanyInfo($modellingCompanyInfo);
                            }
                            
                        }else{
                            $modellingCompanyInfo['incharge_id'] = "";
                            //************tb-modelling-companyに入力する**************//
                            $cooperateCompanyInsert = $company->InsertModellingCompanyInfo($modellingCompanyInfo);
                        }
                        
                    }
                }
            }
            return "success";
        }elseif($message == "updateById"){
            $id = $request->get("id");
            $isUpdated = $partnerCompanyContact->UpdatePartnerCompanyContactById($id, $data);
            //print_r($isUpdated);
            return array('isUpdated' => true);
        }
        
        
    }
    
    public function GetData(Request $request){
        $message = $request->get("message");
        $modellingCompany = new ModellingCompanyModel();
        $personal = new PersonalModel();
        $company = new CompanyModel();
        if($message == "getAllModellingCompany"){
            $modellingCompanyList = $modellingCompany->GetAllModellingCompany();
            if(count($modellingCompanyList)>0){
                return $modellingCompanyList;
            }
        }elseif($message == "getModellingCompanyById"){
            $id = $request->get("id");
            $modellingCompanyInfo = $modellingCompany->GetModellingCompanyById($id);
            
            $resultSet = array();
            if(count($modellingCompanyInfo)>0){
                
                for($j=0; $j<count($modellingCompanyInfo) ; $j++){
                    $companyId = $modellingCompanyInfo[$j]['company_id'];
                    $personalId = $modellingCompanyInfo[$j]['user_id'];
                    if(!empty($personalId)){
                        $personalInfo = $personal->GetPersonalInfoByCompanyIdAndUserId($companyId, $personalId);
                        if(count($personalInfo)>0){
                    
                            for($i=0; $i<count($personalInfo) ; $i++){
                                $tempArray = $modellingCompanyInfo[$j];
                                $tempArray['first_name'] = $personalInfo[$i]['first_name'];
                                $tempArray['last_name'] = $personalInfo[$i]['last_name'];
                                $tempArray['phone'] = $personalInfo[$i]['phone'];
                                $tempArray['outsideCall'] = $personalInfo[$i]['outsideCall'];
                                $tempArray['mail'] = $personalInfo[$i]['mail'];
                                
                                $resultSet[] = $tempArray;
                            }
                        }else{
                            $modellingCompanyInfo[$j]['first_name'] = "";
                            $modellingCompanyInfo[$j]['last_name'] = "";
                            $modellingCompanyInfo[$j]['phone'] = "";
                            $modellingCompanyInfo[$j]['outsideCall'] = "";
                            $modellingCompanyInfo[$j]['mail'] = "";
                            $resultSet[] = $modellingCompanyInfo[$j];
                        }
                    }else{
                        $modellingCompanyInfo[$j]['first_name'] = "";
                        $modellingCompanyInfo[$j]['last_name'] = "";
                        $modellingCompanyInfo[$j]['phone'] = "";
                        $modellingCompanyInfo[$j]['outsideCall'] = "";
                        $modellingCompanyInfo[$j]['mail'] = "";
                        $resultSet[] = $modellingCompanyInfo[$j];
                    }
                    
                }
            }
            
            return array($resultSet);
        }elseif($message == 'getModellingCompanyInfoByName'){
            $companyNameStr = $request->get('companyName');
            if(strpos($companyNameStr, "【") == false){
                $companyName = $companyNameStr;
                $branch = "";
            }else{
                $companyName = substr($companyNameStr, 0, strpos($companyNameStr, "【"));
                $start = strpos($companyNameStr, "【");
                $end = strpos($companyNameStr, "】");
                $branch = substr($companyNameStr,$start , $end);
            }
            
            $companyInfo = $company->GetCompanyByName($companyName);
            if(count($companyInfo)>0){
                $companyId = $companyInfo[0]['id'];
                $companyAndBranchInfo = $modellingCompany->GetCompanyInfoByNameAndBranch($companyId);
                $result = array();
                if(count($companyAndBranchInfo)>0){
                    foreach($companyAndBranchInfo as $r){
                        if(str_contains($branch, $r['name'])){
                            $result[] = $r;
                        }
                    }
                    if(count($result)>0){
                        return $result;
                    }else{
                       return  $companyAndBranchInfo;
                    }
                }
            }
            
            
        }elseif($message == 'getModellingCompanyList'){
            $modellingCompanyList = $modellingCompany->GetCompanyList();
            if(count($modellingCompanyList)>0){
                return $modellingCompanyList;
            }
        }elseif($message == "getModellingCompanyByIdTest"){
            $id = $request->get("id");
            $companyName = $request->get('companyName');
            $branch = $request->get('branch');
            $info = $modellingCompany->GetInfoById($id,$branch);
            $resultSet = array();
            for($j=0; $j<count($info) ; $j++){
                    $info[$j]['company_name'] = $companyName;
                    $companyId = $info[$j]['id'];
                    $personalId = $info[$j]['user_id'];
                    if(!empty($personalId)){
                        $personalInfo = $personal->GetPersonalInfoByCompanyIdAndUserId($companyId, $personalId);
                        if(count($personalInfo)>0){
                    
                            for($i=0; $i<count($personalInfo) ; $i++){
                                $tempArray = $info[$j];
                                $tempArray['first_name'] = $personalInfo[$i]['first_name'];
                                $tempArray['last_name'] = $personalInfo[$i]['last_name'];
                                $tempArray['phone'] = $personalInfo[$i]['phone'];
                                $tempArray['outsideCall'] = $personalInfo[$i]['outsideCall'];
                                $tempArray['mail'] = $personalInfo[$i]['mail'];
                                
                                $resultSet[] = $tempArray;
                            }
                        }else{
                            $info[$j]['first_name'] = "";
                            $info[$j]['last_name'] = "";
                            $info[$j]['phone'] = "";
                            $info[$j]['outsideCall'] = "";
                            $info[$j]['mail'] = "";
                            $resultSet[] = $info[$j];
                        }
                    }else{
                        $info[$j]['first_name'] = "";
                        $info[$j]['last_name'] = "";
                        $info[$j]['phone'] = "";
                        $info[$j]['outsideCall'] = "";
                        $info[$j]['mail'] = "";
                        $resultSet[] = $info[$j];
                    }
                    
                }
        
            return array($resultSet);
        }
        
    }
    
    public function DeleteData(Request $request){
        $message = $request->get('message');
        $id = $request->get('id');
        $partnerCompanyContact = new ModellingCompanyModel();
        if($message == "deleteModellingCompanyById"){
            $isDeleted = $partnerCompanyContact->DeleteModellingCompanyById($id);
            return array("isDeleted" => $isDeleted);
        }
    }
    
    public function UpdateData(Request $request){
        $message = $request->get("message");
        $columnName = $request->get("columnName");
        $loginUser = $request->get("loginUser");
        $partnerCompanyContact = new ModellingCompanyModel();
        if($message == "hideDisplay"){
            $update  = $partnerCompanyContact->HideTableColumnDisplay($columnName,$loginUser);
            return array("DisplayOrNot" =>$update);
        }elseif($message == "showDisplay"){
            $update  = $partnerCompanyContact->ShowTableColumnDisplay($columnName,$loginUser);
            return array("DisplayOrNot" =>$update);
        }
    }
}