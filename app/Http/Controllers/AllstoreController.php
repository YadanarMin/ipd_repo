<?php

namespace App\Http\Controllers;
// use App\Models\ForgeModel;
use App\Models\LoginModel;
use App\Models\AllStoreModel;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPExcel;
use PHPExcel_IOFactory;
use ZipArchive;

class AllstoreController extends Controller 
{
    function index()
    {
        return view('allstore');
    }
    
    function SaveData(Request $request)
    {
        $groupA = $request->get('storeDataA');
        $groupB = $request->get('storeDataB');

        $a_pj_code      = isset($groupA[0]) ? $groupA[0] : "";
        $a_kouji_kikan_code = isset($groupA[1]) ? $groupA[1] : "";
        $a_shiten       = isset($groupA[2]) ? $groupA[2] : "";
        $a_kakudo       = isset($groupA[3]) ? $groupA[3] : "";
        $a_pj_name      = isset($groupA[4]) ? $groupA[4] : "";
        $a_kouji_kubun  = isset($groupA[5]) ? $groupA[5] : "";
        $a_ukeoikin     = isset($groupA[6]) ? $groupA[6] : "";
        $a_youto1       = isset($groupA[7]) ? $groupA[7] : "";
        $a_youto2       = isset($groupA[8]) ? $groupA[8] : "";
        $a_sekou_basyo  = isset($groupA[9]) ? $groupA[9] : "";
        $a_sekkei_state = isset($groupA[10]) ? $groupA[10] : "";
        $a_sekkei       = isset($groupA[11]) ? $groupA[11] : "";
        $a_kouzou       = isset($groupA[12]) ? $groupA[12] : "";
        $a_kaisuu       = isset($groupA[13]) ? $groupA[13] : "";
        $a_tijo         = isset($groupA[14]) ? $groupA[14] : "";
        $a_tika         = isset($groupA[15]) ? $groupA[15] : "";
        $a_ph           = isset($groupA[16]) ? $groupA[16] : "";
        $a_nobe_menseki = isset($groupA[17]) ? $groupA[17] : "";
        $a_tyakkou      = isset($groupA[18]) ? $groupA[18] : "";
        $a_syunkou      = isset($groupA[19]) ? $groupA[19] : "";
        
        $b_pj_state     = isset($groupB[0]) ? $groupB[0] : "";
        $b_sekkei_state = isset($groupB[1]) ? $groupB[1] : "";
        $b_sekou_state  = isset($groupB[2]) ? $groupB[2] : "";
        $b_jiyuu_kinyuu = isset($groupB[3]) ? $groupB[3] : "";
        $b_pj_name      = isset($groupB[4]) ? $groupB[4] : "";
        $b_kakudo       = isset($groupB[5]) ? $groupB[5] : "";
        $b_hattyuusya   = isset($groupB[6]) ? $groupB[6] : "";
        $b_sekkeisya1   = isset($groupB[7]) ? $groupB[7] : "";
        $b_sekkeisya2   = isset($groupB[8]) ? $groupB[8] : "";
        $b_sekou_basyo  = isset($groupB[9]) ? $groupB[9] : "";
        $b_tyakkou      = isset($groupB[10]) ? $groupB[10] : "";
        $b_syunkou      = isset($groupB[11]) ? $groupB[11] : "";
        $b_youto        = isset($groupB[12]) ? $groupB[12] : "";
        $b_kouzou       = isset($groupB[13]) ? $groupB[13] : "";
        $b_kaisuu       = isset($groupB[14]) ? $groupB[14] : "";
        $b_tika         = isset($groupB[15]) ? $groupB[15] : "";
        $b_tijo         = isset($groupB[16]) ? $groupB[16] : "";
        $b_ph           = isset($groupB[17]) ? $groupB[17] : "";
        $b_nobe_menseki = isset($groupB[18]) ? $groupB[18] : "";
        $b_kouji_jimusyo= isset($groupB[19]) ? $groupB[19] : "";
        $b_syotyou      = isset($groupB[20]) ? $groupB[20] : "";
        $b_kouji_buka   = isset($groupB[21]) ? $groupB[21] : "";
        $b_kouji_katyou = isset($groupB[22]) ? $groupB[22] : "";
        $b_eigyou_tantoubu  = isset($groupB[23]) ? $groupB[23] : "";
        $b_eigyou_tantousya = isset($groupB[24]) ? $groupB[24] : "";

        $b_hattyuu_keitai_kentiku   = isset($groupB[25]) ? $groupB[25] : "";
        $b_hattyuu_keitai_setubi    = isset($groupB[26]) ? $groupB[26] : "";
        $b_yosou_koujihi    = isset($groupB[27]) ? $groupB[27] : "";
        $b_kakutei_ukeoikin = isset($groupB[28]) ? $groupB[28] : "";
        $b_tubotanka        = isset($groupB[29]) ? $groupB[29] : "";
        
        $b_koutei_kihonsekkei_start = "";
        $b_koutei_kihonsekkei_end = "";
        $tmp_b_kihonsekkei  = isset($groupB[30]) ? $groupB[30] : "";
        if(strpos($tmp_b_kihonsekkei,'~') !== false){
            $tmp = explode("~",$tmp_b_kihonsekkei);
            $b_koutei_kihonsekkei_start = $tmp[0];
            $b_koutei_kihonsekkei_end = $tmp[1];
        }else if(strpos($tmp_b_kihonsekkei,'～') !== false){
            $tmp = explode("～",$tmp_b_kihonsekkei);
            $b_koutei_kihonsekkei_start = $tmp[0];
            $b_koutei_kihonsekkei_end = $tmp[1];
        }else{
            $b_koutei_kihonsekkei_start = $tmp_b_kihonsekkei;
        }
        
        $b_koutei_jissisekkei_start = "";
        $b_koutei_jissisekkei_end = "";
        $tmp_b_jissisekkei  = isset($groupB[31]) ? $groupB[31] : "";
        if(strpos($tmp_b_jissisekkei,'~') !== false){
            $tmp = explode("~",$tmp_b_jissisekkei);
            $b_koutei_jissisekkei_start = $tmp[0];
            $b_koutei_jissisekkei_end = $tmp[1];
        }else if(strpos($tmp_b_jissisekkei,'～') !== false){
            $tmp = explode("～",$tmp_b_jissisekkei);
            $b_koutei_jissisekkei_start = $tmp[0];
            $b_koutei_jissisekkei_end = $tmp[1];
        }else{
            $b_koutei_jissisekkei_start = $tmp_b_jissisekkei;
        }

        $b_nyuusatu_jiki    = isset($groupB[32]) ? $groupB[32] : "";
        $b_nyuusatu_kettei_jiki = isset($groupB[33]) ? $groupB[33] : "";
        $b_ipd_tantoubuka   = isset($groupB[34]) ? $groupB[34] : "";
        $b_ipd_tantousya    = isset($groupB[35]) ? $groupB[35] : "";
        $b_modeling_state   = isset($groupB[36]) ? $groupB[36] : "";
        $b_modeling_kentiku = isset($groupB[37]) ? $groupB[37] : "";
        $b_modeling_kouzou  = isset($groupB[38]) ? $groupB[38] : "";
        $b_modeling_setubi  = isset($groupB[39]) ? $groupB[39] : "";
        $b_modeling_seisansekkei = isset($groupB[40]) ? $groupB[40] : "";
        $b_modeling_seisan  = isset($groupB[41]) ? $groupB[41] : "";
        $b_bikou            = isset($groupB[42]) ? $groupB[42] : "";
        $b_tousuu           = isset($groupB[43]) ? $groupB[43] : "";

        $tmp_sekkei_start = "";
        if(isset($groupB[44])){ 
            $tmp_sekkei_start = $this->convertDateFormat($groupB[44]);
        }
        $b_koutei_sekkei_model_start = $tmp_sekkei_start;
        
        $tmp_sekkei_end = "";
        if(isset($groupB[45])){ 
            $tmp_sekkei_end = $this->convertDateFormat($groupB[45]);
        }
        $b_koutei_sekkei_model_end = $tmp_sekkei_end;
        
        $tmp_kakunin_start = "";
        if(isset($groupB[46])){ 
            $tmp_kakunin_start = $this->convertDateFormat($groupB[46]);
        }
        $b_koutei_kakunin_sinsei_start = $tmp_kakunin_start;
        
        $tmp_kakunin_end = "";
        if(isset($groupB[47])){ 
            $tmp_kakunin_end = $this->convertDateFormat($groupB[47]);
        }
        $b_koutei_kakunin_sinsei_end = $tmp_kakunin_end;
        
        $tmp_sekisan_start = "";
        if(isset($groupB[48])){ 
            $tmp_sekisan_start = $this->convertDateFormat($groupB[48]);
        }
        $b_koutei_sekisan_model_tougou_start = $tmp_sekisan_start;
        
        $tmp_sekisan_end = "";
        if(isset($groupB[49])){ 
            $tmp_sekisan_end = $this->convertDateFormat($groupB[49]);
        }
        $b_koutei_sekisan_model_tougou_end = $tmp_sekisan_end;
        
        $tmp_kouji_start = "";
        if(isset($groupB[50])){ 
            $tmp_kouji_start = $this->convertDateFormat($groupB[50]);
        }
        $b_koutei_kouji_juujisya_kettei_start = $tmp_kouji_start;
        
        $tmp_kouji_end = "";
        if(isset($groupB[51])){ 
            $tmp_kouji_end = $this->convertDateFormat($groupB[51]);
        }
        $b_koutei_kouji_juujisya_kettei_end = $tmp_kouji_end;
        
        $tmp_genba_start = "";
        if(isset($groupB[52])){ 
            $tmp_genba_start = $this->convertDateFormat($groupB[52]);
        }
        $b_koutei_genba_koutei_kettei_start = $tmp_genba_start;
        
        $tmp_genba_end = "";
        if(isset($groupB[53])){ 
            $tmp_genba_end = $this->convertDateFormat($groupB[53]);
        }
        $b_koutei_genba_koutei_kettei_end = $tmp_genba_end;
        
        $tmp_kouji_start = "";
        if(isset($groupB[54])){ 
            $tmp_kouji_start = $this->convertDateFormat($groupB[54]);
        }
        $b_koutei_kouji_start = $tmp_kouji_start;
        
        $tmp_kouji_end = "";
        if(isset($groupB[55])){ 
            $tmp_kouji_end = $this->convertDateFormat($groupB[55]);
        }
        $b_koutei_kouji_end = $tmp_kouji_end;

        $b_isyou_sekkei = isset($groupB[56]) ? $groupB[56] : "";
        $b_isyou_model  = isset($groupB[57]) ? $groupB[57] : "";
        $b_isyou_syozoku  = isset($groupB[58]) ? $groupB[58] : "";
        $b_kouzou_sekkei= isset($groupB[59]) ? $groupB[59] : "";
        $b_kouzou_model = isset($groupB[60]) ? $groupB[60] : "";
        $b_kouzou_syozoku= isset($groupB[61]) ? $groupB[61] : "";
        $b_setubi_kuutyou_sekkei = isset($groupB[62]) ? $groupB[62] : "";
        $b_setubi_kuutyou_model = isset($groupB[63]) ? $groupB[63] : "";
        $b_setubi_kuutyou_syozoku = isset($groupB[64]) ? $groupB[64] : "";
        
        $b_setubi_eisei_sekkei = isset($groupB[65]) ? $groupB[65] : "";
        $b_setubi_eisei_model = isset($groupB[66]) ? $groupB[66] : "";
        $b_setubi_eisei_syozoku = isset($groupB[67]) ? $groupB[67] : "";
        
        $b_setubi_denki_sekkei = isset($groupB[68]) ? $groupB[68] : "";
        $b_setubi_denki_model = isset($groupB[69]) ? $groupB[69] : "";
        $b_setubi_denki_syozoku = isset($groupB[70]) ? $groupB[70] : "";
        $b_sekou_tantou = isset($groupB[71]) ? $groupB[71] : "";
        $b_sekou_syozoku = isset($groupB[72]) ? $groupB[72] : "";
        $b_seisan_sekkei_tantou = isset($groupB[73]) ? $groupB[73] : "";
        $b_seisan_sekkei_syozoku = isset($groupB[74]) ? $groupB[74] : "";
        $b_seisan_gijutu_tantou = isset($groupB[75]) ? $groupB[75] : "";
        $b_seisan_gijutu_syozoku = isset($groupB[76]) ? $groupB[76] : "";
        $b_sekisan_mitumori_tantou = isset($groupB[77]) ? $groupB[77] : "";
        $b_sekisan_mitumori_syozoku = isset($groupB[78]) ? $groupB[78] : "";
        $b_bim_maneka_tantou = isset($groupB[79]) ? $groupB[79] : "";
        $b_bim_maneka_syozoku = isset($groupB[80]) ? $groupB[80] : "";
        $b_bim_coordinator_tantou = isset($groupB[81]) ? $groupB[81] : "";
        $b_bim_coordinator_syozoku = isset($groupB[82]) ? $groupB[82] : "";
        $b_ipd_center_tantou = isset($groupB[83]) ? $groupB[83] : "";
        $b_ipd_center_syozoku = isset($groupB[84]) ? $groupB[84] : "";
        $b_koujibu_tantou = isset($groupB[85]) ? $groupB[85] : "";
        $b_koujibu_syozoku = isset($groupB[86]) ? $groupB[86] : "";
        
        $b_partner_company = isset($groupB[87]) ? $groupB[87] : "";
        $b_bim_m = isset($groupB[88]) ? $groupB[88] : "";
        $b_sekousya = isset($groupB[89]) ? $groupB[89] : "";
        
        $b_tmp_pj_name = isset($groupB[90]) ? $groupB[90] : "";

        $query = "INSERT INTO tb_allstore_info(id,a_pj_code,a_kouji_kikan_code,a_shiten,a_kakudo
                ,a_pj_name,a_kouji_kubun,a_ukeoikin,a_youto1,a_youto2,a_sekou_basyo,a_sekkei_state
                ,a_sekkei,a_kouzou,a_kaisuu,a_tijo,a_tika,a_ph,a_nobe_menseki,a_tyakkou,a_syunkou
                ,b_pj_state,b_sekkei_state,b_sekou_state,b_jiyuu_kinyuu,b_pj_name,b_kakudo
                ,b_hattyuusya,b_sekkeisya1,b_sekkeisya2,b_sekou_basyo,b_tyakkou,b_syunkou,b_youto
                ,b_kouzou,b_kaisuu,b_tika,b_tijo,b_ph,b_nobe_menseki,b_kouji_jimusyo,b_syotyou
                ,b_kouji_buka,b_kouji_katyou,b_eigyou_tantoubu,b_eigyou_tantousya
                ,b_hattyuu_keitai_kentiku,b_hattyuu_keitai_setubi,b_yosou_koujihi
                ,b_kakutei_ukeoikin,b_tubotanka,b_koutei_kihonsekkei_start,b_koutei_kihonsekkei_end
                ,b_koutei_jissisekkei_start,b_koutei_jissisekkei_end,b_nyuusatu_jiki
                ,b_nyuusatu_kettei_jiki,b_ipd_tantoubuka,b_ipd_tantousya,b_modeling_state
                ,b_modeling_kentiku,b_modeling_kouzou,b_modeling_setubi,b_modeling_seisansekkei
                ,b_modeling_seisan,b_bikou,b_tousuu,b_koutei_sekkei_model_start
                ,b_koutei_sekkei_model_end,b_koutei_kakunin_sinsei_start,b_koutei_kakunin_sinsei_end
                ,b_koutei_sekisan_model_tougou_start,b_koutei_sekisan_model_tougou_end
                ,b_koutei_kouji_juujisya_kettei_start,b_koutei_kouji_juujisya_kettei_end
                ,b_koutei_genba_koutei_kettei_start,b_koutei_genba_koutei_kettei_end
                ,b_koutei_kouji_start,b_koutei_kouji_end
                ,b_isyou_sekkei,b_isyou_model,b_isyou_syozoku,b_kouzou_sekkei,b_kouzou_model,b_kouzou_syozoku
                ,b_setubi_kuutyou_sekkei,b_setubi_kuutyou_model,b_setubi_kuutyou_syozoku
                
                ,b_setubi_eisei_sekkei,b_setubi_eisei_model,b_setubi_eisei_syozoku
                
                ,b_setubi_denki_sekkei,b_setubi_denki_model,b_setubi_denki_syozoku
                ,b_sekou_tantou,b_sekou_syozoku,b_seisan_sekkei_tantou,b_seisan_sekkei_syozoku
                ,b_seisan_gijutu_tantou,b_seisan_gijutu_syozoku,b_sekisan_mitumori_tantou
                ,b_sekisan_mitumori_syozoku,b_bim_maneka_tantou,b_bim_maneka_syozoku
                ,b_bim_coordinator_tantou,b_bim_coordinator_syozoku,b_ipd_center_tantou
                ,b_ipd_center_syozoku,b_koujibu_tantou,b_koujibu_syozoku
                
                ,b_partner_company,b_bim_m,b_sekousya
                
                ,b_tmp_pj_name)
                SELECT COALESCE(MAX(id), 0) + 1,'$a_pj_code','$a_kouji_kikan_code','$a_shiten','$a_kakudo',
                '$a_pj_name','$a_kouji_kubun','$a_ukeoikin','$a_youto1','$a_youto2','$a_sekou_basyo','$a_sekkei_state',
                '$a_sekkei','$a_kouzou','$a_kaisuu','$a_tijo','$a_tika','$a_ph','$a_nobe_menseki','$a_tyakkou','$a_syunkou',
                '$b_pj_state','$b_sekkei_state','$b_sekou_state','$b_jiyuu_kinyuu','$b_pj_name','$b_kakudo',
                '$b_hattyuusya','$b_sekkeisya1','$b_sekkeisya2','$b_sekou_basyo','$b_tyakkou','$b_syunkou','$b_youto',
                '$b_kouzou','$b_kaisuu','$b_tika','$b_tijo','$b_ph','$b_nobe_menseki','$b_kouji_jimusyo','$b_syotyou',
                '$b_kouji_buka','$b_kouji_katyou','$b_eigyou_tantoubu','$b_eigyou_tantousya',
                '$b_hattyuu_keitai_kentiku','$b_hattyuu_keitai_setubi','$b_yosou_koujihi',
                '$b_kakutei_ukeoikin','$b_tubotanka','$b_koutei_kihonsekkei_start','$b_koutei_kihonsekkei_end',
                '$b_koutei_jissisekkei_start','$b_koutei_jissisekkei_end','$b_nyuusatu_jiki',
                '$b_nyuusatu_kettei_jiki','$b_ipd_tantoubuka','$b_ipd_tantousya','$b_modeling_state',
                '$b_modeling_kentiku','$b_modeling_kouzou','$b_modeling_setubi','$b_modeling_seisansekkei',
                '$b_modeling_seisan','$b_bikou','$b_tousuu','$b_koutei_sekkei_model_start',
                '$b_koutei_sekkei_model_end','$b_koutei_kakunin_sinsei_start','$b_koutei_kakunin_sinsei_end',
                '$b_koutei_sekisan_model_tougou_start','$b_koutei_sekisan_model_tougou_end',
                '$b_koutei_kouji_juujisya_kettei_start','$b_koutei_kouji_juujisya_kettei_end',
                '$b_koutei_genba_koutei_kettei_start','$b_koutei_genba_koutei_kettei_end',
                '$b_koutei_kouji_start','$b_koutei_kouji_end',
                '$b_isyou_sekkei','$b_isyou_model','$b_isyou_syozoku','$b_kouzou_sekkei','$b_kouzou_model','$b_kouzou_syozoku',
                '$b_setubi_kuutyou_sekkei','$b_setubi_kuutyou_model','$b_setubi_kuutyou_syozoku',
                
