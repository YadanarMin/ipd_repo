/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
$(document).ready(function(){
    GetAllStoreSetList();
    
    
    $("#txtiPDCodeSearch,#txtProjectNameSearch,#txtBranchSearch,#txtiPDinChargeSearch,#txtKoujiTypeSearch").keyup(delay(function (e) {
      console.log('Time elapsed!', this.value);
      TableRowFilter();
    }, 500));
    
    
    $("#tblSetList").on('click','td',function(){
        var td_index = $(this).index();
        if(td_index > 0) return;
        var tr = $(this).closest('tr');
        var set_id = $(this).find('label').attr('id');
        var set_name = $(this).find('label').html();
        ClearSearchBox();
        if(set_id == undefined) return;// skip last li
        $("#set_name").html("【"+set_name+"】");
        $("#hidAccessId").val(set_id);
        $("#tblSetList tr").removeClass("active");
        tr.addClass('active');
        if(set_id >= 1 && set_id <= 10){
            $("#btnSave").attr('disabled','disabled');
        }else{
           $("#btnSave").removeAttr('disabled'); 
        }
        GetAllStoreDataForAccessSet(set_id);
    });
    var hidAccessId = $("#hidAccessId").val();
    if(hidAccessId == null || hidAccessId == undefined || hidAccessId == ""){
        FirstItemClick();
    }else{
        SetItemClick(hidAccessId,null);
    }
    
});

function FirstItemClick(){
    if($("#tblSetList tbody tr").length > 1){
        $("#tblSetList tbody tr:first").find("td:eq(0)").click();
    }
}

function ClearSearchBox(){
    $("#txtiPDCodeSearch").val('');
    $("#txtProjectNameSearch").val('');
    $("#txtBranchSearch").val('');
    $("#txtiPDinChargeSearch").val('');
}

