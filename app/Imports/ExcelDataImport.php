<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use DateTime;

class ExcelDataImport implements ToCollection,WithMultipleSheets,WithCalculatedFormulas
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
        
    }
    
    public function sheets(): array
    {
        return [
            '■全店元データ' => new ExcelDataImport(),
        ];
    }

    public function ImportDataToDatabase(Array $data)
    {
        $sheet_name = "■全店元データ";
        
        /* echo"<pre>";
                    print_r($data);
                    echo"</pre>";return;*/
        $headers="";
        // $h_bangou = "番号"; 
        // $h_a_pj_code = "PJコード";
        // $h_a_kouji_kikan_code  = "工事基幹コード"; 
        // $h_a_shiten = "店";  
        // $h_a_kakudo = "確度";  
        // $h_a_pj_name = "z";  
        // $h_a_kouji_kubun = "工事区分";  
        // $h_a_ukeoikin = "請負金※太字：10億以上 "; 
        // $h_a_youto1 = "建物用途1";  
        // $h_a_youto2 = "建物用途2";  
        // $h_a_sekou_basyo = "施工場所";  
        // $h_a_sekkei_state = "";  
        // $h_a_sekkei = "設計";  
        // $h_a_kouzou = "建物規模 構造"; 
        // $h_a_kaisuu = "建物規模 階数"; 
        // $h_a_tijo = "建物規模 地上"; 
        // $h_a_tika = "建物規模 地下"; 
        // $h_a_ph = "建物規模 PH"; 
        // $h_a_nobe_menseki = "建物規模 延べ面積（㎡）"; 
        // $h_a_tyakkou = "工程 着工"; 
        // $h_a_syunkou = "工程 竣工"; 
        
        $h_b_pj_state = "プロジェクト稼働状況（選択）"; 
        $h_b_sekkei_state = "取組み状況①設計段階（選択）"; 
        $h_b_sekou_state = "取組み状況②施工段階（選択）"; 
        $h_b_jiyuu_kinyuu = "自由記入欄・Z:その他を選択した場合　・右欄にて選択しきれない内容ご記入ください";  
        
        //基本物件情報
        $h_b_pj_name      = "プロジェクト名称(B)";
        $h_b_tmp_pj_name  = "BIM360プロジェクト名称";
        $h_b_shiten       = "店(B)";
        $h_b_kakudo       = "確度(B)";
        $h_b_hattyuusya   = "発注者";  
        $h_b_sekkeisya1   = "設計者(B)";  
        $h_b_sekkeisya2   = "設計者(B)_支店"; 
        $h_b_sekou_basyo  = "施工場所(B)";  
        $h_b_youto        = "用途(B)"; 
        $h_b_kouzou       = "構造(B)"; 
        $h_b_kaisuu       = "階数(B)"; 
        $h_b_tika         = "地下(B)"; 
        $h_b_tijo         = "地上(B)"; 
        $h_b_ph           = "PH(B)"; 
        $h_b_nobe_menseki = "延べ面積(B)（㎡）"; 
        $h_b_tousuu       = "棟数"; 
        
        //人員
        $h_b_syotyou          = "工事事務所所長_氏名"; 
        $h_b_kouji_jimusyo    = "工事事務所_組織"; 
        $h_b_kouji_katyou     = "工事部担当者_氏名"; 
        $h_b_kouji_buka       = "工事部担当者_組織"; 
        $h_b_eigyou_tantousya = "営業担当者_氏名"; 
        $h_b_eigyou_tantoubu  = "営業担当者_組織"; 
        
        $h_b_isyou_sekkei           = "意匠設計担当者_氏名"; 
        $h_b_isyou_syozoku          = "意匠設計担当者_組織"; 
        $h_b_isyou_model            = "意匠モデラー_氏名"; 
        $h_b_isyou_model_syozoku    = "意匠モデラー_組織"; 

        $h_b_kouzou_sekkei          = "構造設計担当者_氏名"; 
        $h_b_kouzou_syozoku         = "構造設計担当者_組織"; 
        $h_b_kouzou_model           = "構造モデラー_氏名"; 
        $h_b_kouzou_model_syozoku   = "構造モデラー_組織"; 

        $h_b_setubi_kuutyou_sekkei          = "設備空調設計担当者_氏名"; 
        $h_b_setubi_kuutyou_syozoku         = "設備空調設計担当者_組織"; 
        $h_b_setubi_kuutyou_model           = "設備空調モデラー_氏名"; 
        $h_b_setubi_kuutyou_model_syozoku   = "設備空調モデラー_組織"; 

        $h_b_setubi_eisei_sekkei        = "設備衛生設計担当者_氏名"; 
        $h_b_setubi_eisei_syozoku       = "設備衛生設計担当者_組織"; 
        $h_b_setubi_eisei_model         = "設備衛生モデラー_氏名"; 
        $h_b_setubi_eisei_model_syozoku = "設備衛生モデラー_組織"; 

        $h_b_setubi_denki_sekkei        = "設備電気設計担当者_氏名"; 
        $h_b_setubi_denki_syozoku       = "設備電気設計担当者_組織"; 
        $h_b_setubi_denki_model         = "設備電気モデラー_氏名"; 
        $h_b_setubi_denki_model_syozoku = "設備電気モデラー_組織"; 

        $h_b_ss_designer_name = "生産設計担当者_氏名"; 
        $h_b_ss_designer_dept = "生産設計担当者_組織"; 
        $h_b_ss_modeler_name  = "生産設計モデラー_氏名"; 
        $h_b_ss_modeler_dept  = "生産設計モデラー_組織"; 

        $h_b_sekou_tantou           = "施工管理担当者_氏名"; 
        $h_b_sekou_syozoku          = "施工管理担当者_組織";
        
        $h_b_seisan_modeler_name    = "生産モデラー_氏名"; 
        $h_b_seisan_modeler_dept    = "生産モデラー_組織"; 
        
        $h_b_seisan_gijutu_tantou   = "生産技術担当者_氏名"; 
        $h_b_seisan_gijutu_syozoku  = "生産技術担当者_組織"; 
        
        $h_b_sekisan_mitumori_tantou    = "積算担当者_氏名"; 
        $h_b_sekisan_mitumori_syozoku   = "積算担当者_組織"; 
        
        $h_b_bim_maneka_tantou      = "BIMマネジメント課担当者_氏名"; 
        $h_b_bim_maneka_syozoku     = "BIMマネジメント課担当者_組織"; 
        
        $h_b_ipd_center_tantou      = "iPDセンター担当者_氏名"; 
        $h_b_ipd_center_syozoku     = "iPDセンター担当者_組織"; 
        
        $h_b_partner_company        = "協力会社担当者_氏名"; 
        $h_b_partner_company_dept   = "協力会社担当者_組織"; 
        
        $h_b_bim_m                  = "BIMマネージャー_氏名"; 
        $h_b_bim_manager_dept       = "BIMマネージャー_組織"; 
        
        $h_b_bim_coordinator_tantou     = "BIMコーディネーター_氏名"; 
        $h_b_bim_coordinator_syozoku    = "BIMコーディネーター_組織"; 

        //工事発注形態
        $h_b_hattyuu_keitai_kentiku = "建築工事発注形態"; 
        $h_b_hattyuu_keitai_setubi  = "設備工事発注形態"; 
        
        //工事費 等コスト情報
        $h_b_yosou_koujihi      = "予想工事費(百万円)"; 
        $h_b_kakutei_ukeoikin   = "確定請負金(千円)"; 
        $h_b_tubotanka          = "坪単価(万円/坪)";
        
        //工程
        $h_b_nyuusatu_jiki          = "入札_開始"; 
        $h_b_nyuusatu_kettei_jiki   = "入札_完了"; 
        $h_b_kihonsekkei_start      = "基本設計_開始"; 
        $h_b_kihonsekkei_end        = "基本設計_完了"; 
        $h_b_jissisekkei_start      = "実施設計_開始"; 
        $h_b_jissisekkei_end        = "実施設計_完了"; 
        $h_b_koutei_sekkei_model_start      = "設計モデル作成_開始"; 
        $h_b_koutei_sekkei_model_end        = "設計モデル作成_完了"; 
        $h_b_koutei_kakunin_sinsei_start    = "確認申請モデル作成_開始"; 
        $h_b_koutei_kakunin_sinsei_end      = "確認申請モデル作成_完了"; 
        $h_b_koutei_sekisan_model_tougou_start  = "精算見積モデル統合・追記修正_開始"; 
        $h_b_koutei_sekisan_model_tougou_end    = "精算見積モデル統合・追記修正_完了"; 
        $h_b_koutei_kouji_juujisya_kettei_start = "工事従事者決定_開始"; 
        $h_b_koutei_kouji_juujisya_kettei_end   = "工事従事者決定_完了"; 
        $h_b_koutei_genba_koutei_kettei_start   = "現場工程決定_開始"; 
        $h_b_koutei_genba_koutei_kettei_end     = "現場工程決定_完了"; 
        $h_b_koutei_kouji_start = "工事_開始"; 
        $h_b_koutei_kouji_end   = "工事_完了"; 
        $h_b_handover_start = "引き渡し_開始"; 
        $h_b_handover_end   = "引き渡し_完了"; 

        //その他
        $h_b_modeling_state = "モデリング会社_区分"; 
        $h_b_bikou  = "備考1"; 
        $h_c_bikou  = "備考2"; 
        $h_c_order_status       = "受注状況"; 
        $h_c_additional_item    = "追加項目"; 

        if(isset($data[$sheet_name]) && sizeof($data[$sheet_name]) > 6){
            $headers = $this->PrepareHeader($data[$sheet_name][3],$data[$sheet_name][5],1);
        }
        
        /*echo"<pre>";
                    print_r($headers);
                    echo"</pre>";return;*/
        
        foreach($data as $rows){

            foreach($rows as $key=>$row){

               try{ 
                    if($row[1] == "PJコード")continue;//skip header row
                    if(!isset($row[1]) || strlen($row[1]) < 10  || sizeof($row) < 117)continue;
                    $a_pj_code      = $row[1];//iPDコード
                    if($a_pj_code =="" || $a_pj_code == "なし")continue;
                    $a_kouji_kikan_code = $row[2];//工事基幹コード
                    $a_shiten       = $row[3];//店
                    $a_kakudo       = $row[4];//確度
                    $a_pj_name      = $row[5];//名　称
                    $a_kouji_kubun  = $row[6];//工事区分
                    $a_kouji_type   = $row[7];//工事区分名
                    $a_ukeoikin     = $row[8];//請負金※太字：10億以上
                    $a_youto1       = $row[9];//建物用途1
                    $a_youto2       = $row[10];//建物用途2
                    $a_sekou_basyo  = $row[11];//施工場所
                    $a_sekkei_state = $row[12];//
                    $a_sekkei       = $row[13];//設計
                    if(strpos($a_sekkei, '大林') !== false
                    || strpos($a_sekkei, '自社') !== false){//because of always working else conditon of formula ,so rechecking if condition
                        $a_sekkei_state = "自";//formula->=IF(COUNTIF(M229,"*大林*")+COUNTIF(M229,"*自社*"),"自",IF(M229="","ー","他"))
                    }else if($a_sekkei == ''){
                        $a_sekkei_state = "ー";
                    }else{
                        $a_sekkei_state = "他";
                    }
                    $a_kouzou       = $row[14];//構造
                    $a_kaisuu       = $row[15];//階数
                    $a_tijo         = $row[16];//地上
                    $a_tika         = $row[17];//地下
                    $a_ph           = $row[18];//PH
                    $a_nobe_menseki = $row[19];//延べ面積
                    $a_tyakkou      = $row[20];//着工
                    $a_syunkou      = $row[21];//竣工
                    
                    $b_pj_state     = $this->getValue($row,$headers,$h_b_pj_state);     //プロジェクト稼働状況（選択
                    $b_sekkei_state = $this->getValue($row,$headers,$h_b_sekkei_state); //取組み状況①設計段階（選択）
                    $b_sekou_state  = $this->getValue($row,$headers,$h_b_sekou_state);  //取組み状況②施工段階（選択）
                    $b_jiyuu_kinyuu = $this->getValue($row,$headers,$h_b_jiyuu_kinyuu); //自由記入欄\r\n・Z:その他を選択した場合　\r\n・右欄にて選択しきれない内容\r\nご記入ください
 
                    $b_pj_name      = $this->getValue($row,$headers,$h_b_pj_name);      //プロジェクト名称(B)
                    $b_tmp_pj_name  = $this->getValue($row,$headers,$h_b_tmp_pj_name);  //BIM360プロジェクト名称
                    $b_shiten       = $this->getValue($row,$headers,$h_b_shiten);       //支店(B)
                    $b_kakudo       = $this->getValue($row,$headers,$h_b_kakudo);       //確度(B)
                    $b_hattyuusya   = $this->getValue($row,$headers,$h_b_hattyuusya);   //発注者
                    $b_sekkeisya1   = $this->getValue($row,$headers,$h_b_sekkeisya1);   //設計者(B)
                    $b_sekkeisya2   = $this->getValue($row,$headers,$h_b_sekkeisya2);   //設計者(B)_支店
                    $b_sekou_basyo  = $this->getValue($row,$headers,$h_b_sekou_basyo);  //施工場所(B)
                    
                    $b_youto        = $this->getValue($row,$headers,$h_b_youto);        //用途(B)
                    $b_kouzou       = $this->getValue($row,$headers,$h_b_kouzou);       //構造(B)
                    $b_kaisuu       = $this->getValue($row,$headers,$h_b_kaisuu);       //階数(B)
                    $b_tika         = $this->getValue($row,$headers,$h_b_tika);         //地下(B)
                    $b_tijo         = $this->getValue($row,$headers,$h_b_tijo);         //地上(B)
                    $b_ph           = $this->getValue($row,$headers,$h_b_ph);           //PH(B)
                    $b_nobe_menseki = $this->getValue($row,$headers,$h_b_nobe_menseki); //延べ面積(B)（㎡）
                    $b_tousuu       = $this->getValue($row,$headers,$h_b_tousuu);       //棟数
                    
                    $b_syotyou      = $this->getValue($row,$headers,$h_b_syotyou);              //工事事務所所長_氏名
                    $b_kouji_jimusyo= $this->getValue($row,$headers,$h_b_kouji_jimusyo);        //工事事務所_組織
                    $b_kouji_katyou = $this->getValue($row,$headers,$h_b_kouji_katyou);         //工事部担当者_氏名
                    $b_kouji_buka   = $this->getValue($row,$headers,$h_b_kouji_buka);           //工事部担当者_組織
                    $b_eigyou_tantousya = $this->getValue($row,$headers,$h_b_eigyou_tantousya); //営業担当者_氏名
                    $b_eigyou_tantoubu  = $this->getValue($row,$headers,$h_b_eigyou_tantoubu);  //営業担当部_組織

                    $b_isyou_sekkei = $this->getValue($row,$headers,$h_b_isyou_sekkei);                 //意匠設計担当者_氏名
                    $b_isyou_syozoku  = $this->getValue($row,$headers,$h_b_isyou_syozoku);              //意匠設計担当者_組織
                    $b_isyou_model  = $this->getValue($row,$headers,$h_b_isyou_model);                  //意匠モデラー_氏名
                    $b_isyou_model_syozoku  = $this->getValue($row,$headers,$h_b_isyou_model_syozoku);  //意匠モデラー_組織
                    
                    $b_kouzou_sekkei= $this->getValue($row,$headers,$h_b_kouzou_sekkei);                //構造設計担当者_氏名
                    $b_kouzou_syozoku= $this->getValue($row,$headers,$h_b_kouzou_syozoku);              //構造設計担当者_組織
                    $b_kouzou_model = $this->getValue($row,$headers,$h_b_kouzou_model);                 //構造モデラー_氏名
                    $b_kouzou_model_syozoku = $this->getValue($row,$headers,$h_b_kouzou_model_syozoku); //構造モデラー_組織
                    
                    $b_setubi_kuutyou_sekkei = $this->getValue($row,$headers,$h_b_setubi_kuutyou_sekkei);               //設備空調担当者_氏名
                    $b_setubi_kuutyou_syozoku = $this->getValue($row,$headers,$h_b_setubi_kuutyou_syozoku);             //設備空調担当者_組織
                    $b_setubi_kuutyou_model = $this->getValue($row,$headers,$h_b_setubi_kuutyou_model);                 //設備空調モデラー_氏名
                    $b_setubi_kuutyou_model_syozoku = $this->getValue($row,$headers,$h_b_setubi_kuutyou_model_syozoku); //設備空調モデラー_組織
    
                    $b_setubi_eisei_sekkei = $this->getValue($row,$headers,$h_b_setubi_eisei_sekkei);               //設備衛生担当者_氏名
                    $b_setubi_eisei_syozoku = $this->getValue($row,$headers,$h_b_setubi_eisei_syozoku);             //設備衛生担当者_組織
                    $b_setubi_eisei_model = $this->getValue($row,$headers,$h_b_setubi_eisei_model);                 //設備衛生モデラー氏名
                    $b_setubi_eisei_model_syozoku = $this->getValue($row,$headers,$h_b_setubi_eisei_model_syozoku); //設備衛生モデラー組織
                    
                    $b_setubi_denki_sekkei = $this->getValue($row,$headers,$h_b_setubi_denki_sekkei);               //設備電気担当者氏名
                    $b_setubi_denki_syozoku = $this->getValue($row,$headers,$h_b_setubi_denki_syozoku);             //設備電気担当者組織
                    $b_setubi_denki_model = $this->getValue($row,$headers,$h_b_setubi_denki_model);                 //設備電気モデラー氏名
                    $b_setubi_denki_model_syozoku = $this->getValue($row,$headers,$h_b_setubi_denki_model_syozoku); //設備電気モデラー組織

                    $b_ss_designer_name = $this->getValue($row,$headers,$h_b_ss_designer_name);         //生産設計担当者氏名
                    $b_ss_designer_dept = $this->getValue($row,$headers,$h_b_ss_designer_dept);         //生産設計担当者組織
                    $b_ss_modeler_name = $this->getValue($row,$headers,$h_b_ss_modeler_name);           //生産設計モデラー氏名
                    $b_ss_modeler_dept = $this->getValue($row,$headers,$h_b_ss_modeler_dept);           //生産設計モデラー組織

                    $b_sekou_tantou = $this->getValue($row,$headers,$h_b_sekou_tantou);                 //施工管理担当者氏名
                    $b_sekou_syozoku = $this->getValue($row,$headers,$h_b_sekou_syozoku);               //施工管理担当者組織

                    $b_seisan_modeler_name = $this->getValue($row,$headers,$h_b_seisan_modeler_name);   //生産モデラー氏名
                    $b_seisan_modeler_dept = $this->getValue($row,$headers,$h_b_seisan_modeler_dept);   //生産モデラー組織
                    
                    $b_seisan_gijutu_tantou = $this->getValue($row,$headers,$h_b_seisan_gijutu_tantou);         //生産技術担当者氏名
                    $b_seisan_gijutu_syozoku = $this->getValue($row,$headers,$h_b_seisan_gijutu_syozoku);       //生産技術担当者組織

                    $b_sekisan_mitumori_tantou = $this->getValue($row,$headers,$h_b_sekisan_mitumori_tantou);   //積算見積担当者氏名
                    $b_sekisan_mitumori_syozoku = $this->getValue($row,$headers,$h_b_sekisan_mitumori_syozoku); //積算見積担当者組織

                    $b_bim_maneka_tantou = $this->getValue($row,$headers,$h_b_bim_maneka_tantou);       //BIMマネジメント課担当者_氏名
                    $b_bim_maneka_syozoku = $this->getValue($row,$headers,$h_b_bim_maneka_syozoku);     //BIMマネジメント課担当者_組織

                    $b_ipd_center_tantou = $this->getValue($row,$headers,$h_b_ipd_center_tantou);       //iPDセンター担当者_氏名
                    $b_ipd_center_syozoku = $this->getValue($row,$headers,$h_b_ipd_center_syozoku);     //iPDセンター担当者_組織

                    $b_partner_company = $this->getValue($row,$headers,$h_b_partner_company);           //協力会社担当者氏名
                    $b_partner_company_dept = $this->getValue($row,$headers,$h_b_partner_company_dept); //協力会社担当者組織

                    $b_bim_m = $this->getValue($row,$headers,$h_b_bim_m);                               //BIMマネージャー_氏名
                    $b_bim_manager_dept = $this->getValue($row,$headers,$h_b_bim_manager_dept);         //BIMマネージャー_組織

                    $b_bim_coordinator_tantou = $this->getValue($row,$headers,$h_b_bim_coordinator_tantou);     //BIMコーディネーター氏名
                    $b_bim_coordinator_syozoku = $this->getValue($row,$headers,$h_b_bim_coordinator_syozoku);   //BIMコーディネーター組織

                    $b_hattyuu_keitai_kentiku   = $this->getValue($row,$headers,$h_b_hattyuu_keitai_kentiku);   //"建築工事発注形態
                    $b_hattyuu_keitai_setubi    = $this->getValue($row,$headers,$h_b_hattyuu_keitai_setubi);    //設備工事発注形態
                    $b_yosou_koujihi    = $this->getValue($row,$headers,$h_b_yosou_koujihi);    //予想工事費(百万円)
                    $b_kakutei_ukeoikin = $this->getValue($row,$headers,$h_b_kakutei_ukeoikin); //確定請負金(千円)
                    $b_tubotanka        = $this->getValue($row,$headers,$h_b_tubotanka);        //坪単価(万円/坪)

                    //工程
                    $b_nyuusatu_jiki = $this->getValue($row,$headers,$h_b_nyuusatu_jiki);
                    if($b_nyuusatu_jiki !== ""){            //入札時期開始
                        $b_nyuusatu_jiki = $this->checkDateFormat($b_nyuusatu_jiki);
                    }

                    $b_nyuusatu_kettei_jiki = $this->getValue($row,$headers,$h_b_nyuusatu_kettei_jiki);
                    if($b_nyuusatu_kettei_jiki !== ""){     //入札決定時期完了
                        $b_nyuusatu_kettei_jiki = $this->checkDateFormat($b_nyuusatu_kettei_jiki);
                    }
                    
                    $b_koutei_kihonsekkei_start = $this->getValue($row,$headers,$h_b_kihonsekkei_start);
                    if($b_koutei_kihonsekkei_start !== ""){ //基本設計_開始
                        $b_koutei_kihonsekkei_start = $this->checkDateFormat($b_koutei_kihonsekkei_start);
                    }

                    $b_koutei_kihonsekkei_end = $this->getValue($row,$headers,$h_b_kihonsekkei_end);
                    if($b_koutei_kihonsekkei_end !== ""){   //基本設計_完了
                        $b_koutei_kihonsekkei_end = $this->checkDateFormat($b_koutei_kihonsekkei_end);
                    }
                    
                    $b_koutei_jissisekkei_start = $this->getValue($row,$headers,$h_b_jissisekkei_start);
                    if($b_koutei_jissisekkei_start !== ""){ //実施設計_開始
                        $b_koutei_jissisekkei_start = $this->checkDateFormat($b_koutei_jissisekkei_start);
                    }

                    $b_koutei_jissisekkei_end = $this->getValue($row,$headers,$h_b_jissisekkei_end);
                    if($b_koutei_jissisekkei_end !== ""){   //実施設計_完了
                        $b_koutei_jissisekkei_end = $this->checkDateFormat($b_koutei_jissisekkei_end);
                    }


                    $b_koutei_sekkei_model_start = $this->getValue($row,$headers,$h_b_koutei_sekkei_model_start);
                    if($b_koutei_sekkei_model_start !== ""){    //設計モデル作成 開始
                        $b_koutei_sekkei_model_start = $this->checkDateFormat($b_koutei_sekkei_model_start);
                    }

                    $b_koutei_sekkei_model_end = $this->getValue($row,$headers,$h_b_koutei_sekkei_model_end);
                    if($b_koutei_sekkei_model_end !== ""){      //設計モデル作成 完了
                        $b_koutei_sekkei_model_end = $this->checkDateFormat($b_koutei_sekkei_model_end);
                    }

                    $b_koutei_kakunin_sinsei_start = $this->getValue($row,$headers,$h_b_koutei_kakunin_sinsei_start);
                    if($b_koutei_kakunin_sinsei_start !== ""){  //確認申請 開始
                        $b_koutei_kakunin_sinsei_start = $this->checkDateFormat($b_koutei_kakunin_sinsei_start);
                    }

                    $b_koutei_kakunin_sinsei_end = $this->getValue($row,$headers,$h_b_koutei_kakunin_sinsei_end);
                    if($b_koutei_kakunin_sinsei_end !== ""){    //確認申請 完了
                        $b_koutei_kakunin_sinsei_end = $this->checkDateFormat($b_koutei_kakunin_sinsei_end);
                    }

                    $b_koutei_sekisan_model_tougou_start = $this->getValue($row,$headers,$h_b_koutei_sekisan_model_tougou_start);
                    if($b_koutei_sekisan_model_tougou_start !== ""){    //精算見積モデル統合 開始
                        $b_koutei_sekisan_model_tougou_start = $this->checkDateFormat($b_koutei_sekisan_model_tougou_start);
                    }

                    $b_koutei_sekisan_model_tougou_end = $this->getValue($row,$headers,$h_b_koutei_sekisan_model_tougou_end);
                    if($b_koutei_sekisan_model_tougou_end !== ""){      //精算見積モデル統合 完了
                        $b_koutei_sekisan_model_tougou_end = $this->checkDateFormat($b_koutei_sekisan_model_tougou_end);
                    }

                    $b_koutei_kouji_juujisya_kettei_start = $this->getValue($row,$headers,$h_b_koutei_kouji_juujisya_kettei_start);
                    if($b_koutei_kouji_juujisya_kettei_start !== ""){   //工事従事者決定 開始
                        $b_koutei_kouji_juujisya_kettei_start = $this->checkDateFormat($b_koutei_kouji_juujisya_kettei_start);
                    }

                    $b_koutei_kouji_juujisya_kettei_end = $this->getValue($row,$headers,$h_b_koutei_kouji_juujisya_kettei_end);
                    if($b_koutei_kouji_juujisya_kettei_end !== ""){     //工事従事者決定 完了
                        $b_koutei_kouji_juujisya_kettei_end = $this->checkDateFormat($b_koutei_kouji_juujisya_kettei_end);
                    }

                    $b_koutei_genba_koutei_kettei_start = $this->getValue($row,$headers,$h_b_koutei_genba_koutei_kettei_start);
                    if($b_koutei_genba_koutei_kettei_start !== ""){ //現場工程決定 開始
                        $b_koutei_genba_koutei_kettei_start = $this->checkDateFormat($b_koutei_genba_koutei_kettei_start);
                    }

                    $b_koutei_genba_koutei_kettei_end = $this->getValue($row,$headers,$h_b_koutei_genba_koutei_kettei_end);
                    if($b_koutei_genba_koutei_kettei_end !== ""){   //現場工程決定 完了
                        $b_koutei_genba_koutei_kettei_end = $this->checkDateFormat($b_koutei_genba_koutei_kettei_end);
                    }

                    $b_koutei_kouji_start = $this->getValue($row,$headers,$h_b_koutei_kouji_start);
                    if($b_koutei_kouji_start !== ""){   //工事 開始
                        $b_koutei_kouji_start = $this->checkDateFormat($b_koutei_kouji_start);
                    }

                    $b_koutei_kouji_end = $this->getValue($row,$headers,$h_b_koutei_kouji_end);
                    if($b_koutei_kouji_end !== ""){     //工事 完了
                        $b_koutei_kouji_end = $this->checkDateFormat($b_koutei_kouji_end);
                    }

                    $b_handover_start = $this->getValue($row,$headers,$h_b_handover_start);
                    if($b_handover_start !== ""){   //引渡し 開始
                        $b_handover_start = $this->checkDateFormat($b_handover_start);
                    }

                    $b_handover_end = $this->getValue($row,$headers,$h_b_handover_end);
                    if($b_handover_end !== ""){     //引渡し 完了
                        $b_handover_end = $this->checkDateFormat($b_handover_end);
                    }

                    $b_modeling_state   = $a_sekkei_state;  //モデリングState it is the same with a_sekkei_state
                    $b_bikou            = $this->getValue($row,$headers,$h_b_bikou);    //備考1
                    $c_bikou            = $this->getValue($row,$headers,$h_c_bikou);    //備考2
                    $c_order_status     = $this->getValue($row,$headers,$h_c_order_status);     //受注状況
                    $c_additional_item  = $this->getValue($row,$headers,$h_c_additional_item);  //追加項目

                    //******************************

                    $query = "INSERT INTO tb_allstore_info(id,a_pj_code,a_kouji_kikan_code,a_shiten,a_kakudo
                    ,a_pj_name,a_kouji_kubun,a_kouji_type,a_ukeoikin,a_youto1,a_youto2,a_sekou_basyo,a_sekkei_state
                    ,a_sekkei,a_kouzou,a_kaisuu,a_tijo,a_tika,a_ph,a_nobe_menseki,a_tyakkou,a_syunkou
                    ,b_pj_state,b_sekkei_state,b_sekou_state,b_jiyuu_kinyuu,b_pj_name,b_tmp_pj_name,b_shiten,b_kakudo
                    ,b_hattyuusya,b_sekkeisya1,b_sekkeisya2,b_sekou_basyo,b_youto
                    ,b_kouzou,b_kaisuu,b_tika,b_tijo,b_ph,b_nobe_menseki,b_kouji_jimusyo,b_syotyou
                    ,b_kouji_buka,b_kouji_katyou,b_eigyou_tantoubu,b_eigyou_tantousya
                    ,b_hattyuu_keitai_kentiku,b_hattyuu_keitai_setubi,b_yosou_koujihi
                    ,b_kakutei_ukeoikin,b_tubotanka,b_koutei_kihonsekkei_start,b_koutei_kihonsekkei_end
                    ,b_koutei_jissisekkei_start,b_koutei_jissisekkei_end
                    ,b_nyuusatu_jiki,b_nyuusatu_kettei_jiki
                    ,b_modeling_state,b_bikou,c_bikou,c_order_status,c_additional_item
                    ,b_tousuu
                    ,b_koutei_sekkei_model_start,b_koutei_sekkei_model_end
                    ,b_koutei_kakunin_sinsei_start,b_koutei_kakunin_sinsei_end
                    ,b_koutei_sekisan_model_tougou_start,b_koutei_sekisan_model_tougou_end
                    ,b_koutei_kouji_juujisya_kettei_start,b_koutei_kouji_juujisya_kettei_end
                    ,b_koutei_genba_koutei_kettei_start,b_koutei_genba_koutei_kettei_end
                    ,b_koutei_kouji_start,b_koutei_kouji_end
                    ,b_handover_start,b_handover_end
                    ,b_isyou_sekkei,b_isyou_model,b_isyou_syozoku,b_isyou_model_syozoku
                    ,b_kouzou_sekkei,b_kouzou_model,b_kouzou_syozoku,b_kouzou_model_syozoku
                    ,b_setubi_kuutyou_sekkei,b_setubi_kuutyou_model,b_setubi_kuutyou_syozoku,b_setubi_kuutyou_model_syozoku
                    ,b_setubi_eisei_sekkei,b_setubi_eisei_model,b_setubi_eisei_syozoku,b_setubi_eisei_model_syozoku
                    ,b_setubi_denki_sekkei,b_setubi_denki_model,b_setubi_denki_syozoku,b_setubi_denki_model_syozoku
                    ,b_ss_designer_name,b_ss_designer_dept,b_ss_modeler_name,b_ss_modeler_dept
                    ,b_sekou_tantou,b_sekou_syozoku
                    ,b_seisan_modeler_name,b_seisan_modeler_dept
                    ,b_seisan_gijutu_tantou,b_seisan_gijutu_syozoku
                    ,b_sekisan_mitumori_tantou,b_sekisan_mitumori_syozoku
                    ,b_bim_maneka_tantou,b_bim_maneka_syozoku
                    ,b_bim_coordinator_tantou,b_bim_coordinator_syozoku
                    ,b_ipd_center_tantou,b_ipd_center_syozoku
                    ,b_partner_company,b_partner_company_dept
                    ,b_bim_m,b_bim_manager_dept)
                    SELECT COALESCE(MAX(id), 0) + 1,'$a_pj_code','$a_kouji_kikan_code','$a_shiten','$a_kakudo',
                    '$a_pj_name','$a_kouji_kubun','$a_kouji_type','$a_ukeoikin','$a_youto1','$a_youto2','$a_sekou_basyo','$a_sekkei_state',
                    '$a_sekkei','$a_kouzou','$a_kaisuu','$a_tijo','$a_tika','$a_ph','$a_nobe_menseki','$a_tyakkou','$a_syunkou',
                    '$b_pj_state','$b_sekkei_state','$b_sekou_state','$b_jiyuu_kinyuu','$b_pj_name','$b_tmp_pj_name','$b_shiten','$b_kakudo',
                    '$b_hattyuusya','$b_sekkeisya1','$b_sekkeisya2','$b_sekou_basyo','$b_youto',
                    '$b_kouzou','$b_kaisuu','$b_tika','$b_tijo','$b_ph','$b_nobe_menseki','$b_kouji_jimusyo','$b_syotyou',
                    '$b_kouji_buka','$b_kouji_katyou','$b_eigyou_tantoubu','$b_eigyou_tantousya',
                    '$b_hattyuu_keitai_kentiku','$b_hattyuu_keitai_setubi','$b_yosou_koujihi',
                    '$b_kakutei_ukeoikin','$b_tubotanka','$b_koutei_kihonsekkei_start','$b_koutei_kihonsekkei_end',
                    '$b_koutei_jissisekkei_start','$b_koutei_jissisekkei_end',
                    '$b_nyuusatu_jiki','$b_nyuusatu_kettei_jiki',
                    '$b_modeling_state','$b_bikou','$c_bikou','$c_order_status','$c_additional_item',
                    '$b_tousuu',
                    '$b_koutei_sekkei_model_start','$b_koutei_sekkei_model_end',
                    '$b_koutei_kakunin_sinsei_start','$b_koutei_kakunin_sinsei_end',
                    '$b_koutei_sekisan_model_tougou_start','$b_koutei_sekisan_model_tougou_end',
                    '$b_koutei_kouji_juujisya_kettei_start','$b_koutei_kouji_juujisya_kettei_end',
                    '$b_koutei_genba_koutei_kettei_start','$b_koutei_genba_koutei_kettei_end',
                    '$b_koutei_kouji_start','$b_koutei_kouji_end',
                    '$b_handover_start','$b_handover_end',
                    '$b_isyou_sekkei','$b_isyou_model','$b_isyou_syozoku','$b_isyou_model_syozoku',
                    '$b_kouzou_sekkei','$b_kouzou_model','$b_kouzou_syozoku','$b_kouzou_model_syozoku',
                    '$b_setubi_kuutyou_sekkei','$b_setubi_kuutyou_model','$b_setubi_kuutyou_syozoku','$b_setubi_kuutyou_model_syozoku',
                    '$b_setubi_eisei_sekkei','$b_setubi_eisei_model','$b_setubi_eisei_syozoku','$b_setubi_eisei_model_syozoku',
                    '$b_setubi_denki_sekkei','$b_setubi_denki_model','$b_setubi_denki_syozoku','$b_setubi_denki_model_syozoku',
                    '$b_ss_designer_name','$b_ss_designer_dept','$b_ss_modeler_name','$b_ss_modeler_dept',
                    '$b_sekou_tantou','$b_sekou_syozoku',
                    '$b_seisan_modeler_name','$b_seisan_modeler_dept',
                    '$b_seisan_gijutu_tantou','$b_seisan_gijutu_syozoku',
                    '$b_sekisan_mitumori_tantou','$b_sekisan_mitumori_syozoku',
                    '$b_bim_maneka_tantou','$b_bim_maneka_syozoku',
                    '$b_bim_coordinator_tantou','$b_bim_coordinator_syozoku',
                    '$b_ipd_center_tantou','$b_ipd_center_syozoku',
                    '$b_partner_company','$b_partner_company_dept',
                    '$b_bim_m','$b_bim_manager_dept' FROM tb_allstore_info
                    ON DUPLICATE KEY UPDATE a_kouji_kikan_code = '$a_kouji_kikan_code',a_shiten = '$a_shiten',a_kakudo = '$a_kakudo',
                    a_pj_name = '$a_pj_name',a_kouji_kubun = '$a_kouji_kubun',a_kouji_type = '$a_kouji_type',a_ukeoikin = '$a_ukeoikin',a_youto1 = '$a_youto1',a_youto2 = '$a_youto2',a_sekou_basyo = '$a_sekou_basyo',a_sekkei_state = '$a_sekkei_state',
                    a_sekkei = '$a_sekkei',a_kouzou = '$a_kouzou',a_kaisuu = '$a_kaisuu',a_tijo = '$a_tijo',a_tika = '$a_tika',a_ph = '$a_ph',a_nobe_menseki = '$a_nobe_menseki',a_tyakkou = '$a_tyakkou',a_syunkou = '$a_syunkou',
                    b_pj_state = '$b_pj_state',b_sekkei_state = '$b_sekkei_state',b_sekou_state = '$b_sekou_state',b_jiyuu_kinyuu = '$b_jiyuu_kinyuu',b_pj_name = '$b_pj_name',b_tmp_pj_name = '$b_tmp_pj_name',b_shiten = '$b_shiten',b_kakudo = '$b_kakudo',
                    b_hattyuusya = '$b_hattyuusya',b_sekkeisya1 = '$b_sekkeisya1',b_sekkeisya2 = '$b_sekkeisya2',b_sekou_basyo = '$b_sekou_basyo',b_youto = '$b_youto',
                    b_kouzou = '$b_kouzou',b_kaisuu = '$b_kaisuu',b_tika = '$b_tika',b_tijo = '$b_tijo',b_ph = '$b_ph',b_nobe_menseki = '$b_nobe_menseki',b_kouji_jimusyo = '$b_kouji_jimusyo',b_syotyou = '$b_syotyou',
                    b_kouji_buka = '$b_kouji_buka',b_kouji_katyou = '$b_kouji_katyou',b_eigyou_tantoubu = '$b_eigyou_tantoubu',b_eigyou_tantousya = '$b_eigyou_tantousya',
                    b_hattyuu_keitai_kentiku = '$b_hattyuu_keitai_kentiku',b_hattyuu_keitai_setubi = '$b_hattyuu_keitai_setubi',b_yosou_koujihi = '$b_yosou_koujihi',
                    b_kakutei_ukeoikin = '$b_kakutei_ukeoikin',b_tubotanka = '$b_tubotanka',b_koutei_kihonsekkei_start = '$b_koutei_kihonsekkei_start',b_koutei_kihonsekkei_end = '$b_koutei_kihonsekkei_end',
                    b_koutei_jissisekkei_start = '$b_koutei_jissisekkei_start',b_koutei_jissisekkei_end = '$b_koutei_jissisekkei_end',
                    b_nyuusatu_jiki = '$b_nyuusatu_jiki',b_nyuusatu_kettei_jiki = '$b_nyuusatu_kettei_jiki',
                    b_modeling_state = '$b_modeling_state',b_bikou = '$b_bikou',c_bikou = '$c_bikou',c_order_status = '$c_order_status',c_additional_item = '$c_additional_item',
                    b_tousuu = '$b_tousuu',
                    b_koutei_sekkei_model_start = '$b_koutei_sekkei_model_start',b_koutei_sekkei_model_end = '$b_koutei_sekkei_model_end',
                    b_koutei_kakunin_sinsei_start = '$b_koutei_kakunin_sinsei_start',b_koutei_kakunin_sinsei_end = '$b_koutei_kakunin_sinsei_end',
                    b_koutei_sekisan_model_tougou_start = '$b_koutei_sekisan_model_tougou_start',b_koutei_sekisan_model_tougou_end = '$b_koutei_sekisan_model_tougou_end',
                    b_koutei_kouji_juujisya_kettei_start = '$b_koutei_kouji_juujisya_kettei_start',b_koutei_kouji_juujisya_kettei_end = '$b_koutei_kouji_juujisya_kettei_end',
                    b_koutei_genba_koutei_kettei_start = '$b_koutei_genba_koutei_kettei_start',b_koutei_genba_koutei_kettei_end = '$b_koutei_genba_koutei_kettei_end',
                    b_koutei_kouji_start = '$b_koutei_kouji_start',b_koutei_kouji_end = '$b_koutei_kouji_end',
                    b_handover_start = '$b_handover_start',b_handover_end = '$b_handover_end',
                    b_isyou_sekkei = '$b_isyou_sekkei',b_isyou_model = '$b_isyou_model',b_isyou_syozoku = '$b_isyou_syozoku',b_isyou_model_syozoku = '$b_isyou_model_syozoku',
                    b_kouzou_sekkei = '$b_kouzou_sekkei',b_kouzou_model = '$b_kouzou_model',b_kouzou_syozoku = '$b_kouzou_syozoku',b_kouzou_model_syozoku = '$b_kouzou_model_syozoku',
                    b_setubi_kuutyou_sekkei = '$b_setubi_kuutyou_sekkei',b_setubi_kuutyou_model = '$b_setubi_kuutyou_model',b_setubi_kuutyou_syozoku = '$b_setubi_kuutyou_syozoku',b_setubi_kuutyou_model_syozoku = '$b_setubi_kuutyou_model_syozoku',
                    b_setubi_eisei_sekkei = '$b_setubi_eisei_sekkei',b_setubi_eisei_model = '$b_setubi_eisei_model',b_setubi_eisei_syozoku = '$b_setubi_eisei_syozoku',b_setubi_eisei_model_syozoku = '$b_setubi_eisei_model_syozoku',
                    b_setubi_denki_sekkei = '$b_setubi_denki_sekkei',b_setubi_denki_model = '$b_setubi_denki_model',b_setubi_denki_syozoku = '$b_setubi_denki_syozoku',b_setubi_denki_model_syozoku = '$b_setubi_denki_model_syozoku',
                    b_ss_designer_name = '$b_ss_designer_name',b_ss_designer_dept = '$b_ss_designer_dept',b_ss_modeler_name = '$b_ss_modeler_name',b_ss_modeler_dept = '$b_ss_modeler_dept',
                    b_sekou_tantou = '$b_sekou_tantou',b_sekou_syozoku = '$b_sekou_syozoku',
                    b_seisan_modeler_name = '$b_seisan_modeler_name',b_seisan_modeler_dept = '$b_seisan_modeler_dept',
                    b_seisan_gijutu_tantou = '$b_seisan_gijutu_tantou',b_seisan_gijutu_syozoku = '$b_seisan_gijutu_syozoku',
                    b_sekisan_mitumori_tantou = '$b_sekisan_mitumori_tantou',b_sekisan_mitumori_syozoku = '$b_sekisan_mitumori_syozoku',
                    b_bim_maneka_tantou = '$b_bim_maneka_tantou',b_bim_maneka_syozoku = '$b_bim_maneka_syozoku',
                    b_bim_coordinator_tantou = '$b_bim_coordinator_tantou',b_bim_coordinator_syozoku = '$b_bim_coordinator_syozoku',
                    b_ipd_center_tantou = '$b_ipd_center_tantou',b_ipd_center_syozoku = '$b_ipd_center_syozoku',
                    b_partner_company = '$b_partner_company',b_partner_company_dept = '$b_partner_company_dept',
                    b_bim_m = '$b_bim_m',b_bim_manager_dept = '$b_bim_manager_dept'";
            //   return $query;
            // echo $query;return;
                    DB::insert($query);
                
                
                }catch(Exception $e){
                    return null;
                   //return $e->getMessage();
                }
            
            }//inner for looping end
            
        }

    }

    function checkDateFormat($dateString){
        
        $ret = "";
        $tmpDateStr = trim($dateString);
        $strLen = strlen($tmpDateStr);
        
        if($strLen == 8){
            $ret = $tmpDateStr;
        }else if($strLen == 6){
            $ret = $tmpDateStr . "01";
        }else if($strLen == 4){
            $ret = $tmpDateStr . "0101";
        }else{
            //NOP
        }
        
        return $ret;
    }
    
    function convertDateFormatIncludesDot($dateString){
            
        $tmpYear = "";
        $tmpMonth = "";
        $tmpDay = "";

        $ary_sekkei_start = explode(".",$dateString);
        
        if(count($ary_sekkei_start) === 3){
            
            $tmpYear = $ary_sekkei_start[0];
            $tmpMonth = $ary_sekkei_start[1];
            $tmpDay = $ary_sekkei_start[2];

            if($tmpMonth[0] === "0"){
                $tmpMonth[1] = ltrim($tmpMonth[1], "0");
            }
            
            if($tmpDay === ""){
                $tmpDay = "1";
            }else{
                if(count($tmpDay) !== 1 && $tmpDay[0] === "0"){
                    $tmpDay = ltrim($tmpDay, "0");
                }
            }
            
            $tmp_sekkei_start = $tmpYear . "." . $tmpMonth . "." .  $tmpDay;
            
        }else if(count($ary_sekkei_start) === 2){

            $tmpYear = $ary_sekkei_start[0];
            $tmpMonth = $ary_sekkei_start[1];
            $tmpDay = 1;
            
            if($tmpMonth === ""){
                $tmpMonth = "1";
            }else{
                if(count($tmpMonth) !== 1 && $tmpMonth[0] === "0"){
                    $tmpMonth = ltrim($tmpMonth, "0");
                }
            }
            
            $tmp_sekkei_start = $tmpYear . "." . $tmpMonth . "." .  $tmpDay;
            
        }else if(count($ary_sekkei_start) === 1){

            $tmpYear = $ary_sekkei_start[0];
            $tmpMonth = 1;
            $tmpDay = 1;
            
            if($tmpYear === "" || $tmpYear === "-"){
                $tmp_sekkei_start = "";
            }else{
                $tmp_sekkei_start = $tmpYear . "." . $tmpMonth . "." .  $tmpDay;
            }
           
        }else{
            //if '.' is not included, do nothing
            $tmp_sekkei_start = $dateString;
        }
        
        return $tmp_sekkei_start;
    }
    
    function PrepareHeader($main_header,$sub_header,$headerType){
        $headers=array();
        
        for($i = 0; $i < sizeof($main_header); $i++){
            $main = $main_header[$i];
            $sub = $sub_header[$i];
            // if($main != ''){
            //         $main = $main." ".$sub;
            // }else{
                
                if($headerType == 0){
                    $pre_index = $i;
                    $pre_header = "";
                    do{
                        $pre_header = $main_header[--$pre_index];
                    }while( $pre_header == "");
                    $main = $pre_header." ".$sub;
                }else{
                    $main = $sub;
                }
            // }
           
            $headers[$i] = $main;
        }
        
        return $headers;
    }
    
    function getValue($row,$headers,$title){
        $result="";
        foreach($headers as $key=>$h){
            $str = str_replace(array("\r", "\n"), '', $h);
            if(str_contains(trim($str),trim($title))){
                //  if($title == "確度"){//there are two items with the same header,first is skip
                //      if($key <= 21)continue;
                //  }
                //  if($title == "意匠設計 設計担当" || $title == "意匠設計 モデル担当" ||$title == "構造設計 設計担当" ||$title == "構造設計 モデル担当"){//there are two items with the same header,first is skip
                //      if($key < 55)continue;
                //  }
                 
                 return $result = $row[$key];
            }
        }
        return $result;
    }

    function PrepareHeader_old($main_header,$sub_header){
        $headers=array();
        for($i = 0; $i < sizeof($main_header); $i++){
            $main = $main_header[$i];
            $sub = $sub_header[$i];
            if($main != ''){
                    $main = $main." ".$sub;
            }else{
                $pre_index = $i;
                $pre_header = "";
                do{
                    $pre_header = $main_header[--$pre_index];
                }while( $pre_header == "");
                $main = $pre_header." ".$sub;
            }
           
            $headers[$i] = $main;
        }
        
        return $headers;
    }

    function getValueOld($row,$headers,$title){
        $result="";
        foreach($headers as $key=>$h){
            $str = str_replace(array("\r", "\n"), '', $h);
            if(str_contains(trim($str),trim($title))){
                 if($title == "確度"){//there are two items with the same header,first is skip
                     if($key <= 21)continue;
                 }
                 if($title == "意匠設計 設計担当" || $title == "意匠設計 モデル担当" ||$title == "構造設計 設計担当" ||$title == "構造設計 モデル担当"){//there are two items with the same header,first is skip
                     if($key < 55)continue;
                 }
                 
                 return $result = $row[$key];
            }
        }
        return $result;
    }

}