                '$b_setubi_eisei_sekkei','$b_setubi_eisei_model','$b_setubi_eisei_syozoku',
                
                '$b_setubi_denki_sekkei','$b_setubi_denki_model','$b_setubi_denki_syozoku',
                '$b_sekou_tantou','$b_sekou_syozoku','$b_seisan_sekkei_tantou','$b_seisan_sekkei_syozoku',
                '$b_seisan_gijutu_tantou','$b_seisan_gijutu_syozoku','$b_sekisan_mitumori_tantou',
                '$b_sekisan_mitumori_syozoku','$b_bim_maneka_tantou','$b_bim_maneka_syozoku',
                '$b_bim_coordinator_tantou','$b_bim_coordinator_syozoku','$b_ipd_center_tantou',
                '$b_ipd_center_syozoku','$b_koujibu_tantou','$b_koujibu_syozoku',
                
                '$b_partner_company','$b_bim_m','$b_sekousya',
                
                '$b_tmp_pj_name' FROM tb_allstore_info
                ON DUPLICATE KEY UPDATE a_kouji_kikan_code = '$a_kouji_kikan_code',a_shiten = '$a_shiten',a_kakudo = '$a_kakudo',
                a_pj_name = '$a_pj_name',a_kouji_kubun = '$a_kouji_kubun',a_ukeoikin = '$a_ukeoikin',a_youto1 = '$a_youto1',a_youto2 = '$a_youto2',a_sekou_basyo = '$a_sekou_basyo',a_sekkei_state = '$a_sekkei_state',
                a_sekkei = '$a_sekkei',a_kouzou = '$a_kouzou',a_kaisuu = '$a_kaisuu',a_tijo = '$a_tijo',a_tika = '$a_tika',a_ph = '$a_ph',a_nobe_menseki = '$a_nobe_menseki',a_tyakkou = '$a_tyakkou',a_syunkou = '$a_syunkou',
                b_pj_state = '$b_pj_state',b_sekkei_state = '$b_sekkei_state',b_sekou_state = '$b_sekou_state',b_jiyuu_kinyuu = '$b_jiyuu_kinyuu',b_pj_name = '$b_pj_name',b_kakudo = '$b_kakudo',
                b_hattyuusya = '$b_hattyuusya',b_sekkeisya1 = '$b_sekkeisya1',b_sekkeisya2 = '$b_sekkeisya2',b_sekou_basyo = '$b_sekou_basyo',b_tyakkou = '$b_tyakkou',b_syunkou = '$b_syunkou',b_youto = '$b_youto',
                b_kouzou = '$b_kouzou',b_kaisuu = '$b_kaisuu',b_tika = '$b_tika',b_tijo = '$b_tijo',b_ph = '$b_ph',b_nobe_menseki = '$b_nobe_menseki',b_kouji_jimusyo = '$b_kouji_jimusyo',b_syotyou = '$b_syotyou',
                b_kouji_buka = '$b_kouji_buka',b_kouji_katyou = '$b_kouji_katyou',b_eigyou_tantoubu = '$b_eigyou_tantoubu',b_eigyou_tantousya = '$b_eigyou_tantousya',
                b_hattyuu_keitai_kentiku = '$b_hattyuu_keitai_kentiku',b_hattyuu_keitai_setubi = '$b_hattyuu_keitai_setubi',b_yosou_koujihi = '$b_yosou_koujihi',
                b_kakutei_ukeoikin = '$b_kakutei_ukeoikin',b_tubotanka = '$b_tubotanka',b_koutei_kihonsekkei_start = '$b_koutei_kihonsekkei_start',b_koutei_kihonsekkei_end = '$b_koutei_kihonsekkei_end',
                b_koutei_jissisekkei_start = '$b_koutei_jissisekkei_start',b_koutei_jissisekkei_end = '$b_koutei_jissisekkei_end',b_nyuusatu_jiki = '$b_nyuusatu_jiki',
                b_nyuusatu_kettei_jiki = '$b_nyuusatu_kettei_jiki',b_ipd_tantoubuka = '$b_ipd_tantoubuka',b_ipd_tantousya = '$b_ipd_tantousya',b_modeling_state = '$b_modeling_state',
                b_modeling_kentiku = '$b_modeling_kentiku',b_modeling_kouzou = '$b_modeling_kouzou',b_modeling_setubi = '$b_modeling_setubi',b_modeling_seisansekkei = '$b_modeling_seisansekkei',
                b_modeling_seisan = '$b_modeling_seisan',b_bikou = '$b_bikou',b_tousuu = '$b_tousuu',b_koutei_sekkei_model_start = '$b_koutei_sekkei_model_start',
                b_koutei_sekkei_model_end = '$b_koutei_sekkei_model_end',b_koutei_kakunin_sinsei_start = '$b_koutei_kakunin_sinsei_start',b_koutei_kakunin_sinsei_end = '$b_koutei_kakunin_sinsei_end',
                b_koutei_sekisan_model_tougou_start = '$b_koutei_sekisan_model_tougou_start',b_koutei_sekisan_model_tougou_end = '$b_koutei_sekisan_model_tougou_end',
                b_koutei_kouji_juujisya_kettei_start = '$b_koutei_kouji_juujisya_kettei_start',b_koutei_kouji_juujisya_kettei_end = '$b_koutei_kouji_juujisya_kettei_end',
                b_koutei_genba_koutei_kettei_start = '$b_koutei_genba_koutei_kettei_start',b_koutei_genba_koutei_kettei_end = '$b_koutei_genba_koutei_kettei_end',
                b_koutei_kouji_start = '$b_koutei_kouji_start',b_koutei_kouji_end = '$b_koutei_kouji_end',
                b_isyou_sekkei = '$b_isyou_sekkei',b_isyou_model = '$b_isyou_model',b_isyou_syozoku = '$b_isyou_syozoku',b_kouzou_sekkei = '$b_kouzou_sekkei',b_kouzou_model = '$b_kouzou_model',b_kouzou_syozoku = '$b_kouzou_syozoku',
                b_setubi_kuutyou_sekkei = '$b_setubi_kuutyou_sekkei',b_setubi_kuutyou_model = '$b_setubi_kuutyou_model',b_setubi_kuutyou_syozoku = '$b_setubi_kuutyou_syozoku',
                
