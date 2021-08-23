<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\HashtagModel;
use App\Models\PersonalModel;
use App\Models\CompanyModel;


class HashtagController extends Controller
{
    public function index(){
        return view('hashtag_search');
    }
    
    public function Test(){
        return view('text_editor');
    }
    
    public function GetData(Request $request){
        $message = $request->get('message');
        $hashtag = new HashtagModel();
        if($message == 'get_hashtag_list'){
            $hashtag_list = $hashtag->GetHashtagList();
            if(count($hashtag_list)>0){
                return $hashtag_list;
            }
        }elseif($message == 'get_report_data'){
            $personal = new PersonalModel();
            $company = new CompanyModel();
            $search_logic = $request->get('search_logic');
            $hashtag_list = $request->get('hashtag');
            $report_list = $hashtag->GetReportByHashtag($hashtag_list, $search_logic);
            $result_arr = [];
            if(count($report_list) > 0){
                foreach($report_list as $report){
                    $personal_id = $report['saved_user_id'];
                    if(!empty($personal_id)){
                        $personal_info = $personal->GetPersonal($personal_id);
                        if(count($personal_info)>0){
                            $name = $personal_info[0]['name'];
                            $dept_id = $personal_info[0]['dept_id'];
                            if(!empty($dept_id)){
                                $deptInfo = $company->GetDeptById($dept_id);
                                if(count($deptInfo)>0){
                                    $dept_name = $deptInfo[0]['name'];
                                }else{
                                    $dept_name = "";
                                }
                            }else{
                                $dept_name = "";
                            }
                        
                        }else{
                            $name = "";
                            $dept_name = "";
                        }
                        
                        $report['name']   = $name;
                        $report['dept_name'] = $dept_name;
                        $result_arr[] = $report;
                    }
                }
            }
            return $result_arr;
        }
    }
    
}