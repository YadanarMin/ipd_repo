/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

var combo_data = {"PJコード":"${pj_code}","工事基幹コード":"${kouji_kikan_code}","支店":"${shiten}", "確度":"${kakudo}",
                //建物基本情報
                "プロジェクト名称":"${pj_name}","工事区分":"${kakudo}",
                // "請負金":"${ukeoikin}",
                "用途":"${youto}","施工場所":"${sekou_basyo}",
                "設計":"${sekkeisya}","構造":"${kouzou}","階数":"${kaisuu}","地下":"${tika}","地上":"${tijo}","PH":"${ph}","延べ面積":"${nobe_menseki}","着工":"${tyakkou}","竣工":"${syunkou}",
                "プロジェクト稼働状況":"${pj_state}", "取組み状況_設計段階":"${sekkei_state}","取組み状況_施工段階":"${sekou_state}", "発注者":"${hattyuusya}", "棟数":"${tousuu}",
                //人員
                "工事事務所所長_氏名":"${syotyou}","工事事務所_組織":"${kouji_jimusyo}", "工事部担当者_氏名":"${kouji_katyou}","工事部担当者_組織":"${kouji_buka}",
                "営業担当者_氏名":"${eigyou_tantousya}","営業担当者_組織":"${eigyou_tantoubu}",
                "意匠設計担当者_氏名":"${isyou_sekkei}","意匠設計担当者_組織":"${isyou_syozoku}","意匠設計モデラー_氏名":"${isyou_model}","意匠設計モデラー_組織":"${isyou_model_syozoku}",
                "構造設計担当者_氏名":"${kouzou_sekkei}","構造設計担当者_組織":"${kouzou_syozoku}","構造モデラー_氏名":"${kouzou_model}","構造モデラー_組織":"${kouzou_model_syozoku}",
                "設備空調設計担当者_氏名":"${setubi_kuutyou_sekkei}","設備空調設計担当者_組織":"${setubi_kuutyou_syozoku}","設備空調モデラー_氏名":"${setubi_kuutyou_model}","設備空調モデラー_組織":"${setubi_kuutyou_model_syozoku}",
                "設備衛生設計担当者_氏名":"${setubi_eisei_sekkei}","設備衛生設計担当者_組織":"${setubi_eisei_syozoku}","設備衛生モデラー_氏名":"${setubi_eisei_model}","設備衛生モデラー_組織":"${setubi_eisei_model_syozoku}",
                "設備電気設計担当者_氏名":"${setubi_denki_sekkei}","設備電気設計担当者_組織":"${setubi_denki_syozoku}","設備電気モデラー_氏名":"${setubi_denki_model}","設備電気モデラー_組織":"${setubi_denki_model_syozoku}",
                "生産設計担当者_氏名":"${ss_designer_name}","生産設計担当者_組織":"${ss_designer_dept}","生産設計モデラー_氏名":"${ss_modeler_name}","生産設計モデラー_組織":"${ss_modeler_dept}",
                "施工管理担当者_氏名":"${sekou_tantou}","施工管理担当者_組織":"${sekou_syozoku}",
                "生産モデラー_氏名":"${seisan_modeler_name}","生産モデラー_組織":"${seisan_modeler_dept}",
                "生産技術担当者_氏名":"${seisan_gijutu_tantou}","生産技術担当者_組織":"${seisan_gijutu_syozoku}",
                "積算担当者_氏名":"${sekisan_mitumori_tantou}","積算担当者_組織":"${sekisan_mitumori_syozoku}",
                "BIMマネジメント課担当者_氏名":"${bim_maneka_tantou}","BIMマネジメント課担当者_組織":"${bim_maneka_syozoku}",
                "iPDセンター担当者_氏名":"${ipd_center_tantou}","iPDセンター担当者_組織":"${ipd_center_syozoku}",
                "協力会社担当者_氏名":"${partner_company}","協力会社担当者_組織":"${partner_company_dept}",
                "BIMマネージャー_氏名":"${bim_m}","BIMマネージャー_組織":"${bim_manager_dept}",
                "BIMコーディネーター_担当":"${bim_coordinator_tantou}","BIMコーディネーター_組織":"${bim_coordinator_syozoku}",
                //コスト関係
                "建築工事発注形態":"${hattyuu_keitai_kentiku}","設備工事発注形態":"${hattyuu_keitai_setubi}",
                // "予想工事費":"${yosou_koujihi}", "確定請負金":"${kakutei_ukeoikin}","坪単価":"${tubotanka}",
                // 工程
                "入札_開始日":"${nyuusatu_jiki}", "入札_完了日":"${nyuusatu_kettei_jiki}",
                "基本設計_開始日":"${koutei_kihonsekkei_start}","基本設計_完了日":"${koutei_kihonsekkei_end}",
                "実施設計_開始日":"${koutei_jissisekkei_start}","実施設計_完了日":"${koutei_jissisekkei_end}",
                "設計モデル作成_開始日":"${koutei_sekkei_model_start}", "設計モデル作成_完了日":"${koutei_sekkei_model_end}",
                "確認申請_開始日":"${koutei_kakunin_sinsei_start}", "確認申請_完了日":"${koutei_kakunin_sinsei_end}",
                "積算見積モデル統合・追記修正_開始日":"${koutei_sekisan_model_tougou_start}","積算見積モデル統合・追記修正_完了日":"${koutei_sekisan_model_tougou_end}",
                "工事従事者決定_開始日":"${koutei_kouji_juujisya_kettei_start}","工事従事者決定_完了日":"${koutei_kouji_juujisya_kettei_end}",
                "現場工程決定_開始日":"${koutei_genba_koutei_kettei_start}","現場工程決定_完了日":"${koutei_genba_koutei_kettei_end}",
                "工事_開始日":"${koutei_kouji_start}","工事_完了日":"${koutei_kouji_end}",
                "引渡し_開始日":"${handover_start}","引渡し_完了日":"${handover_end}",

                "備考1":"${remarks1}",
                "備考2":"${remarks2}",
                "受注状況":"${order_status}",
                "追加項目":"${additional_item}"};
            