                b_setubi_eisei_sekkei = '$b_setubi_eisei_sekkei',b_setubi_eisei_model = '$b_setubi_eisei_model',b_setubi_eisei_syozoku = '$b_setubi_eisei_syozoku',
                
                b_setubi_denki_sekkei = '$b_setubi_denki_sekkei',b_setubi_denki_model = '$b_setubi_denki_model',b_setubi_denki_syozoku = '$b_setubi_denki_syozoku',
                b_sekou_tantou = '$b_sekou_tantou',b_sekou_syozoku = '$b_sekou_syozoku',b_seisan_sekkei_tantou = '$b_seisan_sekkei_tantou',b_seisan_sekkei_syozoku = '$b_seisan_sekkei_syozoku',
                b_seisan_gijutu_tantou = '$b_seisan_gijutu_tantou',b_seisan_gijutu_syozoku = '$b_seisan_gijutu_syozoku',b_sekisan_mitumori_tantou = '$b_sekisan_mitumori_tantou',
                b_sekisan_mitumori_syozoku = '$b_sekisan_mitumori_syozoku',b_bim_maneka_tantou = '$b_bim_maneka_tantou',b_bim_maneka_syozoku = '$b_bim_maneka_syozoku',
                b_bim_coordinator_tantou = '$b_bim_coordinator_tantou',b_bim_coordinator_syozoku = '$b_bim_coordinator_syozoku',b_ipd_center_tantou = '$b_ipd_center_tantou',
                b_ipd_center_syozoku = '$b_ipd_center_syozoku',b_koujibu_tantou = '$b_koujibu_tantou',b_koujibu_syozoku = '$b_koujibu_syozoku',
                
