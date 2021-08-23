/* Debugログ出力設定 */
var DoorCount = 0;
var WindowCount = 0;
var pretblSunpoScrollPosition = 0;
var pretblMaterialScrollPosition = 0;
var pretblMojiScrollPosition = 0;
var pretblFireScrollPosition = 0;
var pretblIdenInfoScrollPosition = 0;

/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

/* Placeholder名称定義 */
var PLACEHOLDER_NAME_FOLDER         = "Select Folder";
var PLACEHOLDER_NAME_PROJECT        = "Select Project";
var PLACEHOLDER_NAME_VERSION        = "Select Version";
var PLACEHOLDER_NAME_LEVEL          = "Select Level";
var PLACEHOLDER_NAME_WORKSET        = "Select Workset";
var PLACEHOLDER_NAME_CATEGORY       = "Select Category";
var PLACEHOLDER_NAME_TYPENAME       = "Select Typename";
var PLACEHOLDER_NAME_TYPEPANEL      = "Select Typepanel";


$(document).ready(function(){
    
    $(document).on('click', '[name="tab"]', function () {
        var tabId = $(this).attr('id');
        if(tabId == "tab03"){
        var versionSelectedCount = $('#version option:selected').length;
            if(versionSelectedCount == 1 && DoorCount == 0 && WindowCount == 0)
            ShowData();
        }
    });
   
    /*$("#project").select2({
        placeholder:"Folder Loading..."
    });
    $("#item").select2({
        placeholder:"Project Loading..."
    });
    $("#version").select2({
        placeholder:"Version Loading..."
    });
    $("#level").select2({
        placeholder:"Select Level "
    });
    
    $("#workset").select2({
        placeholder:"Select Workset"
    });

    $("#category").select2({
        placeholder:"Select Category"
    });
    
    $("#typeName").select2({
        placeholder:"Select Typename"
    });
    $("#typePanel").select2({
        placeholder:"Select Typepanel"
    });
    
    LoadComboData();

    $("#project").change(function() {
        ProjectChange();
    });

    $("#item").change(function() {
        ItemChange();
    });*/
    
   // TableRowClick();
   // PlusSignClick();
   /* $("ul>#tblIdenInfo").on('scroll', "tbody",function(){
        alert("click");
    });
    if (location.hash !== '') $('a[href="' + location.hash + '"]').tab('show');
        return $('a[data-toggle="tab"]').on('shown', function(e) {
        return location.hash = $(e.target).attr('href').substr(1);
    });*/
    
    

});

function ResetCount() {
    //alert(DoorCount+"\n"+WindowCount);
    DoorCount = 0;
    WindowCount = 0;
    
    pretblSunpoScrollPosition = 0;
    pretblMaterialScrollPosition = 0;
    pretblMojiScrollPosition = 0;
    pretblFireScrollPosition = 0;
    pretblIdenInfoScrollPosition = 0;
}



function DoorWindowBindComboData(data,comboId,placeholder){
    DEBUGLOG("BindComboData", "start", 0);
    
    var appendText = "<option value=''></option>";
    $.each(data,function(key,value){
        if ( (value["name"] != undefined) && (value["name"] != null) ) {
            value["name"] = value["name"].trim();
        }

        if(comboId == "version"){
            var fileName = value["name"]+"("+value["version_number"]+")";
            appendText +="<option value='"+JSON.stringify(value)+"'>"+fileName+"</option>";
        }else{
            appendText +="<option value='"+JSON.stringify(value)+"'>"+value["name"]+"</option>";
        }
        
    });
    $("select#"+comboId+" option").remove();
    $("#"+comboId).append(appendText).select2({placeholder:placeholder}).trigger('changed');
}

function BindDoorWindowComboData(data,comboId,placeholder) {
    DEBUGLOG("BindDoorWindowComboData", "start", 0);
    
    var appendText = "";
    Object.keys(data).forEach(function(key){
        if ( (data[key] == undefined) || (data[key] == null) || (data[key] == "") ) {
            data[key] = "NoName";
        }
        appendText +="<option value='"+JSON.stringify(data[key])+"'>"+data[key]+"</option>";        
    });

    $("select#"+comboId+" option").remove();
    $("#"+comboId).append(appendText).select2({placeholder:placeholder}).trigger('changed');
}