/*var caution_text = "対応表を参照しながら、エクセルでテンプレートを作成してください"
                    +"\n 例えば、エクセルで着工日の情報を入力したいセルに${tyakkou}と入れておけば、 "
                    +"${tyakkou}の部分に着工日が自動で入力されます。";*/

$(document).ready(function(){
    $.ajaxSetup({
        cache:false
    });
    
    //$("#txtA_Caution").html(caution_text);
    //expandTextarea('txtA_Descriptiont');
    $(".creator-info-group").hide();
    
    TextSearch();
    
    CreateInitialTable();
    
    LoadTemplateList();

    $("#txtTemplateName,#file,input[name='templateType']").on("change",function() {
       if($(this).val() !== ""){ $(".text-warning").html('');}
    });
    
    
    $('#levelDiv').on('click',"ul li", function(){
        
        $("#levelDiv li").removeClass('selected');
        $(this).addClass('selected');
        var templateName = $(this).find("label").html();
        var result = GetTemplateDataByName(templateName);
        if(result != ""){
            SetFormData(result);
        }
    });
    
    $("#editTemplateFunc").addClass("tab_switch_on");
});

function switchDisplayExcelDownload(){
    window.location="../document/templateConsole";
}

function switchDisplayExcelMakeTemplate(){
    window.location='../document/downloadConsole';
}

function TextSearch(){
    $('#txtSearch').keyup(function(){
        var textboxValue = $('#txtSearch').val();
        $(".levelList li").each(function() {
            var templateName = $(this).find("label").html();
            if(!templateName.includes(textboxValue)){
                $(this).hide();
            }else{
                $(this).show();
            }
        });
        
    });
}

function expandTextarea(id) {
        document.getElementById(id).addEventListener('keyup', function() {
            this.style.overflow = 'hidden';
            this.style.height = 0;
            this.style.height = this.scrollHeight + 'px';
        }, false);
}

function CreateNewTemplate(){
    ClearFormElement();
    CreateInitialTable();
}

function ClearFormElement(){
    
    $(".text-warning").html('');
    $("#levelDiv li").removeClass('selected');
    $("#txtTemplateName").val('');
    $("#txtTemplateName").removeAttr("disabled");
    $("#txtA_Description").html('');
    $(".creator-info-group").hide();
    $("#selected_file_name").html('');
    $("#tblTemplateVariable tbody tr:not(:last)").remove();
    $("input[name='templateType']").each(function () { $(this).prop('checked', false); });
    $("input[name='templateType']").each(function () { $(this).removeAttr('disabled')});
}