                b_partner_company = '$b_partner_company',b_bim_m = '$b_bim_m',b_sekousya = '$b_sekousya',
                
                b_tmp_pj_name = '$b_tmp_pj_name'";
        // return $query;
        DB::insert($query);
    }

    function SaveData_old(Request $request)
    {
        $line = $request->get('storeData');
        $query_list = [];

        $project_code      = isset($line[0]) ? $line[0] : "";
        $kouji_kikan_code  = isset($line[1]) ? $line[1] : "";
        $branch_store      = isset($line[2]) ? $line[2] : "";
        
        $name               = isset($line[3]) ? $line[3] : "";
        
        $construction_type = isset($line[4]) ? $line[4] : "";
        $operation_state   = isset($line[5]) ? $line[5] : "";
        $sekkei_state   = isset($line[6]) ? $line[6] : "";
        $sekou_state    = isset($line[7]) ? $line[7] : "";
        $jiyuu_kinyuu   = isset($line[8]) ? $line[8] : "";
        $project_name   = isset($line[9]) ? $line[9] : "";
        $kakudo         = isset($line[10]) ? $line[10] : "";
        $hattyuusya     = isset($line[11]) ? $line[11] : "";
        $sekkeisya      = isset($line[12]) ? $line[12] : "";
        $sekkeisya2     = isset($line[13]) ? $line[13] : "";
        $tyakkou        = isset($line[14]) ? $line[14] : "";
        $syunkou        = isset($line[15]) ? $line[15] : "";
        $youto          = isset($line[16]) ? $line[16] : "";
        $kouzou         = isset($line[17]) ? $line[17] : "";
        $kaisuu         = isset($line[18]) ? $line[18] : "";
        $tika           = isset($line[19]) ? $line[19] : "";
        $tijou          = isset($line[20]) ? $line[20] : "";
        $ph             = isset($line[21]) ? $line[21] : "";
        $nobe_menseki   = isset($line[22]) ? $line[22] : "";
        $kouji_jimusyo  = isset($line[23]) ? $line[23] : "";
        $syotyou        = isset($line[24]) ? $line[24] : "";
        $kouji_tantoubu = isset($line[25]) ? $line[25] : "";
        $kouji_katyou   = isset($line[26]) ? $line[26] : "";
        $eigyou_tantoubu  = isset($line[27]) ? $line[27] : "";
        $eigyou_tantousya = isset($line[28]) ? $line[28] : "";
        $isyou_sekkei   = isset($line[29]) ? $line[29] : "";
        $isyou_model    = isset($line[30]) ? $line[30] : "";
        $kouzou_sekkei  = isset($line[31]) ? $line[31] : "";
        $kouzou_model   = isset($line[32]) ? $line[32] : "";
        $setubi_sekkei  = isset($line[33]) ? $line[33] : "";
        $setubi_model   = isset($line[34]) ? $line[34] : "";
        $kentiku_hattyuu_keitai = isset($line[35]) ? $line[35] : "";
        $setubi_hattyuu_keitai  = isset($line[36]) ? $line[36] : "";
        $koutei_kihonsekkei     = isset($line[37]) ? $line[37] : "";
        $koutei_jissisekkei     = isset($line[38]) ? $line[38] : "";
        $nyuusatu_jiki          = isset($line[39]) ? $line[39] : "";
        $nyuusatu_ketteijiki    = isset($line[40]) ? $line[40] : "";
        $ipd_tantou_bu  = isset($line[41]) ? $line[41] : "";
        $ipd_tantou_pd  = isset($line[42]) ? $line[42] : "";
        $model_state    = isset($line[43]) ? $line[43] : "";
        $model_kentiku  = isset($line[44]) ? $line[44] : "";
        $model_kouzou   = isset($line[45]) ? $line[45] : "";
        $model_setubi   = isset($line[46]) ? $line[46] : "";
        $model_ssekkei  = isset($line[47]) ? $line[47] : "";
        $model_seisan   = isset($line[48]) ? $line[48] : "";
        $bikou          = isset($line[49]) ? $line[49] : "";
        $jyuusyo        = isset($line[50]) ? $line[50] : "";
        $tou_meisyo     = isset($line[51]) ? $line[51] : "";
        $tuika_koumoku  = isset($line[52]) ? $line[52] : "";
        
        $query = "INSERT INTO tb_allstore_management(id,name,project_name,project_code,kouji_kikan_code,branch_store,construction_type,operation_state,sekkei_state,sekou_state,jiyuu_kinyuu,kakudo,hattyuusya
                ,sekkeisya,sekkeisya2,tyakkou,syunkou,youto,kouzou,kaisuu,tika,tijou,ph,nobe_menseki,kouji_jimusyo,syotyou,kouji_tantoubu,kouji_katyou
                ,eigyou_tantoubu,eigyou_tantousya,isyou_sekkei,isyou_model,kouzou_sekkei,kouzou_model,setubi_sekkei,setubi_model
                ,kentiku_hattyuu_keitai,setubi_hattyuu_keitai,koutei_kihonsekkei,koutei_jissisekkei,nyuusatu_jiki,nyuusatu_ketteijiki
                ,ipd_tantou_bu,ipd_tantou_pd,model_state,model_kentiku,model_kouzou,model_setubi,model_ssekkei,model_seisan,bikou,jyuusyo,tou_meisyo,tuika_koumoku)
                SELECT COALESCE(MAX(id), 0) + 1,'$name','$project_name','$project_code','$kouji_kikan_code','$branch_store','$construction_type','$operation_state','$sekkei_state','$sekou_state','$jiyuu_kinyuu','$kakudo','$hattyuusya',
                '$sekkeisya','$sekkeisya2','$tyakkou','$syunkou','$youto','$kouzou','$kaisuu','$tika','$tijou','$ph','$nobe_menseki','$kouji_jimusyo','$syotyou','$kouji_tantoubu','$kouji_katyou',
                '$eigyou_tantoubu','$eigyou_tantousya','$isyou_sekkei','$isyou_model','$kouzou_sekkei','$kouzou_model','$setubi_sekkei','$setubi_model',
                '$kentiku_hattyuu_keitai','$setubi_hattyuu_keitai','$koutei_kihonsekkei','$koutei_jissisekkei','$nyuusatu_jiki','$nyuusatu_ketteijiki',
                '$ipd_tantou_bu','$ipd_tantou_pd','$model_state','$model_kentiku','$model_kouzou','$model_setubi','$model_ssekkei','$model_seisan','$bikou','$jyuusyo','$tou_meisyo','$tuika_koumoku' FROM tb_allstore_management
                ON DUPLICATE KEY UPDATE project_name = '$project_name',project_code = '$project_code',kouji_kikan_code = '$kouji_kikan_code',branch_store = '$branch_store',construction_type = '$construction_type',operation_state = '$operation_state',sekkei_state = '$sekkei_state',sekou_state = '$sekou_state',jiyuu_kinyuu = '$jiyuu_kinyuu',kakudo = '$kakudo',hattyuusya = '$hattyuusya',
                sekkeisya = '$sekkeisya',sekkeisya2 = '$sekkeisya2',tyakkou = '$tyakkou',syunkou = '$syunkou',youto = '$youto',kouzou = '$kouzou',kaisuu = '$kaisuu',tika = '$tika',tijou = '$tijou',ph = '$ph',nobe_menseki = '$nobe_menseki',kouji_jimusyo = '$kouji_jimusyo',syotyou = '$syotyou',kouji_tantoubu = '$kouji_tantoubu',kouji_katyou = '$kouji_katyou',
                eigyou_tantoubu = '$eigyou_tantoubu',eigyou_tantousya = '$eigyou_tantousya',isyou_sekkei = '$isyou_sekkei',isyou_model = '$isyou_model',kouzou_sekkei = '$kouzou_sekkei',kouzou_model = '$kouzou_model',setubi_sekkei = '$setubi_sekkei',setubi_model = '$setubi_model',
                kentiku_hattyuu_keitai = '$kentiku_hattyuu_keitai',setubi_hattyuu_keitai = '$setubi_hattyuu_keitai',koutei_kihonsekkei = '$koutei_kihonsekkei',koutei_jissisekkei = '$koutei_jissisekkei',nyuusatu_jiki = '$nyuusatu_jiki',nyuusatu_ketteijiki = '$nyuusatu_ketteijiki',
                ipd_tantou_bu = '$ipd_tantou_bu',ipd_tantou_pd = '$ipd_tantou_pd',model_state = '$model_state',model_kentiku = '$model_kentiku',model_kouzou = '$model_kouzou',model_setubi = '$model_setubi',model_ssekkei = '$model_ssekkei',model_seisan = '$model_seisan',bikou = '$bikou',jyuusyo = '$jyuusyo',tou_meisyo = '$tou_meisyo',tuika_koumoku = '$tuika_koumoku'";
        DB::insert($query);
    }
    
    function GetData(Request $request)
    {
        $loginId = session('login_user_id');
        $shiten_array = array(2=>"東京",3=>"大阪",4=>"名古屋",5=>"九州",6=>"東北",7=>"札幌",8=>"広島",9=>"四国",10=>"北陸");
        
        $limitString = "";
        $offset = $request->get('allstore_offset');
        if(isset($offset)){
            $limitString = " LIMIT " . $offset . ",50";
        }

        $filterString = "";
        $filter = $request->get('filter');
        if(isset($filter)){
            $filterString = $this->getFilterSQLString($filter);
        }
        
        $query = "SELECT allstore_set_id FROM tb_project_access_setting WHERE login_user_id = $loginId";
        $result = DB::select($query);
        $result = json_decode(json_encode($result),true);
        if($result == null || $result[0]["allstore_set_id"] == null || $result[0]["allstore_set_id"] == 0){//custom data
            $query = "SELECT * FROM tb_allstore_info 
                WHERE FIND_IN_SET(a_pj_code,(SELECT accessable_projects FROM tb_project_access_setting WHERE login_user_id = $loginId)) ORDER BY id" . $limitString;
        }else if($result[0]["allstore_set_id"] == 1){//all accessable set
            if($filterString != ""){
                $filterString = preg_replace("/AND/", "", $filterString, 1);
                $filterString = " WHERE " . $filterString;
            }
            $query = "SELECT * FROM tb_allstore_info" . $filterString . " ORDER BY id" . $limitString;
        }else if($result[0]["allstore_set_id"] >= 2 && $result[0]["allstore_set_id"] <=10){//get data by shiten
            $authority_set_id = $result[0]["allstore_set_id"];
            $accessable_shiten = $shiten_array[$authority_set_id];
            $query = "SELECT * FROM tb_allstore_info WHERE a_shiten LIKE '$accessable_shiten%'" . $filterString . " ORDER BY id" . $limitString;
        }else{//get user defined set
            $allstore_set_id = $result[0]["allstore_set_id"];
            $query = "SELECT * FROM tb_allstore_info 
                WHERE FIND_IN_SET(a_pj_code,(SELECT detail FROM tb_allstore_authority_set WHERE id = $allstore_set_id))" . $filterString . " ORDER BY id" . $limitString; 
        }

        //$query = "SELECT * FROM tb_allstore_info";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    
    function GetRecordNum(Request $request)
    {
        $loginId = session('login_user_id');
        $shiten_array = array(2=>"東京",3=>"大阪",4=>"名古屋",5=>"九州",6=>"東北",7=>"札幌",8=>"広島",9=>"四国",10=>"北陸");
        
        $filterString = "";
        $filter = $request->get('filter');
        if(isset($filter)){
            $filterString = $this->getFilterSQLString($filter);
        }
        
        $query = "SELECT allstore_set_id FROM tb_project_access_setting WHERE login_user_id = $loginId";
        $result = DB::select($query);
        $result = json_decode(json_encode($result),true);
        if($result == null || $result[0]["allstore_set_id"] == null || $result[0]["allstore_set_id"] == 0){//custom data
            $query = "SELECT count(*) as num FROM tb_allstore_info 
                WHERE FIND_IN_SET(a_pj_code,(SELECT accessable_projects FROM tb_project_access_setting WHERE login_user_id = $loginId))";
        }else if($result[0]["allstore_set_id"] == 1){//all accessable set
            if($filterString != ""){
                $filterString = preg_replace("/AND/", "", $filterString, 1);
                $filterString = " WHERE " . $filterString;
            }
            $query = "SELECT count(*) as num FROM tb_allstore_info" . $filterString;
        }else if($result[0]["allstore_set_id"] >= 2 && $result[0]["allstore_set_id"] <=10){//get data by shiten
            $authority_set_id = $result[0]["allstore_set_id"];
            $accessable_shiten = $shiten_array[$authority_set_id];
            $query = "SELECT count(*) as num FROM tb_allstore_info WHERE a_shiten LIKE '$accessable_shiten%'" . $filterString;
        }else{//get user defined set
            $allstore_set_id = $result[0]["allstore_set_id"];
            $query = "SELECT count(*) as num FROM tb_allstore_info 
                WHERE FIND_IN_SET(a_pj_code,(SELECT detail FROM tb_allstore_authority_set WHERE id = $allstore_set_id))" . $filterString; 
        }
        
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }
    
    function getFilterSQLString($filter)
    {
        $filterString = "";

        if($filter["ipd_code"] != ""){
            $filterString = $filterString . " AND a_pj_code LIKE '%" . $filter["ipd_code"] . "%'";
        }
        if($filter["kikan_code"] != ""){
            $filterString = $filterString . " AND a_kouji_kikan_code LIKE '%" . $filter["kikan_code"] . "%'";
        }
        if($filter["pj_name"] != ""){
            $filterString = $filterString . " AND (a_pj_name LIKE '%" . $filter["pj_name"] . "%' OR b_pj_name LIKE '%" . $filter["pj_name"] . "%' OR b_tmp_pj_name LIKE '%" . $filter["pj_name"] . "%')";
        }
        if($filter["branch_name"] != ""){
            $filterString = $filterString . " AND (a_shiten LIKE '%" . $filter["branch_name"] . "%' OR b_shiten LIKE '%" . $filter["branch_name"] . "%')";
        }
        if($filter["kouji_type"] != ""){
            $filterString = $filterString . " AND a_kouji_type LIKE '%" . $filter["kouji_type"] . "%'";
        }
        if($filter["ipd_tantou"] != ""){
            $filterString = $filterString . " AND b_ipd_center_tantou LIKE '%" . $filter["ipd_tantou"] . "%'";
        }

        return $filterString;
    }
    
    function GetDataByPjCode(Request $request)
    {
        $projectCode = $request->get('projectCode');
        $query = "SELECT * FROM tb_allstore_info WHERE a_pj_code = '$projectCode'";
        $data = DB::select($query);     
        return json_decode(json_encode($data),true);
    }

    function GetDataByPjCodeList($pj_code_list)
    {
        $condition = "";
        if (!empty($pj_code_list)){
            foreach($pj_code_list as $value){
                if ($condition === ""){
                    $condition .= "WHERE ";
                }else{
                    $condition .= " OR ";
                }
                
                $condition .= "a_pj_code = '$value'";
            }
        }
        $query = " SELECT a_pj_code as PJコード, a_kouji_kikan_code as 工事基幹コード,"
                    ."a_shiten as 支店,"
                    ."IF(b_kakudo IS NULL or b_kakudo='',a_kakudo,b_kakudo) as 確度,"
                    
                    ."(CASE WHEN b_tmp_pj_name != '' THEN b_tmp_pj_name "
                    ." WHEN b_pj_name != '' THEN  b_pj_name ELSE a_pj_name END ) as プロジェクト名称,"
				    ."a_kouji_kubun as 工事区分,"
					."a_ukeoikin as 請負金,"
					."IF(b_youto IS NULL or b_youto='',a_youto1,b_youto) as 用途,"
    				."IF(b_sekou_basyo IS NULL or b_sekou_basyo='',a_sekou_basyo,b_sekou_basyo) as 施工場所,"
    				
    				."IF(b_sekkeisya1 IS NULL or b_sekkeisya1='', a_sekkei, b_sekkeisya1)as 設計,"
					."IF(b_kouzou IS NULL or b_kouzou='', a_kouzou,b_kouzou) as 構造,"
					."IF(b_kaisuu IS NULL or b_kaisuu='', a_kaisuu,b_kaisuu) as 階数,"
					."IF(b_tika IS NULL or b_tika='',a_tika,b_tika) as 地下,"
					."IF(b_tijo IS NULL or b_tijo='',a_tijo,b_tijo) as 地上,"
					."IF(b_ph IS NULL or b_ph='', a_ph,b_ph) as PH,"
				    ."IF(b_nobe_menseki IS NULL or b_nobe_menseki='',a_nobe_menseki,b_nobe_menseki) as 延べ面積,"
					."IF(b_koutei_kouji_start IS NULL or b_koutei_kouji_start='',a_tyakkou,b_koutei_kouji_start) as 着工,"
					."IF(b_koutei_kouji_end IS NULL or b_koutei_kouji_end='', a_syunkou,b_koutei_kouji_end) as 竣工,"
	                ."b_pj_state as プロジェクト稼働状況,"
		            ."b_sekkei_state as 取組み状況_設計段階,"
	                ."b_sekou_state as 取組み状況_施工段階,"
					."b_hattyuusya as 発注者,"
					."b_tousuu as 棟数,"
					
		        	."b_syotyou as 工事事務所所長_氏名,"
				    ."b_kouji_jimusyo as 工事事務所_組織,"
		            ."b_kouji_katyou as 工事部担当者_氏名,"
			        ."b_kouji_buka as 工事部担当者_組織,"
            		."b_eigyou_tantousya as 営業担当者_氏名,"
				    ."b_eigyou_tantoubu as 営業担当者_組織,"
				    
        			."b_isyou_sekkei as 意匠設計担当者_氏名,"
        			."b_isyou_syozoku as 意匠設計担当者_組織,"
        			."b_isyou_model as 意匠設計モデラー_氏名,"
        			."b_isyou_model_syozoku as 意匠設計モデラー_組織,"
        			."b_kouzou_sekkei as 構造設計担当者_氏名,"
        			."b_kouzou_syozoku as 構造設計担当者_組織,"
        			."b_kouzou_model as 構造モデラー_氏名,"
        			."b_kouzou_model_syozoku as 構造モデラー_組織,"
        			."b_setubi_kuutyou_sekkei as 設備空調設計担当者_氏名,"
        			."b_setubi_kuutyou_syozoku as 設備空調設計担当者_組織,"
        			."b_setubi_kuutyou_model as 設備空調モデラー_氏名,"
        			."b_setubi_kuutyou_model_syozoku as 設備空調モデラー_組織,"
        			."b_setubi_eisei_sekkei as 設備衛生設計担当者_氏名,"
        			."b_setubi_eisei_syozoku as 設備衛生設計担当者_組織,"
        			."b_setubi_eisei_model as 設備衛生モデラー_氏名,"
        			."b_setubi_eisei_model_syozoku as 設備衛生モデラー_組織,"
        			."b_setubi_denki_sekkei as 設備電気設計担当者_氏名,"
        			."b_setubi_denki_syozoku as 設備電気設計担当者_組織,"
        			."b_setubi_denki_model as 設備電気モデラー_氏名,"
        			."b_setubi_denki_model_syozoku as 設備電気モデラー_組織,"
        			."b_ss_designer_name as 生産設計担当者_氏名,"
        			."b_ss_designer_dept as 生産設計担当者_組織,"
        			."b_ss_modeler_name as 生産設計モデラー_氏名,"
        			."b_ss_modeler_dept as 生産設計モデラー_組織,"
				    ."b_sekou_tantou as 施工管理担当者_氏名,"
				    ."b_sekou_syozoku as 施工管理担当者_組織,"
        			."b_seisan_modeler_name as 生産モデラー_氏名,"
        			."b_seisan_modeler_dept as 生産モデラー_組織,"
        			
        			."b_seisan_gijutu_tantou as 生産技術担当者_氏名,"
        			."b_seisan_gijutu_syozoku as 生産技術担当者_組織,"
        			."b_sekisan_mitumori_tantou as 積算担当者_氏名,"
        			."b_sekisan_mitumori_syozoku as 積算担当者_組織,"
        			."b_bim_maneka_tantou as BIMマネジメント課担当者_氏名,"
                	."b_bim_maneka_syozoku as BIMマネジメント課担当者_組織,"
        			."b_ipd_center_syozoku as iPDセンター担当者_氏名,"
        			."b_ipd_center_tantou as iPDセンター担当者_組織,"
    				."b_partner_company as 協力会社担当者_氏名,"
    				."b_partner_company_dept as 協力会社担当者_組織,"
					."b_bim_m as BIMマネージャー_氏名,"
					."b_bim_manager_dept as BIMマネージャー_組織,"
                	."b_bim_coordinator_tantou as BIMコーディネーター_担当,"
                	."b_bim_coordinator_syozoku as BIMコーディネーター_組織,"

            		."b_hattyuu_keitai_kentiku as 建築工事発注形態,"
            		."b_hattyuu_keitai_setubi as 設備工事発注形態,"
    				."b_yosou_koujihi as 予想工事費,"
    				."b_kakutei_ukeoikin as 確定請負金,"
					."b_tubotanka as 坪単価,"

				    ."b_nyuusatu_jiki as 入札_開始日,"
        			."b_nyuusatu_kettei_jiki as 入札_完了日,"
        			."b_koutei_kihonsekkei_start as 基本設計_開始日,"
        			."b_koutei_kihonsekkei_end as 基本設計_完了日,"
        			."b_koutei_jissisekkei_start as 実施設計_開始日,"
        			."b_koutei_jissisekkei_end as 実施設計_完了日,"
                	."b_koutei_sekkei_model_start as 設計モデル作成_開始日,"
                	."b_koutei_sekkei_model_end as 設計モデル作成_完了日,"
        			."b_koutei_kakunin_sinsei_start as 確認申請_開始日,"
        			."b_koutei_kakunin_sinsei_end as 確認申請_完了日,"
            		."b_koutei_sekisan_model_tougou_start as 積算見積モデル統合・追記修正_開始日,"
            		."b_koutei_sekisan_model_tougou_end as 積算見積モデル統合・追記修正_完了日,"
                	."b_koutei_kouji_juujisya_kettei_start as 工事従事者決定_開始日,"
                	."b_koutei_kouji_juujisya_kettei_end as 工事従事者決定_完了日,"
            		."b_koutei_genba_koutei_kettei_start as 現場工程決定_開始日,"
            	    ."b_koutei_genba_koutei_kettei_end as 現場工程決定_完了日,"
        			."b_koutei_kouji_start as 工事_開始日,"
        			."b_koutei_kouji_end as 工事_完了日,"	
        			."b_handover_start as 引渡し_開始日,"
        			."b_handover_end as 引渡し_完了日,"	

					."b_bikou as 備考1,"
					."c_bikou as 備考2,"
					."c_order_status as 受注状況,"
					."c_additional_item as 追加項目"
         ." FROM tb_allstore_info ".$condition;
        $data = DB::select($query);
        return json_decode(json_encode($data),true);
    }
    
    function DeleteData()
    {
        // $query = "DELETE FROM tb_allstore_management";
        $query = "DELETE FROM tb_allstore_info";
        DB::delete($query);
        return "success";
    }
    
    function UpdateDisplayReportFlag(Request $request){
        $message = $request->get('message');
        
        try{
            $projectCode = $request->get('projectCode');
            $flag = $request->get('flag');
            
            if($message == "update_report_flag"){
                $allstore = new AllStoreModel();
                $tmp_allstore_id = $allstore->GetIdByProjectCode($projectCode);
                $query = "UPDATE tb_allstore_info SET display_report_flag=$flag WHERE a_pj_code = '$projectCode'";
                $data = DB::update($query);     
                
                if(!empty($tmp_allstore_id)){
                    $allstore_id = $tmp_allstore_id[0]["id"];
                    $personal_id = session('login_user_id');
                    $allstore->SetReportFlagHistory($allstore_id,$personal_id,$flag);
                }
                
            }else if($message == "update_bimmaneka_flag"){
                $query = "UPDATE tb_allstore_info SET output_bimmaneka_flag=$flag WHERE a_pj_code = '$projectCode'";
                $data = DB::update($query);     
            }else if($message == "update_setubibim_flag"){
                $query = "UPDATE tb_allstore_info SET output_setubibim_flag=$flag WHERE a_pj_code = '$projectCode'";
                $data = DB::update($query);     
            }else if($message == "update_estimate_flag"){
                $query = "UPDATE tb_allstore_info SET display_estimate_flag=$flag WHERE a_pj_code = '$projectCode'";
                $data = DB::update($query); 
            }

            return "success";

        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    function convertDateFormat($dateString){
            
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
           
        }
        
        
        return $tmp_sekkei_start;
    }
    
    function BIMmaneExcelDownload(){

        // $url = parse_url($_SERVER['REQUEST_URI']);
        // $codeString = explode('=',$url['query']);
        // $id = $codeString[1];
        // $forge = new ForgeModel();
        $result = $this->GetData();
        
        if($result != null)
        {
            $data = $result[0];
            // print_r($data);
            $inputFileName="/var/www/html/iPD/app/Exports/Template/OsakaBIMManekaTemplate.xlsx";
    
            //  Read your Excel workbook
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $excel = $objReader->load($inputFileName);
            } catch(Exception $e) {
                die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
            }
            
            
            $columnCnt = 0;
            $columnAry = ["C","E","G","I","K","M","O","Q","S","U","W","Y","AA","AC","AE","AG","AI","AK","AM","AO","AQ","AS","AU","AW","AY","BA","BC","BE","BG","BI","BK","BM","BO","BQ"];
            foreach($result as $row){
                if($row["output_bimmaneka_flag"] == 1){
                    $excel->setActiveSheetIndex(0);
                    $sheet = $excel->getActiveSheet();
                    
                    $columnStr = $columnAry[$columnCnt];
                    $columnCnt++;
                    
                    $pj_name = $row["b_tmp_pj_name"] != "" ? $row["b_tmp_pj_name"] : "";
                    $pj_name = $pj_name != "" ? $pj_name: $row["b_pj_name"];
                    $pj_name = $pj_name != "" ? $pj_name: $row["a_pj_name"];
                    $pj_name = $pj_name != "" ? $pj_name: $row["a_pj_name"];
                    if(strpos($pj_name,"と同じ") !== false){
                        $pj_name = $row["a_pj_name"];
                    }
                    $kouzou = $row["b_kouzou"] != "" ? $row["b_kouzou"] : $row["a_kouzou"];
                    $tika = $row["b_tika"] != "" ? $row["b_tika"] : $row["a_tika"];
                    $tijo = $row["b_tijo"] != "" ? $row["b_tijo"] : $row["a_tijo"];
                    $kaisuu = "";
                    if($tijo != ""){
                        $kaisuu = $tijo . "F";
                    }
                    if($tika != "" && $tika != "0" && $tika != "-"){
                        $kaisuu = $kaisuu . "/B" . $tika . "F";
                    }
                    $kouzou_kibo = $kouzou . "  " . $kaisuu;
                    $nobe_menseki = $row["b_nobe_menseki"] != "" ? $row["b_nobe_menseki"] : $row["a_nobe_menseki"];
                    $tyakkou = $row["b_koutei_kouji_start"] != "" ? $row["b_koutei_kouji_start"] : $row["a_tyakkou"];
                    $syunkou = $row["b_koutei_kouji_end"] != "" ? $row["b_koutei_kouji_end"] : $row["a_syunkou"];
            
                    $sheet->setCellValue($columnStr."4",$pj_name);
                    $sheet->setCellValue($columnStr."5",$row["a_pj_code"]);
                    $sheet->setCellValue($columnStr."6",$kouzou_kibo);
                    $sheet->setCellValue($columnStr."7",$nobe_menseki);
                    $sheet->setCellValue($columnStr."8",$tyakkou);
                    $sheet->setCellValue($columnStr."9",$syunkou);

                    $bimm = explode("/", $row["b_bim_m"]);
                    $bimm_count = count($bimm) > 5 ? 5:count($bimm);
                    for ($i = 0 ; $i < $bimm_count; $i++){
                        $rowStr = (string)(15 + $i);
                        $cellStr = $columnStr . $rowStr;
                        $sheet->setCellValue($cellStr,$bimm[$i]);
                    }

                    $isyou_sekkei = explode("/", $row["b_isyou_sekkei"]);
                    $isyou_sekkei_count = count($isyou_sekkei) > 4 ? 4:count($isyou_sekkei);
                    for ($i = 0 ; $i < $isyou_sekkei_count; $i++){
                        $rowStr = (string)(21 + $i);
                        $cellStr = $columnStr . $rowStr;
                        $sheet->setCellValue($cellStr,$isyou_sekkei[$i]);
                    }
                    
                    $kouzou_sekkei = explode("/", $row["b_kouzou_sekkei"]);
                    $kouzou_sekkei_count = count($kouzou_sekkei) > 4 ? 4:count($kouzou_sekkei);
                    for ($i = 0 ; $i < $kouzou_sekkei_count; $i++){
                        $rowStr = (string)(26 + $i);
                        $cellStr = $columnStr . $rowStr;
                        $sheet->setCellValue($cellStr,$kouzou_sekkei[$i]);
                    }

                    $kuutyou_sekkei = explode("/", $row["b_setubi_kuutyou_sekkei"]);
                    $eisei_sekkei = explode("/", $row["b_setubi_eisei_sekkei"]);
                    if($kuutyou_sekkei[0] != ""){
                        $sheet->setCellValue($columnStr."31", "(空調)" . $kuutyou_sekkei[0]);
                    }
                    if($eisei_sekkei[0] != ""){
                        $sheet->setCellValue($columnStr."32", "(衛星)" . $eisei_sekkei[0]);
                    }
                    
                    $denki_sekkei = explode("/", $row["b_setubi_denki_sekkei"]);
                    $denki_sekkei_count = count($denki_sekkei) > 4 ? 4:count($denki_sekkei);
                    for ($i = 0 ; $i < $denki_sekkei_count; $i++){
                        $rowStr = (string)(36 + $i);
                        $cellStr = $columnStr . $rowStr;
                        $sheet->setCellValue($cellStr,$denki_sekkei[$i]);
                    }
                    
                    $seisan_sekkei = explode("/", $row["b_ss_designer_name"]);
                    $seisan_sekkei_sosiki = explode("/", $row["b_ss_designer_dept"]);
                    $seisan_sekkei_count = count($seisan_sekkei) > 9 ? 9:count($seisan_sekkei);
                    for ($i = 0 ; $i < $seisan_sekkei_count; $i++){
                        $rowStr = (string)(41 + $i);
                        $cellStr = $columnStr . $rowStr;
                        $tmp_seisan_sekkei_sosiki = count($seisan_sekkei_sosiki) < ($i+1) ? "" : $seisan_sekkei_sosiki[$i];
                        $tmp_seisan_sekkei_sosiki = $tmp_seisan_sekkei_sosiki != "" ? "(".$tmp_seisan_sekkei_sosiki.")" : "";
                        $sheet->setCellValue($cellStr,$seisan_sekkei[$i] . $tmp_seisan_sekkei_sosiki);
                    }
                    
                    $koujibu_tantou = explode("/", $row["b_kouji_katyou"]);
                    $koujibu_tantou_count = count($koujibu_tantou) > 14 ? 14:count($koujibu_tantou);
                    for ($i = 0 ; $i < $koujibu_tantou_count; $i++){
                        $rowStr = (string)(51 + $i);
                        $cellStr = $columnStr . $rowStr;
                        $sheet->setCellValue($cellStr,$koujibu_tantou[$i]);
                    }
                    
                    $partner_company = explode("/", $row["b_partner_company"]);
                    $partner_company_count = count($partner_company) > 5 ? 5:count($partner_company);
                    for ($i = 0 ; $i < $partner_company_count; $i++){
                        $rowStr = (string)(65 + $i);
                        $cellStr = $columnStr . $rowStr;
                        $sheet->setCellValue($cellStr,$partner_company[$i]);
                    }
                    
                }
            }

            $sheet->setTitle(date("Ymd"));
            //出力するファイル名
            $filename = "大阪本店BIMマネ課管理表（担当者一覧）.xlsx";
            
            $writer = PHPExcel_IOFactory::createWriter($excel, "Excel2007");
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename=\"".$filename."\"");
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        }else{
            
            print_r($result);
    
        }
    }
    
    function FaciBIMExcelDownload(Request $request){

        // $url = parse_url($_SERVER['REQUEST_URI']);
        // $codeString = explode('=',$url['query']);
        // $id = $codeString[1];
        // $forge = new ForgeModel();
        $result = $this->GetData();
        
        if($result != null)
        {
            $data = $result[0];
            print_r($data);
    
            $inputFileName="/var/www/html/iPD/app/Exports/Template/SetubiBIMKickoffSheetTemplate.xlsx";
            
            
            //  Read your Excel workbook
            try {
                $zipFileName = '設備BIMキックオフシート.zip';
                $zipFilePath = '/var/www/html/iPD/' . $zipFileName;
                $zip = new ZipArchive;
                $res = $zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE);
                
                if ($res === true) {
                    
                    $unlinkList = array();
                    
                    foreach($result as $row){
                        if($row["output_setubibim_flag"] == 1){
        
                            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                            $excel = $objReader->load($inputFileName);
                            
                            $excel->setActiveSheetIndex(0);
                            $sheet = $excel->getActiveSheet();
                            
                            $pj_name = $row["b_tmp_pj_name"] != "" ? $row["b_tmp_pj_name"] : "";
                            $pj_name = $pj_name != "" ? $pj_name: $row["b_pj_name"];
                            $pj_name = $pj_name != "" ? $pj_name: $row["a_pj_name"];
                            $nobe_menseki = $row["b_nobe_menseki"] != "" ? $row["b_nobe_menseki"] : $row["a_nobe_menseki"];
                            $kouzou = $row["b_kouzou"] != "" ? $row["b_kouzou"] : $row["a_kouzou"];
                            $tika = $row["b_tika"] != "" ? $row["b_tika"] : $row["a_tika"];
                            if($tika == "0"){
                                $tika = "-";
                            }
                            $tijo = $row["b_tijo"] != "" ? $row["b_tijo"] : $row["a_tijo"];
                            $youto = "";
                            if($row["b_youto"] != ""){
                                $youto = $row["b_youto"];
                            }else{
                                $youto = $row["a_youto1"] != "" ? $row["a_youto1"]: "";
                                if($row["a_youto2"] != ""){
                                    $youto = $youto != "" ? $youto . "/" . $row["a_youto2"]: "";
                                }
                            }
                            
                            $setubi_kikai = "";
                            if($row["b_setubi_kuutyou_sekkei"] != ""){
                                $setubi_kikai = "(空調)" . $row["b_setubi_kuutyou_sekkei"];
                                
                                if($row["b_setubi_eisei_sekkei"] != ""){
                                    $setubi_kikai = $setubi_kikai . "/(設備)" . $row["b_setubi_eisei_sekkei"];
                                }
                            }else{
                                if($row["b_setubi_eisei_sekkei"] != ""){
                                    $setubi_kikai = "(設備)" . $row["b_setubi_eisei_sekkei"];
                                }
                            }
                            
                            $setubi_denki = $row["b_setubi_denki_sekkei"] != "" ? $row["b_setubi_denki_sekkei"] : "";
                            $isyou_sekkei = $row["b_isyou_sekkei"] != "" ? $row["b_isyou_sekkei"] : "";
                            $kouzou_sekkei = $row["b_kouzou_sekkei"] != "" ? $row["b_kouzou_sekkei"] : "";
                            $ipd_center_tantou = $row["b_ipd_center_tantou"] != "" ? $row["b_ipd_center_tantou"] : "";
                            $bim_maneka_tantou = $row["b_bim_maneka_tantou"] != "" ? $row["b_bim_maneka_tantou"] : "";
                            $tyakkou = $row["b_tyakkou"] != "" ? $row["b_tyakkou"] : $row["a_tyakkou"];
                            $syunkou = $row["b_syunkou"] != "" ? $row["b_syunkou"] : $row["a_syunkou"];
        
                            if($pj_name != "")     { $sheet->setCellValue("D3",$pj_name); }         //物件名称
                            if($nobe_menseki != ""){ $sheet->setCellValue("D4",$nobe_menseki); }    //延床面積
                            if($kouzou != "")      { $sheet->setCellValue("J4",$kouzou); }          //構造
                            if($tika != "")        { $sheet->setCellValue("O4",$tika); }            //地下
                            if($tijo != "")        { $sheet->setCellValue("S4",$tijo); }            //地上
                            if($youto != "")        { $sheet->setCellValue("D5",$youto); }          //用途
                            if($row["a_shiten"] != ""){ $sheet->setCellValue("D6",$row["a_shiten"]); }  //店
                            if($setubi_kikai != ""){ $sheet->setCellValue("S7",$setubi_kikai); }    //設備設計(機械)担当者
                            if($setubi_denki != ""){ $sheet->setCellValue("S8",$setubi_denki); }    //設備設計(電気)担当者
                            if($isyou_sekkei != ""){ $sheet->setCellValue("S10",$isyou_sekkei); }   //意匠設計担当部->担当者
                            if($kouzou_sekkei != ""){ $sheet->setCellValue("S11",$kouzou_sekkei); } //構造設計担当部->担当者
                            if($ipd_center_tantou != ""){ $sheet->setCellValue("S12",$ipd_center_tantou); } //iPDセンター担当者
                            if($bim_maneka_tantou != ""){ $sheet->setCellValue("S13",$bim_maneka_tantou); } //BIMマネ課担当者
                            if($tyakkou != ""){ $sheet->setCellValue("K19",$tyakkou); }             //着工予定
                            if($syunkou != ""){ $sheet->setCellValue("T19",$syunkou); }             //竣工予定
        
                            // $sheet->setTitle("【記入用】設備設計キックオフシート");
                            //出力するファイル名
                            $filename = "【". $pj_name ."】設備BIMキックオフシート.xlsx";
                            array_push($unlinkList,$filename);
                            
                            $writer = PHPExcel_IOFactory::createWriter($excel, "Excel2007");
                            header('Content-Type: application/vnd.ms-excel');
                            header("Content-Disposition: attachment;filename=\"".$filename."\"");
                            header('Cache-Control: max-age=0');
                            $writer->save($filename);
                            
                            $zip->addFile($filename);
                        }
                    }
                    
                    $zip->close();
                    mb_http_output("pass");
                    ob_end_clean();
                    
                    header('Content-Type: application/zip');
                    header('Content-disposition: attachment; filename='.$zipFileName);
                    header('Content-Length: ' . filesize($zipFilePath));
                    readfile($zipFilePath);
                    
                    unlink($zipFilePath);
                    foreach($unlinkList as $unlinkFileName){
                        unlink($unlinkFileName);
                    }
                }

            } catch(Exception $e) {
                die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
            }
        }else{
            
            print_r($result);
    
        }
    }
    
    function UpdateLatestBOXData(){

        if(session()->has('access_token')){
            $access_token = session('access_token');
            if($access_token == ""){
                return "no_token";
            }
            
            if(session()->has('authority_id')){
                $login = new LoginModel();
                $result = $login->GetBoxAuthority(session('authority_id'));
               
                // if($result[0]["box_access"] == 1){
                    $common = new CommonController();
                    $excel_folder_id = "134217819825";//CCC取り込み用/全店物件データ
                    $allStoreList = $common->UpdateAllStoreFromBox($excel_folder_id,$access_token);
                    
                    $this->RecordAllstoreUpdateHistory();

                    return "success";
                // }else{
                //     return "no_authority";
                // }
            }
            
        }else{
            return "no_token";
        }
    }
    
    function getAllstoreUpdateHistory(Request $request){
        $message = $request->get('message');

        try{
            if($message == "box_load_history"){
                $query = "SELECT * FROM tb_allstore_update_history";
                $data = DB::select($query);     
                return json_decode(json_encode($data),true);
            }else if($message == "report_flag_history"){
                $allstore = new AllStoreModel();
                $data = $allstore->GetReportFlagHistory();
                return $data;
            }
        }catch(Exception $e){
            return $e->getMessage();
        }
        
    }
    
    
    function RecordAllstoreUpdateHistory(){
        
        $userName = "";
        if(session()->has('userName')){
	        $userName = session('userName');
        }
        $date = date("Y-m-d H:i:s");

        $query = "INSERT INTO tb_allstore_update_history(id,name,updating)
                  SELECT COALESCE(MAX(id), 0) + 1,'$userName', cast( '$date' as datetime ) FROM tb_allstore_update_history";
        $data = DB::insert($query);
        return "success";
    }
}