function ShowData(){
    DEBUGLOG("ReportForgeRoomData", "start", 0);
    ShowLoading();
    var category_list = [];
    var level_list = [];
    var workset_list=[];
    var typename_list=[];
    var typepanel_list=[];
    
    var overviewData = {"Elements":0,"Area":0,"RoomName":0,"CalcHeight":0,"Shucho":0,"RoomHeight":0};
    var chartData = {};
    var totalData = {};
    var versionSelectedCount = $('#version option:selected').length;
    var ajaxCnt = 0;
    if(versionSelectedCount > 1)return;
    $("#category option:selected").each(function(){
        category_list.push($(this).val());
    });
    
    $("#level option:selected").each(function(){
        level_list.push($(this).text());
    });
    $("#workset option:selected").each(function(){
        workset_list.push($(this).text());
    });
    $("#typeName option:selected").each(function(){
        typename_list.push($(this).text());
    });
    /*$("#typePanel option:selected").each(function(){
        typepanel_list.push($(this).text());
    });*/
    
    $('#version option:selected').each(function(){
        var valArr =JSON.parse($(this).val());
        var version_number = valArr.version_number;
        var item_id = valArr.item_id;
        
        return $.ajax({
            url: "../forge/getData",
            type: 'post',
            data:{_token: CSRF_TOKEN,message:"getDoorWindowData",version_number:version_number,
                    item_id:item_id,category_list:category_list,level_list:level_list,
                    workset_list:workset_list,typename_list:typename_list,typepanel_list:typepanel_list},
            success :function(data) {
                console.log(data);
                if(data["door"].length== 0 && data["window"].length == 0){
                    
                    AppendTable("tblOverall","");
                    AppendTable("tblMaterial","");
                    AppendTable("tblSunpou","");
                    AppendTable("tblFire","");
                    AppendTable("tblMoji","");
                    AppendTable("tblIdenInfo","");
                }
                if(data["door"].length > 0 || data["window"].length > 0){
                    DoorCount = data["door"].length;
                    WindowCount = data["window"].length;
                    CreateTable(data);
                    
                    TableRowClick();
                    PlusSignClick();
                    $('table').on('scroll', function() {
                      $("#" + this.id + " > *").width($(this).width() + $(this).scrollLeft());
                    });
                }
                HideLoading();
                
            },
            error:function(err){
                console.log(err);
                HideLoading();
            }
        });
    });
    
}

