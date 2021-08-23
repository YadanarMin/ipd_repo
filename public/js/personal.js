/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
var g_personalData = {};
var g_allStoreData = [];
var g_authorityData = [];

$(document).ready(function(){
	
    var login_user_id = $("#hidLoginID").val();
    var img_src = "../public/image/user_settings.png";
    var url = "personal/index";
    var content_name = "統合人員管理";
    recordAccessHistory(login_user_id,img_src,url,content_name);
    
    
    initialize();
    getAllStoreData();
    getAuthorityData();
	
	$("#allperson").addClass("tab_switch_on");
    $("#personalField").addClass("detailDisplayNone");
	
    $('#userCategorySelect button').on('click', function(){
        var id = $(this).attr("id");
        // console.log(id);
        filterUserCategory(id);
    });
    
    $("#personalNameList tbody").on("click","tr", function(e){

        if($(this).hasClass('selected')){
            return;
        }
        return;
        var userName = $(this).text();
        var rrr = $(this).html();
        var tempAry = rrr.split("value");

        var cur_id = tempAry[0];
        var start_idx = cur_id.indexOf('"') + 1;
        var end_idx = cur_id.indexOf('"', start_idx);
        cur_id = cur_id.substring(start_idx, end_idx);
        
        rrr = tempAry[1];
        var start_idx = rrr.indexOf('"') + 1;
        var end_idx = rrr.indexOf('"', start_idx);
        var categoryName = rrr.substring(start_idx, end_idx);
        var array = categoryName.split(',');
        var department = "";
        categoryName = array[0];
        // department = array[1];
        
        // console.log(rrr);
        // console.log(cur_id);
        // console.log(userName);
        // console.log(categoryName);
        // console.log(department);
        
        $(this).addClass("selected").siblings().removeClass("selected");
        displayPersonalInfo(categoryName,userName,cur_id);
    });
});