function CreateInitialTable(){

    var combo_legth = Object.keys(combo_data).length;
    var values = Object.values(combo_data);
    for(var i = 0 ; i < combo_legth ; i++){

        var td_row= "";
        td_row += "<tr>";
        td_row += "<td>";
            td_row += "<select id='cmb"+i+"' class='full-wd custom-combo'>"; 
                    $.each(combo_data,function(key,col_name){
                        td_row += "<option>"+key+"</option>";
                    });
            td_row += "</select>";
        td_row += "</td>";
        td_row += "<td>";
            td_row += "<div style='display:flex;'>";
                    td_row += "<input type='text' class='full-wd custom-combo' id='txt"+i+"' value='"+values[i]+"'/>";
                    td_row += "<img src='../public/image/delete_sign.png' class='delete-icon' onclick='RemoveRow(this)'>";
            td_row += "</div>";
        td_row += "</td>";
        td_row += "</tr>";
        $("#tblTemplateVariable tbody tr:last").before(td_row);
        $("#cmb"+i+" option").eq(i).prop('selected', true);//skip select item,so i+1 index select
        $("#cmb"+i).select2();
    }
    
    //make tbody disable if user is not admin
    DisableTableBody();
}

function LoadTemplateList(){
    $.ajax({
        url: "../document/getData",
        async:true,
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"get_template_list"},
        success :function(data) {
            console.log(data);
            if(data != null){
               CreateTemplateList(data);
            }
        },
        error:function(err){
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
}

function GetTemplateDataByName(templateName){
    var result = ""
    $.ajax({
        url: "../document/getData",
        async:false,
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"get_template_byname",templateName:templateName},
        success :function(data) {
            console.log(data);
            if(data != null){
               result = data;
            }
        },
        error:function(err){
            console.log(err);
             return result;
            //alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
    
    return result;
}

function SetFormData(form_data){
    var data = form_data[0];
    console.log(data);
    $(".text-warning").html('');
    $("#txtTemplateName").val(data["name"]);
    $("#txtTemplateName").attr("disabled","disabled");
    $("#txtA_Description").html(data["description"]);
    
    //creator info
    $(".creator-info-group").show();
    $("#txtNewTemplateCreator").val(data["created_user_name"]);
    $("#txtOrganization").val(data["created_orgainzation_name"]);
    $("#txtNewTemplateCreatedDate").val(data["created_date"]);
    
    $("#txtlastTemplateCreator").val(data["updated_user_name"]);
    $("#txtlastOrganization").val(data["updated_orgainzation_name"]);
    $("#txtlastTemplateCreatedDate").val(data["updated_date"]);

    $("#selected_file_name").html("参照ファイル　:　"+data["file_name"]);
    if(data["file_name"] !== ""){
        $("#selected_file_name").append('<img id="menuImage" src="../public/image/1070_dl_h.png" style="width:25px;height:25px;" alt="メニュー" onClick="downloadTemplateFile()">');
    }
    $("input[name='templateType']").each(function() {
       var value = $(this).val();
       if(value == data["type"]){
           $(this).prop("checked","checked");
       }
       $(this).attr("disabled","disabled");
   });
   
   var variable_keys = data["item_key"].split(',');
   var variable_values = data["item_val"].split(',');
   $("#tblTemplateVariable tbody tr:not(:last)").remove();
   $.each(variable_keys,function(i, item) {
       var td_row= "";
        td_row += "<tr>";
        td_row += "<td>";
            td_row += "<select id='cmb"+i+"' class='full-wd custom-combo'>"; 
                    $.each(combo_data,function(key,col_name){
                        td_row += "<option>"+key+"</option>";
                    });
            td_row += "</select>";
        td_row += "</td>";
        td_row += "<td>";
            td_row += "<div style='display:flex;'>";
                    td_row += "<input type='text' class='full-wd custom-combo' id='txt"+i+"' value='"+variable_values[i]+"'/>";
                    td_row += "<img src='../public/image/delete_sign.png' class='delete-icon' onclick='RemoveRow(this)'>";
            td_row += "</div>";
        td_row += "</td>";
        td_row += "</tr>";
        $("#tblTemplateVariable tbody tr:last").before(td_row);
        $("#cmb"+i+" option").each(function(){
          if($(this).text() == item){
             $(this).prop('selected', true);
          }
        });
        //$("#cmb"+i+" option:equals("+item+")").prop('selected', true);
        $("#cmb"+i).select2();
   })
   
   
    //make tbody disable if user is not admin
    DisableTableBody();
    
}

function DisableTableBody(){
    var login_authority = $("#hidAuthority_id").val();
    if(login_authority != 1){
        $('#tblTemplateVariable input, #tblTemplateVariable select, #tblTemplateVariable img').prop('disabled', true);
        $("#tblTemplateVariable img").prop("onclick", false);
        $('#tblTemplateVariable input, #tblTemplateVariable select').css({"color":"#a9a9a9"});
    }
}

function CreateTemplateList(data){
     var li = "";
    $.each(data,function(key,item){
        li += "<li>";
            //li += "<div id='template"+key+"' class='levelElement'>";
                li += "<label>"+item["name"]+"</label>";
            //li += "</div>";
        li += "</li>";
    });
   $("#levelDiv ul li").remove();
   $("#levelDiv ul ").append(li);
   
}

function AddNewRow(){
   var last_row_idex = $("#tblTemplateVariable tbody tr:last").index();
   //alert(last_row_idex);
   var index = last_row_idex;
   var combo_values = Object.values(combo_data);
   var td_row= "";
    td_row += "<tr>";
    td_row += "<td>";
        td_row += "<select id='cmb"+index+"' class='full-wd custom-combo'>"; 
                $.each(combo_data,function(key,col_name){
                    td_row += "<option>"+key+"</option>";
                });
        td_row += "</select>";
    td_row += "</td>";
    td_row += "<td>";
        td_row += "<div style='display:flex;'>";
                td_row += "<input type='text' class='full-wd custom-combo' id='txt"+index+"' values=''/>";
                td_row += "<img src='../public/image/delete_sign.png' class='delete-icon' onclick='RemoveRow(this)'>";
        td_row += "</div>";
    td_row += "</td>";
    td_row += "</tr>";
    $("#tblTemplateVariable tbody tr:last").before(td_row);
    $("#cmb"+index).select2();
}

function RemoveRow(ele){
   var row_index = $(ele).closest('tr').index();
   $("#tblTemplateVariable tbody tr").eq(row_index).remove();
    
}

function SaveTemplate() {
    var validate_check = CheckValidation();
    if(validate_check == true){
        var tblData = GetTemplateVariable();
        //console.log(tblData);return;
        var variable_keys = tblData["keys"];
        var variable_values = tblData["values"];
        if(variable_keys.length !== variable_values.length){
            $(".text-warning").html("置換文字列に空白項目があります。直してください。");
            return;
        }
        
        var templateName = $("#txtTemplateName").val();
        
        var templateType = $("input[name='templateType']:checked").val();
        var description = $("#txtA_Description").html();
        var selected_file_name = $("#selected_file_name").html();
        var old_file_name = "";
        if(selected_file_name !== "" && selected_file_name != undefined){
            old_file_name = (selected_file_name.split(':')[1]).trim();
        }

        var form =$("#template-form");  // $("#template-form").serialize();
        var form_data = new FormData(form[0]);
        form_data.append("variable_keys",variable_keys);
        form_data.append("variable_values",variable_values);
        form_data.append("message","save_template");
        form_data.append("templateName",templateName);
        form_data.append("templateType",templateType);
        form_data.append("old_file_name",old_file_name);
        //var file_data = $("#file").prop("files")[0];
        //form_data.append("zz",file_data);
         console.log(form_data);
        /*var form_params = {name:templateName,
                            templateType:templateType,description:description,
                            variable_keys:variable_keys,variable_values:variable_values,file:file_template_form};*/
        
        $.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: "../document/saveData",
        type: 'post',
        enctype: 'multipart/form-data',
        processData: false,  // Important!
        contentType: false,
        cache: false,
        data:form_data,
        success :function(data) {
            console.log("==========save=======");
            console.log(data);
            if(data.includes("success")){
                alert("テンプレート登録しました。");
                location.reload();
            }
        },
        error:function(err){
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
        
    }
}

function CheckValidation(){
    var templateName = $("#txtTemplateName").val();
    var selected_file_name = $("#selected_file_name").html();
    var file = $('#file').val();
    if ($('#file').hasClass('hide-ele')){
        file = "no need to validate";
    }
    var templateType = $("input[name='templateType']:checked").val();
    
    if(templateName == "" || templateName == undefined){
        $(".text-warning").html("テンプレート名を入力してください。");
        return false;
    }else{
        if($('#txtTemplateName').prop('disabled') == false){
             var result = DuplicateCheckForTemplateName(templateName);
            if(result != ""){
                $(".text-warning").html("テンプレート名が既に存在します。直してください。");
                return false; 
            }
        }
       
    }
    
    if(selected_file_name === "" || selected_file_name === undefined){
        if(file == "" || file == undefined){
            $(".text-warning").html("ファイルを選択してください。");
            return false;
        }  
    }
    
    if(templateType === "" || templateType === undefined){
         $(".text-warning").html("テンプレート形式を選択してください。");
         return false;
    }
    
    if(file !== "" && file !== undefined){
        var result = CheckFileAlreadyExisted(file,templateName);
        if(result != ""){
            $(".text-warning").html("参照ファイルが他のプロジェクトに使用しています。{ "+result+" }");
            return false; 
        }
        //return false;
    }
    
    $(".text-warning").html("");
    return true;
}

function GetTemplateVariable(){
    var key_array = [];
    var val_array = [];
    var rowCount = $('#tblTemplateVariable tbody tr').length;

    $("#tblTemplateVariable tbody tr").each(function(){
        var currentIndex = $(this).index();

        if(rowCount == currentIndex+1) return;//skip last row
        var item_key = $(this).find('td').eq(0).find('select').select2('val');
        var item_val = $(this).find('td').eq(1).find("input[type=text]").val();
        if(item_key !== "" && item_key !== undefined && item_key!== "select item")
            key_array.push(item_key);
        if(item_val !== "" && item_val !== undefined && item_key!== "select item")
            val_array.push(item_val);
    });
    return {"keys":key_array,"values":val_array};
}

function DeleteTemplate(){
    var templateName = $("#levelDiv li.selected").text();
    if(templateName === "" || templateName === undefined) return;
    var delete_file_name = "";
    var selected_file_name = $("#selected_file_name").html();
    if(selected_file_name !== "" && selected_file_name != undefined){
        delete_file_name = (selected_file_name.split(':')[1]).trim();
    }
    var result = window.confirm("Are you sure you want to delete { "+templateName+" } ?");
    if(result){
        $.ajax({
        url: "../document/deleteData",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"delete_template_byname",name:templateName,"delete_file_name":delete_file_name},
        success :function(data) {
            //alert(JSON.stringify(data));
            if(data.includes("success")){
                location.reload();
            }
        },
        error:function(err){
            console.log(err);
            alert("Failed in delete. Please try again!!");
        }
    });
    }
}

function CheckFileAlreadyExisted(filePath,templateName){

    var file_name = filePath.split('\\').pop();
    var result= "";
    $.ajax({
        url: "../document/getData",
        async:false,
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"get_template_byfilename","file_name":file_name,"templateName":templateName},
        success :function(data) {
            //console.log(data);
            if(data != null && data.length > 0){
                $.each(data, function (index, value)  
                {  if(result == "")
                        result += value["name"];
                    else
                        result += ","+value["name"];
                });
               
            }
        },
        error:function(err){
            console.log(err);
            return result;
            //alert("Failed in delete. Please try again!!");
        }
    });
    
    return result;
}

function DuplicateCheckForTemplateName(templateName){

    var result= "";
    $.ajax({
        url: "../document/getData",
        async:false,
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"get_template_byname","templateName":templateName},
        success :function(data) {
            //console.log(data);
            if(data != null && data.length > 0){
                $.each(data, function (index, value){
                    if(result == "")
                        result += value["name"];
                    else
                        result += ","+value["name"];
                });
               
            }
        },
        error:function(err){
            console.log(err);
            return result;
            //alert("Failed in delete. Please try again!!");
        }
    });
    
    return result;
}

function downloadTemplateFile(){
    
    var tmpStr = $("#selected_file_name").html();
    tmpStr = tmpStr.replace("参照ファイル　:　", "");
    var templateFileName = tmpStr.slice(0, tmpStr.indexOf("<"));
    
    window.location="/iPD/document/outputDefaultExcelTemplate/"+templateFileName;
}
