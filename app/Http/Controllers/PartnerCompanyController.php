<?php

namespace App\Http\Controllers;
use App\Models\PartnerCompanyModel;
use App\Models\CompanyModel;
use App\Models\PersonalModel;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class PartnerCompanyController extends Controller
{
    
    public function index()
    {
        return view('partnerCompanyInfo');
    }
    
    public function ShowList(){
        $loginUser = session("userName");
        $partnerCompany = new PartnerCompanyModel();
        $company = new CompanyModel();
        $personal = new PersonalModel();
        //$partnerCompanyList = $partnerCompany->GetAllPartnerCompany1();
        $partnerCompanyList = $partnerCompany->GetAllPartnerCompanyInfo();
        $companyIdList = array();
        $resultSet = array();
        if(count($partnerCompanyList)>0){
            
            for($i=0; $i<count($partnerCompanyList); $i++){
                
                $companyId = $partnerCompanyList[$i]['company_id'];
                if(!in_array($companyId, $companyIdList)){
                    $companyIdList[]=$companyId;
                    $cooperateInfo = $partnerCompany->GetCooperateInfoById($companyId);
                
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
                        $partnerCompanyList[$i]['incharge'] = $inchargeInfo;
                        $resultSet[] = $partnerCompanyList[$i];
                    }
                }else{
                    
                }
                
            }
            
            
        }
        
        $hideOrShow  = $partnerCompany->HideOrShowTable($loginUser);
        $numOfIncharge = $partnerCompany->GetMaxNumberOfIncharge();
        $data = [
            'partnerCompanyInfoList'  => $resultSet,
            'hideOrShow'   => $hideOrShow,
            'numOfIncharge' => $numOfIncharge[0]->numOfIncharge
        ];
        
        return view ('partnerCompanyInfoList')->with($data);
    }
    
    public function SaveData(Request $request){
        
        $message = $request->get("message");
        $data  = $request->get("partnerCompanyInfo");
        $partnerCompany = new PartnerCompanyModel();
        $company = new CompanyModel();
        $personal = new PersonalModel();
        if($message == "insertPartnerCompany"){
            // try{
                
            //     $partnerCompany->companyName = $data['companyName'];
            //     $partnerCompany->jobType = $data['jobType'];
            //     $partnerCompany->yaruki = $data['yaruki'];
            //     $partnerCompany->revit = $data['revit'];
            //     $partnerCompany->ipd = $data['ipd'];
            //     $partnerCompany->satelliteExp = $data['satelliteExp'];
            //     $partnerCompany->satelliteProjName = $data['satelliteProjName'];
            //     $partnerCompany->remark = $data['remark'];
            //     $partnerCompany->inchargeName = $data['inchargeName'];
            //     $partnerCompany->phone = $data['phone'];
            //     $partnerCompany->email = $data['email'];
                
            //     $partnerCompany->save();
            //     return array("isInserted" => true);
                
            // }catch(Exception $e){
            //     return array("isInserted" => false);
            // }
            
            $partnerCompanyInfo =  $request->get("partnerCompanyInfo");
            if(count($partnerCompanyInfo)>0){
                $companyName = $partnerCompanyInfo['companyName'];
                $industryType = $partnerCompanyInfo['jobType'];
                $checkExistingCompany = $company->GetCompanyInfoByNameAndCompanyTypeId($companyName,3);
                
                //************tb-companyに入力する**************//
                if(count($checkExistingCompany)>0){
                    $partnerCompanyInfo['company_id'] = $checkExistingCompany[0]['id'];
                }else{
                    $partnerCompanyInsert = $company->InsertNewCompany($companyName,3,$industryType);
                    if(str_contains($partnerCompanyInsert, 'success')){
                        $companyInfo = $company->GetCompanyInfoByNameAndCompanyTypeId($companyName,3);
                        $partnerCompanyInfo['company_id'] = $companyInfo[0]['id'];
                    }
                }
                //************tb-companyに入力する**************//
                
                //************tb-personalに入力する**************//
                
                $personalInfo = $partnerCompanyInfo['inchargeInfo'];
                if(count($personalInfo)>0){
                    foreach($personalInfo as $p){
                        if(!empty($p['first_name']) && !empty($p['mail'])){
                            $p['company_id'] = $partnerCompanyInfo['company_id'];
                            $personalInsert = $personal->InsertPersonalCooperateCompany($p);
                            $inchargeId = $personal->GetInchargeIdByMail($p['mail']);
                            if(count($inchargeId)>0){
                                $partnerCompanyInfo['incharge_id'] = $inchargeId[0]['id'];
                                print_r($partnerCompanyInfo);
                                //************tb-cooperate-companyに入力する**************//
                                $cooperateCompanyInsert = $company->InsertCooperateCompanyInfo($partnerCompanyInfo);
                            }
                            
                        }else{
                            $partnerCompanyInfo['incharge_id'] = "";
                            //************tb-cooperate-companyに入力する**************//
                            $cooperateCompanyInsert = $company->InsertCooperateCompanyInfo($partnerCompanyInfo);
                        }
                        
                    }
                }
                //************tb-personalに入力する**************//
                
                // //************tb-cooperate-companyに入力する**************//
                // $cooperateCompanyInsert = $company->InsertCooperateCompanyInfo($partnerCompanyInfo);
                // //************tb-cooperate-companyに入力する**************//
                
                
            }
            // print_r($partnerCompanyInfo);
            return "success";
            
        }elseif($message == "updateById"){
            $id = $request->get("id");
            $isUpdated = $partnerCompany->UpdatePartnerCompanyById($id, $data);
            //print_r($isUpdated);
            return array('isUpdated' => $isUpdated);
        }
        
        
    }
    
    public function GetData(Request $request){
        $message = $request->get("message");
        $partnerCompany = new PartnerCompanyModel();
        $company = new CompanyModel();
        $personal = new PersonalModel();
        if($message == "getAllPartnerCompany"){
            $partnerCompanyList = $partnerCompany->GetAllPartnerCompany();
            return array("ListOfPartnerCompany"=>$partnerCompanyList);
        }elseif($message == "getCompanyInfoByName"){
            $companyName = $request->get("companyName");
            $companyInfo = $company->GetCompanyInfoByName($companyName);
            return $companyInfo;
        }elseif($message == "getPartnerCompanyById"){
            $id = $request->get("id");
            $partnerCompanyInfo = $partnerCompany->GetPartnerCompanyById($id);
            
            $resultSet = array();
            if(count($partnerCompanyInfo)>0){
                
                for($j=0; $j<count($partnerCompanyInfo) ; $j++){
                    $companyId = $partnerCompanyInfo[$j]['company_id'];
                    $personalId = $partnerCompanyInfo[$j]['user_id'];
                    if(!empty($personalId)){
                        $personalInfo = $personal->GetPersonalInfoByCompanyIdAndUserId($companyId, $personalId);
                        if(count($personalInfo)>0){
                    
                            for($i=0; $i<count($personalInfo) ; $i++){
                                $tempArray = $partnerCompanyInfo[$j];
                                $tempArray['first_name'] = $personalInfo[$i]['first_name'];
                                $tempArray['last_name'] = $personalInfo[$i]['last_name'];
                                $tempArray['phone'] = $personalInfo[$i]['phone'];
                                $tempArray['outsideCall'] = $personalInfo[$i]['outsideCall'];
                                $tempArray['mail'] = $personalInfo[$i]['mail'];
                                
                                $resultSet[] = $tempArray;
                            }
                        }else{
                            $partnerCompanyInfo[$j]['first_name'] = "";
                            $partnerCompanyInfo[$j]['last_name'] = "";
                            $partnerCompanyInfo[$j]['phone'] = "";
                            $partnerCompanyInfo[$j]['outsideCall'] = "";
                            $partnerCompanyInfo[$j]['mail'] = "";
                            $resultSet[] = $partnerCompanyInfo[$j];
                        }
                    }else{
                        $partnerCompanyInfo[$j]['first_name'] = "";
                        $partnerCompanyInfo[$j]['last_name'] = "";
                        $partnerCompanyInfo[$j]['phone'] = "";
                        $partnerCompanyInfo[$j]['outsideCall'] = "";
                        $partnerCompanyInfo[$j]['mail'] = "";
                        $resultSet[] = $partnerCompanyInfo[$j];
                    }
                    
                }
            }
            // print_r($resultSet);
            return array($resultSet);
        }elseif($message == "getMaruFirst"){
            $columnName = $request->get("columnName");
            $maruFirstPartnerCompany = $partnerCompany->GetMaruFirstPartnerCompany($columnName);
            $companyIdList = array();
            $resultSet = array();
            if(count($maruFirstPartnerCompany)>0){
            
                for($i=0; $i<count($maruFirstPartnerCompany); $i++){
                
                    $companyId = $maruFirstPartnerCompany[$i]['company_id'];
                    if(!in_array($companyId, $companyIdList)){
                        $companyIdList[]=$companyId;
                        $cooperateInfo = $partnerCompany->GetCooperateInfoById($companyId);
                
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
                            $maruFirstPartnerCompany[$i]['incharge'] = $inchargeInfo;
                            $resultSet[] = $maruFirstPartnerCompany[$i];
                        }
                    }else{
                    
                    }
                }
            }
            return $resultSet;
        }
    }
    
    public function UpdateData(Request $request){
        $message = $request->get("message");
        $columnName = $request->get("columnName");
        $loginUser = $request->get("loginUser");
        $partnerCompany = new PartnerCompanyModel();
        if($message == "hideDisplay"){
            $update  = $partnerCompany->HideTableColumnDisplay($columnName,$loginUser);
            return array("DisplayOrNot" =>$update);
        }elseif($message == "showDisplay"){
            $update  = $partnerCompany->ShowTableColumnDisplay($columnName,$loginUser);
            return array("DisplayOrNot" =>$update);
        }
    }
    
    public function DeleteData(Request $request){
        $message = $request->get('message');
        $id = $request->get('id');
         $partnerCompany = new PartnerCompanyModel();
        if($message == "deletePartnerCompanyById"){
            $isDeleted = $partnerCompany->DeletePartnerCompanyById($id);
            return array("isDeleted" => $isDeleted);
        }
    }
}