function GetAllStoreDataForAccessSet(set_id){
    //var authority_set_id = $("#hidAccessId").val();
    $.ajax({
        url: "/iPD/projectAccessSetting/getData",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"get_allstore_for_access_set",authority_set_id:set_id},
        success :function(data) {
            console.log(data);
            if(data != ""){
              CreateAccessSetTable(data);
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function GetAllStoreSetList(){

    $.ajax({
        url: "/iPD/projectAccessSetting/getData",
        type: 'post',
        async:false,
        data:{_token: CSRF_TOKEN,message:"get_allstore_set_list"},
        success :function(data) {
            console.log(data);
            if(data != ""){
              CreateAuthoritySetTable(data);
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function CreateAccessSetTable(data){
    var row = "";
    var chkRow = "";
    var accessable_count = 0;
    $.each(data,function(key, item) {
        row += "<tr>";
            row += "<td>"+item["a_pj_code"]+"</td>";
            row += "<td>"+item["a_pj_name"]+"</td>";
            row += "<td>"+item["b_pj_name"]+"</td>";
            row += "<td>"+item["b_tmp_pj_name"]+"</td>";
            row += "<td>"+item["a_shiten"]+"</td>";
            row += "<td>"+item["a_kouji_type"]+"</td>";
            row += "<td>"+item["b_ipd_center_tantou"]+"</td>";
         row += "</tr>";  
         
        chkRow +="<tr>";
            if(item["accessable"] == 1){
                accessable_count++;
                chkRow += "<td><input type='checkbox' id='"+item['a_pj_code']+"' checked='checked'></td>";
            }else{
                chkRow += "<td><input type='checkbox' id='"+item['a_pj_code']+"'></td>";
            }
       chkRow +="</tr>";   
        
    });
    $("#total").html(accessable_count);
    $("#tblAuthoritySet tbody tr").remove();
    $("#tblAuthoritySet tbody").append(row);
    
    $("#tbCheckBox tbody tr").remove();
    $("#tbCheckBox tbody").append(chkRow);
}

function CreateAuthoritySetTable(authoritySetList) {
    
    var row = "";
    $.each(authoritySetList,function(key,item){
        if(item["authority_set_name"] == "カスタム"){
            return;
        }else if(item["id"] >=1 && item["id"] <=10){//default set
            row += "<tr class='list-group-item'>";
                row += "<td colspan='3' ><label id='"+item["id"]+"' class='txtNew'>"+item["authority_set_name"]+"</label> </td>";
            row += "</tr>"; 
        }else{
            
            row += "<tr class='list-group-item'>";
                row += "<td class='td-width'><label id='"+item["id"]+"' class='txtNew'>"+item["authority_set_name"]+"</label> </td>";
                row += '<td data-toggle="tooltip" data-placement="top" title="セット名編集"><a href="javascript:void(0)" onClick="EditAccessSetName('+item["id"]+',this)"><img class="appIconBig" src="/iPD/public/image/edit.png" alt="" height="20" width="15"></a></td>';
                // <i onClick="EditAccessSetName('+item["id"]+',this)" class="fa fa-refresh" aria-hidden="true"></i>
                row += '<td data-toggle="tooltip" data-placement="top" title="削除"><a href="javascript:void(0)" onClick="DeleteAccessSet('+item["id"]+',\''+item["authority_set_name"]+'\')"><img class="appIconBig" src="/iPD/public/image/trash.png" alt="" height="20" width="15"></a></td>';
            row += "</tr>"; 
        }
        
    });

    $("#tblSetList tr:not(:last)").remove()
    $("#tblSetList tbody tr:last").before(row);
}


function SaveAccessSetDetail(){
    var project_list = [];
    var set_id = $("#hidAccessId").val();
    $("#tbCheckBox tbody tr:visible").each(function(index) {
        var chk = $(this).find('input[type="checkbox"]');
        var pj_code = chk.attr('id');
        if(chk.prop("checked") == true){
            project_list.push(pj_code);
        }
       
    });
//console.log(project_list);
    $.ajax({
        url: "/iPD/projectAccessSetting/saveData",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"save_access_set_detail","set_id":set_id,"project_list":JSON.stringify(project_list)},
        success :function(data) {
            //console.log(data);return;
            if(data.includes("success")){
                console.log("succssfully updated!");
                var user_id = $("#hidUserId").val();
                var user_name = $("#hidUserName").val();
                window.location="/iPD/projectAccessSetting/authoritySet/"+user_id+"/"+user_name+"/"+set_id;
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function CheckAll() {
    $("#tbCheckBox tbody tr:visible").each(function(index) {
        var chk = $(this).find('input[type="checkbox"]');
        chk.prop("checked",true);
    });
}

function UnCheckAll(){
    $("#tbCheckBox tbody tr:visible").each(function(index) {
        var chk = $(this).find('input[type="checkbox"]');
        chk.prop("checked",false);
    });
}


function TableRowFilter(){
    //alert($("#txtKoujiTypeSearch").val());
    $("#tblAuthoritySet tbody tr").each(function() {
        $(this).show();
    });
    $("#tbCheckBox tbody tr").each(function() {
        $(this).show();
    });
    
    var txtiPDCodeSearch = $("#txtiPDCodeSearch").val(); 
    var txtProjectNameSearch = $("#txtProjectNameSearch").val();
    var txtBranchSearch = $("#txtBranchSearch").val();
    var txtiPDinChargeSearch = $("#txtiPDinChargeSearch").val();
    var txtKoujiTypeSearch = $("#txtKoujiTypeSearch").val();
    if(txtiPDCodeSearch !== "" || txtProjectNameSearch !== "" || txtBranchSearch !== "" || txtKoujiTypeSearch !== "" || txtiPDinChargeSearch){
        $("#tblAuthoritySet tbody tr").each(function(index) {
            var ipdCode = $(this).find("td:eq(0)").text();
            var projectName = $(this).find("td:eq(1)").text();
            var projectName_b = $(this).find("td:eq(2)").text();
            var projectName_c = $(this).find("td:eq(3)").text();
            var branch = $(this).find("td:eq(4)").text();
            var koujiType = $(this).find("td:eq(5)").text();
            var inCharge = $(this).find("td:eq(6)").text();
           if((!ipdCode.includes(txtiPDCodeSearch))
             || (!projectName.includes(txtProjectNameSearch) && !projectName_b.includes(txtProjectNameSearch) && !projectName_c.includes(txtProjectNameSearch))
             || !branch.includes(txtBranchSearch)
             || !koujiType.includes(txtKoujiTypeSearch)
             || !inCharge.includes(txtiPDinChargeSearch)){
                $("#tbCheckBox tbody tr").eq(index).hide();
                $(this).hide();
              }else{
                  $("#tbCheckBox tbody tr").eq(index).show();
                  $(this).show();
              }
        });

    }else{
         $("#tblAuthoritySet tbody tr").each(function() {
             $(this).show();
         });
          $("#tbCheckBox tbody tr").each(function() {
             $(this).show();
         });
    }
    
    $("#tblAuthoritySet >tbody >tr:visible:odd").css("background-color", "#f2f2f2");
    $("#tblAuthoritySet >tbody >tr:visible:even").css("background-color", "#fff");
}


function AddAccessSet(){
    
   var last_row_idex = $("#tblSetList tbody tr:last").index();
   //alert(last_row_idex);
   var index = last_row_idex;
   
   var td_row= "";
    td_row += "<tr class='list-group-item'>";
        td_row += "<td><input type='text' name='txtNew' class='txtNew'/></td>";
        td_row += '<td colspan="2" height="20px"><a href="javascript:void(0)" onClick="SaveNewAuthoritySet(this)">確定</a></td>';
    td_row += "</tr>";
    $("#tblSetList tbody tr:last").before(td_row);

}

function SaveNewAuthoritySet(ele) {
   var tr = $(ele).closest('tr');
   var newAuthoritySetName = tr.find('input[class="txtNew"]').val();
   if(newAuthoritySetName == "" || newAuthoritySetName == undefined) return;
    $.ajax({
        url: "/iPD/projectAccessSetting/saveData",
        type: 'post',
        async:false,
        data:{_token: CSRF_TOKEN,message:"save_new_allstore_set",new_authority_set_name:newAuthoritySetName},
        success :function(data) {
            if(data.includes("success")){
                GetAllStoreSetList();
                SetItemClick(null,newAuthoritySetName);
                
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function SetItemClick(setId,setName){
    $("#tblSetList tbody tr").each(function(index) {
         var id = $(this).find("td:eq(0)").find('label').attr('id');
         var name = $(this).find("td:eq(0)").find('label').html();
         if(setId != null){
            if(id == undefined) return;
            if(id === setId){
                $(this).find("td:eq(0)").click();
            }
         }else{
             
            if(name == undefined) return;
            if(name === setName){
                $(this).find("td:eq(0)").click();
            } 
         }
     
    });
}

function EditAccessSetName(id,ele) {
    var tr = $(ele).closest('tr');
    var lbl = tr.find('label');
    var setName = lbl.html();
    $("#updateName").val(setName);
    $("#updateName").attr('name',id);//set ele id to name property
    $("#update_popup").show("100").css({
            top: (tr.offset().top+40) + "px",
            left: (tr.offset().left+50) + "px"
    });
}

function UpdateSetName(){
    var id = $("#updateName").attr('name');
    var newSetName = $("#updateName").val();
    $.ajax({
        url: "/iPD/projectAccessSetting/saveData",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"update_allstore_set_name",access_set_id:id,access_set_name:newSetName},
        success :function(data) {
            if(data.includes("success")){
                GetAllStoreSetList();
                SetItemClick(id);

               $("#update_popup").hide(100); 
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function DeleteAccessSet(id,setName) {
    var cfrm = confirm(setName+" を削除していいですか？");
    if(cfrm == false) return;
    $.ajax({
        url: "/iPD/projectAccessSetting/deleteData",
        type: 'post',
        async:false,
        data:{_token: CSRF_TOKEN,message:"delete_allstore_set",access_set_id:id},
        success :function(data) {
            if(data.includes("success")){
                GetAllStoreSetList();
            }
            var user_id = $("#hidUserId").val();
            var user_name = $("#hidUserName").val();
            window.location="/iPD/projectAccessSetting/authoritySet/"+user_id+"/"+user_name;
        },
        error:function(err){
            console.log(err);
        }
    });
}

function ClosePopUp(){
    $("#update_popup").hide(100);
}

function delay(callback, ms) {
  var timer = 0;
  return function() {
    var context = this, args = arguments;
    clearTimeout(timer);
    timer = setTimeout(function () {
      callback.apply(context, args);
    }, ms || 0);
  };
}


function GoTo(str){
    var user_id = $("#hidUserId").val();
    var user_name = $("#hidUserName").val();
    window.location = "/iPD/projectAccessSetting/"+str+"/"+user_id+"/"+user_name;
}