function CreateTable(data){
    
    var tblSunpou = "";
    var tblMaterial = "";
    var tblFire = "";
    var tblMoji = "";
    var tblIdenInfo = "";
    var tblOverall = "";
    
    //tblSunpou += "<table id='tblSunpou'>";
        tblOverall += "<thead>";
            tblOverall += "<tr>";
                tblOverall += "<th class='col2'>element_id</th>";
                tblOverall += "<th class='col2'>タイプ名</th>";
                tblOverall += "<th class='col3'>レベル</th>";
                tblOverall += "<th class='col4'>タイプ_パネル</th>";
                tblOverall += "<th class='col2'>金物_ハンドル_</th>";
                tblOverall += "<th class='col2'>金物_支持金物_</th>";
                tblOverall += "<th class='col2'>金物_締り金物_</th>";
            tblOverall += "</tr>";
        tblOverall += "</thead>";
        tblOverall += "<tbody>";
        
        tblSunpou += "<thead>";
            tblSunpou += "<tr>";
                tblSunpou += "<th class='col2'>element_id</th>";
                tblSunpou += "<th class='col2'>タイプ名</th>";
                tblSunpou += "<th class='col3'>幅</th>";
                tblSunpou += "<th class='col3'>高さ</th>";
                tblSunpou += "<th class='col2'>厚さ</th>";
                tblSunpou += "<th class='col2'>全幅</th>";
                tblSunpou += "<th class='col2'>全高</th>";
            tblSunpou += "</tr>";
        tblSunpou += "</thead>";
        tblSunpou += "<tbody>";
        
    //tblMaterial += "<table>";
        tblMaterial += "<thead>";
            tblMaterial += "<tr>";
                tblMaterial += "<th class='col1'>element_id</th>";
                tblMaterial += "<th class='col1'>仕様_ガラス仕様_</th>";
                tblMaterial += "<th class='col1'>仕様_ガラリ仕様_</th>";
                tblMaterial += "<th class='col1'>仕様_本体仕上_</th>";
                tblMaterial += "<th class='col2'>仕様_本体材質_</th>";
                tblMaterial += "<th class='col2'>仕様_枠仕上_</th>";
                tblMaterial += "<th class='col2'>仕様_枠材質_</th>";
                tblMaterial += "<th class='col2'>枠材(文字)</th>";
            tblMaterial += "</tr>";
        tblMaterial += "</thead>";
        tblMaterial += "<tbody>";
        
        tblFire += "<thead>";
            tblFire += "<tr>";
                tblFire += "<th class='col1'>element_id</th>";
                tblFire += "<th class='col1'>ラベル_防火性能_</th>";
                tblFire += "<th class='col1'>法_防火性能_</th>";
                tblFire += "<th class='col1'>ラベル_常時開放_</th>";
                tblFire += "<th class='col2'>ラベル_認定_</th>";
                tblFire += "<th class='col2'>ラベル_遮煙_</th>";
                tblFire += "<th class='col2'>法_常時開放_</th>";
                tblFire += "<th class='col2'>法_認定品_</th>";
                tblFire += "<th class='col2'>法_遮煙_</th>";
            tblFire += "</tr>";
        tblFire += "</thead>";
        tblFire += "<tbody>";
        
        tblMoji += "<thead>";
            tblMoji += "<tr>";
                tblMoji += "<th class='col1'>element_id</th>";
                tblMoji += "<th class='col1'>性能_気密_</th>";
                tblMoji += "<th class='col2'>性能_遮音_</th>";
                tblMoji += "<th class='col1'>文字_アンダーカット_</th>";
                tblMoji += "<th class='col1'>文字_ガラスサイズ_</th>";
                tblMoji += "<th class='col2'>文字_ガラリサイズ_</th>";
                tblMoji += "<th class='col2'>文字_備考_</th>";
                tblMoji += "<th class='col2'>文字_扉厚_</th>";
                tblMoji += "<th class='col2'>文字_本体機構_</th>";
                tblMoji += "<th class='col2'>仕様_額縁材質_</th>";
                tblMoji += "<th class='col2'>性能_耐風圧_</th>";
                tblMoji += "<th class='col2'>性能_水密_</th>";
            tblMoji += "</tr>";
        tblMoji += "</thead>";
        tblMoji += "<tbody>";
        
        tblIdenInfo += "<thead>";
            tblIdenInfo += "<tr>";
                tblIdenInfo += "<th class='col1'>element_id</th>";
                tblIdenInfo += "<th class='col1'>形状_ガラリ_</th>";
                tblIdenInfo += "<th class='col2'>形状_召し合せ_</th>";
                tblIdenInfo += "<th class='col1'>形状_姿_</th>";
                tblIdenInfo += "<th class='col1'>形状_枠_</th>";
                tblIdenInfo += "<th class='col2'>形状_沓摺り_</th>";
                tblIdenInfo += "<th class='col2'>符号_番号_主_</th>";
                tblIdenInfo += "<th class='col2'>符号_番号_副_</th>";
                tblIdenInfo += "<th class='col2'>符号_記号_</th>";
                tblIdenInfo += "<th class='col2'>符号_電気_</th>";
                tblIdenInfo += "<th class='col2'>耐火等級</th>";
                tblIdenInfo += "<th class='col2'>製造元</th>";
                tblIdenInfo += "<th class='col2'>説明</th>";
                tblIdenInfo += "<th class='col2'>金物_特殊金物_</th>";
                tblIdenInfo += "<th class='col2'>OmniClass タイトル</th>";
                tblIdenInfo += "<th class='col2'>OmniClass 番号</th>";
                tblIdenInfo += "<th class='col2'>形状_水切り_</th>";
                tblIdenInfo += "<th class='col2'>コメント</th>";
                tblIdenInfo += "<th class='col2'>ワークセット</th>";
            tblIdenInfo += "</tr>";
        tblIdenInfo += "</thead>";
        tblIdenInfo += "<tbody>";
        
    $.each(data["door"],function(key,item){
        
        tblOverall += " <tr id='"+item["element_db_id"]+"'>";
            tblOverall += "<td class='col2'>"+item["element_id"]+"</td>";
            tblOverall += "<td class='col2'>"+item["type_name"]+"</td>";
            tblOverall += "<td class='col3'>"+item["level"]+"</td>";
            tblOverall += "<td class='col4'>"+item["type_door_panel"]+"</td>";
            tblOverall += "<td class='col2'>"+item["kanamono_handoru"]+"</td>";
            tblOverall += "<td class='col2'>"+item["kanamono_shiji_kanamono"]+"</td>";
            tblOverall += "<td class='col2'>"+item["kanamono_shimari_kanamono"]+"</td>";
        tblOverall += "</tr>";
        
        tblSunpou += " <tr>";
            tblSunpou += "<td class='col2'>"+item["element_id"]+"</td>";
            tblSunpou += "<td class='col2'>"+item["type_name"]+"</td>";
            tblSunpou += "<td class='col3'>"+item["width"]+"</td>";
            tblSunpou += "<td class='col3'>"+item["height"]+"</td>";
            tblSunpou += "<td class='col2'>"+item["thickness"]+"</td>";
            tblSunpou += "<td class='col2'>"+item["full_width"]+"</td>";
            tblSunpou += "<td class='col2'>"+item["full_height"]+"</td>";
        tblSunpou += "</tr>";
        
        tblMaterial += " <tr>";
            tblMaterial += "<td class='col1'>"+item["element_id"]+"</td>";
            tblMaterial += "<td class='col1'>"+item["shiyou_garasu_shiyou"]+"</td>";
            tblMaterial += "<td class='col1'>"+item["shiyou_garari_shiyou"]+"</td>";
            tblMaterial += "<td class='col1'>"+item["shiyou_hontai_shiage"]+"</td>";
            tblMaterial += "<td class='col2'>"+item["shiyou_hontai_zaishitsu"]+"</td>";
            tblMaterial += "<td class='col2'>"+item["shiyou_waku_shiage"]+"</td>";
            tblMaterial += "<td class='col2'>"+item["shiyou_waku_zaishitsu"]+"</td>";
            tblMaterial += "<td class='col2'>"+item["wakuzai_moji"]+"</td>";
        tblMaterial += "</tr>";
        
        tblFire += " <tr>";
            tblFire += "<td class='col1'>"+item["element_id"]+"</td>";
            tblFire += "<td class='col1'>"+item["raberu_bouka_seinou"]+"</td>";
            tblFire += "<td class='col1'>"+item["hou_bouka_seinou"]+"</td>";
            tblFire += "<td class='col1'>"+item["raberu_jouji_kaihou"]+"</td>";
            tblFire += "<td class='col2'>"+item["raberu_nintei"]+"</td>";
            tblFire += "<td class='col2'>"+item["raberu_shaen"]+"</td>";
            tblFire += "<td class='col2'>"+item["hou_jouji_kaihou"]+"</td>";
            tblFire += "<td class='col2'>"+item["hou_ninteihin"]+"</td>";
            tblFire += "<td class='col2'>"+item["hou_shaen"]+"</td>";
        tblFire += "</tr>";
        
        tblMoji += "<tr>";
            tblMoji += "<td class='col1'>"+item["element_id"]+"</td>";
            tblMoji += "<td class='col1'>"+item["seinou_kimitsu"]+"</td>";
            tblMoji += "<td class='col2'>"+item["seinou_shaon"]+"</td>";
            tblMoji += "<td class='col1'>"+item["moji_undercut"]+"</td>";
            tblMoji += "<td class='col1'>"+item["moji_garasu_size"]+"</td>";
            tblMoji += "<td class='col2'>"+item["moji_garari_size"]+"</td>";
            tblMoji += "<td class='col2'>"+item["moji_bikou"]+"</td>";
            tblMoji += "<td class='col2'>"+item["moji_tobira_atsu"]+"</td>";
            tblMoji += "<td class='col2'>"+item["moji_hontai_kikou"]+"</td>";
            tblMoji += "<td class='col2'></td>";
            tblMoji += "<td class='col2'></td>";
            tblMoji += "<td class='col2'></td>";
        tblMoji += "</tr>";
        
        tblIdenInfo += "<tr>";
            tblIdenInfo += "<td class='col1'>"+item["element_id"]+"</td>";
            tblIdenInfo += "<td class='col1'>"+item["keijou_garari"]+"</td>";
            tblIdenInfo += "<td class='col2'>"+item["keijou_meshiawase"]+"</td>";
            tblIdenInfo += "<td class='col1'>"+item["keijou_sugata"]+"</td>";
            tblIdenInfo += "<td class='col1'>"+item["keijyou_waku"]+"</td>";
            tblIdenInfo += "<td class='col2'>"+item["keijou_kutsuzui"]+"</td>";
            tblIdenInfo += "<td class='col2'>"+item["fugou_bangou_omo"]+"</td>";
            tblIdenInfo += "<td class='col2'>"+item["fugou_bangou_fuku"]+"</td>";
            tblIdenInfo += "<td class='col2'>"+item["fugou_kigou"]+"</td>";
            tblIdenInfo += "<td class='col2'>"+item["fugou_denki"]+"</td>";
            tblIdenInfo += "<td class='col2'>"+item["taika_toukyuu"]+"</td>";
            tblIdenInfo += "<td class='col2'>"+item["seizoumoto"]+"</td>";
            tblIdenInfo += "<td class='col2'>"+item["setsumei"]+"</td>";
            tblIdenInfo += "<td class='col2'>"+item["kanamono_tokushu"]+"</td>";
            tblIdenInfo += "<td class='col2'>"+item["OmniClass_title"]+"</td>";
            tblIdenInfo += "<td class='col2'>"+item["Omniclass_number"]+"</td>";
            tblIdenInfo += "<td class='col2'></td>";
            tblIdenInfo += "<td class='col2'>"+item["comment"]+"</td>";
            tblIdenInfo += "<td class='col2'>"+item["workset"]+"</td>";
        tblIdenInfo += "</tr>";    
        
    });
    $.each(data["window"],function(key,item){
         tblOverall += " <tr id='"+item["element_db_id"]+"'>";
            tblOverall += "<td class='col2'>"+item["element_id"]+"</td>";
            tblOverall += "<td class='col2'>"+item["type_name"]+"</td>";
            tblOverall += "<td class='col3'>"+item["level"]+"</td>";
            tblOverall += "<td class='col4'>"+item["type_window_panel"]+"</td>";
            tblOverall += "<td class='col2'></td>";
            tblOverall += "<td class='col2'>"+item["kanamono_shiji"]+"</td>";
            tblOverall += "<td class='col2'>"+item["kanamono_shimari"]+"</td>";
        tblOverall += "</tr>";
        
        tblSunpou += " <tr>";
            tblSunpou += "<td class='col2'>"+item["element_id"]+"</td>";
            tblSunpou += "<td class='col2'>"+item["type_name"]+"</td>";
            tblSunpou += "<td class='col3'>"+item["width"]+"</td>";
            tblSunpou += "<td class='col3'>"+item["height"]+"</td>";
            tblSunpou += "<td class='col2'></td>";
            tblSunpou += "<td class='col2'>"+item["full_width"]+"</td>";
            tblSunpou += "<td class='col2'>"+item["full_height"]+"</td>";
        tblSunpou += "</tr>";
        
         tblMaterial += " <tr>";
            tblMaterial += "<td class='col1'>"+item["element_id"]+"</td>";
            tblMaterial += "<td class='col1'>"+item["shiyou_garasu_shiyou"]+"</td>";
            tblMaterial += "<td class='col1'></td>";
            tblMaterial += "<td class='col1'>"+item["shiyou_hontai_shiage"]+"</td>";
            tblMaterial += "<td class='col2'>"+item["shiyou_hontai_zaishitus"]+"</td>";
            tblMaterial += "<td class='col2'>"+item["shiyou_gakubuchi_shiage"]+"</td>";
            tblMaterial += "<td class='col2'></td>";
            tblMaterial += "<td class='col2'></td>";
        tblMaterial += "</tr>";
        
        tblFire += " <tr>";
            tblFire += "<td class='col1'>"+item["element_id"]+"</td>";
            tblFire += "<td class='col1'>"+item["label_fire_protection"]+"</td>";
            tblFire += "<td class='col1'>"+item["law_fire_protection"]+"</td>";
            tblFire += "<td class='col1'></td>";
            tblFire += "<td class='col2'></td>";
            tblFire += "<td class='col2'></td>";
            tblFire += "<td class='col2'></td>";
            tblFire += "<td class='col2'></td>";
            tblFire += "<td class='col2'></td>";
        tblFire += "</tr>";
        
        tblMoji += "<tr>";
            tblMoji += "<td class='col1'>"+item["element_id"]+"</td>";
            tblMoji += "<td class='col1'>"+item["seinou_kimitsu"]+"</td>";
            tblMoji += "<td class='col2'>"+item["seinou_shaon"]+"</td>";
            tblMoji += "<td class='col1'></td>";
            tblMoji += "<td class='col1'></td>";
            tblMoji += "<td class='col2'>"+item["moji_garari_saizu"]+"</td>";
            tblMoji += "<td class='col2'>"+item["moji_bikou"]+"</td>";
            tblMoji += "<td class='col2'></td>";
            tblMoji += "<td class='col2'>"+item["moji_hontai_kikou"]+"</td>";
            tblMoji += "<td class='col2'>"+item["shiyou_gakubuchi"]+"</td>";
            tblMoji += "<td class='col2'>"+item["seinou_taifuu_atsu"]+"</td>";
            tblMoji += "<td class='col2'>"+item["seinou_suimitsu"]+"</td>";
        tblMoji += "</tr>";
        
        tblIdenInfo += "<tr>";
            tblIdenInfo += "<td class='col1'>"+item["element_id"]+"</td>";
            tblIdenInfo += "<td class='col1'>"+item["keijou_garari"]+"</td>";
            tblIdenInfo += "<td class='col2'></td>";
            tblIdenInfo += "<td class='col1'></td>";
            tblIdenInfo += "<td class='col1'></td>";
            tblIdenInfo += "<td class='col2'></td>";
            tblIdenInfo += "<td class='col2'>"+item["fugou_bangou_omo"]+"</td>";
            tblIdenInfo += "<td class='col2'>"+item["fugou_bangou_fuku"]+"</td>";
            tblIdenInfo += "<td class='col2'>"+item["fugou_kigou"]+"</td>";
            tblIdenInfo += "<td class='col2'></td>";
            tblIdenInfo += "<td class='col2'></td>";
            tblIdenInfo += "<td class='col2'>"+item["manufacturer"]+"</td>";
            tblIdenInfo += "<td class='col2'>"+item["description"]+"</td>";
            tblIdenInfo += "<td class='col2'>"+item["kanamono_tokushu"]+"</td>";
            tblIdenInfo += "<td class='col2'>"+item["OmniClass_title"]+"</td>";
            tblIdenInfo += "<td class='col2'>"+item["OmniClass_number"]+"</td>";
            tblIdenInfo += "<td class='col2'>"+item["keijou_mizukiri"]+"</td>";
            tblIdenInfo += "<td class='col2'>"+item["comment"]+"</td>";
            tblIdenInfo += "<td class='col2'>"+item["workset"]+"</td>";
        tblIdenInfo += "</tr>"; 
    });
    
    tblOverall += "</tbody>";
    tblSunpou += "</tbody>";
    tblMaterial += "</tbody>";
    tblFire += "</tbody>";
    tblMoji += "</tbody>";
    tblIdenInfo += "</tbody>";

    
    AppendTable("tblOverall",tblOverall);
    $("#Overall").find("span").removeClass("glyphicon-plus");
    $("#Overall").find("span").addClass("glyphicon-minus");
    
    AppendTable("tblSunpou",tblSunpou);
    $("#tblSunpou").hide();
    
    AppendTable("tblMaterial",tblMaterial);
    $("#tblMaterial").hide();
    
    AppendTable("tblFire",tblFire);
    $("#tblFire").hide();
    
    AppendTable("tblMoji",tblMoji);
    $("#tblMoji").hide();
    
    AppendTable("tblIdenInfo",tblIdenInfo);
    $("#tblIdenInfo").hide();
}

