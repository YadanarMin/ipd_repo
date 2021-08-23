<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class AutoSaveForgeProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forge:save_properties';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'auto save forge properties';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->GetForgeProperties();
         //DB::table('tb_project')->delete();
    }

    function GetForgeProperties(){
        $conf = new \Autodesk\Auth\Configuration();//escape from current name space by using '/'
        $conf->getDefaultConfiguration()
        ->setClientId('1JdTGvw0dhm50GbMqfnocubhm5D70P1X')
        ->setClientSecret('eMqGzyufSlyBiChD');//Fmori of client_id and secret_key

        
        try {
           
            //get version urns from database
            $version_db_info = $this->GetAutoSaveProjectUrns();
            
        /*echo"<pre>";
        print_r($version_db_info);
        echo"</pre>";return;*/
            foreach($version_db_info as $version){
                $project_name = $version["project_name"];
                //if(strpos($project_name,'関西国際空港T1リノベーション工事') === true || strpos($project_name,'（仮称）資生堂九州工場新築工事') === true)continue;

                //if(strpos($project_name,'日本生命熊本ビル新築工事') === false)continue;
                print_r($project_name);
                
                $authObj = new \Autodesk\Auth\OAuth2\TwoLeggedAuth();
                $scopes = array("code:all","data:read","data:write","bucket:read");
                $authObj->setScopes($scopes);
        
                $authObj->fetchToken();
                $access_token = $authObj->getAccessToken();
                $authObj->setAccessToken($access_token);
                $derivInst = new \Autodesk\Forge\Client\Api\DerivativesApi($authObj);

                $urn = $version["forge_version_id"];//forge_id
                $version_id = $version["id"];//database_id
                $item_id = $version["item_id"];//db_project id
                $item_name = $version["item_name"];
                $version_number = $version["version_number"];               
                if($version_number == 1)continue; 
                //if($item_id != 0)continue;
                $check_exist = $this->GetAlreadySavedFlag($item_id,$version_number);

                if($check_exist[0]["already_saved"] == 1) continue;
                //print_r($project_name);return;
                $metaDataObj = $derivInst->getMetadata(base64_encode($urn),null);
                //print_r($metaDataObj);
                if(empty($metaDataObj["data"]["metadata"]))continue;
                    
                $metaData = $metaDataObj["data"]["metadata"];
                unset($metaDataObj["data"]);
                clearstatcache();
                gc_collect_cycles();
                foreach($metaData as $mData){
                    $category_list = array();
                    $viewName = $mData["name"];
                    if(strpos($viewName,'新しい建設') === false)continue;
                    //&& strpos($viewName,'基礎') == false 
                    /*if(strpos($viewName,'新しい建設') === false && strpos($viewName,'既存') === false 
                        && strpos($viewName,'鉄骨') === false && strpos($viewName,'基礎') == false 
                        && strpos($viewName,'足場') === false && strpos($viewName,'仮設') === false
                        && strpos($viewName,'仮囲い') === false )continue;*/

                        //echo $viewName;
                        //echo $version_number;//continue;
                    $guid = $mData["guid"];
                    $viewTree = $derivInst->getModelviewMetadata(base64_encode($urn),$guid,null);
                    if(!isset($viewTree['data']['objects']))continue;
                    $hirechyData = $viewTree['data']['objects'];
                    unset($viewTree);
                    clearstatcache();
                    gc_collect_cycles();
                    foreach($hirechyData as $vData){                    
                        $categoris = $vData['objects'];
                        foreach($categoris as $category){
                            $type_ids = array();
                            $category_name = $category['name'];
                             
                            if($category_name == "構造柱" || $category_name == "構造フレーム" || $category_name == "床" 
                            || $category_name == "壁" || $category_name == "構造基礎" ||  $category_name == "窓" 
                            ||$category_name == "ドア" || $category_name == "部屋"){   //  
                                //print_r($category_name);          
                                $materials = $category["objects"];                              
                                foreach($materials as $material){
                                    if($category_name == "部屋"){
                                        if(!isset($material['objectid']) || $material['objectid'] == "undefined" || $material['objectid'] == "")continue; 
                                        $typeID = $material['objectid'];
                                        array_push($type_ids,$typeID);   
                                    }else{
                                        if(!isset($material['objects']) || $material['objects'] == "undefined" || $material['objects'] == "")continue; 
                                        $types = $material['objects'];                                  
                                        foreach($types as $type){ 
                                            $type_pro = $type['objects'];
                                            foreach($type_pro as $property) {
                                                $typeID = $property['objectid'];
                                                array_push($type_ids,$typeID);   
                                            }                                                                                                                                                                                         
                                      }
                                    }
                                }
                              //break; 
                            }
                         
                            if(sizeof($type_ids) > 0){
                                $category_list[$category_name] = array_unique($type_ids);
                            }
                            
                           
                        }                            
                    }
                    //break;
                    if(sizeof($category_list) > 0){
                        $this->PrepareProperties($category_list,$derivInst,$urn,$guid,$viewName,$item_id,$version_number,$version_id);
                    }
                    unset($hirechyData);
                    //save kouji project info
                    //$this->SaveProjectInfomation($derivInst,$urn,$guid,$item_name,$version_number);
                    
                    //save forge project info
                    //$this->SaveForgeProjectInfomation($derivInst,$urn,$guid,$item_name);
                }
                
               
                unset($metaData);
                clearstatcache();
                gc_collect_cycles();
                //update tb_forge_ver already_saved flag to 1 
                //$this->UpdateAlreadySavedFlag($item_id,$version_number);

                
            }           
            
            
        } catch (Exception $e) {
            //echo 'Exception when calling forge library function : ', $e->getMessage(), PHP_EOL;
        }
    }



    function PrepareProperties($category_list,$derivInst,$urn,$guid,$viewName,$item_id,$version_number,$version_id){
        try{
            $allProperties = $derivInst->getModelviewPropertiesByZip(base64_encode($urn),$guid,"gzip");
            $property = json_decode($allProperties,true);//convert json string to object array

            $allProperties = isset($property['data']['collection']) ?$property['data']['collection'] :"";//re-use varibale for memory up within process

            unset($property['data']);
            //unset($result);
            clearstatcache();
            gc_collect_cycles();
            $column_properties = array();
            foreach($category_list as $name=>$type_id_list){
                $category_name = $name;
                $save_list = array();
                $tekkin_list = array();

                foreach($type_id_list as $type_id){
                    
                    foreach($allProperties as $key=>$property){
                        if($property['objectid'] != $type_id || !isset($property["properties"]))  continue;
                        $element_name = $property['name'];
                        if($category_name == "窓" || $category_name == "ドア"){
                            
                            if(!isset($property["properties"]["寸法"]) || !isset($property["properties"]["拘束"]) || !isset($property["properties"]["識別情報"]) || !isset($property["properties"]["マテリアル / 仕上"]) 
                                || !isset($property["properties"]["一般"]) || !isset($property["properties"]["文字"])  || !isset($property["properties"]["防火"]) || !isset($property["properties"]["グラフィックス"]))continue;
                            $saveData = $this->FilterWindowAndDoorProperty($property["properties"],$element_name,$category_name,$viewName);
                            array_push($save_list,$saveData); 
                        }else if($category_name == "部屋" ){
 
                            if(!isset($property['properties']["寸法"]) || !isset($property['properties']["識別情報"]) || !isset($property['properties']["拘束"]))continue;
                            $room = $this->FilterRoomProperties($property['properties'], $element_name);
                            array_push($save_list,$room);
                        }else{

                            if(!isset($property["properties"]["寸法"]) || !isset($property["properties"]["拘束"]) || !isset($property["properties"]["識別情報"])|| !isset($property["properties"]["マテリアル / 仕上"]))continue;
                            $saveData = $this->FilterProperty($property["properties"],$element_name,$category_name,$viewName);
                            array_push($save_list,$saveData);   
                            if($category_name == "構造柱" || $category_name == "構造フレーム" || $category_name == "構造基礎"){
                                if(isset($property["properties"]["その他"])){
                                    $kattocho = "";
                                    if(isset($property["properties"]["構造"])){
                                        $tempData = $property["properties"]["構造"];
                                        $kouzo = json_decode(json_encode($tempData),true);
                                        $kattocho = isset($kouzo["カット長"]) ? $kouzo["カット長"] : "";
                                        //print_r($kattocho);exit;
                                    }
                                    $tekkinData = $this->FilterTekkinProperty($property["properties"]["その他"],$element_name,$category_name,$property["properties"]["寸法"],
                                                                              $property["properties"]["拘束"],$kattocho,$viewName,$type_id);
                                    array_push($tekkin_list,$tekkinData);
                                }
                                   
                            }
                        }
                        
                        unset($allProperties[$key]['properties']);
                        //return;                         
                    }
                }

            
                if(sizeof($save_list) > 0){
                    switch($category_name){
                        case "構造柱" : $this->SaveColumn($save_list,$version_id,$item_id,$version_number);break;
                        case "構造フレーム" : $this->SaveBeam($save_list,$version_id,$item_id,$version_number);break;
                        case "床" : $this->SaveFloor($save_list,$version_id,$item_id,$version_number);break;
                        case "壁" : $this->SaveWall($save_list,$version_id,$item_id,$version_number);break;
                        case "構造基礎" : $this->SaveFoundation($save_list,$version_id,$item_id,$version_number);break;
                        case "窓" : $this->SaveWindow($save_list,$version_id,$item_id,$version_number);break;
                        case "ドア" : $this->SaveDoor($save_list,$version_id,$item_id,$version_number);break;
                        case "部屋" : $this->SaveRoomProperties($version_number,$version_id,$item_id,$save_list);break;
                    }
                    //update tb_forge_ver already_saved flag to 1 
                    $this->UpdateAlreadySavedFlag($item_id,$version_number);
                    
                }

                if(sizeof($tekkin_list) > 0){
                    switch($category_name){
                        case "構造柱" : $this->SaveColumnTekkin($tekkin_list,$version_id,$item_id,$version_number);break;
                        case "構造フレーム" : $this->SaveBeamTekkin($tekkin_list,$version_id,$item_id,$version_number);break;                               
                        case "構造基礎" : $this->SaveFoundationTekkin($tekkin_list,$version_id,$item_id,$version_number);break;
                    }
                }
            } 
            
            $allProperties = "";//clear allProperties variable for memory up
            //unset($allProperties);
            clearstatcache();
            gc_collect_cycles(); 
        }catch(Exception $ex){
            //$ex->getMessage();
        }    
        
    }

    /**
     * special character escaping single code fun() 
     * given parameter[string]
     * return escape string
     * to save special char to database
     */
    function escape_string($string){
        $escape_string = str_replace("'", "\'",$string);
        return $escape_string;
    }

    /**l
     * Update already_saved to 1 
     * for skip next time save
     */
    public function UpdateAlreadySavedFlag($item_id,$version_number)
    {
        $query = "UPDATE  tb_forge_version SET already_saved = 1 WHERE item_id = $item_id AND version_number = $version_number";
        DB::update($query);
    }

    function GetAlreadySavedFlag($item_id,$version_number){
        $query = "SELECT already_saved FROM tb_forge_version WHERE item_id = $item_id AND version_number = $version_number LIMIT 1";
        $result = DB::select($query);
        return json_decode(json_encode($result),true);//change array object to array
    }

    public function FilterProperty($property,$element_name,$category_name,$viewName)
    {
        $material = (object)$property['マテリアル / 仕上'];//convert array to object
        $identification_info = (object)$property["識別情報"];
        $kosoku = (object)$property['拘束'];
        $sunPo = (object)$property['寸法'];
       
        $typeName = isset($identification_info->タイプ名) ? $identification_info->タイプ名 : "";
        $workset = isset($identification_info->ワークセット) ? $identification_info->ワークセット :"";
        $kouzouMaterial = isset($material->構造マテリアル) ? $material->構造マテリアル: $material->フーチング_マテリアル;       
        $level = "";
        if(isset($kosoku->参照レベル)|| isset($kosoku->基準レベル)){
            $level = isset($kosoku->参照レベル) ? $kosoku->参照レベル: $kosoku->基準レベル; 
        } 
        $volume = 0;
        if($category_name != "構造基礎"){
            if(isset($sunPo->容積))
             $volume =  preg_replace("/[^0-9.]/", "",$sunPo->容積);//get float from string 
        }else{
            $width=0;$length=0;$depth=0;
            if(isset($sunPo->幅)|| isset($sunPo->W))
                $width = isset($sunPo->幅) ? preg_replace("/[^0-9.]/", "",$sunPo->幅) : preg_replace("/[^0-9.]/", "",$sunPo->W);
            if(isset($sunPo->長さ)|| isset($sunPo->H))
                $length = isset($sunPo->長さ) ? preg_replace("/[^0-9.]/", "",$sunPo->長さ) : preg_replace("/[^0-9.]/", "",$sunPo->H);
            if(isset($sunPo->厚さ)|| isset($sunPo->D))
                $depth = isset($sunPo->厚さ) ? preg_replace("/[^0-9.]/", "",$sunPo->厚さ) :  preg_replace("/[^0-9.]/", "",$sunPo->D);
            $volume = ($width/1000) * ($length/1000) * ($depth/1000);
        }
        $tempArr= explode(" [", $element_name);
        $family_name = $tempArr[0];
        $element_id = preg_replace("/[^0-9.]/", "", $tempArr[1]);
        
        unset($property);
        clearstatcache();
        gc_collect_cycles(); 
        return array("type_name"=>$typeName,"material"=>$kouzouMaterial,"level"=>$level,"volume"=>$volume,"workset"=>$workset,"family_name" =>$family_name,"element_id"=>$element_id,"phase"=>$viewName,"element_db_id"=>$element_name);
        
    }

    public function FilterWindowAndDoorProperty($property,$element_name,$category_name,$viewName)
    {
        $material = json_decode(json_encode($property['マテリアル / 仕上']),true);
        $iden_info = json_decode(json_encode($property['識別情報']),true);
        $sunpou = json_decode(json_encode($property['寸法']),true);
        $kousoku = json_decode(json_encode($property['拘束']),true);
        $general = json_decode(json_encode($property['一般']),true);
        $bouka = json_decode(json_encode($property['防火']),true);
        $moji = json_decode(json_encode($property['文字']),true);
        $graphic = json_decode(json_encode($property['グラフィックス']),true);

        //拘束
        $level = isset($kousoku["基準レベル"]) ? $kousoku["基準レベル"] : "";
        $lower_frame_height = isset($kousoku["下枠高さ"]) ? $kousoku["下枠高さ"] : "";
        
        //構築just for door
        $kinou = "";
        if($category_name == "ドア"){
            $kouchiku = json_decode(json_encode($property['構築']),true);
            $kinou = isset($kouchiku["機能"]) ? $kouchiku["機能"] : "";
        }
        
        
        //グラフィックス
        $type_door_panel = isset($graphic["タイプ_ドア_パネル_"]) ? $graphic["タイプ_ドア_パネル_"] : "";
        
        $type_generl_gFU = isset($graphic["タイプ_一般_gFU_"]) ? $graphic["タイプ_一般_gFU_"] : "";
        $type_window_panel = isset($graphic["タイプ_窓_パネル_"]) ? $graphic["タイプ_窓_パネル_"] : "";
        $type_window_drainer = isset($graphic["タイプ_窓_水切り"]) ? $graphic["タイプ_窓_水切り"] : "";
        
        //文字
        $seinou_kimitsu = isset($moji["性能_気密_"]) ? $moji["性能_気密_"] : "";
        $seinou_shaon = isset($moji["性能_遮音_"]) ? $moji["性能_遮音_"] : "";
        $moji_undercut = isset($moji["文字_アンダーカット_"]) ? $moji["文字_アンダーカット_"] : "";
        $moji_garasu_size = isset($moji["文字_ガラスサイズ_"]) ? $moji["文字_ガラスサイズ_"] : "";
        $moji_garari_size = isset($moji["文字_ガラリサイズ_"]) ? $moji["文字_ガラリサイズ_"] : "";
        $moji_bikou = isset($moji["文字_備考_"]) ? $moji["文字_備考_"] : "";
        $moji_tobira_atsu = isset($moji["文字_扉厚_"]) ? $moji["文字_扉厚_"] : "";
        $moji_hontai_kikou = isset($moji["文字_本体機構_"]) ? $moji["文字_本体機構_"] : "";
        
        $shiyou_gakubuchi_zaishitsu = isset($moji["仕様_額縁材質_"]) ? $moji["仕様_額縁材質_"] : "";
        $seinou_taifuu_atsu = isset($moji["性能_耐風圧_"]) ? $moji["性能_耐風圧_"] : "";
        $seinou_suimitsu = isset($moji["性能_水密_"]) ? $moji["性能_水密_"] : "";
        
         //マレリアル
        $wakuzai_moji = isset($material["枠材(文字)"]) ?  $material["枠材(文字)"] : "";
        $shiage_moji = isset($material["仕上(文字)"]) ?  $material["仕上(文字)"] : "";
        $shiyou_garasu_shiyou = isset($material["仕様_ガラス仕様_"]) ?  $material["仕様_ガラス仕様_"] : "";
        $shiyou_garari_shiyou = isset($material["仕様_ガラリ仕様_"]) ?  $material["仕様_ガラリ仕様_"] : "";
        $shiyou_hontai_shiage = isset($material["仕様_本体仕上_"]) ?  $material["仕様_本体仕上_"] : "";
        $shiyou_hontai_zaishitsu = isset($material["仕様_本体材質_"]) ?  $material["仕様_本体材質_"] : "";
        $shiyou_waku_shiage = isset($material["仕様_枠仕上_"]) ?  $material["仕様_枠仕上_"] : "";
        $shiyou_waku_zaishitsu = isset($material["仕様_枠材質_"]) ?  $material["仕様_枠材質_"] : "";
        
        $shiyou_gakubuchi_shiage = isset($material["仕様_額縁仕上_"]) ?  $material["仕様_額縁仕上_"] : "";
        
         //寸法
        $H_door_part = isset($sunpou["H_ドア部_"]) ? $sunpou["H_ドア部_"] : "";
        $offset_inside = isset($sunpou["オフセット_内_"]) ? $sunpou["オフセット_内_"] : "";
        $offset_outside = isset($sunpou["オフセット_外_"]) ? $sunpou["オフセット_外_"] : "";
        $full_width = isset($sunpou["全幅"]) ? $sunpou["全幅"] : "";
        $full_height = isset($sunpou["全高"]) ? $sunpou["全高"] : "";
        $thickness = isset($sunpou["厚さ"]) ? $sunpou["厚さ"] : "";
        $koteichi_shitawaku_mikomi = isset($sunpou["固定値_下枠見込み"]) ? $sunpou["固定値_下枠見込み"] : "";
        $koteichi_touatari_mikomi = isset($sunpou["固定値_戸当り見込み"]) ? $sunpou["固定値_戸当り見込み"] : "";
        $koteichi_waku_mikomi = isset($sunpou["固定値_枠見込み"]) ? $sunpou["固定値_枠見込み"] : "";
        $width = isset($sunpou["幅"]) ? $sunpou["幅"] : "";
        $height = isset($sunpou["高さ"]) ? $sunpou["高さ"] : "";
        $dan_netsu_atsu = isset($sunpou["断熱厚"]) ? $sunpou["断熱厚"] : "";
        
        $H_mume = isset($sunpou["H_無目_"]) ? $sunpou["H_無目_"] : "";
        $H_madobu = isset($sunpou["H_窓部_"]) ? $sunpou["H_窓部_"] : "";
        $sasshinten_kara_tenjou = isset($sunpou["サッシ天から天井_"]) ? $sunpou["サッシ天から天井_"] : "";
        $chiri_gakubuchi = isset($sunpou["チリ_額縁"]) ? $sunpou["チリ_額縁"] : "";
        $naiheki_boido_enchou = isset($sunpou["内壁ボイド延長_上"]) ? $sunpou["内壁ボイド延長_上"] : "";
        $dakimikomi = isset($sunpou["抱き見込み_"]) ? $sunpou["抱き見込み_"] : "";
        $gakubuchi_mikomi = isset($sunpou["額縁見込み_"]) ? $sunpou["額縁見込み_"] : "";
        
         //識別情報
        $type_name = isset($iden_info["タイプ名"]) ? $iden_info["タイプ名"] : "";
        $comment = isset($iden_info["コメント"]) ? $iden_info["コメント"] : "";
        $workset = isset($iden_info["ワークセット"]) ? $iden_info["ワークセット"] : "";
        $price = isset($iden_info["価格"]) ? $iden_info["価格"] : "";
        $keijou_garari = isset($iden_info["形状_ガラリ_"]) ? $iden_info["形状_ガラリ_"] : "";
        $keijou_meshiawase = isset($iden_info["形状_召し合せ_"]) ? $iden_info["形状_召し合せ_"] : "";
        $keijou_sugata = isset($iden_info["形状_姿_"]) ? $iden_info["形状_姿_"] : "";
        $keijyou_waku = isset($iden_info["形状_枠_"]) ? $iden_info["形状_枠_"] : "";
        $keijou_kutsuzui = isset($iden_info["形状_沓摺り_"]) ? $iden_info["形状_沓摺り_"] : "";
        $fugou_bangou_omo = isset($iden_info["符号_番号_主_"]) ? $iden_info["符号_番号_主_"] : "";
        $fugou_bangou_fuku = isset($iden_info["符号_番号_副_"]) ? $iden_info["符号_番号_副_"] : "";
        $fugou_kigou = isset($iden_info["符号_記号_"]) ? $iden_info["符号_記号_"] : "";
        $fugou_denki = isset($iden_info["符号_電気_"]) ? $iden_info["符号_電気_"] : "";
        $taika_toukyuu = isset($iden_info["耐火等級"]) ? $iden_info["耐火等級"] : "";
        $seizoumoto = isset($iden_info["製造元"]) ? $iden_info["製造元"] : "";
        $setsumei = isset($iden_info["説明"]) ? $iden_info["説明"] : "";
        $kanamono_tokushu = isset($iden_info["金物_特殊金物_"]) ? $iden_info["金物_特殊金物_"] : "";
        $OmniClass_title = isset($iden_info["OmniClass タイトル"]) ? $iden_info["OmniClass タイトル"] : "";
        $OmniClass_number = isset($iden_info["OmniClass 番号"]) ? $iden_info["OmniClass 番号"] : "";
        
        $keijyou_mizukiri = isset($iden_info["形状_水切り_"]) ? $iden_info["形状_水切り_"] : "";
        //$manufacturer= isset($iden_info["製造元"]) ? $iden_info["製造元"] : ""; 
        
        //防火
        $raberu_jouji_kaihou = isset($bouka["ラベル_常時開放_"]) ? $bouka["ラベル_常時開放_"] : "";
        $raberu_nintei = isset($bouka["ラベル_認定_"]) ? $bouka["ラベル_認定_"] : "";
        $raberu_shaen = isset($bouka["ラベル_遮煙_"]) ? $bouka["ラベル_遮煙_"] : "";
        $raberu_bouka_seinou = isset($bouka["ラベル_防火性能_"]) ? $bouka["ラベル_防火性能_"] : "";
        $hou_jouji_kaihou = isset($bouka["法_常時開放_"]) ? $bouka["法_常時開放_"] : "";
        $hou_ninteihin = isset($bouka["法_認定品_"]) ? $bouka["法_認定品_"] : "";
        $hou_shaen = isset($bouka["法_遮煙_"]) ? $bouka["法_遮煙_"] : "";
        $hou_bouka_seinou = isset($bouka["法_防火性能_"]) ? $bouka["法_防火性能_"] : "";
        
        //一般
        $kanamono_handoru = isset($general["金物_ハンドル_"]) ? $general["金物_ハンドル_"] : "";
        $kanamono_shiji_kanamono = isset($general["金物_支持金物_"]) ? $general["金物_支持金物_"] : "";
        $kanamono_shimari_kanamono = isset($general["金物_締り金物_"]) ? $general["金物_締り金物_"] : "";
        
        $tempArr= explode(" [", $element_name);
        $family_name = $tempArr[0];
        $element_id = preg_replace("/[^0-9.]/", "", $tempArr[1]);
        
        unset($property);
        clearstatcache();
        gc_collect_cycles(); 
        if($category_name == "窓"){
        return array(
        "level"=>$level,  
        "lower_frame_height "=>$lower_frame_height,
        "type_general_gFU"=> $type_generl_gFU,
        "type_window_panel"=>$type_window_panel,
        "type_window_drainer"=>$type_window_drainer,
        "shiyou_gakubuchi"=>$shiyou_gakubuchi_zaishitsu,
        "seinou_kimitsu"=>$seinou_kimitsu,
        "seinou_suimitsu"=>$seinou_suimitsu,
        "seinou_taifuu_atsu"=>$seinou_taifuu_atsu,
        "seinou_shaon"=>$seinou_shaon,
        "moji_garari_saizu"=> $moji_garari_size,
        "moji_bikou"=>$moji_bikou,
        "moji_hontai_kikou"=>$moji_hontai_kikou,
        "shiyou_garasu_shiyou"=>$shiyou_garasu_shiyou,
        "shiyou_hontai_shiage"=>$shiyou_hontai_shiage,
        "shiyou_hontai_zaishitus"=>$shiyou_hontai_zaishitsu,
        "shiyou_gakubuchi_shiage"=>$shiyou_gakubuchi_shiage,
        "H_mume"=>$H_mume,
        "H_madobu"=>$H_madobu,
        "offset_inside"=>$offset_inside,
        "sasshinten_kara_tenjou"=> $sasshinten_kara_tenjou,
        "chiri_gakubuchi"=>  $chiri_gakubuchi,
        "full_width"=>$full_width,
        "full_height"=>$full_height,
        "naiheki_boido_enchou"=>  $naiheki_boido_enchou,
        "width"=>$width,
        "dakimikomi"=> $dakimikomi,
        "dan_netsu_atsu"=> $dan_netsu_atsu,
        "gakubuchi_mikomi"=>$gakubuchi_mikomi,
        "height"=>$height,
        "comment"=> $comment,
        "workset"=>  $workset,
        "OmniClass_title"=> $OmniClass_title,
        "OmniClass_number"=>$OmniClass_number,
        "price"=>$price,
        "keijou_garari"=> $keijou_garari,
        "keijou_mizukiri"=>$keijyou_mizukiri,
        "fugou_bangou_omo"=> $fugou_bangou_omo,
        "fugou_bangou_fuku"=>$fugou_bangou_fuku,
        "fugou_kigou"=>$fugou_kigou,
        "manufacturer"=> $seizoumoto,
        "description"=> $setsumei,
        "kanamono_tokushu"=>$kanamono_tokushu,
        "kanamono_shiji"=> $kanamono_shiji_kanamono,
        "kanamono_shimari"=>$kanamono_shimari_kanamono,
        "label_fire_protection"=>$raberu_bouka_seinou,
        "law_fire_protection"=>$hou_bouka_seinou,
        "type_name"=>$type_name,
        "element_id"=>$element_id,
        "element_db_id"=>$element_name);
    }else{
        
        return array(
        "level"=>$level,
        "lower_frame_height"=>$lower_frame_height,
        "kinou"=>$kinou,
        "type_door_panel"=>$type_door_panel ,
        "seinou_kimitsu"=>$seinou_kimitsu,
        "seinou_shaon"=>$seinou_shaon,
        "moji_undercut"=>$moji_undercut,
        "moji_garasu_size"=>$moji_garasu_size,
        "moji_garari_size"=>$moji_garari_size,
        "moji_bikou"=>$moji_bikou,
        "moji_tobira_atsu"=>$moji_tobira_atsu,
        "moji_hontai_kikou"=>$moji_hontai_kikou,
        "wakuzai_moji"=>$wakuzai_moji,
        "shiage_moji"=>$shiage_moji,
        "shiyou_garasu_shiyou"=>$shiyou_garasu_shiyou,
        "shiyou_garari_shiyou"=>$shiyou_garari_shiyou,
        "shiyou_hontai_shiage"=>$shiyou_hontai_shiage,
        "shiyou_hontai_zaishitsu"=>$shiyou_hontai_zaishitsu,
        "shiyou_waku_shiage"=>$shiyou_waku_shiage,
        "shiyou_waku_zaishitsu"=>$shiyou_waku_zaishitsu,
        "H_door_part"=>$H_door_part,
        "offset_inside"=>$offset_inside,
        "offset_outside"=>$offset_outside,
        "full_width"=>$full_width,
        "full_height"=>$full_height,
        "thickness"=>$thickness,
        "koteichi_shitawaku_mikomi"=>$koteichi_shitawaku_mikomi,
        "koteichi_touatari_mikomi"=>$koteichi_touatari_mikomi,
        "koteichi_waku_mikomi"=>$koteichi_waku_mikomi,
        "width"=>$width,
        "height"=>$height,
        "dan_netsu_atsu"=>$dan_netsu_atsu,
        "comment"=>$comment,
        "workset"=>$workset,
        "price"=>$price,
        "keijou_garari"=>$keijou_garari,
        "keijou_meshiawase"=>$keijou_meshiawase,
        "keijou_sugata"=>$keijou_sugata,
        "keijyou_waku"=>$keijyou_waku,
        "keijou_kutsuzui"=>$keijou_kutsuzui,
        "fugou_bangou_omo"=>$fugou_bangou_omo,
        "fugou_bangou_fuku"=>$fugou_bangou_fuku,
        "fugou_kigou"=>$fugou_kigou,
        "fugou_denki"=>$fugou_denki,
        "taika_toukyuu"=>$taika_toukyuu,
        "seizoumoto"=>$seizoumoto,
        "setsumei"=>$setsumei,
        "kanamono_tokushu"=>$kanamono_tokushu,
        "OmniClass_title"=>$OmniClass_title,
        "OmniClass_number"=>$OmniClass_number,
        "raberu_jouji_kaihou"=>$raberu_jouji_kaihou,
        "raberu_nintei"=>$raberu_nintei,
        "raberu_shaen"=>$raberu_shaen,
        "raberu_bouka_seinou"=>$raberu_bouka_seinou,
        "hou_jouji_kaihou"=>$hou_jouji_kaihou,
        "hou_ninteihin"=>$hou_ninteihin,
        "hou_shaen"=>$hou_shaen,
        "hou_bouka_seinou"=>$hou_bouka_seinou,
        "kanamono_handoru"=>$kanamono_handoru,
        "kanamono_shiji_kanamono"=>$kanamono_shiji_kanamono,
        "kanamono_shimari_kanamono"=>$kanamono_shimari_kanamono,
        "type_name"=>$type_name,
        "element_id"=>$element_id,
        "element_db_id"=>$element_name); 
    }
        
    }
    
    function FilterRoomProperties($properties, $element_name){

        $sunpo = json_decode(json_encode($properties["寸法"]), true);//change stdclass to array
        $shiage = json_decode(json_encode($properties["識別情報"]), true);
        $kosoku = json_decode(json_encode($properties ["拘束"]), true);

        $tempArr= explode(" [", $element_name);
        //$family_name = $tempArr[0];
        $element_id = preg_replace("/[^0-9.]/", "", $tempArr[1]);
 
        $roomname = isset($shiage["名前"]) ? $shiage["名前"] : "";
        $level = isset($kosoku["レベル"]) ? $kosoku["レベル"] : "";

        $shiage_tenjo = isset($shiage["仕上 天井"]) ? $shiage["仕上 天井"] : "";
        $tenjo_shitaji = isset($shiage["天井下地"]) ? $shiage["天井下地"] : "";
        $mawaribuchi = isset($shiage["廻縁"]) ? $shiage["廻縁"] : "";
        $shiage_kabe = isset($shiage["仕上 壁"]) ? $shiage["仕上 壁"] : "";
        $kabe_shitaji = isset($shiage["壁下地"]) ? $shiage["壁下地"] : "";
        $habaki = isset($shiage["幅木"]) ? $shiage["幅木"] : "";
        $shiage_yuka = isset($shiage["仕上 床"]) ? $shiage["仕上 床"] : "";
        $yuka_shitaji = isset($shiage["床下地"]) ? $shiage["床下地"] : "";

        $shucho =  (isset($sunpo["周長"]) && $sunpo["周長"] != "") ?  preg_replace("/[^0-9.]/", "",$sunpo["周長"]) : 0;
        $menseki_kakikomi = (isset($sunpo["室面積（書き込み）_ob"]) && $sunpo["室面積（書き込み）_ob"] != "") ? preg_replace("/[^0-9.]/", "",$sunpo["室面積（書き込み）_ob"]) : 0;
        $santei_takasa = (isset($sunpo["算定高さ"]) && $sunpo["算定高さ"] != "")? preg_replace("/[^0-9.]/", "",$sunpo["算定高さ"] ): 0;
        $heya_takasa = (isset($sunpo["部屋高さ(レベル指定)"]) && $sunpo["部屋高さ(レベル指定)"] != "") ? preg_replace("/[^0-9.]/", "",$sunpo["部屋高さ(レベル指定)"]) : 0;                 
        $menseki = (isset($sunpo["面積"]) && $sunpo["面積"] != "") ? preg_replace("/[^0-9.]/", "",$sunpo["面積"]) : 0;
        $workset = (isset($shiage["ワークセット"])) ? $shiage["ワークセット"] : "";
        
        return array("forge_room_id"=>$element_id,"element_db_id"=>$element_name,"room_name"=>$roomname,"level"=>$level,"shiage_tenjo"=>$shiage_tenjo,"tenjo_shitaji"=>$tenjo_shitaji,
                    "mawaribuchi"=>$mawaribuchi,"shiage_kabe"=>$shiage_kabe,"kabe_shitaji"=>$kabe_shitaji,"habaki"=>$habaki,"shiage_yuka"=>$shiage_yuka,"yuka_shitaji"=>$yuka_shitaji,
                    "shucho"=>$shucho,"menseki_kakikomi"=>$menseki_kakikomi,"santei_takasa"=>$santei_takasa,"heya_takasa"=>$heya_takasa,"menseki"=>$menseki,"workset"=>$workset);      

    }
    
    function FilterTekkinProperty($tekkinProperty,$element_name,$category_name,$sunpoProperty,$kosokuProperty,$kattocho,$viewName){
        $tekkin = json_decode(json_encode($tekkinProperty),true);
        $sunpo = json_decode(json_encode($sunpoProperty),true);
        $kosoku = json_decode(json_encode($kosokuProperty),true);
        $tempArr= explode(" ", $element_name);
        $family_name = $tempArr[0];
        $element_id = preg_replace("/[^0-9.]/", "", $tempArr[1]);
        if($category_name == "構造フレーム"){
            $B = isset($sunpo["B"]) ? $sunpo["B"] : "";
            $H = isset($sunpo["H"]) ? $sunpo["H"] : "";
            //$kattocho = isset($sunpo["カット長"]) ? $sunpo["カット長"] : "";
            $level = isset($kosoku["参照レベル"])? $kosoku["参照レベル"] : $kosoku["基準レベル"]; 

            //始端
            $start_upper_diameter = isset($tekkin["始端 上主筋 太径"]) ? $tekkin["始端 上主筋 太径"] : "";
            $start_upper_firstRowCount = isset($tekkin["始端 上主筋 1段筋太本数"]) ? $tekkin["始端 上主筋 1段筋太筋本数"] : "";
            $start_upper_secondRowCount = isset($tekkin["始端 上主筋 2段筋太筋本数"]) ? $tekkin["始端 上主筋 2段筋太筋本数"] : ""; 
            $start_lower_diameter = isset($tekkin["始端 下主筋 太径"]) ? $tekkin["始端 下主筋 太径"] : "";
            $start_lower_firstRowCount = isset($tekkin["始端 下主筋 1段筋太筋本数"]) ? $tekkin["始端 下主筋 1段筋太筋本数"] : ""; 
            $start_lower_secondRowCount = isset($tekkin["始端 下主筋 2段筋太筋本数"]) ? $tekkin["始端 下主筋 2段筋太筋本数"] : "";        
            $start_rib_diameter = isset($tekkin["始端 肋筋径"]) ? $tekkin["始端 肋筋径"]: "";  
            $start_rib_count = isset($tekkin["始端 肋筋本数"]) ? $tekkin["始端 肋筋本数"] :""; 
            $start_rib_pitch = isset($tekkin["始端 肋筋ピッチ"]) ? $tekkin["始端 肋筋ピッチ"] : ""; 

            //中央
            $center_upper_diameter = isset($tekkin["中央 上主筋 太径"]) ? $tekkin["中央 上主筋 太径"] : "";
            $center_upper_firstRowCount = isset($tekkin["中央 上主筋 1段筋太筋本数"]) ? $tekkin["中央 上主筋 1段筋太筋本数"] : "";
            $center_upper_secondRowCount = isset($tekkin["中央 上主筋 2段筋太筋本数"]) ? $tekkin["中央 上主筋 2段筋太筋本数"] : ""; 
            $center_lower_diameter = isset($tekkin["中央 下主筋 太径"]) ? $tekkin["中央 下主筋 太径"] : "";
            $center_lower_firstRowCount = isset($tekkin["中央 下主筋 1段筋太筋本数"]) ? $tekkin["中央 下主筋 1段筋太筋本数"] : ""; 
            $center_lower_secondRowCount = isset($tekkin["中央 下主筋 2段筋太筋本数"]) ? $tekkin["中央 下主筋 2段筋太筋本数"] : "";        
            $center_rib_diameter = isset($tekkin["中央 肋筋径"]) ? $tekkin["中央 肋筋径"]: "";  
            $center_rib_count = isset($tekkin["中央 肋筋本数"]) ? $tekkin["中央 肋筋本数"] :""; 
            $center_rib_pitch = isset($tekkin["中央 肋筋ピッチ"]) ? $tekkin["中央 肋筋ピッチ"] : ""; 

            //終端
            $end_upper_diameter = isset($tekkin["終端 上主筋 太径"]) ? $tekkin["終端 上主筋 太径"] : "";
            $end_upper_firstRowCount = isset($tekkin["終端 上主筋 1段筋太筋本数"]) ? $tekkin["終端 上主筋 1段筋太筋本数"] : "";
            $end_upper_secondRowCount = isset($tekkin["終端 上主筋 2段筋太筋本数"]) ? $tekkin["終端 上主筋 2段筋太筋本数"] : ""; 
            $end_lower_diameter = isset($tekkin["終端 下主筋 太径"]) ? $tekkin["終端 下主筋 太径"] : "";
            $end_lower_firstRowCount = isset($tekkin["終端 下主筋 1段筋太筋本数"]) ? $tekkin["終端 下主筋 1段筋太筋本数"] : ""; 
            $end_lower_secondRowCount = isset($tekkin["終端 下主筋 2段筋太筋本数"]) ? $tekkin["終端 下主筋 2段筋太筋本数"] : "";        
            $end_rib_diameter = isset($tekkin["終端 肋筋径"]) ? $tekkin["終端 肋筋径"]: "";  
            $end_rib_count = isset($tekkin["終端 肋筋本数"]) ? $tekkin["終端 肋筋本数"] :""; 
            $end_rib_pitch = isset($tekkin["終端 肋筋ピッチ"]) ? $tekkin["終端 肋筋ピッチ"] : ""; 

            return array("B"=>$B,"H"=>$H,"kattocho"=>$kattocho,"level"=>$level,
                        "start_upper_diameter"=>$start_upper_diameter,"start_upper_firstRowCount"=>$start_upper_firstRowCount,"start_upper_secondRowCount"=>$start_upper_secondRowCount,
                        "start_lower_diameter"=>$start_lower_diameter,"start_lower_firstRowCount"=>$start_lower_firstRowCount,"start_lower_secondRowCount"=>$start_lower_secondRowCount,
                        "start_rib_diameter"=>$start_rib_diameter,"start_rib_count"=>$start_rib_count,"start_rib_pitch"=>$start_rib_pitch,
                     
                        "end_upper_diameter"=>$end_upper_diameter,"end_upper_firstRowCount"=>$end_upper_firstRowCount,"end_upper_secondRowCount"=>$end_upper_secondRowCount,
                        "end_lower_diameter"=>$end_lower_diameter,"end_lower_firstRowCount"=>$end_lower_firstRowCount,"end_lower_secondRowCount"=>$end_lower_secondRowCount,
                        
                        "center_upper_diameter"=>$center_upper_diameter,"center_upper_firstRowCount"=>$center_upper_firstRowCount,"center_upper_secondRowCount"=>$center_upper_secondRowCount,
                        "center_lower_diameter"=>$center_lower_diameter,"center_lower_firstRowCount"=>$center_lower_firstRowCount,"center_lower_secondRowCount"=>$center_lower_secondRowCount,
                        "center_rib_diameter"=>$center_rib_diameter,"center_rib_count"=>$center_rib_count,"center_rib_pitch"=>$center_rib_pitch,
                       "end_rib_diameter"=>$end_rib_diameter,"end_rib_count"=>$end_rib_count,"end_rib_pitch"=>$end_rib_pitch,"element_id"=>$element_id,"phase"=>$viewName,"element_db_id"=>$element_name);

        }else if($category_name == "構造柱"){

            $W = isset($sunpo["W"]) ? $sunpo["W"] : "";
            $D = isset($sunpo["D"]) ? $sunpo["D"] : "";
            $volume = isset($sunpo["容積"]) ? $sunpo["容積"] : "";
            $level = isset($kosoku["参照レベル"])? $kosoku["参照レベル"] : $kosoku["基準レベル"]; 
           
             //柱頭
            $start_diameter = isset($tekkin["柱頭 主筋太径"]) ? $tekkin["柱頭 主筋太径"] : "";    
            $start_X_firstRowCount  = isset($tekkin["柱頭 主筋X方向1段太筋本数"]) ? $tekkin["柱頭 主筋X方向1段太筋本数"] : "" ; 
            $start_X_secondRowCount = isset($tekkin["柱頭 主筋X方向2段太筋本数"]) ? $tekkin["柱頭 主筋X方向2段太筋本数"] : ""; 
            $start_Y_firstRowCount = isset($tekkin["柱頭 筋Y方向1段太筋本数"]) ? $tekkin["柱頭 主筋Y方向1段太筋本数"] : "";
            $start_Y_secondRowCount = isset($tekkin["柱頭 主筋Y方向2段太筋本数"]) ? $tekkin["柱頭 主筋Y方向2段太筋本数"] : ""; 
            $start_rib_diameter = isset($tekkin["柱頭 帯筋径"]) ? $tekkin["柱頭 帯筋径"] : "";  
            $start_rib_pitch = isset($tekkin["柱頭 帯筋ピッチ"]) ? $tekkin["柱頭 帯筋ピッチ"] : "";  

             //柱脚 
            $end_diameter = isset($tekkin["柱脚 主筋太径"]) ? $tekkin["柱脚 主筋太径"] : "";   
            $end_X_firstRowCount  = isset($tekkin["柱脚 主筋X方向1段太筋本数"]) ? $tekkin["柱脚 主筋X方向1段太筋本数"] : "" ; 
            $end_X_secondRowCount = isset($tekkin["柱脚 主筋X方向2段太筋本数"]) ? $tekkin["柱脚 主筋X方向2段太筋本数"] : ""; 
            $end_Y_firstRowCount = isset($tekkin["柱脚 主筋Y方向1段太筋本数"]) ? $tekkin["柱脚 主筋Y方向1段太筋本数"] : "";
            $end_Y_secondRowCount = isset($tekkin["柱脚 主筋Y方向2段太筋本数"]) ? $tekkin["柱脚 主筋Y方向2段太筋本数"] : ""; 
            $end_rib_diameter = isset($tekkin["柱脚 帯筋径"]) ? $tekkin["柱脚 帯筋径"] : "";  
            $end_rib_pitch = isset($tekkin["柱脚 帯筋ピッチ"]) ? $tekkin["柱脚 帯筋ピッチ"] : "";  

            return array("W"=>$W,"D"=>$D,"volume"=>$volume,"level"=>$level,
                    "start_diameter"=>$start_diameter,"start_X_firstRowCount"=>$start_X_firstRowCount,"start_X_secondRowCount"=>$start_X_secondRowCount,
                    "start_Y_firstRowCount"=>$start_Y_firstRowCount,"start_Y_secondRowCount"=>$start_Y_secondRowCount,"start_rib_diameter"=>$start_rib_diameter,"start_rib_pitch"=>$start_rib_pitch,
                    "end_diameter"=>$end_diameter,"end_X_firstRowCount"=>$end_X_firstRowCount,"end_X_secondRowCount"=>$end_X_secondRowCount,
                    "end_Y_firstRowCount"=>$end_Y_firstRowCount,"end_Y_secondRowCount"=>$end_Y_secondRowCount,"end_rib_diameter"=>$end_rib_diameter,"end_rib_pitch"=>$end_rib_pitch,"element_id"=>$element_id,"phase"=>$viewName,"element_db_id"=>$element_name);

        }else if($category_name == "構造基礎"){
            $D = isset($sunpo["D"]) ? $sunpo["D"] : "";
            $H = isset($sunpo["H"]) ? $sunpo["H"] : "";
            $W = isset($sunpo["W"]) ? $sunpo["W"] : "";
            $level = "";
            if(isset($kosoku["参照レベル"]) || isset($kosoku["基準レベル"])){
                $level = isset($kosoku["参照レベル"])? $kosoku["参照レベル"] : $kosoku["基準レベル"]; 
            }
            

             //上端筋
            $upper_X_diameter = isset($tekkin["上端筋_X方向_鉄筋径"]) ? $tekkin["上端筋_X方向_鉄筋径"] : "";   
            $upper_X_count = isset($tekkin["上端筋_X方向_鉄筋本数"]) ? $tekkin["上端筋_X方向_鉄筋本数"] : ""; 
            $upper_Y_diameter = isset($tekkin["上端筋_Y方向_鉄筋径"]) ? $tekkin["上端筋_Y方向_鉄筋径"] : ""; 
            $upper_Y_count = isset($tekkin["上端筋_Y方向_鉄筋本数"]) ? $tekkin["上端筋_Y方向_鉄筋本数"] : "";

            //下端筋
            $lower_X_diameter = isset($tekkin["下端筋_X方向_鉄筋径"]) ? $tekkin["下端筋_X方向_鉄筋径"] : "";   
            $lower_X_count = isset($tekkin["下端筋_X方向_鉄筋本数"]) ? $tekkin["下端筋_X方向_鉄筋本数"] : ""; 
            $lower_Y_diameter = isset($tekkin["下端筋_Y方向_鉄筋径"]) ? $tekkin["下端筋_Y方向_鉄筋径"] : ""; 
            $lower_Y_count = isset($tekkin["下端筋_Y方向_鉄筋本数"]) ? $tekkin["下端筋_Y方向_鉄筋本数"] : "";

            return array("D"=>$D,"H"=>$H,"W"=>$W,"level"=>$level,
                        "upper_X_diameter"=>$upper_X_diameter,"upper_X_count"=>$upper_X_count,"upper_Y_diameter"=>$upper_Y_diameter,"upper_Y_count"=>$upper_Y_count,
                        "lower_X_diameter"=>$lower_X_diameter,"lower_X_count"=>$lower_X_count,"lower_Y_diameter"=>$lower_Y_diameter,"lower_Y_count"=>$lower_Y_count,"element_id"=>$element_id,"phase"=>$viewName,"element_db_id"=>$element_name);
        }

    }

    function GetAutoSaveProjectUrns(){
        $query = "SELECT * FROM (SELECT fp.name as project_name,fv.id,fv.forge_version_id,fv.version_number,fi.id as item_id,fi.name as item_name from tb_forge_version fv
                    LEFT JOIN  tb_forge_item  fi on fv.item_id = fi.id
                    LEFT JOIN tb_project fp on fi.project_id = fp.id
                    WHERE fp.auto_save_properties = 1 ORDER BY fi.id,fv.version_number DESC) as temp GROUP BY item_name ";       
        $result = DB::select($query);
        return json_decode(json_encode($result),true);//change array object to array
    }

    function GetAutoSaveProjectFolderUrns(){
        $query = "SELECT forge_project_id as project_urn FROM tb_project WHERE auto_save_properties = 1";       
        $result = DB::select($query);
        return json_decode(json_encode($result),true);//change array object to array
    }
    
    function SaveRoomProperties($version_number,$version_id,$item_id,$room_list){

        $save_element_id = array_column($room_list,"forge_room_id");
         
        $current_ver_ids = ($save_element_id == "") ? "'"."ALL_UNCHECK"."'" : "'" . implode ( "', '", $save_element_id ) . "'";//convert array to string with single code
        $select_deleted_query = "SELECT element_id FROM tb_forge_room WHERE item_id = $item_id AND version_number < $version_number
                                 AND element_id NOT IN($current_ver_ids)";

        $deleted_elements = DB::select($select_deleted_query);

         if(sizeof($deleted_elements) > 0){               
             foreach($deleted_elements as $deleted_id){
                
                 $ele_id = $deleted_id->element_id;
                 $insert_ids_query = "INSERT IGNORE INTO tb_forge_room_deleted (id,element_id,item_id,version_id,version_number)
                                     SELECT MAX(id) +1,$ele_id,$item_id,$version_id,$version_number FROM tb_forge_room_deleted";
                 DB::insert($insert_ids_query);
             }               
         }

        foreach($room_list as $room){
            $element_id =$room["forge_room_id"];
            $element_db_id = $this->escape_string($room["element_db_id"]);
            $room_name = $this->escape_string($room["room_name"]);
            $level = $this->escape_string($room["level"]);
            $shiage_tenjo = $this->escape_string($room["shiage_tenjo"]);
            $tenjo_shitaji = $this->escape_string($room["tenjo_shitaji"]);
            $mawaribuchi = $this->escape_string($room["mawaribuchi"]);
            $shiage_kabe = $this->escape_string($room["shiage_kabe"]);
            $kabe_shitaji = $this->escape_string($room["kabe_shitaji"]);
            $habaki = $this->escape_string($room["habaki"]);
            $shiage_yuka = $this->escape_string($room["shiage_yuka"]);
            $yuka_shitaji = $this->escape_string($room["yuka_shitaji"]);
            $shucho = $room["shucho"];
            $menseki_kakikomi = $room["menseki_kakikomi"];
            $santei_takasa = $room["santei_takasa"];
            $heya_takasa = $room["heya_takasa"];
            $menseki =$room["menseki"];
            $workset = $room["workset"];

            DB::insert("CALL room_insert_procedure($item_id,$element_id,'$element_db_id','$room_name','$level','$shiage_tenjo','$tenjo_shitaji','$mawaribuchi','$shiage_kabe','$kabe_shitaji','$habaki',
                                                    '$shiage_yuka','$yuka_shitaji',$shucho,$menseki_kakikomi,$santei_takasa,$heya_takasa,$menseki,'$workset',
                                                     $version_id,$version_number)");
            
        }
    }
    
    public function SaveWindow($save_list,$version_id,$item_id,$version_number){
        
       try{
           $save_element_id = array_column($save_list,"element_id");
         
           $current_ver_ids = ($save_element_id == "") ? "'"."ALL_UNCHECK"."'" : "'" . implode ( "', '", $save_element_id ) . "'";//convert array to string with single code
           $select_deleted_query = "SELECT element_id FROM tb_forge_window WHERE item_id = $item_id AND version_number < $version_number
                                    AND element_id NOT IN($current_ver_ids)";

           $deleted_elements = DB::select($select_deleted_query);

            if(sizeof($deleted_elements) > 0){               
                foreach($deleted_elements as $deleted_id){
                   
                    $ele_id = $deleted_id->element_id;
                    $insert_ids_query = "INSERT IGNORE INTO tb_forge_window_deleted (id,element_id,item_id,version_id,version_number)
                                        SELECT MAX(id) +1,$ele_id,$item_id,$version_id,$version_number FROM tb_forge_window_deleted";
                    DB::insert($insert_ids_query);
                }               
            }

            foreach($save_list as $data){
                 $level=$data["level"];  
                 $lower_frame_height=$data["lower_frame_height "];
                 $type_general_gFU=$data["type_general_gFU"];
                 $type_window_panel=$data["type_window_panel"];
                 $type_window_drainer=$data["type_window_drainer"];
                 $shiyou_gakubuchi=$data["shiyou_gakubuchi"];
                 $seinou_kimitsu=$data["seinou_kimitsu"];
                 $seinou_suimitsu=$data["seinou_suimitsu"];
                 $seinou_taifuu_atsu=$data["seinou_taifuu_atsu"];
                 $seinou_shaon=$data["seinou_shaon"];
                 $moji_garari_saizu=$data["moji_garari_saizu"];
                 $moji_bikou=$data["moji_bikou"];
                 $moji_hontai_kikou=$data["moji_hontai_kikou"];
                 $shiyou_garasu_shiyou=$data["shiyou_garasu_shiyou"];
                 $shiyou_hontai_shiage=$data["shiyou_hontai_shiage"];
                 $shiyou_hontai_zaishitus=$data["shiyou_hontai_zaishitus"];
                 $shiyou_gakubuchi_shiage=$data["shiyou_gakubuchi_shiage"];
                 $H_mume=$data["H_mume"];
                 $H_madobu=$data["H_madobu"];
                 $offset_inside=$data["offset_inside"];
                 $sasshinten_kara_tenjou=$data["sasshinten_kara_tenjou"];
                 $chiri_gakubuchi=$data["chiri_gakubuchi"];
                 $full_width=$data["full_width"];
                 $full_height=$data["full_height"];
                 $naiheki_boido_enchou=$data["naiheki_boido_enchou"];
                 $width=$data["width"];
                 $dakimikomi=$data["dakimikomi"];
                 $dan_netsu_atsu=$data["dan_netsu_atsu"];
                 $gakubuchi_mikomi=$data["gakubuchi_mikomi"];
                 $height=$data["height"];
                 $comment=$data["comment"];
                 $workset=$data["workset"];
                 $OmniClass_title=$data["OmniClass_title"];
                 $OmniClass_number=$data["OmniClass_number"];
                 $price=$data["price"];
                 $keijou_garari=$data["keijou_garari"];
                 $keijou_mizukiri=$data["keijou_mizukiri"];
                 $fugou_bangou_omo=$data["fugou_bangou_omo"];
                 $fugou_bangou_fuku=$data["fugou_bangou_fuku"];
                 $fugou_kigou=$data["fugou_kigou"];
                 $manufacturer=$data["manufacturer"];
                 $description=$data["description"];
                 $kanamono_tokushu=$data["kanamono_tokushu"];
                 $kanamono_shiji=$data["kanamono_shiji"];
                 $kanamono_shimari=$data["kanamono_shimari"];
                 $label_fire_protection=$data["label_fire_protection"];
                 $law_fire_protection=$data["law_fire_protection"];
                 $type_name=$data["type_name"];
                 $element_id=$data["element_id"];
                 $element_db_id=$data["element_db_id"];
                 DB::insert("CALL window_insert_procedure('$type_name' ,
                        $item_id,
                        $element_id,
                        '$element_db_id' ,
                        '$level',  
                        '$lower_frame_height',   
                        '$type_general_gFU',   
                        '$type_window_panel', 
                        '$type_window_drainer',
                        '$shiyou_gakubuchi' , 
                        '$seinou_kimitsu',
                        '$seinou_suimitsu',
                        '$seinou_taifuu_atsu' ,
                        '$seinou_shaon',
                        '$moji_garari_saizu' ,
                        '$moji_bikou',
                        '$moji_hontai_kikou',
                        '$shiyou_garasu_shiyou',
                        '$shiyou_hontai_shiage',
                        '$shiyou_hontai_zaishitus',
                        '$shiyou_gakubuchi_shiage' ,
                        '$H_mume',
                        '$H_madobu',
                        '$offset_inside',
                        '$sasshinten_kara_tenjou' ,
                        '$chiri_gakubuchi',  
                        '$full_width',
                        '$full_height',
                        '$naiheki_boido_enchou' , 
                        '$width',
                        '$dakimikomi' ,
                        '$dan_netsu_atsu' ,
                        '$gakubuchi_mikomi',
                        '$height',
                        '$comment' ,
                        '$workset' , 
                        '$OmniClass_title' ,
                        '$OmniClass_number',
                        '$price',
                        '$keijou_garari' ,
                        '$keijou_mizukiri',
                        '$fugou_bangou_omo' ,
                        '$fugou_bangou_fuku',
                        '$fugou_kigou',
                        '$manufacturer' ,
                        '$description' ,
                        '$kanamono_tokushu',
                        '$kanamono_shiji' ,
                        '$kanamono_shimari',
                        '$label_fire_protection',
                        '$law_fire_protection',
                        $version_id,
                        $version_number)");
            }

       }catch(Exception $e){
           print_r($e->getMessage());
       }
        
    }
    
    public function SaveDoor($save_list,$version_id,$item_id,$version_number){
        
       try{
           $save_element_id = array_column($save_list,"element_id");
         
           $current_ver_ids = ($save_element_id == "") ? "'"."ALL_UNCHECK"."'" : "'" . implode ( "', '", $save_element_id ) . "'";//convert array to string with single code
           $select_deleted_query = "SELECT element_id FROM tb_forge_door WHERE item_id = $item_id AND version_number < $version_number
                                    AND element_id NOT IN($current_ver_ids)";

           $deleted_elements = DB::select($select_deleted_query);

            if(sizeof($deleted_elements) > 0){               
                foreach($deleted_elements as $deleted_id){
                   
                    $ele_id = $deleted_id->element_id;
                    $insert_ids_query = "INSERT IGNORE INTO tb_forge_door_deleted (id,element_id,item_id,version_id,version_number)
                                        SELECT MAX(id) +1,$ele_id,$item_id,$version_id,$version_number FROM tb_forge_door_deleted";
                    DB::insert($insert_ids_query);
                }               
            }

            foreach($save_list as $data){
                
                $level=$data["level"];
                $lower_frame_height=$data["lower_frame_height"];
                $kinou=$data["kinou"];
                $type_door_panel=$data["type_door_panel"];
                $seinou_kimitsu=$data["seinou_kimitsu"];
                $seinou_shaon=$data["seinou_shaon"];
                $moji_undercut=$data["moji_undercut"];
                $moji_garasu_size=$data["moji_garasu_size"];
                $moji_garari_size=$data["moji_garari_size"];
                $moji_bikou=$data["moji_bikou"];
                $moji_tobira_atsu=$data["moji_tobira_atsu"];
                $moji_hontai_kikou=$data["moji_hontai_kikou"];
                $wakuzai_moji=$data["wakuzai_moji"];
                $shiage_moji=$data["shiage_moji"];
                $shiyou_garasu_shiyou=$data["shiyou_garasu_shiyou"];
                $shiyou_garari_shiyou=$data["shiyou_garari_shiyou"];
                $shiyou_hontai_shiage=$data["shiyou_hontai_shiage"];
                $shiyou_hontai_zaishitsu=$data["shiyou_hontai_zaishitsu"];
                $shiyou_waku_shiage=$data["shiyou_waku_shiage"];
                $shiyou_waku_zaishitsu=$data["shiyou_waku_zaishitsu"];
                $H_door_part=$data["H_door_part"];
                $offset_inside=$data["offset_inside"];
                $offset_outside=$data["offset_outside"];
                $full_width=$data["full_width"];
                $full_height=$data["full_height"];
                $thickness=$data["thickness"];
                $koteichi_shitawaku_mikomi=$data["koteichi_shitawaku_mikomi"];
                $koteichi_touatari_mikomi=$data["koteichi_touatari_mikomi"];
                $koteichi_waku_mikomi=$data["koteichi_waku_mikomi"];
                $width=$data["width"];
                $height=$data["height"];
                $dan_netsu_atsu=$data["dan_netsu_atsu"];
                $comment=$data["comment"];
                $workset=$data["workset"];
                $price=$data["price"];
                $keijou_garari=$data["keijou_garari"];
                $keijou_meshiawase=$data["keijou_meshiawase"];
                $keijou_sugata=$data["keijou_sugata"];
                $keijyou_waku=$data["keijyou_waku"];
                $keijou_kutsuzui=$data["keijou_kutsuzui"];
                $fugou_bangou_omo=$data["fugou_bangou_omo"];
                $fugou_bangou_fuku=$data["fugou_bangou_fuku"];
                $fugou_kigou=$data["fugou_kigou"];
                $fugou_denki=$data["fugou_denki"];
                $taika_toukyuu=$data["taika_toukyuu"];
                $seizoumoto=$data["seizoumoto"];
                $setsumei=$data["setsumei"];
                $kanamono_tokushu=$data["kanamono_tokushu"];
                $OmniClass_title=$data["OmniClass_title"];
                $OmniClass_number=$data["OmniClass_number"];
                $raberu_jouji_kaihou=$data["raberu_jouji_kaihou"];
                $raberu_nintei=$data["raberu_nintei"];
                $raberu_shaen=$data["raberu_shaen"];
                $raberu_bouka_seinou=$data["raberu_bouka_seinou"];
                $hou_jouji_kaihou=$data["hou_jouji_kaihou"];
                $hou_ninteihin=$data["hou_ninteihin"];
                $hou_shaen=$data["hou_shaen"];
                $hou_bouka_seinou=$data["hou_bouka_seinou"];
                $kanamono_handoru=$data["kanamono_handoru"];
                $kanamono_shiji_kanamono=$data["kanamono_shiji_kanamono"];
                $kanamono_shimari_kanamono=$data["kanamono_shimari_kanamono"];
                $type_name=$data["type_name"];
                $element_id=$data["element_id"];
                $element_db_id=$data["element_db_id"];
                DB::insert("CALL door_insert_procedure('$type_name',
                         $item_id,
                         $element_id,
                        '$element_db_id',
                        '$level',
                        '$lower_frame_height',
                        '$kinou',
                        '$type_door_panel',
                        '$seinou_kimitsu',
                        '$seinou_shaon',
                        '$moji_undercut',
                        '$moji_garasu_size',
                        '$moji_garari_size',
                        '$moji_bikou',
                        '$moji_tobira_atsu',
                        '$moji_hontai_kikou',
                        '$wakuzai_moji',
                        '$shiage_moji',
                        '$shiyou_garasu_shiyou',
                        '$shiyou_garari_shiyou',
                        '$shiyou_hontai_shiage',
                        '$shiyou_hontai_zaishitsu',
                        '$shiyou_waku_shiage',
                        '$shiyou_waku_zaishitsu',
                        '$H_door_part',
                        '$offset_inside',
                        '$offset_outside',
                        '$full_width',
                        '$full_height',
                        '$thickness',
                        '$koteichi_shitawaku_mikomi',
                        '$koteichi_touatari_mikomi',
                        '$koteichi_waku_mikomi',
                        '$width',
                        '$height',
                        '$dan_netsu_atsu',
                        '$comment',
                        '$workset',
                        '$price',
                        '$keijou_garari',
                        '$keijou_meshiawase',
                        '$keijou_sugata',
                        '$keijyou_waku',
                        '$keijou_kutsuzui',
                        '$fugou_bangou_omo',
                        '$fugou_bangou_fuku',
                        '$fugou_kigou',
                        '$fugou_denki',
                        '$taika_toukyuu',
                        '$seizoumoto',
                        '$setsumei',
                        '$kanamono_tokushu',
                        '$OmniClass_title',
                        '$OmniClass_number',
                        '$raberu_jouji_kaihou',
                        '$raberu_nintei',
                        '$raberu_shaen',
                        '$raberu_bouka_seinou',
                        '$hou_jouji_kaihou',
                        '$hou_ninteihin',
                        '$hou_shaen',
                        '$hou_bouka_seinou',
                        '$kanamono_handoru',
                        '$kanamono_shiji_kanamono',
                        '$kanamono_shimari_kanamono',
                         $version_id,
                         $version_number)");        
        }

       }catch(Exception $e){
           print_r($e->getMessage());
       }
        
    }
    
    public function SaveColumn($save_list,$version_id,$item_id,$version_number){
        
       try{
           $save_element_id = array_column($save_list,"element_id");
         
           $current_ver_ids = ($save_element_id == "") ? "'"."ALL_UNCHECK"."'" : "'" . implode ( "', '", $save_element_id ) . "'";//convert array to string with single code
           $select_deleted_query = "SELECT element_id FROM tb_forge_column WHERE item_id = $item_id AND version_number < $version_number
                                    AND element_id NOT IN($current_ver_ids)";

           $deleted_elements = DB::select($select_deleted_query);

            if(sizeof($deleted_elements) > 0){               
                foreach($deleted_elements as $deleted_id){
                   
                    $ele_id = $deleted_id->element_id;
                    $insert_ids_query = "INSERT IGNORE INTO tb_forge_column_deleted (id,element_id,item_id,version_id,version_number)
                                        SELECT MAX(id) +1,$ele_id,$item_id,$version_id,$version_number FROM tb_forge_column_deleted";
                    DB::insert($insert_ids_query);
                }               
            }

            foreach($save_list as $data){
                $type_name =$data["type_name"]; //$this->escape_string($data["type_name"]);
                $material =$data["material"];// $this->escape_string($data["material"]);
                $level = $this->escape_string($data["level"]);
                $volume = $data["volume"];
                $workset = $data["workset"];
                $family_name = $this->escape_string($data["family_name"]);
                $element_id = $data["element_id"];
                $phase = $data["phase"];
                $element_db_id = $data["element_db_id"];
                DB::insert("CALL column_insert_procedure($item_id,$element_id,'$element_db_id','$type_name','$material','$level',$volume,'$family_name','$workset',$version_id,$version_number,'$phase')");
                
            }

       }catch(Exception $e){
           print_r($e->getMessage());
       }
        
    }

    public function SaveBeam($save_list,$version_id,$item_id,$version_number){
        
       try{
           $save_element_id = array_column($save_list,"element_id");
         
           $current_ver_ids = ($save_element_id == "") ? "'"."ALL_UNCHECK"."'" : "'" . implode ( "', '", $save_element_id ) . "'";//convert array to string with single code
           $select_deleted_query = "SELECT element_id FROM tb_forge_beam WHERE item_id = $item_id AND version_number < $version_number
                                    AND element_id NOT IN($current_ver_ids)";

           $deleted_elements = DB::select($select_deleted_query);

            if(sizeof($deleted_elements) > 0){               
                foreach($deleted_elements as $deleted_id){
                   
                    $ele_id = $deleted_id->element_id;
                    $insert_ids_query = "INSERT IGNORE INTO tb_forge_beam_deleted (id,element_id,item_id,version_id,version_number)
                                        SELECT MAX(id) +1,$ele_id,$item_id,$version_id,$version_number FROM tb_forge_beam_deleted";
                    DB::insert($insert_ids_query);
                }               
            }

            foreach($save_list as $data){
                $type_name = $this->escape_string($data["type_name"]);
                $material = $this->escape_string($data["material"]);
                $level = $this->escape_string($data["level"]);
                $volume = $data["volume"];
                $workset = $data["workset"];
                $family_name = $this->escape_string($data["family_name"]);
                $element_id = $data["element_id"];
                $phase = $data["phase"];
                $element_db_id = $data["element_db_id"];
                DB::insert("CALL beam_insert_procedure($item_id,$element_id,'$element_db_id','$type_name','$material','$level',$volume,'$family_name','$workset',$version_id,$version_number,'$phase')");

            }

       }catch(Exception $e){
           print_r($e->getMessage());
       }

    }

    public function SaveFloor($save_list,$version_id,$item_id,$version_number){
        
       try{
           $save_element_id = array_column($save_list,"element_id");
         
           $current_ver_ids = ($save_element_id == "") ? "'"."ALL_UNCHECK"."'" : "'" . implode ( "', '", $save_element_id ) . "'";//convert array to string with single code
           $select_deleted_query = "SELECT element_id FROM tb_forge_floor WHERE item_id = $item_id AND version_number < $version_number
                                    AND element_id NOT IN($current_ver_ids)";

           $deleted_elements = DB::select($select_deleted_query);

            if(sizeof($deleted_elements) > 0){               
                foreach($deleted_elements as $deleted_id){
                   
                    $ele_id = $deleted_id->element_id;
                    $insert_ids_query = "INSERT IGNORE INTO tb_forge_floor_deleted (id,element_id,item_id,version_id,version_number)
                                        SELECT MAX(id) +1,$ele_id,$item_id,$version_id,$version_number FROM tb_forge_floor_deleted";
                    DB::insert($insert_ids_query);
                }               
            }

            foreach($save_list as $data){
                $type_name = $this->escape_string($data["type_name"]);
                $material = $this->escape_string($data["material"]);
                $level = $this->escape_string($data["level"]);
                $volume = $data["volume"];
                $workset = $data["workset"];
                $family_name = $this->escape_string($data["family_name"]);
                $element_id = $data["element_id"];
                $phase = $data["phase"];
                $element_db_id = $data["element_db_id"];

                DB::insert("CALL floor_insert_procedure($item_id,$element_id,'$element_db_id','$type_name','$material','$level',$volume,'$family_name','$workset',$version_id,$version_number,'$phase')");

            }

       }catch(Exception $e){
           print_r($e->getMessage());
       }
        
    }
    
    public function SaveWall($save_list,$version_id,$item_id,$version_number){
        
        try{
            $save_element_id = array_column($save_list,"element_id");
          
            $current_ver_ids = ($save_element_id == "") ? "'"."ALL_UNCHECK"."'" : "'" . implode ( "', '", $save_element_id ) . "'";//convert array to string with single code
            $select_deleted_query = "SELECT element_id FROM tb_forge_wall WHERE item_id = $item_id AND version_number < $version_number
                                     AND element_id NOT IN($current_ver_ids)";
 
            $deleted_elements = DB::select($select_deleted_query);
 
             if(sizeof($deleted_elements) > 0){               
                 foreach($deleted_elements as $deleted_id){
                    
                     $ele_id = $deleted_id->element_id;
                     $insert_ids_query = "INSERT IGNORE INTO tb_forge_wall_deleted (id,element_id,item_id,version_id,version_number)
                                         SELECT MAX(id) +1,$ele_id,$item_id,$version_id,$version_number FROM tb_forge_wall_deleted";
                     DB::insert($insert_ids_query);
                 }               
             }
                    
             foreach($save_list as $data){
                 $type_name = $this->escape_string($data["type_name"]);
                $material = $this->escape_string($data["material"]);
                $level = $this->escape_string($data["level"]);
                $volume = $data["volume"];
                $workset = $data["workset"];
                $family_name = $this->escape_string($data["family_name"]);
                $element_id = $data["element_id"];
                $phase = $data["phase"];
                $element_db_id = $data["element_db_id"];
 
                 DB::insert("CALL wall_insert_procedure($item_id,$element_id,'$element_db_id','$type_name','$material','$level',$volume,'$family_name','$workset',$version_id,$version_number,'$phase')");
 
             }

        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function SaveFoundation($save_list,$version_id,$item_id,$version_number){
        
        try{
            $save_element_id = array_column($save_list,"element_id");
          
            $current_ver_ids = ($save_element_id == "") ? "'"."ALL_UNCHECK"."'" : "'" . implode ( "', '", $save_element_id ) . "'";//convert array to string with single code
            $select_deleted_query = "SELECT element_id FROM tb_forge_foundation WHERE item_id = $item_id AND version_number < $version_number
                                     AND element_id NOT IN($current_ver_ids)";
 
            $deleted_elements = DB::select($select_deleted_query);
 
             if(sizeof($deleted_elements) > 0){               
                 foreach($deleted_elements as $deleted_id){
                    
                     $ele_id = $deleted_id->element_id;
                     $insert_ids_query = "INSERT IGNORE INTO tb_forge_foundation_deleted (id,element_id,item_id,version_id,version_number)
                                         SELECT MAX(id) +1,$ele_id,$item_id,$version_id,$version_number FROM tb_forge_foundation_deleted";
                     DB::insert($insert_ids_query);
                 }               
             }

             foreach($save_list as $data){
                $type_name = $this->escape_string($data["type_name"]);
                $material = $this->escape_string($data["material"]);
                $level = $this->escape_string($data["level"]);
                $volume = $data["volume"];
                $workset = $data["workset"];
                $family_name = $this->escape_string($data["family_name"]);
                $element_id = $data["element_id"];
                $phase = $data["phase"];
                $element_db_id = $data["element_db_id"];
 
                 DB::insert("CALL foundation_insert_procedure($item_id,$element_id,'$element_db_id','$type_name','$material','$level',$volume,'$family_name','$workset',$version_id,$version_number,'$phase')");
 
             }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
         
    }

    public function SaveColumnTekkin($save_list,$version_id,$item_id,$version_number){
        
        try{
           
             foreach($save_list as $data){
                 $W = $data["W"];
                 $D = $data["D"];
                 $volume = $data["volume"];
                 $level = $this->escape_string($data["level"]);
                 $start_diameter =$data["start_diameter"]; 
                 $start_X_firstRowCount =$data["start_X_firstRowCount"];
                 $start_X_secondRowCount = $this->escape_string($data["start_X_secondRowCount"]);
                 $start_Y_firstRowCount = $data["start_Y_firstRowCount"];
                 $start_Y_secondRowCount = $data["start_Y_secondRowCount"];
                 $start_rib_diameter = $this->escape_string($data["start_rib_diameter"]);
                 $start_rib_pitch = $data["start_rib_pitch"];

                 $end_diameter =$data["end_diameter"]; 
                 $end_X_firstRowCount =$data["end_X_firstRowCount"];
                 $end_X_secondRowCount = $this->escape_string($data["end_X_secondRowCount"]);
                 $end_Y_firstRowCount = $data["end_Y_firstRowCount"];
                 $end_Y_secondRowCount = $data["end_Y_secondRowCount"];
                 $end_rib_diameter = $this->escape_string($data["end_rib_diameter"]);
                 $end_rib_pitch = $data["end_rib_pitch"];
                 $element_id = $data["element_id"];
                 $phase = $data["phase"];
                 $element_db_id = $data["element_db_id"];
 
                 $query = "INSERT IGNORE INTO tb_forge_column_tekkin"
                 ."(id,item_id,element_id,element_db_id,W,D,volume,level,start_diameter,start_X_firstRowCount,start_X_secondRowCount,"
                 ."start_Y_firstRowCount,start_Y_secondRowCount,start_rib_diameter,start_rib_pitch,"
                 ."end_diameter,end_X_firstRowCount,end_X_secondRowCount,"
                 ."end_Y_firstRowCount,end_Y_secondRowCount,end_rib_diameter,end_rib_pitch,version_id,version_number,phase)"
                 ."SELECT COALESCE(MAX(id), 0) + 1,$item_id,$element_id,'$element_db_id','$W','$D','$volume','$level','$start_diameter','$start_X_firstRowCount','$start_X_secondRowCount',"
                 ."'$start_Y_firstRowCount','$start_Y_secondRowCount','$start_rib_diameter','$start_rib_pitch',"
                 ."'$end_diameter','$end_X_firstRowCount','$end_X_secondRowCount',"
                 ."'$end_Y_firstRowCount','$end_Y_secondRowCount','$end_rib_diameter','$end_rib_pitch',$version_id,$version_number,'$phase' FROM tb_forge_column_tekkin"
                 ." ON DUPLICATE KEY UPDATE "
                 ."element_db_id = '$element_db_id',"
                 ."W = '$W',"
                 ."D = '$D',"
                 ."volume = '$volume',"
                 ."level = '$level',"
                 ."start_diameter = '$start_diameter',"
                 ."start_X_firstRowCount = '$start_X_firstRowCount',"
                 ."start_X_secondRowCount = '$start_X_secondRowCount',"
                 ."start_Y_firstRowCount = '$start_Y_firstRowCount',"
                 ."start_Y_secondRowCount = '$start_Y_secondRowCount',"
                 ."start_rib_diameter = '$start_rib_diameter',"
                 ."start_rib_pitch = '$start_rib_pitch',"
                 ."end_diameter = '$end_diameter',"
                 ."end_X_firstRowCount = '$end_X_firstRowCount',"
                 ."end_X_secondRowCount = '$end_X_secondRowCount',"
                 ."end_Y_firstRowCount = '$end_Y_firstRowCount',"
                 ."end_Y_secondRowCount = '$end_Y_secondRowCount',"
                 ."end_rib_diameter = '$end_rib_diameter',"
                 ."end_rib_pitch = '$end_rib_pitch',"
                 ."version_id = $version_id,"
                 ."version_number = $version_number,"
                 ."phase = '$phase'";
                
                DB::insert($query);

                //DB::insert("CALL column_insert_procedure($item_id,$element_id,'$type_name','$material','$level',$volume,'$family_name','$workset',$version_id,$version_number)");
                 
             }
 
        }catch(Exception $e){
            print_r($e->getMessage());
        }
         
    }
 
    public function SaveBeamTekkin($save_list,$version_id,$item_id,$version_number){
         
        try{
           
             foreach($save_list as $data){
                 $B = $data["B"];
                 $H = is_array($data["H"])? $data["H"][0]: $data["H"];
                 $kattocho = $data["kattocho"];
                 $level = $this->escape_string($data["level"]);
                 $start_upper_diameter = $data["start_upper_diameter"];
                 $start_upper_firstRowCount = $data["start_upper_firstRowCount"];
                 $start_upper_secondRowCount = $data["start_upper_secondRowCount"];
                 $start_lower_diameter = $data["start_lower_diameter"];
                 $start_lower_firstRowCount = $data["start_lower_firstRowCount"];
                 $start_lower_secondRowCount = $data["start_lower_secondRowCount"];
                 $start_rib_diameter = $data["start_rib_diameter"];
                 $start_rib_count = $data["start_rib_count"];
                 $start_rib_pitch = $data["start_rib_pitch"];

                 $center_upper_diameter = $data["center_upper_diameter"];
                 $center_upper_firstRowCount = $data["center_upper_firstRowCount"];
                 $center_upper_secondRowCount = $data["center_upper_secondRowCount"];
                 $center_lower_diameter = $data["center_lower_diameter"];
                 $center_lower_firstRowCount = $data["center_lower_firstRowCount"];
                 $center_lower_secondRowCount = $data["center_lower_secondRowCount"];
                 $center_rib_diameter = $data["center_rib_diameter"];
                 $center_rib_count = $data["center_rib_count"];
                 $center_rib_pitch = $data["center_rib_pitch"];

                 $end_upper_diameter = $data["end_upper_diameter"];
                 $end_upper_firstRowCount = $data["end_upper_firstRowCount"];
                 $end_upper_secondRowCount = $data["end_upper_secondRowCount"];
                 $end_lower_diameter = $data["end_lower_diameter"];
                 $end_lower_firstRowCount = $data["end_lower_firstRowCount"];
                 $end_lower_secondRowCount = $data["end_lower_secondRowCount"];
                 $end_rib_diameter = $data["end_rib_diameter"];
                 $end_rib_count = $data["end_rib_count"];
                 $end_rib_pitch = $data["end_rib_pitch"];
                 $element_id = $data["element_id"];
                 $phase = $data["phase"];
                 $element_db_id = $data["element_db_id"];
                
                 $query = "INSERT  INTO tb_forge_beam_tekkin"
                            ."(id,item_id,element_id,element_db_id,B,H,kattocho,level,start_upper_diameter,start_upper_firstRowCount,start_upper_secondRowCount,"
                            ."start_lower_diameter,start_lower_firstRowCount,start_lower_secondRowCount,"
                            ."start_rib_diameter,start_rib_count,start_rib_pitch,"

                            ."center_upper_diameter,center_upper_firstRowCount,center_upper_secondRowCount,"
                            ."center_lower_diameter,center_lower_firstRowCount,center_lower_secondRowCount,"
                            ."center_rib_diameter,center_rib_count,center_rib_pitch,"

                            ."end_upper_diameter,end_upper_firstRowCount,end_upper_secondRowCount,"
                            ."end_lower_diameter,end_lower_firstRowCount,end_lower_secondRowCount,"
                            ."end_rib_diameter,end_rib_count,end_rib_pitch,version_id,version_number,phase)"
                            ."SELECT COALESCE(MAX(id), 0) + 1,$item_id,$element_id,'$element_db_id','$B','$H','$kattocho','$level','$start_upper_diameter','$start_upper_firstRowCount','$start_upper_secondRowCount',"
                            ."'$start_lower_diameter','$start_lower_firstRowCount','$start_lower_secondRowCount',"
                            ."'$start_rib_diameter','$start_rib_count','$start_rib_pitch',"

                            ."'$center_upper_diameter','$center_upper_firstRowCount','$center_upper_secondRowCount',"
                            ."'$center_lower_diameter','$center_lower_firstRowCount','$center_lower_secondRowCount',"
                            ."'$center_rib_diameter','$center_rib_count','$center_rib_pitch',"

                            ."'$end_upper_diameter','$end_upper_firstRowCount','$end_upper_secondRowCount',"
                            ."'$end_lower_diameter','$end_lower_firstRowCount','$end_lower_secondRowCount',"
                            ."'$end_rib_diameter','$end_rib_count','$end_rib_pitch',$version_id,$version_number,'$phase' FROM tb_forge_beam_tekkin"
                            ." ON DUPLICATE KEY UPDATE "
                            ."element_db_id = '$element_db_id',"
                            ."B = '$B',"
                            ."H = '$H',"
                            ."kattocho = '$kattocho',"
                            ."level = '$level',"
                            ."start_upper_diameter = '$start_upper_diameter',"
                            ."start_upper_firstRowCount = '$start_upper_firstRowCount',"
                            ."start_upper_secondRowCount = '$start_upper_secondRowCount',"
                            ."start_lower_diameter = '$start_lower_diameter',"
                            ."start_lower_firstRowCount = '$start_lower_firstRowCount',"
                            ."start_lower_secondRowCount = '$start_lower_secondRowCount',"
                            ."start_rib_diameter = '$start_rib_diameter',"
                            ."start_rib_count = '$start_rib_count',"
                            ."start_rib_pitch = '$start_rib_pitch',"

                            ."center_upper_diameter = '$center_upper_diameter',"
                            ."center_upper_firstRowCount = '$center_upper_firstRowCount',"
                            ."center_upper_secondRowCount = '$center_upper_secondRowCount',"
                            ."center_lower_diameter = '$center_lower_diameter',"
                            ."center_lower_firstRowCount = '$center_lower_firstRowCount',"
                            ."center_lower_secondRowCount = '$center_lower_secondRowCount',"
                            ."center_rib_diameter = '$center_rib_diameter',"
                            ."center_rib_count = '$center_rib_count',"
                            ."center_rib_pitch = '$center_rib_pitch',"

                            ."end_upper_diameter = '$end_upper_diameter',"
                            ."end_upper_firstRowCount = '$end_upper_firstRowCount',"
                            ."end_upper_secondRowCount = '$end_upper_secondRowCount',"
                            ."end_lower_diameter = '$end_lower_diameter',"
                            ."end_lower_firstRowCount = '$end_lower_firstRowCount',"
                            ."end_lower_secondRowCount = '$end_lower_secondRowCount',"
                            ."end_rib_diameter = '$end_rib_diameter',"
                            ."end_rib_count = '$end_rib_count',"
                            ."end_rib_pitch = '$end_rib_pitch',"
                            ."version_id = $version_id,"
                            ."version_number = $version_number,"
                            ."phase = '$phase'";

                 DB::insert($query);
 
                 //DB::insert("CALL beam_insert_procedure($item_id,$element_id,'$type_name','$material','$level',$volume,'$family_name','$workset',$version_id,$version_number)");
 
             }
 
        }catch(Exception $e){
            print_r($e->getMessage());
        }
 
    }

    public function SaveFoundationTekkin($save_list,$version_id,$item_id,$version_number){
        
        try{
           
             foreach($save_list as $data){
                $D = $data["D"];
                $H = $data["H"];
                $W = $data["W"];
                $level = $this->escape_string($data["level"]);
                $upper_X_diameter = $data["upper_X_diameter"];
                $upper_X_count = $data["upper_X_count"];
                $upper_Y_diameter = $data["upper_Y_diameter"];
                $upper_Y_count = $data["upper_Y_count"];
                $lower_X_diameter = $data["lower_X_diameter"];
                $lower_X_count = $data["lower_X_count"];
                $lower_Y_diameter = $data["lower_Y_diameter"];
                $lower_Y_count = $data["lower_Y_count"];
                $element_id = $data["element_id"];
                $phase = $data["phase"];
                $element_db_id = $data["element_db_id"];

                $query = "INSERT  INTO tb_forge_foundation_tekkin"
                ."(id,item_id,element_id,element_db_id,D,H,W,level,upper_X_diameter,upper_X_count,upper_Y_diameter,upper_Y_count"
                .",lower_X_diameter,lower_X_count,lower_Y_diameter,lower_Y_count,version_id,version_number,phase)"
                ."SELECT COALESCE(MAX(id), 0) + 1,$item_id,$element_id,'$element_db_id','$D','$H','$W','$level','$upper_X_diameter','$upper_X_count','$upper_Y_diameter','$upper_Y_count'"
                .",'$lower_X_diameter','$lower_X_count','$lower_Y_diameter','$lower_Y_count',$version_id,$version_number,'$phase' FROM tb_forge_foundation_tekkin"
                ." ON DUPLICATE KEY UPDATE " 
                ." element_db_id = '$element_db_id',"
                ."D = '$D',"
                ."H = '$H',"
                ." W = '$W',"
                ."level = '$level',"
                ."upper_X_diameter ='$upper_X_diameter',"
                ."upper_X_count = '$upper_X_count',"
                ."upper_Y_diameter = '$upper_Y_diameter',"
                ."upper_Y_count = '$upper_Y_count',"
                ."lower_X_diameter = '$lower_X_diameter',"
                ."lower_X_count = '$lower_X_count',"
                ."lower_Y_diameter = '$lower_Y_diameter',"
                ."lower_Y_count = '$lower_Y_count',"
                ."version_id = $version_id,"
                ."version_number = $version_number,"
                ."phase = '$phase'";
 
                 DB::insert($query);
                 
             }
 
        }catch(Exception $e){
            print_r($e->getMessage());
        }
         
    }
}