function initialize(){
    // console.log("initialize start");
    
    $.ajax({
        url: "../personal/getData",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"",id:0},
        success :function(data) {
            if(data != null){
            	console.log(data);
            	g_personalData = data;
			    createUserList(data);
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function getAllStoreData(){
    // console.log("getAllStoreData start");
    
    $.ajax({
        url: "../personal/getData",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"allStore",id:0},
        success :function(data) {
            if(data != null){
            	console.log(data);
            	g_allStoreData = data;
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function getAuthorityData(){
    
    $.ajax({
        url: "../user/getAuthorityData",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"getAllAuthority",authority_id:0,authority_name:""},
        success :function(result) {
           if(result.length > 0){
                // console.log(result);
                g_authorityData = result;
           }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function createUserList(data){
    
    var appendText = "";
    if(data.length === 0){
        return;
    }
    
    for (let key in data) {
        var curArray = data[key];
        var nameKey = "name";

        if(key === "userCode"){
            continue;
        }else if(key === "login"){
            
        }else if(key === "foreign"){
            nameKey = "username";
        }else if(key === "partnerComp"){
            nameKey = "inchargeName";
        }else if(key === "partnerCompCont"){
            nameKey = "partnerInchargeName";
        }else{
            
        }
        
        var name = "";
        for(var i = 0; i < curArray.length; i++){
            if(curArray[i][nameKey] === null){
                continue;
            }
            name = curArray[i][nameKey];
            
            appendText += "<tr>";
            if(key === "login"){
                var dept = curArray[i]["department"];
                if(dept === "大林組"){
                    appendText +=     "<td id='user"+i.toString()+"' value='" + key + "," + dept + "' style='width:300px;'>" + name + "</td>";
                    appendText +=     "<td style='width:39px;padding-left:12px;'><input type='hidden' value=''><input id='display_user_flag' type='checkbox' value='"+name+","+key+","+dept+"'></td>";

                }else{
                    appendText +=     "<td id='user"+i.toString()+"' style='display:none;' value='" + key + "," + dept + "'>" + name + "</td>";
                    appendText +=     "<td style='display:none;'><input type='hidden' value='' style='display:none;'><input id='display_user_flag' style='display:none;' type='checkbox' value='"+name+","+key+","+dept+"'></td>";
                }
                
            }else{
                appendText +=     "<td id='user"+i.toString()+"' value='" + key + "' style='width:300px;'>" + name + "</td>";
                appendText +=     "<td style='width:39px;padding-left:12px;'><input type='hidden' value=''><input id='display_user_flag' type='checkbox' value='"+name+","+key+","+dept+"'></td>";
            }
            appendText += "</tr>";
        }
    }
    
    // return;
    $("#userList").append(appendText);
}

function filterUserCategory(id){
    
    console.log("filterUserCategory start:" + id);
    
    var curCategory = "";
    
    if(id === "allperson"){
        
        curCategory = "allperson";
        $("#partner").removeClass("tab_switch_on");
        $("#subcon").removeClass("tab_switch_on");
        $("#employee").removeClass("tab_switch_on");
        $("#excStudent").removeClass("tab_switch_on");
        $("#"+id).addClass("tab_switch_on");
        
    }else if(id === "excStudent"){
        
        curCategory = "foreign";
        $("#allperson").removeClass("tab_switch_on");
        $("#partner").removeClass("tab_switch_on");
        $("#subcon").removeClass("tab_switch_on");
        $("#employee").removeClass("tab_switch_on");
        $("#"+id).addClass("tab_switch_on");
        
    }else if(id === "employee"){
        
        curCategory = "login";
        $("#allperson").removeClass("tab_switch_on");
        $("#partner").removeClass("tab_switch_on");
        $("#subcon").removeClass("tab_switch_on");
        $("#excStudent").removeClass("tab_switch_on");
        $("#"+id).addClass("tab_switch_on");
        
    }else if(id === "subcon"){
        
        curCategory = "partnerComp";
        $("#allperson").removeClass("tab_switch_on");
        $("#partner").removeClass("tab_switch_on");
        $("#employee").removeClass("tab_switch_on");
        $("#excStudent").removeClass("tab_switch_on");
        $("#"+id).addClass("tab_switch_on");
        
    }else if(id === "partner"){
        
        curCategory = "partnerCompCont";
        $("#allperson").removeClass("tab_switch_on");
        $("#subcon").removeClass("tab_switch_on");
        $("#employee").removeClass("tab_switch_on");
        $("#excStudent").removeClass("tab_switch_on");
        $("#"+id).addClass("tab_switch_on");
        
    }else{
        // NOP
    }
    
    $("#userList tr").each(function(index){
        var rrr = $(this).html();
        
        var tempAry = rrr.split("value");

        var cur_id = tempAry[0];
        var start_idx = cur_id.indexOf('"') + 1;
        var end_idx = cur_id.indexOf('"', start_idx);
        cur_id = cur_id.substring(start_idx, end_idx);
        
        rrr = tempAry[1];
        var start_idx = rrr.indexOf('"') + 1;
        var end_idx = rrr.indexOf('"', start_idx);
        var categoryName = rrr.substring(start_idx, end_idx);
        var department = "";
        
        if(curCategory === "login" || id === "allperson"){
            var array = categoryName.split(',');
            categoryName = array[0];
            department = array[1];
        }

        if(curCategory === categoryName || curCategory === "allperson"){
            if(categoryName === "login" && department !== "大林組"){
                $(this).hide();
            }else{
                $(this).show();
            }
        }else{
            $(this).hide();
        }
    });

}

function displayPersonalInfo(categoryName,userName,cur_id){
    
    // console.log(g_personalData);

    var appendText = createDetailCardHTML(categoryName,userName,cur_id);

    $("#personalList").addClass("cardScroll");
    $("#personalField li").remove();
    $("#personalField").append(appendText);
}

function createDetailCardHTML(categoryName,userName,cur_id){
    
    var appendText = "";
    var email = "";
    var phoneNum = "";
    var workLocation = "";
    var organization = "";
    var userCode = "";
    var isExistRevit = false;
    var isExpSatellite = false;
    var partnerJobType = "";
    var postalCode = "";
    var foreignInfo = {"name":"","start":"","end":""};
    var currentData = g_personalData[categoryName];
    var codeData = g_personalData["code"];
    var loginData = g_personalData["login"];
    var foreignData = g_personalData["foreign"];
    var authorityId = 0;
    var authorityName = "";
    
    var isAuthorityCCC = false;
    
    if(categoryName === "login"){

        for(var i=0; i<currentData.length; i++){
            var curName = currentData[i]["name"];
            if(curName === userName){
                organization = currentData[i]["organization"];
                email = currentData[i]["email"];
                authorityId = loginData[i]["authority_id"];
                isAuthorityCCC = true;
                break;
            }
        }
        
        for(var i=0; i<foreignData.length; i++){
            var curName = foreignData[i]["username"] === null ? "" : foreignData[i]["username"];
            if(curName === userName){
                foreignInfo["name"] = foreignData[i]["s_type"] === null ? "" : foreignData[i]["s_type"];
                foreignInfo["start"] = foreignData[i]["s_startDate"] === null ? "" : foreignData[i]["s_startDate"];
                foreignInfo["end"] = foreignData[i]["s_endDate"] === null ? "" : foreignData[i]["s_endDate"];
                break;
            }
        }

    }else if(categoryName === "foreign"){
        
        for(var i=0; i<currentData.length; i++){
            var curName = currentData[i]["username"];
            if(curName === userName){
                userCode = currentData[i]["s_code"] === null ? "" : currentData[i]["s_code"];
                break;
            }
        }
        
        for(var i=0; i<loginData.length; i++){
            var curName = loginData[i]["name"];
            if(curName === userName){
                organization = loginData[i]["organization"];
                email = loginData[i]["email"];
                authorityId = loginData[i]["authority_id"];
                isAuthorityCCC = true;
                break;
            }
        }
        
        for(var i=0; i<foreignData.length; i++){
            var curName = foreignData[i]["username"] === null ? "" : foreignData[i]["username"];
            if(curName === userName){
                foreignInfo["name"] = foreignData[i]["s_type"] === null ? "" : foreignData[i]["s_type"];
                foreignInfo["start"] = foreignData[i]["s_startDate"] === null ? "" : foreignData[i]["s_startDate"];
                foreignInfo["end"] = foreignData[i]["s_endDate"] === null ? "" : foreignData[i]["s_endDate"];
                break;
            }
        }
        
    }else if(categoryName === "partnerComp"){
        
        for(var i=0; i<currentData.length; i++){
            var curName = currentData[i]["inchargeName"];
            if(curName === userName){
                organization = currentData[i]["companyName"];
                email = currentData[i]["email"] === null ? "" : currentData[i]["email"];
                phoneNum = currentData[i]["phone"] === null ? "" : currentData[i]["phone"];
                isExistRevit = currentData[i]["revit"] === null || currentData[i]["revit"] !== "〇" ? false : true;
                isExpSatellite = currentData[i]["satelliteExp"] === null || currentData[i]["satelliteExp"] !== "〇" ? false : true;
                break;
            }
        }
        
    }else if(categoryName === "partnerCompCont"){
        
        for(var i=0; i<currentData.length; i++){
            var curName = currentData[i]["partnerInchargeName"];
            if(curName === userName){
                organization = currentData[i]["partnerCompanyName"];
                email = currentData[i]["partnerEmail"] === null ? "" : currentData[i]["partnerEmail"];
                phoneNum = currentData[i]["partnerPhone"] === null ? "" : currentData[i]["partnerPhone"];
                partnerJobType = currentData[i]["partnerJobType"] === null ? "" : currentData[i]["partnerJobType"];
                postalCode = currentData[i]["partnerMailCode"] === null ? "" : currentData[i]["partnerMailCode"];
                workLocation = currentData[i]["partnerCompanyAddress"] === null ? "" : currentData[i]["partnerCompanyAddress"];
                break;
            }
        }
        
    }else{
        //NOP
    }
    
    
    for(var i=0; i<codeData.length; i++){
        var curName = codeData[i]["name"];
        if(curName === userName){
            userCode = codeData[i]["user_code"];
            break;
        }
    }

    var matchList = [];    
    for(var i=0; i<g_allStoreData.length; i++){
        
        var tmpMatchInfo = chkUserNameByAllStore(g_allStoreData[i], userName);
        if(Object.keys(tmpMatchInfo).length !== 0){
            matchList.push(tmpMatchInfo);
        }
    }
    
    if(foreignInfo["name"] !== ""){
        var name = foreignInfo["name"];
        var startDate = foreignInfo["start"];
        startDate = startDate.replace(/-/g, '');
        var endDate = foreignInfo["end"];
        endDate = endDate.replace(/-/g, '');
        var tmpArray = {};
        tmpArray = {"projectName":foreignInfo["name"], "matchTypeList":[],
                    "start":startDate, "end":endDate };
        matchList.push(tmpArray);
    }
    
    console.log(matchList);
    // matchList = sortDateStringDESC(matchList);
    matchList = [...matchList].sort((a, b) => b.start - a.start);
    console.log(matchList);
    
    if(authorityId !== 0){
        for(var i=0; i<g_authorityData.length; i++){
            
            var id = g_authorityData[i]["id"];
            if(id === authorityId){
                authorityName = g_authorityData[i]["name"];
                break;
            }
        }
    }

    appendText += "<li class='personalData'>";
    appendText +=     "<div class='personalOverview' id='personalOverview_"+cur_id+"'>";

    appendText +=         "<div class='pimage' id='pimage_"+cur_id+"'>";

    appendText +=             "<figure class='pfiguimage' id='pfiguimage_"+cur_id+"'>";
    appendText +=                 "<img src='/iPD/public/image/personal_df.png' alter='氏名' width='110px' style='margin:20px 10px 0 20px;'>";
    appendText +=             "</figure>";

    // appendText +=             "<figure class='pfiguimage' id='pfiguimage_"+cur_id+"'>";
    // appendText +=                 "<img src='/iPD/public/image/profile_background.png' alter='背景' width='130px' style='position:absolute;z-index:1;'>";
    // appendText +=                 "<img src='/iPD/public/image/personal_df.png' alter='氏名' width='110px' style='position:absolute;z-index:2;margin:20px 10px 0 10px;'>";
    // // appendText +=                 "<figcaption class='pfigcimage' id='pfigcimage_"+cur_id+"'>" + userName + "<br>" + organization + "</figcaption>";
    // appendText +=             "</figure>";

    appendText +=             "<div style='text-align:center;font-size:large;'>" + userName;
    appendText +=             "</div>";
    appendText +=             "<div style='color:gray;font-size:xx-small;margin:0 23px;'>" + organization;
    appendText +=             "</div>";
    appendText +=         "</div>";

    appendText +=         "<div class='pworkstatus' id='pworkstatus_"+cur_id+"'>";
    appendText +=             "<div style='margin:20px 0 0 14px;font-size:large;'>Work Status</div>";
    
    if(categoryName === "login"){

        if(userCode !== ""){
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 16px;'>個人コード：</div><div>" + userCode + "</div></div>";
        }else{
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 16px;'>個人コード：</div><div style='color:silver;'>No Data</div></div>";
        }
        if(email !== ""){
            appendText +=             "<div style='display:flex;margin:5px 0 0 0;'><div style='margin:0 0 0 53px;'>MAIL：</div><div>" + email + "</div></div>";
        }else{
            appendText +=             "<div style='display:flex;margin:5px 0 0 0;'><div style='margin:0 0 0 53px;'>MAIL：</div><div style='color:silver;'>No Data</div></div>";
        }
        if(phoneNum !== ""){
            appendText +=             "<div style='display:flex;margin:5px 0 0 0;'><div style='margin:0 0 0 60px;'>TEL：</div><div>" + phoneNum + "</div></div>";
        }else{
            appendText +=             "<div style='display:flex;margin:5px 0 0 0;'><div style='margin:0 0 0 60px;'>TEL：</div><div style='color:silver;'>No Data</div></div>";
        }
        if(workLocation !== ""){
            appendText +=             "<div style='display:flex;margin:5px 0 0 0;'><div style='margin:0 0 0 44px;'>勤務地：</div><div>" + workLocation + "</div></div>";
        }else{
            appendText +=             "<div style='display:flex;margin:5px 0 0 0;'><div style='margin:0 0 0 44px;'>勤務地：</div><div style='color:silver;'>No Data</div></div>";
        }
        // appendText +=             "<div style='display:flex;'>";
        if(foreignInfo["name"] !== ""){
            appendText +=                 "<div style='display:flex;margin:5px 0 0 0;'><div style='margin:0 0 0 30px;'>留学経験：</div><div style='color:coral;'>有</div></div>";
        }else{
            appendText +=                 "<div style='display:flex;margin:5px 0 0 0;'><div style='margin:0 0 0 30px;'>留学経験：</div><div style=''>無</div></div>";
        }
        if(isAuthorityCCC){
            appendText +=                 "<div style='display:flex;margin:5px 0 0 0;'><div style='margin:0 0 0 29px;'>CCC権限：</div><div style=''>" + authorityName + "</div></div>";
        }else{
            appendText +=                 "<div style='display:flex;margin:5px 0 0 0;'><div style='margin:0 0 0 29px;'>CCC権限：</div><div style=''>無</div></div>";
        }
        // appendText +=             "</div>";

    }else if(categoryName === "foreign"){
        
        if(userCode !== ""){
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 50px;'>個人コード：</div><div>" + userCode + "</div></div>";
        }else{
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 50px;'>個人コード：</div><div style='color:silver;'>No Data</div></div>";
        }
        if(email !== ""){
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 87px;'>MAIL：</div><div>" + email + "</div></div>";
        }else{
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 87px;'>MAIL：</div><div style='color:silver;'>No Data</div></div>";
        }
        if(phoneNum !== ""){
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 94px;'>TEL：</div><div>" + phoneNum + "</div></div>";
        }else{
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 94px;'>TEL：</div><div style='color:silver;'>No Data</div></div>";
        }
        if(workLocation !== ""){
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 77px;'>勤務地：</div><div>" + workLocation + "</div></div>";
        }else{
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 77px;'>勤務地：</div><div style='color:silver;'>No Data</div></div>";
        }
        // appendText +=             "<div style='display:flex;'>";
        if(foreignInfo["name"] !== ""){
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 63px;'>留学経験：</div><div style='color:coral;'>有</div></div>";
        }else{
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 63px;'>留学経験：</div><div style=''>無</div></div>";
        }
        if(isAuthorityCCC){
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 19px;'>CCC権限：</div><div style=''>" + authorityName + "</div></div>";
        }else{
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 19px;'>CCC権限：</div><div style=''>無</div></div>";
        }
        // appendText +=             "</div>";
        
    }else if(categoryName === "partnerComp"){
        
        if(email !== ""){
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 87px;'>MAIL：</div><div>" + email + "</div></div>";
        }else{
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 87px;'>MAIL：</div><div style='color:silver;'>No Data</div></div>";
        }
        if(phoneNum !== ""){
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 94px;'>TEL：</div><div>" + phoneNum + "</div></div>";
        }else{
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 94px;'>TEL：</div><div style='color:silver;'>No Data</div></div>";
        }
        if(isExistRevit){
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 60px;'>Revit有無：</div><div style='color:coral;'>有</div></div>";
        }else{
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 60px;'>Revit有無：</div><div style=''>無</div></div>";
        }
        if(isExpSatellite){
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 22px;'>サテライト経験：</div><div style='color:coral;'>有</div></div>";
        }else{
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 22px;'>サテライト経験：</div><div style=''>無</div></div>";
        }

    }else if(categoryName === "partnerCompCont"){
        
        if(email !== ""){
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 87px;'>MAIL：</div><div>" + email + "</div></div>";
        }else{
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 87px;'>MAIL：</div><div style='color:silver;'>No Data</div></div>";
        }
        if(phoneNum !== ""){
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 94px;'>TEL：</div><div>" + phoneNum + "</div></div>";
        }else{
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 94px;'>TEL：</div><div style='color:silver;'>No Data</div></div>";
        }

        if(partnerJobType !== ""){
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 92px;'>職種：</div><div>" + partnerJobType + "</div></div>";
        }else{
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 92px;'>職種：</div><div style='color:silver;'>No Data</div></div>";
        }
        if(postalCode !== ""){
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 64px;'>郵便番号：</div><div>" + postalCode + "</div></div>";
        }else{
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 64px;'>郵便番号：</div><div style='color:silver;'>No Data</div></div>";
        }
        if(workLocation !== ""){
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 78px;'>勤務地：</div><div>" + workLocation + "</div></div>";
        }else{
            appendText +=             "<div style='display:flex;margin:10px 0 0 0;'><div style='margin:0 0 0 78px;'>勤務地：</div><div style='color:silver;'>No Data</div></div>";
        }

    }else{
        //NOP
    }

    appendText +=         "</div>";

    appendText +=     "</div>";
    
    appendText +=     "<hr>";
    
    appendText +=     "<div class='personalDetail' id='personalDetail_"+cur_id+"'>";

    appendText +=         "<div class='pbuilding' id='pbuilding_"+cur_id+"'>";
    appendText +=             "<div style='font-size:large;'>年表</div>";
    
    if(matchList.length !== 0){
        //物件関連start
        appendText +=         "<table>";
        appendText +=             "<thead><tr>";
        appendText +=                 "<th style='width:300px;text-align:center;'>項目名</th>";
        appendText +=                 "<th style='width:75px;text-align:center;'>開始</th>";
        appendText +=                 "<th style='width:75px;text-align:center;'>終了</th>";
        appendText +=             "</tr></thead>";
        appendText +=             "<tbody>";
    
        for(var i=0; i<matchList.length; i++){
            var pjName = matchList[i]["projectName"];
            var startDate = matchList[i]["start"];
            var endDate = matchList[i]["end"];
    
            appendText +=             "<tr>";
            appendText +=             "<td style='width:300px;'>" + pjName + "</td>";
            if(startDate !== ""){
                appendText +=             "<td style='width:75px;'>" + startDate + "</td>";
            }else{
                appendText +=             "<td style='width:75px;'> - </td>";
            }
            if(endDate !== ""){
                appendText +=             "<td style='width:75px;'>" + endDate + "</td>";
            }else{
                appendText +=             "<td style='width:75px;'> - </td>";
            }
            appendText +=             "</tr>";
        }
        
        appendText +=             "</tbody>";
        appendText +=         "</table>";
        //物件関連end
    }else{
        appendText +=         "<div style='color:silver;'>No Data</div>";
    }
    
    appendText +=         "</div>";

    appendText += "</li>";
    
    return appendText;
}

function chkUserNameByAllStore(storeData, userName){

    var result = {};
    if(!Object.keys(storeData).length){
        return result;
    }
    
    var tmpArray = [];
    tmpArray.push({ "key":"b_kouji_katyou","data":storeData['b_kouji_katyou'] });
    tmpArray.push({ "key":"b_eigyou_tantousya","data":storeData["b_eigyou_tantousya"] });
    tmpArray.push({ "key":"b_isyou_sekkei","data":storeData["b_isyou_sekkei"] });
    tmpArray.push({ "key":"b_isyou_model","data":storeData["b_isyou_model"] });
    tmpArray.push({ "key":"b_kouzou_sekkei","data":storeData["b_kouzou_sekkei"] });
    tmpArray.push({ "key":"b_kouzou_model","data":storeData["b_kouzou_model"] });
    tmpArray.push({ "key":"b_setubi_kuutyou_sekkei","data":storeData["b_setubi_kuutyou_sekkei"] });
    tmpArray.push({ "key":"b_setubi_kuutyou_model","data":storeData["b_setubi_kuutyou_model"] });
    tmpArray.push({ "key":"b_setubi_eisei_sekkei","data":storeData["b_setubi_eisei_sekkei"] });
    tmpArray.push({ "key":"b_setubi_eisei_model","data":storeData["b_setubi_eisei_model"] });
    tmpArray.push({ "key":"b_setubi_denki_sekkei","data":storeData["b_setubi_denki_sekkei"] });
    tmpArray.push({ "key":"b_setubi_denki_model","data":storeData["b_setubi_denki_model"] });
    tmpArray.push({ "key":"b_ss_designer_name","data":storeData["b_ss_designer_name"] });
    tmpArray.push({ "key":"b_ss_modeler_name","data":storeData["b_ss_modeler_name"] });
    tmpArray.push({ "key":"b_sekou_tantou","data":storeData["b_sekou_tantou"] });
    tmpArray.push({ "key":"b_seisan_modeler_name","data":storeData["b_seisan_modeler_name"] });
    tmpArray.push({ "key":"b_seisan_gijutu_tantou","data":storeData["b_seisan_gijutu_tantou"] });
    tmpArray.push({ "key":"b_sekisan_mitumori_tantou","data":storeData["b_sekisan_mitumori_tantou"] });
    tmpArray.push({ "key":"b_bim_maneka_tantou","data":storeData["b_bim_maneka_tantou"] });
    tmpArray.push({ "key":"b_bim_coordinator_tantou","data":storeData["b_bim_coordinator_tantou"] });
    tmpArray.push({ "key":"b_ipd_center_tantou","data":storeData["b_ipd_center_tantou"] });
    tmpArray.push({ "key":"b_bim_m","data":storeData["b_bim_m"] });
    
    var isMatch = false
    var matchTypeList = [];
    for(var i=0; i<tmpArray.length; i++){
        if(userName === tmpArray[i]["data"]){
            matchTypeList.push(tmpArray[i]["key"]);
            isMatch = true;
        }
    }
    
    if(matchTypeList.length > 0){
        var projectName = "";
        if(storeData["b_tmp_pj_name"] === ""){
            if(storeData["b_pj_name"] === ""){
                projectName = storeData["a_pj_name"];
            }else{
                if(storeData["b_pj_name"].indexOf('と同じ') != -1){
                    projectName = storeData["a_pj_name"];
                }else{
                    projectName = storeData["b_pj_name"];
                }
            }
        }else{
            projectName = storeData["b_tmp_pj_name"];
        }
        result = { "projectName":projectName, "matchTypeList":matchTypeList, "start":storeData["b_koutei_kouji_start"], "end":storeData["b_koutei_kouji_end"] };
    }
    
    // if(isMatch){
    //     console.log(matchTypeList);
    //     console.log(storeData);
    //     console.log(userName);
    //     console.log(tmpArray);
    //     console.log(result);
    // }

    return result;
}

function ResetPersonalDetail(){
    
    $("#personalNameList tbody tr").each(function() {
        $(this).removeClass("selected");
    });
    
    $("#personalField li").remove();
}

function sortDateStringDESC(data){
    // console.log(data);
    
    // for(var i=0; i<data.length; i++){
    //     var pjName = data[i]["projectName"];
    //     var startDate = data[i]["start"];
    //     var endDate = data[i]["end"];
        
        
    // }
}

function ShowPersonalDetail(){
    //一覧表示とカード表示どちらも表示する
    console.log("ShowPersonalDetail start");
    
    var checkedUserList = [];
    
    $("#userList tr").each(function(index){
        var curInput = $(this).find("#display_user_flag");
        var name_key_dept = curInput.val();
        var tmpArray = name_key_dept.split(",");
        var name = tmpArray[0];
        var type = tmpArray[1];
        var dept = tmpArray[2];
        
        var isChecked = curInput.prop("checked");
        if(isChecked){
           checkedUserList.push( { "type":type,"name":name,"dept":dept } );
        }
    });
    
    console.log(checkedUserList);
    
    DisplayUserDetail(checkedUserList);
}

function DisplayUserDetail(checkedUserList){
    
    if(checkedUserList.length === 0){
        return;
    }
    
    //一覧表示形式の作成
    var appendTableText = createDetailTableHTML(checkedUserList);

    $("#personalField li").remove();
    $("#detailTable tr td").remove();
    $("#detailTable").append(appendTableText);
    
    
    //カード形式のテーブル作成
    var appendCardText = "";
    $.each(checkedUserList,function(key,value){
         appendCardText += createDetailCardHTML(value["type"],value["name"],key);
    });
    
    if(!$("#personalList").hasClass("cardScroll")){
        $("#personalList").addClass("cardScroll");
    }
    
    $("#personalField").append(appendCardText);
    
    $("#detailTable").removeClass("detailDisplayNone");
    $(".personalData").addClass("detailDisplayNone");
}

function createDetailTableHTML(userList){
    var appendText = "";
    var code = "";
    var name = "";
    var dept = "";
    var mail = "";
    var tel = "";
    var address = "";
    var isStudyAb = "";
    var isAuthCCC = "";
    var isNenpyou = "";
    var isRevit = "";
    var isSatellite = "";
    
    appendText += "<thead>";
    appendText += "<tr>";

    appendText +=   "<th style='width:39px;'>No.</th>";
    appendText +=   "<th style='width:87px;'>個人コード</th>";
    appendText +=   "<th style='width:100px;'>氏名</th>";
    appendText +=   "<th style='width:250px;'>所属</th>";
    appendText +=   "<th style='width:250px;'>MAIL</th>";
    appendText +=   "<th style='width:150px;'>TEL</th>";
    appendText +=   "<th style='width:250px;'>勤務地</th>";
    appendText +=   "<th style='width:73px;'>留学経験</th>";
    appendText +=   "<th style='width:100px;'>CCC権限</th>";
    appendText +=   "<th style='width:48px;'>年表</th>";
    appendText +=   "<th style='width:80px;'>Revit有無</th>";
    appendText +=   "<th style='width:115px;'>サテライト経験</th>";

    appendText +=   "</tr>";
    appendText += "</thead>";
    
    appendText += "<tbody>";

    $.each(userList,function(key,value){
        appendText += "<tr>";
        
        var userAttr = getUserAttr(value);
        /*
    result = {  "code":userCode,"name":name,"dept":organization,
                "email":email,"tel":phoneNum,"address":workLocation,
                "isRevit":isExistRevit,"isSatellite":isExpSatellite,"isAuthCCC":isAuthorityCCC
                "foreignInfo":foreignInfo, "matchList":matchList};
        */

        var tmpIsRevit = "";
        if(userAttr["isRevit"] === "0"){
            tmpIsRevit = "無";
        }else if(userAttr["isRevit"] === "1"){
            tmpIsRevit = "有";
        }else{
            tmpIsRevit = "-";
        }
        var tmpIsSatellite = "";
        if(userAttr["isSatellite"] === "0"){
            tmpIsSatellite = "無";
        }else if(userAttr["isSatellite"] === "1"){
            tmpIsSatellite = "有";
        }else{
            tmpIsSatellite = "-";
        }
        var tmpIsAuthCCC = "";
        if(userAttr["isAuthCCC"]){
            tmpIsAuthCCC = userAttr["authorityName"];
        }else{
            tmpIsAuthCCC = "無";
        }
        var tmpIsStudyAbload = ""
        if(userAttr["foreignInfo"]["name"] === ""){
            tmpIsStudyAbload = "無";
        }else{
            tmpIsStudyAbload = "有";
        }

        var index = key + 1 ;
        appendText += "<td style='width:39px;text-align:center;'>"+ index +"</td>";
        appendText += "<td style='width:87px;'>"+ userAttr["code"] +"</td>";
        appendText += "<td style='width:100px;'>"+ userAttr["name"] +"</td>";
        appendText += "<td style='width:250px;'>"+ userAttr["dept"] +"</td>";
        appendText += "<td style='width:250px;'>"+ userAttr["email"] +"</td>";
        appendText += "<td style='width:150px;'>"+ userAttr["tel"] +"</td>";
        appendText += "<td style='width:250px;'>"+ userAttr["address"] +"</td>";
        appendText += "<td style='width:73px;text-align:center;'>"+ tmpIsStudyAbload +"</td>";
        appendText += "<td style='width:100px;'>"+ tmpIsAuthCCC +"</td>";
        appendText += "<td style='width:48px;'>LINK</td>";    //ここにリンクを入れる
        appendText += "<td style='width:80px;text-align:center;'>"+ tmpIsRevit +"</td>";
        appendText += "<td style='width:115px;text-align:center;'>"+ tmpIsSatellite +"</td>";
        
        appendText += "</tr>";    
    });
    
    appendText += "</tbody>";

    return appendText;
}

function getUserAttr(data){
    
    var result = {};
    var userName = data["name"];
    var categoryName = data["type"];
    var in_dept = data["dept"];

    var userCode = "";
    var organization = "";
    var email = "";
    var phoneNum = "";
    var workLocation = "";
    var isExistRevit = "";
    var isExpSatellite = "";
    var isAuthorityCCC = false;

    var partnerJobType = "";
    
    var postalCode = "";
    var foreignInfo = {"name":"","start":"","end":""};
    var currentData = g_personalData[categoryName];
    var codeData = g_personalData["code"];
    var loginData = g_personalData["login"];
    var foreignData = g_personalData["foreign"];
    var authorityId = 0;
    var authorityName = "";
    
    if(categoryName === "login"){

        for(var i=0; i<currentData.length; i++){
            var curName = currentData[i]["name"];
            if(curName === userName){
                organization = currentData[i]["organization"];
                email = currentData[i]["email"];
                phoneNum = "";
                workLocation = "";
                isExistRevit = "";
                isExpSatellite = "";
                authorityId = loginData[i]["authority_id"];
                isAuthorityCCC = true;
                break;
            }
        }
        
        for(var i=0; i<foreignData.length; i++){
            var curName = foreignData[i]["username"] === null ? "" : foreignData[i]["username"];
            if(curName === userName){
                foreignInfo["name"] = foreignData[i]["s_type"] === null ? "" : foreignData[i]["s_type"];
                foreignInfo["start"] = foreignData[i]["s_startDate"] === null ? "" : foreignData[i]["s_startDate"];
                foreignInfo["end"] = foreignData[i]["s_endDate"] === null ? "" : foreignData[i]["s_endDate"];
                break;
            }
        }

    }else if(categoryName === "foreign"){
        
        for(var i=0; i<currentData.length; i++){
            var curName = currentData[i]["username"];
            if(curName === userName){
                userCode = currentData[i]["s_code"] === null ? "" : currentData[i]["s_code"];
                break;
            }
        }
        
        for(var i=0; i<loginData.length; i++){
            var curName = loginData[i]["name"];
            if(curName === userName){
                organization = loginData[i]["organization"];
                email = loginData[i]["email"];
                phoneNum = "";
                workLocation = "";
                isExistRevit = "";
                isExpSatellite = "";
                authorityId = loginData[i]["authority_id"];
                isAuthorityCCC = true;
                break;
            }
        }
        
        for(var i=0; i<foreignData.length; i++){
            var curName = foreignData[i]["username"] === null ? "" : foreignData[i]["username"];
            if(curName === userName){
                foreignInfo["name"] = foreignData[i]["s_type"] === null ? "" : foreignData[i]["s_type"];
                foreignInfo["start"] = foreignData[i]["s_startDate"] === null ? "" : foreignData[i]["s_startDate"];
                foreignInfo["end"] = foreignData[i]["s_endDate"] === null ? "" : foreignData[i]["s_endDate"];
                break;
            }
        }
        
    }else if(categoryName === "partnerComp"){
        
        for(var i=0; i<currentData.length; i++){
            var curName = currentData[i]["inchargeName"];
            if(curName === userName){
                organization = currentData[i]["companyName"];
                email = currentData[i]["email"] === null ? "" : currentData[i]["email"];
                phoneNum = currentData[i]["phone"] === null ? "" : currentData[i]["phone"];
                workLocation = "";
                isExistRevit = currentData[i]["revit"] === null || currentData[i]["revit"] !== "〇" ? "0" : "1";
                isExpSatellite = currentData[i]["satelliteExp"] === null || currentData[i]["satelliteExp"] !== "〇" ? "0" : "1";
                authorityId = 0;
                break;
            }
        }
        
    }else if(categoryName === "partnerCompCont"){
        
        for(var i=0; i<currentData.length; i++){
            var curName = currentData[i]["partnerInchargeName"];
            if(curName === userName){
                organization = currentData[i]["partnerCompanyName"];
                email = currentData[i]["partnerEmail"] === null ? "" : currentData[i]["partnerEmail"];
                phoneNum = currentData[i]["partnerPhone"] === null ? "" : currentData[i]["partnerPhone"];
                workLocation = currentData[i]["partnerCompanyAddress"] === null ? "" : currentData[i]["partnerCompanyAddress"];
                isExistRevit = "";
                isExpSatellite = "";
                partnerJobType = currentData[i]["partnerJobType"] === null ? "" : currentData[i]["partnerJobType"];
                postalCode = currentData[i]["partnerMailCode"] === null ? "" : currentData[i]["partnerMailCode"];
                authorityId = 0;
                break;
            }
        }
        
    }else{
        //NOP
    }
    
    
    for(var i=0; i<codeData.length; i++){
        var curName = codeData[i]["name"];
        if(curName === userName){
            userCode = codeData[i]["user_code"];
            break;
        }
    }

    var matchList = [];    
    for(var i=0; i<g_allStoreData.length; i++){
        
        var tmpMatchInfo = chkUserNameByAllStore(g_allStoreData[i], userName);
        if(Object.keys(tmpMatchInfo).length !== 0){
            matchList.push(tmpMatchInfo);
        }
    }
    
    if(foreignInfo["name"] !== ""){
        var name = foreignInfo["name"];
        var startDate = foreignInfo["start"];
        startDate = startDate.replace(/-/g, '');
        var endDate = foreignInfo["end"];
        endDate = endDate.replace(/-/g, '');
        var tmpArray = {};
        tmpArray = {"projectName":foreignInfo["name"], "matchTypeList":[],
                    "start":startDate, "end":endDate };
        matchList.push(tmpArray);
    }
    
    matchList = [...matchList].sort((a, b) => b.start - a.start);
    
    if(authorityId !== 0){
        for(var i=0; i<g_authorityData.length; i++){
            
            var id = g_authorityData[i]["id"];
            if(id === authorityId){
                authorityName = g_authorityData[i]["name"];
                break;
            }
        }
    }
    
    result = {  "code":userCode,"name":userName,"dept":organization,
                "email":email,"tel":phoneNum,"address":workLocation,
                "isRevit":isExistRevit,"isSatellite":isExpSatellite,
                "isAuthCCC":isAuthorityCCC,"authorityName":authorityName,
                "foreignInfo":foreignInfo, "matchList":matchList};
    
    return result;
}

function SwitchDetailTypeTable(){
    $("#detailTable").removeClass("detailDisplayNone");
    $(".personalData").addClass("detailDisplayNone");
}

function SwitchDetailTypeCard(){
    $("#detailTable").addClass("detailDisplayNone");
    $(".personalData").removeClass("detailDisplayNone");
}