function AppendTable(tblId, appendStr){
    $("#"+tblId+" thead").remove();
    $("#"+tblId+" tbody").remove();
    $("#"+tblId).append(appendStr);
}

var isOpen = true;
function toggleFilter(){
    if(isOpen){
        $(".menu-content").css("display","none");
        isOpen = false;
    }else{
        $(".menu-content").css("display","block");
        isOpen = true;
    }
}


function TableRowClick(){

    $("#tblOverall").on('click', 'tbody tr', function(){
       var element_id = $(this).find("td:first").text();
       var element_db_id = $(this).attr('id');
       //alert(element_db_id);
       $("#tblOverall > tbody> tr").removeClass("active");
       $(this).toggleClass("active");
       
       ScrollTopToSelectedRow("tblSunpou",element_id);
       ScrollTopToSelectedRow("tblMaterial",element_id);
       ScrollTopToSelectedRow("tblMoji",element_id);
       ScrollTopToSelectedRow("tblFire",element_id);
       ScrollTopToSelectedRow("tblIdenInfo",element_id);
       if(element_db_id != undefined)
        ViewerHighLight(element_db_id);
      
    });

}


function ScrollTopToSelectedRow(tblId,element_id){
     $("#"+tblId+" tbody tr").each(function() { 
         
        var current_id = $(this).find("td:first").text();
        if(element_id == current_id){
             var rowpos = $(this).position();
             //var thHeight = $("#"+tblId+" thead tr").find("th:first").height();
             var current_td_height = $(this).find("td:first").innerHeight();

             var scroll_position = 0;
             switch(tblId){
                 case "tblSunpou" :scroll_position = rowpos.top+pretblSunpoScrollPosition;break;
                 case "tblMaterial" :scroll_position = rowpos.top+pretblMaterialScrollPosition;break;
                 case "tblFire" :scroll_position = rowpos.top+pretblFireScrollPosition;break;
                 case "tblMoji" :scroll_position = rowpos.top+pretblMojiScrollPosition;break;
                 case "tblIdenInfo" :scroll_position = rowpos.top+pretblIdenInfoScrollPosition;break;
             }
             //var scroll_position = rowpos.top+preScrollPosition-thHeight;//minus 62 for thead value
             $("#"+tblId).show();
             $("ul > li").each(function() {
                 var li_Id =$(this).attr('id');
                 if(li_Id != "Overall"){
                    var span = $(this).find('span');
                    span.addClass("glyphicon-plus");
                    span.removeClass("glyphicon-minus");
                 }
             });
             $("#"+tblId+" > tbody").animate({scrollTop:scroll_position});
             $("#"+tblId+" > tbody").css("height",current_td_height+5);

              switch(tblId){
                 case "tblSunpou" :pretblSunpoScrollPosition += rowpos.top;break;
                 case "tblMaterial" :pretblMaterialScrollPosition += rowpos.top;break;
                 case "tblFire" :pretblFireScrollPosition += rowpos.top;break;
                 case "tblMoji" :pretblMojiScrollPosition += rowpos.top;break;
                 case "tblIdenInfo" :pretblIdenInfoScrollPosition += rowpos.top;break;
             }
        }
    });
}

function PlusSignClick(){
    $("ul > li").on('click',function(){
        //remove tblOverall Active row
        //$("#tblOverall > tbody > tr").removeClass("active");
        
        var icon_class = $(this).find('span').attr('class');
        if(icon_class == undefined)return;
        
        if(icon_class.includes("glyphicon-plus")){//plus
            var id= $(this).attr('id');
            $("#tbl"+id).slideDown();
            $("#tbl"+id+" > tbody").css("height","200px");
            var span = $(this).find('span');
            span.removeClass("glyphicon-plus");
            span.addClass("glyphicon-minus");
        }else{
            var span = $(this).find('span');
            span.addClass("glyphicon-plus");
            span.removeClass("glyphicon-minus");
            var id= $(this).attr('id');
            $("#tbl"+id).slideUp();
        }
        
    });
}
