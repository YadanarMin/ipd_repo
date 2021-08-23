<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class ProjectMgtModel extends Model
{
    public function saveProject($projectName){

        DB::beginTransaction(); 
        try{
            $ws_query = "INSERT IGNORE INTO tb_work_summary (name,version) VAlUES('$projectName',0)";
            DB::insert($ws_query);
            
            $bp_query = "INSERT IGNORE INTO tb_bimactionplan (project_name,version) VAlUES('$projectName','Ver.1.0')";
            DB::insert($bp_query);
            
            DB::commit();
            
        }catch(Exception $e){
            DB::rollBack();
        }
        
        return "success";
    }

    public function editProject($projectName, $updateData){
        
        $query="UPDATE tb_work_summary SET name = ?,
            koujimeisho = ?,
            sekoubasho = ?,
            koujikanrisha = ?,
            sekousha = ?,
            shozonchi = ?,
            denwa = ?,
            fax = ?,
            shokatsurokisho = ?,
            timeInterval = ?,
            kaitouya = ?,
            glPlus = ?,
            glMinus = ?,
            kussakufukasa = ?,
            okujyou = ?,
            gaisou = ?,
            shikichimenseki = ?,
            kenchikumenseki = ?,
            ukeoikin = ?,
            shikyuzai = ?
         WHERE name = '$projectName'";
         
        DB::update($query, $updateData);
        return "success";
    }

    public function editProject2($projectName, $hachuusha, $sekkeisha, $meisho, $startTime, $endTime, $kenchikuyoto, $kozo, $zouchijyo, $kaichika, $yukamenseki){

        $query="UPDATE tb_bimactionplan SET project_name = '$projectName',
            orderer = '$hachuusha',
            sekkeisya = '$sekkeisha',
            kouji_jimusyo = '$meisho',
            tyakkou = '$startTime',
            syunkou = '$endTime',
            building_use = '$kenchikuyoto',
            kouzou = '$kozo',
            tijou = '$zouchijyo',
            tika = '$kaichika',
            total_floor_area = '$yukamenseki'
         WHERE project_name = '$projectName'";

        DB::update($query);
        return "success";
    }

    public function editImplementationDoc($oldName, $newName, $updateData){

        DB::beginTransaction(); 
        try{
            $ws_query="UPDATE tb_work_summary SET name = '$newName' WHERE name = '$oldName' ";
            DB::update($ws_query);

            $bp_query="UPDATE tb_bimactionplan SET project_name = ?,
                version = ?,
                orderer = ?,
                address = ?,
                building_use = ?,
                building_num = ?,
                tika = ?,
                tijou = ?,
                total_floor_area = ?,
                project_code = ?,
                kouji_kikan_code = ?,
                branch_store = ?,
                construction_type = ?,
                sekkeisya = ?,
                tyakkou = ?,
                syunkou = ?,
                kouzou = ?,
                kouji_jimusyo = ?,
                box_date1 = ?,box_upload_file1 = ?,box_rev_person1 = ?,
                box_date2 = ?,box_upload_file2 = ?,box_rev_person2 = ?,
                box_date3 = ?,box_upload_file3 = ?,box_rev_person3 = ?,
                ken_org = ?,ken_name = ?,
                kou_org = ?,kou_name = ?,
                sku_org = ?,sku_name = ?,
                sde_org = ?,sde_name = ?,
                sek_org = ?,sek_name = ?,
                sei_org = ?,sei_name = ?,
                koj_org = ?,koj_name = ?,
                sgi_org = ?,sgi_name = ?,
                smi_org = ?,smi_name = ?,
                bmn_org = ?,bmn_name = ?,
                pds_org = ?,pds_name = ?,
                mdl_org = ?,mdl_name = ?,
                sbk_org = ?,sbk_name = ?,
                sbd_org = ?,sbd_name = ?,
                fsa_org = ?,fsa_name = ?,
                fse_org = ?,fse_name = ?,
                make_model_start = ?,make_model_end = ?,make_model_bikou = ?,
                sinsei_start = ?,sinsei_end = ?,sinsei_bikou = ?,
                seisan_start = ?,seisan_end = ?,seisan_bikou = ?,
                kouji_start = ?,kouji_end = ?,kouji_bikou = ?,
                genba_start = ?,genba_end = ?,genba_bikou = ?,
                sekou_start = ?,sekou_end = ?,sekou_bikou = ?,
                hiki_start = ?,hiki_end = ?,hiki_bikou = ?,
                ken_sw = ?,kou_sw = ?,sku_sw = ?,sde_sw = ?,
                mdl_sw = ?,sek_sw = ?,sei_sw = ?,sbk_sw = ?,
                sbd_sw = ?,fsa_sw = ?,fse_sw = ?,
                base_linex = ?,base_liney = ?,
                rev_ver1 = ?,rev_date1 = ?,rev_contents1 = ?,rev_name1 = ?,
                rev_ver2 = ?,rev_date2 = ?,rev_contents2 = ?,rev_name2 = ?,
                rev_ver3 = ?,rev_date3 = ?,rev_contents3 = ?,rev_name3 = ?
             WHERE project_name = '$oldName'";
             
            DB::update($bp_query, $updateData);
            
            DB::commit();
            
        }catch(Exception $e){
            DB::rollBack();
        }
        
        return "success";
    }

    public function getProject($projectName){
        
        $query = "SELECT * FROM tb_work_summary WHERE name = '$projectName'";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }

    public function getImplementationDocByProject($projectName){
        
        $query = "SELECT * FROM tb_bimactionplan WHERE project_name = '$projectName'";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }

    public function getImplementationDocComboData($radio_state){
        
// 		if($projectName === ""){
		    
            //$query = "SELECT DISTINCT * FROM tb_bimactionplan";
            $where = "";
            if($radio_state != "all_project"){
                $where = " WHERE (a_pj_name IN(SELECT name FROM tb_project WHERE auto_save_properties = 1 ) 
                              OR b_pj_name IN(SELECT name FROM tb_project WHERE auto_save_properties = 1 )
                              OR b_tmp_pj_name IN(SELECT name FROM tb_project WHERE auto_save_properties = 1 ))";
            }
            
            $query = "SELECT a_sekou_basyo as address,
                             a_pj_code ,
                             ( CASE WHEN b_tmp_pj_name != '' THEN b_tmp_pj_name
                               WHEN b_pj_name != '' AND b_pj_name NOT LIKE '%と同じ%' THEN b_pj_name
                               ELSE a_pj_name END ) as project_name,
                            IF(b_youto IS NULL or b_youto='',a_youto1,b_youto) as building_use, 
                            b_hattyuusya as orderer,
                            a_shiten as branch_store,
                            a_kouji_kubun as construction_type,
                            IF(b_kouzou IS NULL or b_kouzou='',a_kouzou,b_kouzou) as kouzou, 
                            IF(b_sekkeisya1 IS NULL or b_sekkeisya1='',a_sekkei,b_sekkeisya1) as sekkeisya,
                            t.kou_org,t.sku_org,t.sde_org,
                            t.sek_org,t.sei_org,t.koj_org,
                            t.sgi_org,t.smi_org,t.bmn_org,
                            t.pds_org,t.mdl_org,t.sbk_org,
                            t.sbk_org,t.fsa_org,t.fse_org
                      FROM tb_allstore_info
                      LEFT JOIN tb_bimactionplan as t ON t.project_code = a_pj_code OR (t.project_name = a_pj_name OR t.project_name = b_pj_name OR t.project_name = b_tmp_pj_name )".$where;
			
// 		}else if(is_array($projectName)){
//             $cnt = 0;
//             $condition = " WHERE";
            
//             foreach($projectName as $value){
//                 if ($cnt !== 0){
//                     $condition .= " OR";
//                 }
//                 $condition .= " tb_bimactionplan.project_name LIKE '%$value%'";
//                 $cnt++;
// 	        }

//             $query = "SELECT * FROM tb_bimactionplan"+$condition;

// 		}else if(is_string($projectName)){
		    
// 		    $query = "SELECT * FROM tb_bimactionplan WHERE project_name LIKE '%$projectName%'";
// 		}
        
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }

    public function getProjectNameByImplementationDoc($prjAddressList, $buildingUseList, $ordererList, $branchStoreList, $constructionTypeList, $constructionList,$designerList){
        
		$condition				= "";
		$prjAddrCondition		= "";
		$buildingUseCondition	= "";
		$ordererCondition		= "";
// 		$relatedCompCondition	= "";
        $branchStoreCondition   = "";
        $conTypeCondition		= "";
        $conCondition	    	= "";
        $designerCondition		= "";
    		
    	if(!empty($prjAddressList)){
            $cnt = 0;
            foreach($prjAddressList as $value){
                if ($cnt !== 0){
                    $prjAddrCondition .= " OR";
                }
                //$prjAddrCondition .= " tb_bimactionplan.address = '$value'";
                $prjAddrCondition .= " a_sekou_basyo = '$value'";
                $cnt++;
            }
    	}
    	if(!empty($buildingUseList)){
            $cnt = 0;
            foreach($buildingUseList as $value){
                if ($cnt !== 0){
                    $buildingUseCondition .= " OR";
                }
                //$buildingUseCondition .= " tb_bimactionplan.building_use = '$value'";
                $buildingUseCondition .= "( b_youto = '$value' OR a_youto1 = '$value' )";
                $cnt++;
            }
    	}
    	if(!empty($ordererList)){
            $cnt = 0;
            foreach($ordererList as $value){
                if ($cnt !== 0){
                    $ordererCondition .= " OR";
                }
                //$ordererCondition .= " tb_bimactionplan.orderer = '$value'";
                $ordererCondition .= " b_hattyuusya = '$value'";
                $cnt++;
            }
    	}
    // 	if(!empty($relatedCompanyList)){
    //         $cnt = 0;
    //         foreach($relatedCompanyList as $value){
    //             if ($cnt !== 0){
    //                 $relatedCompCondition .= " OR";
    //             }
    //             $relatedCompCondition .= " tb_bimactionplan.ken_org = '$value'";
    //             $relatedCompCondition .= " OR tb_bimactionplan.kou_org = '$value'";
    //             $relatedCompCondition .= " OR tb_bimactionplan.sku_org = '$value'";
    //             $relatedCompCondition .= " OR tb_bimactionplan.sde_org = '$value'";
    //             $relatedCompCondition .= " OR tb_bimactionplan.sek_org = '$value'";
    //             $relatedCompCondition .= " OR tb_bimactionplan.sei_org = '$value'";
    //             $relatedCompCondition .= " OR tb_bimactionplan.koj_org = '$value'";
    //             $relatedCompCondition .= " OR tb_bimactionplan.sgi_org = '$value'";
    //             $relatedCompCondition .= " OR tb_bimactionplan.smi_org = '$value'";
    //             $relatedCompCondition .= " OR tb_bimactionplan.bmn_org = '$value'";
    //             $relatedCompCondition .= " OR tb_bimactionplan.pds_org = '$value'";
    //             $relatedCompCondition .= " OR tb_bimactionplan.mdl_org = '$value'";
    //             $relatedCompCondition .= " OR tb_bimactionplan.sbk_org = '$value'";
    //             $relatedCompCondition .= " OR tb_bimactionplan.sbk_org = '$value'";
    //             $relatedCompCondition .= " OR tb_bimactionplan.fsa_org = '$value'";
    //             $relatedCompCondition .= " OR tb_bimactionplan.fse_org = '$value'";
    //             $cnt++;
    //         }
    // 	}
    	if(!empty($branchStoreList)){
            $cnt = 0;
            foreach($branchStoreList as $value){
                if ($cnt !== 0){
                    $branchStoreCondition .= " OR";
                }
                //$branchStoreCondition .= " tb_bimactionplan.branch_store = '$value'";
                $branchStoreCondition .= " a_shiten = '$value'";
                $cnt++;
            }
    	}
    	if(!empty($constructionTypeList)){
            $cnt = 0;
            foreach($constructionTypeList as $value){
                if ($cnt !== 0){
                    $conTypeCondition .= " OR";
                }
                //$conTypeCondition .= " tb_bimactionplan.construction_type = '$value'";
                $conTypeCondition .= " a_kouji_kubun = '$value'";
                $cnt++;
            }
    	}
    	if(!empty($constructionList)){
            $cnt = 0;
            foreach($constructionList as $value){
                if ($cnt !== 0){
                    $conCondition .= " OR";
                }
                //$conCondition .= " tb_bimactionplan.kouzou = '$value'";
                $conCondition .= "( b_kouzou  = '$value' OR a_kouzou  = '$value')";
                $cnt++;
            }
    	}
    	if(!empty($designerList)){
            $cnt = 0;
            foreach($designerList as $value){
                if ($cnt !== 0){
                    $designerCondition .= " OR";
                }
                //$designerCondition .= " tb_bimactionplan.sekkeisya = '$value'";
                $designerCondition .= "( b_sekkeisya1 = '$value' OR a_sekkei = '$value' )";
                $cnt++;
            }
    	}

    	
    	if( ($prjAddrCondition !== "") || ($buildingUseCondition !== "") || ($ordererCondition !== "") ||
    		($branchStoreCondition !== "") || ($conTypeCondition !== "") ||
    		($conCondition !== "") || ($designerCondition !== "")){
    		$condition = " WHERE";
    		$condition .= $prjAddrCondition;
    		$condition .= ($condition !== " WHERE" && $buildingUseCondition !== "")? " AND ".$buildingUseCondition : $buildingUseCondition;
    		$condition .= ($condition !== " WHERE" && $ordererCondition !== "")? " AND ".$ordererCondition : $ordererCondition;
            $condition .= ($condition !== " WHERE" && $branchStoreCondition !== "")? " AND ".$branchStoreCondition : $branchStoreCondition;
            $condition .= ($condition !== " WHERE" && $conTypeCondition !== "")? " AND ".$conTypeCondition : $conTypeCondition;
            $condition .= ($condition !== " WHERE" && $conCondition !== "")? " AND ".$conCondition : $conCondition;
            $condition .= ($condition !== " WHERE" && $designerCondition !== "")? " AND ".$designerCondition : $designerCondition;
    	}
    	
    	if( ($prjAddrCondition === "") && ($buildingUseCondition === "") && ($ordererCondition === "") &&
    	    ($branchStoreCondition === "") && ($conTypeCondition === "") &&
    	    ($conCondition === "") && ($designerCondition === "")){
    		//$condition = " WHERE tb_bimactionplan.project_name = 'failed'";
    		$condition = " WHERE tb_allstore_info.a_pj_name = 'failed'";
    	}
    	
    	//$query = "SELECT * FROM tb_bimactionplan $condition";
    	$query = "SELECT (CASE WHEN b_tmp_pj_name != '' THEN b_tmp_pj_name 
    	                  WHEN b_pj_name != '' AND b_pj_name NOT LIKE '%と同じ%' THEN  b_pj_name
    	                  ELSE a_pj_name END ) as project_name
                FROM tb_allstore_info $condition";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }

    public function deleteProject($projectName){
        
        DB::beginTransaction(); 
        try{
            $ws_query = "DELETE FROM tb_work_summary WHERE name = '$projectName'";
            DB::delete($ws_query);
            
            $bp_query = "DELETE FROM tb_bimactionplan WHERE project_name = '$projectName'";
            DB::delete($bp_query);
            
            DB::commit();
            
        }catch(Exception $e){
            DB::rollBack();
        }

        return "success";
    }
    
}
