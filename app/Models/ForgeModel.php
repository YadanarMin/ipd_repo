<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class ForgeModel extends Model
{
    protected $table = '';
    /**
     * [15/6/2021] BY Yadanar
     * project combo
     * effect pages[ﾓﾃﾞﾙｽﾄﾚｰｼﾞ、ﾓﾃﾞﾙ分析、部屋ﾃﾞｰﾀ分析]
     * combo data filter by 閲覧権限設定のmodel data setting of each user
     * parameter session of login_user_id
    **/
    public function GetProjects()
    {
        $loginId = session('login_user_id');
        $query = "SELECT *
                    FROM tb_project 
                    WHERE auto_save_properties = 1 
                    AND id NOT IN('214','249','253','318') 
                    AND (CASE 
                        WHEN (SELECT model_data_set_id FROM tb_project_access_setting WHERE login_user_id = $loginId) = 0 then
                            FIND_IN_SET (id,(SELECT accessable_models FROM tb_project_access_setting WHERE login_user_id = $loginId))
                        WHEN (SELECT model_data_set_id FROM tb_project_access_setting WHERE login_user_id = $loginId) = 1 then  
                             id IN (SELECT id FROM tb_project WHERE auto_save_properties = 1)
                        ELSE           
                            FIND_IN_SET (id,(SELECT detail FROM tb_model_data_authority_set WHERE id=(SELECT model_data_set_id FROM tb_project_access_setting WHERE login_user_id = $loginId)))
                        END)
                    ORDER BY name ASC";//id IN(SELECT project_id FROM tb_forge_item) for map display project remove
                    
        $data = DB::select($query); 
        return json_decode(json_encode($data),true);
        //AND FIND_IN_SET (id,(SELECT accessable_models FROM tb_project_access_setting WHERE login_user_id = $loginId))
    }
    
    public function GetAllProject()
    {
        $query = "SELECT * FROM tb_project ORDER BY auto_save_properties DESC,auto_backup DESC, name ASC";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    
    public function GetAllBackupProject()
    {
        $query = "SELECT * FROM tb_project ORDER BY auto_backup DESC, name ASC";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    
    /**
     * [15/6/2021] BY Yadanar
     * item combo
     * effect pages[ﾓﾃﾞﾙｽﾄﾚｰｼﾞ、ﾓﾃﾞﾙ分析、部屋ﾃﾞｰﾀ分析]
     * combo data filter by 閲覧権限設定のmodel data setting of each user
     * parameter session of login_user_id
    **/
    public function GetItems()
    {
        $loginId = session('login_user_id');
        $query = "SELECT ti.*
                  FROM tb_forge_item as ti
                  LEFT JOIN tb_project as tp ON tp.id = ti.project_id
                  WHERE tp.auto_save_properties = 1
                  AND (CASE 
                        WHEN (SELECT model_data_set_id FROM tb_project_access_setting WHERE login_user_id = $loginId) = 0 then
                            FIND_IN_SET (tp.id,(SELECT accessable_models FROM tb_project_access_setting WHERE login_user_id = $loginId))
                        WHEN (SELECT model_data_set_id FROM tb_project_access_setting WHERE login_user_id = $loginId) = 1 then  
                             tp.id IN (SELECT id FROM tb_project WHERE auto_save_properties = 1)
                        ELSE           
                            FIND_IN_SET (tp.id,(SELECT detail FROM tb_model_data_authority_set WHERE id=(SELECT model_data_set_id FROM tb_project_access_setting WHERE login_user_id = $loginId)))
                        END)";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    
    /**
     * [15/6/2021] BY Yadanar
     * version combo
     * effect pages[ﾓﾃﾞﾙｽﾄﾚｰｼﾞ、ﾓﾃﾞﾙ分析、部屋ﾃﾞｰﾀ分析]
     * combo data filter by 閲覧権限設定のmodel data setting of each user
     * parameter session of login_user_id
    **/
    public function GetVersions()
    {
        $loginId = session('login_user_id');
        $query = "SELECT item.name,vrs.*
                  FROM tb_forge_version vrs 
                  LEFT JOIN tb_forge_item item ON item.id = vrs.item_id
                  LEFT JOIN tb_project as tp ON tp.id = item.project_id
                  WHERE tp.auto_save_properties = 1
                  AND (CASE 
                        WHEN (SELECT model_data_set_id FROM tb_project_access_setting WHERE login_user_id = $loginId) = 0 then
                            FIND_IN_SET (tp.id,(SELECT accessable_models FROM tb_project_access_setting WHERE login_user_id = $loginId))
                        WHEN (SELECT model_data_set_id FROM tb_project_access_setting WHERE login_user_id = $loginId) = 1 then  
                             tp.id IN (SELECT id FROM tb_project WHERE auto_save_properties = 1)
                        ELSE           
                            FIND_IN_SET (tp.id,(SELECT detail FROM tb_model_data_authority_set WHERE id=(SELECT model_data_set_id FROM tb_project_access_setting WHERE login_user_id = $loginId)))
                        END)";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    
    public function GetProjectInfo()
    {
        $query = "SELECT * FROM tb_forge_project_info";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }

    public function GetProjectsByProjectNames($projectNameList)
    {
        $condition = "";
        if(!empty($projectNameList)){
            $cnt = 0;
            $condition = " WHERE";
            
            foreach($projectNameList as $value){
                if ($cnt !== 0){
                    $condition .= " OR";
                }
                $prjName = $value;
                $condition .= " tb_project.name LIKE '%$prjName%'";
                $cnt++;
            }
            
            $condition .= " AND auto_save_properties = 1 ORDER BY name ASC";
        }
        else{
            $condition .= " WHERE auto_save_properties = 1 ORDER BY name ASC";
        }

        $query = "SELECT * FROM tb_project $condition";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    
    public function GetProjectByProjectName($projectName)
    {
        $condition = "";
        if(!empty($projectName)){
            $condition = " WHERE tb_project.name LIKE '%$projectName%' AND auto_save_properties = 1 ORDER BY name ASC";
        }

        $query = "SELECT * FROM tb_project $condition";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    
    public function GetItemsByProjects($projects)
    {
        if(empty($projects)){
            return [];            
        }else{
            
            $cnt = 0;
            $condition = " WHERE";
            
            foreach($projects as $value){
                if ($cnt !== 0){
                    $condition .= " OR";
                }
                $project_id = $value['id'];
                
                $condition .= " tb_forge_item.project_id = $project_id";
                $cnt++;
            }

            $query = "SELECT * FROM tb_forge_item $condition";
            $data = DB::select($query);     
            return json_decode(json_encode($data),true);
        }
    }

    public function GetVersionsByItems($items)
    {
        if(empty($items)){
            return [];            
        }else{
            $cnt = 0;
            $condition = "WHERE";
            
            foreach($items as $value){
                if ($cnt !== 0){
                    $condition .= " OR";
                }
                $item_id = $value['id'];
                $condition .= " vrs.item_id = $item_id";
                $cnt++;
            }

            $query = "SELECT item.name,vrs.* FROM tb_forge_version vrs 
                      LEFT JOIN  tb_forge_item item  ON item.id = vrs.item_id
                      $condition
                      ORDER BY item.name,vrs.version_number ASC";
            $data = DB::select($query);     
            return json_decode(json_encode($data),true);
        }
    }
    
    public function GetItemsByProject($project)
    {
        $query = "SELECT ti.* FROM tb_forge_item ti
                  LEFT JOIN tb_project tp ON tp.id = ti.project_id
                  WHERE tp.name='$project'";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }

    public function GetItemsByPjCode($pjCode)
    {
        $query = "SELECT ti.* FROM tb_forge_item ti
                  LEFT JOIN tb_project tp ON tp.id = ti.project_id
                  WHERE tp.name='$pjCode%'";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }

    public function GetLatedVersionsByItem($itemIdArray)
    {
        $idList = ($itemIdArray == "") ? "'"."ALL_UNCHECK"."'" : "'" . implode ( "', '", $itemIdArray ) . "'";//convert array to string with single code
        $query = "SELECT MAX(version_number) as version_number, MAX(storage_size) as storage_size,
                 (SELECT name FROM tb_forge_item WHERE id = item_id) as name
                 FROM tb_forge_version 
                 WHERE item_id IN ($idList)
                 AND version_number IN ( SELECT MAX(version_number) as version_number FROM tb_forge_version WHERE item_id IN ($idList) GROUP BY item_id)
                 GROUP BY item_id";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    
    public function GetVersionsByProject($project,$itemName)
    {
        $item_condition;
        if($itemName == ""){
            $item_condition = $itemName;
        }else{
            $item_condition = " AND item.name='$itemName'";
        }

        $query = "SELECT item.name,vrs.* FROM tb_forge_version vrs
                  LEFT JOIN  tb_forge_item item  ON item.id = vrs.item_id
                  LEFT JOIN tb_project tp ON tp.id = item.project_id
                  WHERE tp.name = '$project'".$item_condition."
                  ORDER BY item.name,vrs.version_number DESC";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }

    public function GetVersionsByPjCode($pjCode,$itemName)
    {
        $item_condition;
        if($itemName == ""){
            $item_condition = $itemName;
        }else{
            $item_condition = " AND item.name='$itemName'";
        }

        $query = "SELECT item.name,vrs.* FROM tb_forge_version vrs
                  LEFT JOIN  tb_forge_item item  ON item.id = vrs.item_id
                  LEFT JOIN tb_project tp ON tp.id = item.project_id
                  WHERE tp.name = '$pjCode%'".$item_condition."
                  ORDER BY item.name,vrs.version_number DESC";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }

    
    public function GetMaterailsByProject($project)
    {
        $query =  "(SELECT material_name as name from tb_forge_column 
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY material_name)
                    UNION 
                    (SELECT material_name as name from tb_forge_column_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY material_name)
                    UNION 
                    (SELECT material_name as name from tb_forge_beam
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY material_name)
                    UNION 
                    (SELECT material_name as name from tb_forge_beam_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY material_name)
                    UNION 
                    (SELECT material_name as name from tb_forge_floor
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY material_name)
                    UNION 
                    (SELECT material_name as name from tb_forge_floor_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY material_name)
                    UNION 
                    (SELECT material_name as name from tb_forge_wall
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY material_name)
                    UNION 
                    (SELECT material_name as name from tb_forge_wall_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY material_name)
                    UNION 
                    (SELECT material_name as name from tb_forge_foundation
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY material_name)
                    UNION 
                    (SELECT material_name as name from tb_forge_foundation_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY material_name)";
      $data = DB::select($query);     
      return json_decode(json_encode($data),true);
    }
    
    public function GetMaterailsByPjCode($pjCode)
    {
        $query =  "(SELECT material_name as name from tb_forge_column 
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY material_name)
                    UNION 
                    (SELECT material_name as name from tb_forge_column_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY material_name)
                    UNION 
                    (SELECT material_name as name from tb_forge_beam
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY material_name)
                    UNION 
                    (SELECT material_name as name from tb_forge_beam_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY material_name)
                    UNION 
                    (SELECT material_name as name from tb_forge_floor
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY material_name)
                    UNION 
                    (SELECT material_name as name from tb_forge_floor_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY material_name)
                    UNION 
                    (SELECT material_name as name from tb_forge_wall
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY material_name)
                    UNION 
                    (SELECT material_name as name from tb_forge_wall_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY material_name)
                    UNION 
                    (SELECT material_name as name from tb_forge_foundation
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY material_name)
                    UNION 
                    (SELECT material_name as name from tb_forge_foundation_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY material_name)";
      $data = DB::select($query);     
      return json_decode(json_encode($data),true);
    }


    public function GetWorksetsByProject($project)
    {
        $query =  "(SELECT workset as name from tb_forge_column 
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_column_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_beam
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_beam_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_floor
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_floor_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_wall
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_wall_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_foundation
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_foundation_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_door
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_door_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_window
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_window_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY workset)";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }

    public function GetWorksetsByPjCode($pjCode)
    {
        $query =  "(SELECT workset as name from tb_forge_column 
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_column_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_beam
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_beam_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_floor
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_floor_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_wall
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_wall_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_foundation
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_foundation_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_door
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_door_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_window
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY workset)
                    UNION 
                    (SELECT workset as name from tb_forge_window_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY workset)";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }

    public function GetLevelsByProject($project)
    {
        $query =  "(SELECT level as name from tb_forge_column 
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_column_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_beam
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_beam_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_floor
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_floor_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_wall
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_wall_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_foundation
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_foundation_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_door
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_door_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_window
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_window_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY level)";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    
    public function GetLevelsByPjCode($pjCode)
    {
        $query =  "(SELECT level as name from tb_forge_column 
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_column_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_beam
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_beam_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_floor
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_floor_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_wall
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_wall_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_foundation
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_foundation_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_door
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_door_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_window
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY level)
                    UNION 
                    (SELECT level as name from tb_forge_window_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY level)";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }


    public function GetFamilyNamesByProject($project)
    {
        $query =  "(SELECT family_name as name from tb_forge_column
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY family_name)
                    UNION 
                    (SELECT family_name as name from tb_forge_column_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY family_name)
                    UNION 
                    (SELECT family_name as name from tb_forge_beam
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY family_name)
                    UNION 
                    (SELECT family_name as name from tb_forge_beam_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY family_name)
                    UNION 
                    (SELECT family_name as name from tb_forge_floor
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY family_name)
                    UNION 
                    (SELECT family_name as name from tb_forge_floor_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY family_name)
                    UNION 
                    (SELECT family_name as name from tb_forge_wall
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY family_name)
                    UNION 
                    (SELECT family_name as name from tb_forge_wall_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY family_name)
                    UNION 
                    (SELECT family_name as name from tb_forge_foundation
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY family_name)
                    UNION 
                    (SELECT family_name as name from tb_forge_foundation_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY family_name)";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    
    public function GetFamilyNamesByPjCode($pjCode)
    {
        $query =  "(SELECT family_name as name from tb_forge_column
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY family_name)
                    UNION 
                    (SELECT family_name as name from tb_forge_column_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY family_name)
                    UNION 
                    (SELECT family_name as name from tb_forge_beam
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY family_name)
                    UNION 
                    (SELECT family_name as name from tb_forge_beam_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY family_name)
                    UNION 
                    (SELECT family_name as name from tb_forge_floor
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY family_name)
                    UNION 
                    (SELECT family_name as name from tb_forge_floor_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY family_name)
                    UNION 
                    (SELECT family_name as name from tb_forge_wall
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY family_name)
                    UNION 
                    (SELECT family_name as name from tb_forge_wall_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY family_name)
                    UNION 
                    (SELECT family_name as name from tb_forge_foundation
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY family_name)
                    UNION 
                    (SELECT family_name as name from tb_forge_foundation_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY family_name)";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }


    public function GetTypeNamesByProject($project)
    {
        $query =  "(SELECT type_name as name from tb_forge_column 
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_column_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_beam
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_beam_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_floor
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_floor_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_wall
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_wall_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_foundation
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_foundation_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_door
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_door_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_window
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_window_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY type_name)";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    
    public function GetTypeNamesByPjCode($pjCode)
    {
        $query =  "(SELECT type_name as name from tb_forge_column 
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_column_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_beam
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_beam_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_floor
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_floor_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_wall
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_wall_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_foundation
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_foundation_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_door
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_door_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_window
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY type_name)
                    UNION 
                    (SELECT type_name as name from tb_forge_window_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$pjCode%'
                    GROUP BY type_name)";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }

    
    public function GetTargetByVersion($project,$targetName,$versionNumber)
    {
        $query =  "(SELECT $targetName as name from tb_forge_column 
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber
                    GROUP BY $targetName)
                    UNION 
                    (SELECT $targetName as name from tb_forge_column_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber
                    GROUP BY $targetName)
                    UNION 
                    (SELECT $targetName as name from tb_forge_beam
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber
                    GROUP BY $targetName)
                    UNION 
                    (SELECT $targetName as name from tb_forge_beam_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber
                    GROUP BY $targetName)
                    UNION 
                    (SELECT $targetName as name from tb_forge_floor
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber
                    GROUP BY $targetName)
                    UNION 
                    (SELECT $targetName as name from tb_forge_floor_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber
                    GROUP BY $targetName)
                    UNION 
                    (SELECT $targetName as name from tb_forge_wall
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber
                    GROUP BY $targetName)
                    UNION 
                    (SELECT $targetName as name from tb_forge_wall_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber
                    GROUP BY $targetName)
                    UNION 
                    (SELECT $targetName as name from tb_forge_foundation
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber
                    GROUP BY $targetName)
                    UNION 
                    (SELECT $targetName as name from tb_forge_foundation_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber
                    GROUP BY $targetName) ";
                    
        if($targetName == "level" || $targetName == "workset" || $targetName == "type_name"){
            $query .= "UNION 
                        (SELECT $targetName as name from tb_forge_door
                        LEFT JOIN  tb_forge_item item  ON item.id = item_id
                        LEFT JOIN tb_project tp ON tp.id = item.project_id
                        WHERE tp.name = '$project' AND version_number <= $versionNumber
                        GROUP BY $targetName)
                        UNION 
                        (SELECT $targetName as name from tb_forge_door_updated
                        LEFT JOIN  tb_forge_item item  ON item.id = item_id
                        LEFT JOIN tb_project tp ON tp.id = item.project_id
                        WHERE tp.name = '$project' AND version_number <= $versionNumber
                        GROUP BY $targetName)
                        UNION 
                        (SELECT $targetName as name from tb_forge_window
                        LEFT JOIN  tb_forge_item item  ON item.id = item_id
                        LEFT JOIN tb_project tp ON tp.id = item.project_id
                        WHERE tp.name = '$project' AND version_number <= $versionNumber
                        GROUP BY $targetName)
                        UNION 
                        (SELECT $targetName as name from tb_forge_window_updated
                        LEFT JOIN  tb_forge_item item  ON item.id = item_id
                        LEFT JOIN tb_project tp ON tp.id = item.project_id
                        WHERE tp.name = '$project' AND version_number <= $versionNumber
                        GROUP BY $targetName)";
        }   
        
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }

    public function GetTargetByColumnList($project,$targetName,$versionNumber,$levelList,$worksetList,$materialList,$familyNameList,$typeNameList)
    {
        $isSetColumn = false;
        $condition = "AND (";
        if (!empty($levelList)){
            foreach($levelList as $value){
                if ($value !== reset($levelList)){
                    $condition .= " OR ";
                }
                $condition .= "level = '$value'";
                $isSetColumn = true;
            }
        }
        if (!empty($worksetList)){
            foreach($worksetList as $value){
                if ($value === reset($worksetList)){
                    if ($isSetColumn){
                        $condition .= " OR ";
                    }
                }
                else{
                    $condition .= " OR ";
                }
                $condition .= "workset = '$value'";
                $isSetColumn = true;
            }
        }
        if (!empty($materialList)){
            foreach($materialList as $value){
                if ($value === reset($materialList)){
                    if ($isSetColumn){
                        $condition .= " OR ";
                    }
                }
                else{
                    $condition .= " OR ";
                }
                $condition .= "material_name = '$value'";
                $isSetColumn = true;
            }
        }
        if (!empty($familyNameList)){
            foreach($familyNameList as $value){
                if ($value === reset($familyNameList)){
                    if ($isSetColumn){
                        $condition .= " OR ";
                    }
                }
                else{
                    $condition .= " OR ";
                }
                $condition .= "family_name = '$value'";
                $isSetColumn = true;
            }
        }
        if (!empty($typeNameList)){
            foreach($typeNameList as $value){
                if ($value === reset($typeNameList)){
                    if ($isSetColumn){
                        $condition .= " OR ";
                    }
                }
                else{
                    $condition .= " OR ";
                }
                $condition .= "type_name = '$value'";
                $isSetColumn = true;
            }
        }
        if (!$isSetColumn){ $condition = ""; }else{ $condition .= ")"; }
        
        $query =  "(SELECT $targetName as name from tb_forge_column 
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber $condition
                    GROUP BY $targetName)
                    UNION 
                    (SELECT $targetName as name from tb_forge_column_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber $condition
                    GROUP BY $targetName)
                    UNION 
                    (SELECT $targetName as name from tb_forge_beam
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber $condition
                    GROUP BY $targetName)
                    UNION 
                    (SELECT $targetName as name from tb_forge_beam_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber $condition
                    GROUP BY $targetName)
                    UNION 
                    (SELECT $targetName as name from tb_forge_floor
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber $condition
                    GROUP BY $targetName)
                    UNION 
                    (SELECT $targetName as name from tb_forge_floor_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber $condition
                    GROUP BY $targetName)
                    UNION 
                    (SELECT $targetName as name from tb_forge_wall
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber $condition
                    GROUP BY $targetName)
                    UNION 
                    (SELECT $targetName as name from tb_forge_wall_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber $condition
                    GROUP BY $targetName)
                    UNION 
                    (SELECT $targetName as name from tb_forge_foundation
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber $condition
                    GROUP BY $targetName)
                    UNION 
                    (SELECT $targetName as name from tb_forge_foundation_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber $condition
                    GROUP BY $targetName)";
                    
                    if(($targetName == "level" || $targetName == "workset" || $targetName == "type_name") && empty($familyNameList && empty($materialList))){
            /*$query .= "UNION 
                    (SELECT $targetName as name from tb_forge_door
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber $condition
                    GROUP BY $targetName)
                    UNION 
                    (SELECT $targetName as name from tb_forge_door_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber $condition
                    GROUP BY $targetName)
                    UNION 
                    (SELECT $targetName as name from tb_forge_window
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber $condition
                    GROUP BY $targetName)
                    UNION 
                    (SELECT $targetName as name from tb_forge_window_updated
                    LEFT JOIN  tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project' AND version_number <= $versionNumber $condition
                    GROUP BY $targetName)";*/
        }   
        // return $query;
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }

    public function GetRoomInfoByProject($project)
    {
        $query =  "(SELECT * from tb_forge_room 
                    LEFT JOIN tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY room_name)
                    UNION
                    (SELECT * from tb_forge_room_updated
                    LEFT JOIN tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY room_name)";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    
    public function GetDoorInfoByProject($project)
    {
        $query =  "(SELECT * from tb_forge_door
                    LEFT JOIN tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY element_id)
                    UNION
                    (SELECT * from tb_forge_door_updated
                    LEFT JOIN tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY element_db_id)";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    
    public function GetWindowInfoByProject($project)
    {
        $query =  "(SELECT * from tb_forge_window
                    LEFT JOIN tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY element_id)
                    UNION
                    (SELECT * from tb_forge_window_updated
                    LEFT JOIN tb_forge_item item  ON item.id = item_id
                    LEFT JOIN tb_project tp ON tp.id = item.project_id
                    WHERE tp.name = '$project'
                    GROUP BY element_id)";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }

    public function UpdateProjectAutoSaveFlag($updateProject){
        $projects = ($updateProject == "") ? "'"."ALL_UNCHECK"."'" : "'" . implode ( "', '", $updateProject ) . "'";//convert array to string with single code
        //$backupProjects = ($backupProject == "") ? "'"."ALL_UNCHECK"."'"  : "'" . implode ( "', '", $backupProject ) . "'";
        $query = "UPDATE tb_project 
                  SET auto_save_properties = (CASE 
                                              WHEN name IN ($projects) 
                                                THEN 1
                                                ELSE 0
                                            END)";
        DB::update($query);
         //insert project_name into tb_bimactionplan,tb_work_summary
        DB::beginTransaction();    
        try {
            foreach($updateProject as $prjName){
                $insertQuery = "INSERT IGNORE INTO tb_bimactionplan (project_name) VALUES('$prjName')";
                DB::insert($insertQuery);
                 
                $insertSummaryQuery = "INSERT IGNORE INTO tb_work_summary (name) VALUES('$prjName')";
                DB::insert($insertSummaryQuery);
             }
         
            DB::commit();
        
        } catch(Exception $e) {
        DB::rollBack();
        
        }
         
        return "success";
    }
    
    public function UpdateProjectBackupFlag($backupProject){
        
        $backupProjects = ($backupProject == "") ? "'"."ALL_UNCHECK"."'"  : "'" . implode ( "', '", $backupProject ) . "'";
        $query = "UPDATE tb_project 
                  SET auto_backup = (CASE 
                                      WHEN name IN ($backupProjects) 
                                        THEN 1
                                        ELSE 0
                                    END)";
        DB::update($query);
        return "success";
    }
    
    public function GetDataByVersion($version_number,$item_id,$category_list,$material_list,$workset_list,$level_list,$familyName_list,$typeName_list,$typeName_filter){
        $query="";
        $condition = "";
        $windowDoorcondition = "";
        
        if($material_list != ""){
            $material_str = "'" . implode ( "', '", $material_list ) . "'";
            $condition .= " AND material_name IN ($material_str)";
        }
        
        if($familyName_list != ""){
            $familyName_str = "'" . implode ( "', '", $familyName_list ) . "'";
            $condition .= " AND family_name IN ($familyName_str)";
        }

        if($typeName_filter != ""){
            $condition .= " AND type_name LIKE BINARY '%$typeName_filter%'";
        }
        
         if($typeName_list != ""){
            $typeName_str = "'" . implode ( "', '", $typeName_list ) . "'";
            $condition .= " AND type_name IN ($typeName_str)";
            $windowDoorcondition .= " AND type_name IN ($typeName_str)";
        }
        
        if($workset_list != ""){
            $workset_str = "'" . implode ( "', '", $workset_list ) . "'";
            $condition .= " AND workset IN ($workset_str)";
            $windowDoorcondition .= " AND workset IN ($workset_str)";
        }
        if($level_list != ""){
            $level_str = "'" . implode ( "', '", $level_list ) . "'";
            $condition .= " AND level IN ($level_str)";
            $windowDoorcondition .= " AND level IN ($level_str)";
        }
        //print_r($category_list);exit;
      
        $version_condition;
        if($version_number == ""){
            $version_condition = "(Select version_number from tb_forge_version  where item_id = 1 ORDER BY version_number DESC limit 1)";
        }else{
            $version_condition = $version_number;
        }
        
        if($category_list == "" || in_array('column', $category_list)){//find given value is exist or not in array
        
        $query .= "(SELECT 
                      col.*,0 as price,null as type_panel
                  FROM
                      ((SELECT tmp.* 
                        FROM 
                        (SELECT 
                          *
                          FROM tb_forge_column_updated as t1
                          WHERE t1.version_number <= ".$version_condition." AND t1.item_id = $item_id ".$condition."
                          ORDER BY t1.version_number DESC)as tmp
                      GROUP BY tmp.element_id)
                      UNION ALL 
                      (SELECT *
                        FROM tb_forge_column
                        WHERE version_number <= ".$version_condition." AND item_id = $item_id ".$condition.")) as col
                WHERE col.element_id NOT IN (SELECT element_id from tb_forge_column_deleted WHERE version_number <= $version_condition and item_id = $item_id)
                  GROUP BY col.element_id ORDER BY col.level)";

        }

        if($category_list == "" || in_array('beam', $category_list)){

            if($query != "") $query .= " UNION ALL ";        
            $query .= "(SELECT 
                        beam.*,0 as price,null as type_panel
                    FROM
                        ((SELECT tmp.*
                        FROM
                        (SELECT 
                           *
                            FROM tb_forge_beam_updated as t1
                            WHERE t1.version_number <= ".$version_condition." AND t1.item_id = $item_id ".$condition."
                            ORDER BY t1.version_number DESC)as tmp
                        GROUP BY tmp.element_id)
                        UNION ALL 
                        (SELECT *
                          FROM tb_forge_beam
                          WHERE version_number <= ".$version_condition." AND item_id = $item_id ".$condition.")) as beam
                    WHERE beam.element_id NOT IN (SELECT element_id from tb_forge_beam_deleted WHERE version_number <= $version_condition AND item_id = $item_id)
                    GROUP BY beam.element_id
                    ORDER BY beam.level)";
        }

        if($category_list == "" || in_array('floor', $category_list)){
            if($query != "") $query .= " UNION ALL ";        
            $query .= "(SELECT 
                        flr.*,0 as price,null as type_panel
                    FROM
                        ((SELECT tmp.*
                        FROM
                            (SELECT 
                                *
                            FROM tb_forge_floor_updated as t1
                            WHERE t1.version_number <= ".$version_condition." AND t1.item_id = $item_id ".$condition."
                            ORDER BY t1.version_number DESC)as tmp
                        GROUP BY tmp.element_id)
                        UNION ALL 
                        (SELECT *
                          FROM tb_forge_floor
                          WHERE version_number <= ".$version_condition." AND item_id = $item_id ".$condition.")) as flr
                    WHERE flr.element_id NOT IN (SELECT element_id from tb_forge_floor_deleted WHERE version_number <= $version_condition AND item_id = $item_id)
                    GROUP BY flr.element_id
                    ORDER BY flr.level)";
        }

        if($category_list == "" || in_array('wall', $category_list)){
            if($query != "") $query .= " UNION ALL ";        
            $query .= "(SELECT 
                       wall.*,0 as price,null as type_panel
                    FROM
                        ((SELECT tmp.*
                        FROM
                        (SELECT 
                            *
                            FROM tb_forge_wall_updated as t1
                            WHERE t1.version_number <= ".$version_condition." AND t1.item_id = $item_id ".$condition."
                            ORDER BY t1.version_number DESC)as tmp
                        GROUP BY tmp.element_id)
                        UNION ALL 
                        (SELECT *
                          FROM tb_forge_wall
                          WHERE version_number <= ".$version_condition." AND item_id = $item_id ".$condition.")) as wall
                    WHERE wall.element_id NOT IN (SELECT element_id from tb_forge_wall_deleted WHERE version_number <= $version_condition AND item_id = $item_id)
                    GROUP BY wall.element_id
                    ORDER BY wall.level)";
        }

        if($category_list == "" || in_array('foundation', $category_list)){
            if($query != "") $query .= " UNION ALL ";        
            $query .= "(SELECT 
                        fd.*,0 as price,null as type_panel
                    FROM
                        ((SELECT tmp.*
                        FROM
                        (SELECT 
                            *
                            FROM tb_forge_foundation_updated as t1
                            WHERE t1.version_number <= ".$version_condition." AND t1.item_id = $item_id ".$condition."
                            ORDER BY t1.version_number DESC)as tmp
                        GROUP BY tmp.element_id)
                        UNION ALL 
                        (SELECT *
                          FROM tb_forge_foundation
                          WHERE version_number <= ".$version_condition." AND item_id = $item_id ".$condition.")) as fd
                    WHERE fd.element_id NOT IN (SELECT element_id from tb_forge_foundation_deleted WHERE version_number <= $version_condition AND item_id = $item_id)
                    GROUP BY fd.element_id
                    ORDER BY fd.level)";
        }
        
        if($category_list == "" || (in_array('window', $category_list) && $material_list == "" && $familyName_list == "")){
            if($query != "") $query .= " UNION ALL ";        
            $query .= "(SELECT 
                        window.id as id,window.type_name as type_name,window.item_id as item_id,window.element_id as element_id
                        ,window.element_db_id as element_db_id,null as material_name, 
                        window.level as level ,0 as volume,null as family_name,
                        window.workset as workset,window.version_id as version_id ,window.version_number as version_number,null as phase,
                        window.price as price,IFNULL(window.type_window_panel,'empty') as type_panel
                    FROM
                        ((SELECT tmp.*
                        FROM
                        (SELECT 
                            id,type_name,item_id,element_id,element_db_id,level,workset,version_id,version_number,price,type_window_panel
                            FROM tb_forge_window_updated as t1
                            WHERE t1.version_number <= ".$version_condition." AND t1.item_id = $item_id ".$windowDoorcondition."
                            ORDER BY t1.version_number DESC)as tmp
                        GROUP BY tmp.element_id)
                        UNION ALL 
                        (SELECT id,type_name,item_id,element_id,element_db_id,level,workset,version_id,version_number,price,type_window_panel
                          FROM tb_forge_window
                          WHERE version_number <= ".$version_condition." AND item_id = $item_id ".$windowDoorcondition.")) as window
                    WHERE window.element_id NOT IN (SELECT element_id from tb_forge_window_deleted WHERE version_number <= $version_condition AND item_id = $item_id)
                    GROUP BY window.element_id
                    ORDER BY window.level)";
        }
        
        if($category_list == "" || (in_array('door', $category_list)&& $material_list == "" && $familyName_list == "")){
            if($query != "") $query .= " UNION ALL ";        
            $query .= "(SELECT 
                        door.id as id,door.type_name as type_name,door.item_id as item_id,door.element_id as element_id
                        ,door.element_db_id as element_db_id,null as material_name, 
                        door.level as level ,0 as volume,null as family_name,
                        door.workset as workset,door.version_id as version_id ,door.version_number as version_number,null as phase,
                        door.price as price,IFNULL(door.type_door_panel,'empty') as type_panel
                    FROM
                        ((SELECT tmp.*
                        FROM
                        (SELECT 
                            id,type_name,item_id,element_id,element_db_id,level,workset,version_id,version_number,price,type_door_panel
                            FROM tb_forge_door_updated as t1
                            WHERE t1.version_number <= ".$version_condition." AND t1.item_id = $item_id ".$windowDoorcondition."
                            ORDER BY t1.version_number DESC)as tmp
                        GROUP BY tmp.element_id)
                        UNION ALL 
                        (SELECT id,type_name,item_id,element_id,element_db_id,level,workset,version_id,version_number,price,type_door_panel
                          FROM tb_forge_door
                          WHERE version_number <= ".$version_condition." AND item_id = $item_id ".$windowDoorcondition.")) as door
                    WHERE door.element_id NOT IN (SELECT element_id from tb_forge_door_deleted WHERE version_number <= $version_condition AND item_id = $item_id)
                    GROUP BY door.element_id
                    ORDER BY door.level)";
        }


        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }

    public function GetRoomDataByVersion($version_number,$item_id,$workset_list,$level_list,$room_list,
                                          $tenjoShiage_list,$kabeShiage_list,$yukaShiage_list,
                                          $tenjoShitaji_list,$kabeShitaji_list,$yukaShitaji_list,
                                          $habaki_list,$mawaribuchi_list){
        $query="";
        $condition = "";

        if($workset_list != ""){
            if ($workset_list[0] == "NoName"){
                $condition .= " AND workset = ''";
            }else{
                $workset_str = "'" . implode ( "', '", $workset_list ) . "'";
                $condition .= " AND workset IN ($workset_str)";
            }
        }
        if($level_list != ""){
            if ($level_list[0] == "NoName"){
                $condition .= " AND level = ''";
            }else{
                $level_str = "'" . implode ( "', '", $level_list ) . "'";
                $condition .= " AND level IN ($level_str)";
            }
        }
        if($room_list != ""){
            if ($room_list[0] == "NoName"){
                $condition .= " AND room_name = ''";
            }else{
                $room_str = "'" . implode ( "', '", $room_list ) . "'";
                $condition .= " AND room_name IN ($room_str)";
            }
        }
        if($tenjoShiage_list != ""){
            if ($tenjoShiage_list[0] == "NoName"){
                $condition .= " AND shiage_tenjo = ''";
            }else{
                $tenjoShiage_str = "'" . implode ( "', '", $tenjoShiage_list ) . "'";
                $condition .= " AND shiage_tenjo IN ($tenjoShiage_str)";
            }
        }
        if($kabeShiage_list != ""){
            if ($kabeShiage_list[0] == "NoName"){
                $condition .= " AND shiage_kabe = ''";
            }else{
                $kabeShiage_str = "'" . implode ( "', '", $kabeShiage_list ) . "'";
                $condition .= " AND shiage_kabe IN ($kabeShiage_str)";
            }
        }
        if($yukaShiage_list != ""){
            if ($yukaShiage_list[0] == "NoName"){
                $condition .= " AND shiage_yuka = ''";
            }else{
                $yukaShiage_str = "'" . implode ( "', '", $yukaShiage_list ) . "'";
                $condition .= " AND shiage_yuka IN ($yukaShiage_str)";
            }
        }
        if($tenjoShitaji_list != ""){
            if ($tenjoShitaji_list[0] == "NoName"){
                $condition .= " AND tenjo_shitaji = ''";
            }else{
                $tenjoShitaji_str = "'" . implode ( "', '", $tenjoShitaji_list ) . "'";
                $condition .= " AND tenjo_shitaji IN ($tenjoShitaji_str)";
            }
        }
        if($kabeShitaji_list != ""){
            if ($kabeShitaji_list[0] == "NoName"){
                $condition .= " AND kabe_shitaji = ''";
            }else{
                $kabeShitaji_str = "'" . implode ( "', '", $kabeShitaji_list ) . "'";
                $condition .= " AND kabe_shitaji IN ($kabeShitaji_str)";
            }
        }
        if($yukaShitaji_list != ""){
            if ($yukaShitaji_list[0] == "NoName"){
                $condition .= " AND yuka_shitaji = ''";
            }else{
                $yukaShitaji_str = "'" . implode ( "', '", $yukaShitaji_list ) . "'";
                $condition .= " AND yuka_shitaji IN ($yukaShitaji_str)";
            }
        }
        if($habaki_list != ""){
            if ($habaki_list[0] == "NoName"){
                $condition .= " AND habaki = ''";
            }else{
                $habaki_str = "'" . implode ( "', '", $habaki_list ) . "'";
                $condition .= " AND habaki IN ($habaki_str)";
            }
        }
        if($mawaribuchi_list != ""){
            if ($mawaribuchi_list[0] == "NoName"){
                $condition .= " AND mawaribuchi = ''";
            }else{
                $mawaribuchi_str = "'" . implode ( "', '", $mawaribuchi_list ) . "'";
                $condition .= " AND mawaribuchi IN ($mawaribuchi_str)";
            }
        }


        $room_query = "(SELECT 
                        room.*
                    FROM
                        ((SELECT tmp.*
                        FROM
                        (SELECT 
                            *
                            FROM tb_forge_room_updated as t1
                            WHERE t1.version_number <= ".$version_number." AND t1.item_id = $item_id ".$condition."
                            ORDER BY t1.version_number DESC)as tmp
                        GROUP BY tmp.element_id)
                        UNION ALL 
                        (SELECT *
                          FROM tb_forge_room
                          WHERE version_number <= ".$version_number." AND item_id = $item_id ".$condition.")) as room
                    WHERE room.element_id NOT IN (SELECT element_id from tb_forge_room_deleted WHERE version_number <= $version_number AND item_id = $item_id)
                    GROUP BY room.element_id
                    ORDER BY room.level)";
       /* $room_query = "(SELECT 
                  MAX(rm.id)as id,
                  MAX(rm.room_name)as room_name,
                  MAX(rm.element_id)as element_id,
                  MAX(rm.level)as level,
                  MAX(rm.shiage_tenjo)as shiage_tenjo,
                  MAX(rm.tenjo_shitaji) as tenjo_shitaji,
                  MAX(rm.mawaribuchi) as mawaribuchi,
                  MAX(rm.shiage_kabe) as shiage_kabe,
                  MAX(rm.kabe_shitaji) as kabe_shitaji,
                  MAX(rm.habaki) as habaki,
                  MAX(rm.shiage_yuka) as shiage_yuka,
                  MAX(rm.yuka_shitaji) as yuka_shitaji,
                  MAX(rm.shucho) as shucho,
                  MAX(rm.menseki_kakikomi) as menseki_kakikomi,
                  MAX(rm.santei_takasa) as santei_takasa,
                  MAX(rm.heya_takasa) as heya_takasa,
                  MAX(rm.menseki) as menseki,
                  MAX(rm.workset) as workset,
                  $version_number as version_number
              FROM
                  ((SELECT *
                  FROM tb_forge_room_updated as t1
                  WHERE t1.version_number <= $version_number AND t1.item_id = $item_id ".$condition."
    
                  GROUP BY t1.element_id)
                  UNION ALL 
                  (SELECT *
                    FROM tb_forge_room
                    WHERE version_number <= $version_number AND item_id = $item_id ".$condition.")) as rm
              GROUP BY rm.element_id
              ORDER BY rm.level)";*/

        $data = DB::select($room_query);     
        return json_decode(json_encode($data),true);
    }
    
    
    public function GetTekkinData($item_id){
        $query = "SELECT * FROM tb_forge_column_tekkin WHERE item_id = $item_id";
        $column_tekkin = DB::select($query);     
        $column_tekkin_data =  json_decode(json_encode($column_tekkin),true);
        
        $query = "SELECT * FROM tb_forge_beam_tekkin WHERE item_id = $item_id";
        $beam_tekkin = DB::select($query);     
        $beam_tekkin_data =  json_decode(json_encode($beam_tekkin),true);
        
        $query = "SELECT * FROM tb_forge_foundation_tekkin WHERE item_id = $item_id";
        $foundation_tekkin = DB::select($query);     
        $foundation_tekkin_data =  json_decode(json_encode($foundation_tekkin),true);
        
        return array("column_tekkin_data"=>$column_tekkin_data,"beam_tekkin_data"=>$beam_tekkin_data,"foundation_tekkin_data"=>$foundation_tekkin_data);

    }
    
    public function GetTekkinExcelData($item_id){
        $query =   "SELECT element_id,W,D,volume,level,
                    start_diameter,start_X_firstRowCount,start_X_secondRowCount,start_Y_firstRowCount,start_Y_secondRowCount,start_rib_diameter,start_rib_pitch,
                    end_diameter,end_X_firstRowCount,end_X_secondRowCount,end_Y_firstRowCount,end_Y_secondRowCount,end_rib_diameter,end_rib_pitch
                    FROM tb_forge_column_tekkin WHERE item_id = $item_id";
        $column_tekkin = DB::select($query);     
        $column_tekkin_data =  json_decode(json_encode($column_tekkin),true);

        $query =   "SELECT element_id,B,H,kattocho,level,
                    start_upper_diameter,start_upper_firstRowCount,start_upper_secondRowCount,
                    start_lower_diameter,start_lower_firstRowCount,start_lower_secondRowcount,
                    start_rib_diameter,start_rib_count,start_rib_pitch,
                    center_upper_diameter,center_upper_firstRowCount,center_upper_secondRowCount,
                    center_lower_diameter,center_lower_firstRowCount,center_lower_secondRowcount,
                    center_rib_diameter,center_rib_count,center_rib_pitch,
                    end_upper_diameter,end_upper_firstRowCount,end_upper_secondRowCount,
                    end_lower_diameter,end_lower_firstRowCount,end_lower_secondRowcount,
                    end_rib_diameter,end_rib_count,end_rib_pitch
                    FROM tb_forge_beam_tekkin WHERE item_id = $item_id";
        $beam_tekkin = DB::select($query);     
        $beam_tekkin_data =  json_decode(json_encode($beam_tekkin),true);

        $query =   "SELECT element_id,D,H,W,level
                    upper_X_diameter,upper_X_count,upper_Y_diameter,upper_Y_count,
                    lower_X_diameter,lower_X_count,lower_Y_diameter,lower_Y_count
                    FROM tb_forge_foundation_tekkin WHERE item_id = $item_id";
        $foundation_tekkin = DB::select($query);     
        $foundation_tekkin_data =  json_decode(json_encode($foundation_tekkin),true);
        
        return array("column_tekkin_data"=>$column_tekkin_data,"beam_tekkin_data"=>$beam_tekkin_data,"foundation_tekkin_data"=>$foundation_tekkin_data);

    }
    
    public function GetKoujiProjects(){
        $query = "SELECT  result.*, (select (forge_version_id) from tb_forge_version where id = result.lated_id) as version_urn 
                    FROM 
                    (SELECT temp.project_id as id,(temp.name) as name,(tv.id) as lated_id,(tv.version_number)as version FROM 
                        (SELECT ti.id as item_id,tp.name,tp.id as project_id
                            FROM tb_project tp
                            LEFT JOIN tb_forge_item  ti ON ti.project_id = tp.id
                            WHERE tp.auto_save_properties = 1
                            GROUP BY tp.id) 
                            as temp
                        LEFT JOIN tb_forge_version tv ON tv.item_id = temp.item_id
                        ORDER BY temp.name,tv.version_number DESC)
                    as result
                    GROUP BY result.name
                    ORDER BY  result.version DESC";// SELECT * FROM tb_project WHERE auto_save_properties = 1 ORDER BY name ASC
        $data = DB::select($query);     
        $result =  json_decode(json_encode($data),true);
        return $result;
    }
    
    public function GetKoujiProjectById($id){
        $query = "SELECT summary.*,bimaction.* FROM tb_work_summary as summary
                    LEFT JOIN tb_bimactionplan as bimaction ON bimaction.project_name = summary.name
                    WHERE summary.name = (SELECT name FROM tb_project WHERE id = $id)";
        $data = DB::select($query);     
        $result =  json_decode(json_encode($data),true);
        return $result;
    }
    
    public function GetImplementationProjectById($project_name){
        $query = "SELECT * FROM tb_bimactionplan WHERE project_name = '$project_name'";
        $data = DB::select($query);     
        $result =  json_decode(json_encode($data),true);
        return $result;
    }
    
    function GetDoorWindowData($item_id,$version_number,$category_list,$level_list,$workset_list,$typename_list,$typepanel_list){
        $windowCondition = "";
        $doorCondition = "";
        $doorResult="";
        $windowResult="";
        
        if($level_list != ""){
            if ($level_list[0] == "NoName"){
                $windowCondition .= " AND level = ''";
                $doorCondition .= " AND level = ''";
            }else{
                $level_str = "'" . implode ( "', '", $level_list ) . "'";
                $windowCondition .= " AND level IN ($level_str)";
                $doorCondition .= " AND level IN ($level_str)";
            }
        }
        
        if($workset_list != ""){
            if ($workset_list[0] == "NoName"){
                $windowCondition .= " AND workset = ''";
                $doorCondition .= " AND workset = ''";
            }else{
                $workset_str = "'" . implode ( "', '", $workset_list ) . "'";
                $windowCondition .= " AND workset IN ($workset_str)";
                $doorCondition .= " AND workset IN ($workset_str)";
            }
        }
       
        if($typename_list != ""){
            if ($typename_list[0] == "NoName"){
                $windowCondition .= " AND type_name = ''";
                $doorCondition .= " AND type_name = ''";
            }else{
                $typename_str = "'" . implode ( "', '", $typename_list ) . "'";
                $windowCondition .= " AND type_name IN ($typename_str)";
                $doorCondition .= " AND type_name IN ($typename_str)";
            }
        }
        
        if($typepanel_list != ""){
            if ($typepanel_list[0] == "NoName"){
                $windowCondition .= " AND type_name = ''";
                $doorCondition .= " AND type_name = ''";
            }else{
                $typepanel_str = "'" . implode ( "', '", $typepanel_list ) . "'";
                $windowCondition .= " AND type_window_panel IN ($typepanel_str)";
                $doorCondition .= " AND type_door_panel IN ($typepanel_str)";
            }
        }
        if($category_list == "" || (in_array('door', $category_list))){
            $doorQuery = "(SELECT 
                        *
                    FROM
                        ((SELECT tmp.*
                        FROM
                        (SELECT 
                            *
                            FROM tb_forge_door_updated as t1
                            WHERE t1.version_number <= ".$version_number." AND t1.item_id = $item_id ".$doorCondition."
                            ORDER BY t1.version_number DESC)as tmp
                        GROUP BY tmp.element_id)
                        UNION ALL 
                        (SELECT *
                          FROM tb_forge_door
                          WHERE version_number <= ".$version_number." AND item_id = $item_id ".$doorCondition.")) as door
                    WHERE door.element_id NOT IN (SELECT element_id from tb_forge_door_deleted WHERE version_number <= $version_number AND item_id = $item_id)
                    GROUP BY door.element_id
                    ORDER BY door.level)";
            $data = DB::select($doorQuery);     
            $doorResult =  json_decode(json_encode($data),true);
        }
        
        if($category_list == "" || (in_array('window', $category_list))){
             $windowQuery = "(SELECT 
                       *
                    FROM
                        ((SELECT tmp.*
                        FROM
                        (SELECT 
                           *
                            FROM tb_forge_window_updated as t1
                            WHERE t1.version_number <= ".$version_number." AND t1.item_id = $item_id ".$windowCondition."
                            ORDER BY t1.version_number DESC)as tmp
                        GROUP BY tmp.element_id)
                        UNION ALL 
                        (SELECT *
                          FROM tb_forge_window
                          WHERE version_number <= ".$version_number." AND item_id = $item_id ".$windowCondition.")) as window
                    WHERE window.element_id NOT IN (SELECT element_id from tb_forge_window_deleted WHERE version_number <= $version_number AND item_id = $item_id)
                    GROUP BY window.element_id
                    ORDER BY window.level)";
            $data = DB::select($windowQuery);     
            $windowResult =  json_decode(json_encode($data),true);
        }
        
        
        return array("door"=>$doorResult,"window"=>$windowResult);
    }
    
}
