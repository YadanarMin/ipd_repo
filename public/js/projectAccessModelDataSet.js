/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
$(document).ready(function(){
    
    GetModelDataSetList();
    
    SearchModel();
    
    $("#tblSetList").on('click','td',function(){
        var td_index = $(this).index();
        if(td_index > 0) return;
        var tr = $(this).closest('tr');
        var set_id = $(this).find('label').attr('id');
        var set_name = $(this).find('label').html();

        if(set_id == undefined) return;// skip last li
        $("#set_name").html("【"+set_name+"】");
        $("#hidAccessId").val(set_id);
        $("#tblSetList tr").removeClass("active");
        tr.addClass('active');
        if(set_id == 1){
            $("#btnSave").attr('disabled','disabled');
        }else{
           $("#btnSave").removeAttr('disabled'); 
        }
        GetModelDataForAccessSet(set_id);
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

function SearchModel(){
    $("#txtSearch").keyup(function(){
        var textSearch = $(this).val();
        $("#tblAuthoritySet tbody tr").each(function() {
            var modelName = $(this).find("td:nth-child(1)").text();
            if(!modelName.includes(textSearch)){
                $(this).hide();
            }else{
                $(this).show();
            }
        });
    })
}

function GetModelDataSetList(){
    $.ajax({
        url: "/iPD/projectAccessSetting/getData",
        type: 'post',
        async:false,
        data:{_token: CSRF_TOKEN,message:"get_model_data_set_list"},
        success :function(data) {
            console.log(data);
            if(data != ""){
              //CreateAccessSetList(data);
              CreateAuthoritySetTable(data);
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function GetModelDataForAccessSet(){
   var authority_set_id = $("#hidAccessId").val();
    $.ajax({
        url: "/iPD/projectAccessSetting/getData",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"get_modeldata_for_access_set",authority_set_id:authority_set_id},
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

function CreateAuthoritySetTable(authoritySetList) {
    
    var row = "";
    $.each(authoritySetList,function(key,item){
        
        if(item["authority_set_name"] == "カスタム" || item['id'] == 0){
            return;
            // row += "<tr class='list-group-item'>";
            //     row += "<td colspan='3'><label id='"+item["id"]+"' class='txtNew' >"+item["authority_set_name"]+"</label></td>";
            // row += "</tr>";
        }else if(item['id'] == 1){
             row += "<tr class='list-group-item'>";
                row += "<td colspan='3'><label id='"+item["id"]+"' class='txtNew'>"+item["authority_set_name"]+"</label> </td>";
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

function AddAccessSet(){
    
   var last_row_idex = $("#tblSetList tbody tr:last").index();
   //alert(last_row_idex);
   var index = last_row_idex;
   
   var td_row= "";
    td_row += "<tr class='list-group-item'>";
        td_row += "<td><input type='text' name='txtNew' width='270px;' class='txtNew'/></td>";
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
        data:{_token: CSRF_TOKEN,message:"save_new_model_data_set",new_authority_set_name:newAuthoritySetName},
        success :function(data) {
            if(data.includes("success")){
                GetModelDataSetList();
                SetItemClick(null,newAuthoritySetName);
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}


function CreateAccessSetTable(data){
    var row = "";
    var accessable_count = 0;
    $.each(data,function(key, item) {
        row += "<tr>";
            row += "<td>"+item["name"]+"</td>";
            if(item["accessable"] == 1){
                accessable_count++;
                row += "<td><input type='checkbox' id='"+item['id']+"' checked='checked'></td>";
            }else{
                row += "<td><input type='checkbox' id='"+item['id']+"'></td>";
            }
            
        row += "</tr>";
    });
    $("#total").html(accessable_count);
    $("#tblAuthoritySet tbody tr").remove();
    $("#tblAuthoritySet tbody").append(row);
}

function SaveAccessSetDetail(){
    
    var model_list = [];
    var authority_set_id = $("#hidAccessId").val();
    $("#tblAuthoritySet tbody tr:visible").each(function(index) {
        var chk = $(this).find('input[type="checkbox"]');
        var model_id = chk.attr('id');
        if(chk.prop("checked") == true)
            model_list.push(model_id);
    });
    

    $.ajax({
        url: "/iPD/projectAccessSetting/saveData",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"save_model_data_set_detail",authority_set_id:authority_set_id,model_list:JSON.stringify(model_list)},
        success :function(data) {
            if(data.includes("success")){
                console.log("succssfully updated!");
                var user_id = $("#hidUserId").val();
                var user_name = $("#hidUserName").val();
                window.location="/iPD/projectAccessSetting/modelDataSet/"+user_id+"/"+user_name+"/"+authority_set_id;
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

function CheckAll() {
    $("#tblAuthoritySet tbody tr:visible").each(function(index) {
        var chk = $(this).find('input[type="checkbox"]');
        chk.prop("checked",true);
    });
}

function UnCheckAll(){
    $("#tblAuthoritySet tbody tr:visible").each(function(index) {
        var chk = $(this).find('input[type="checkbox"]');
        chk.prop("checked",false);
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
        data:{_token: CSRF_TOKEN,message:"update_model_data_set_name",access_set_id:id,access_set_name:newSetName},
        success :function(data) {
            if(data.includes("success")){
                GetModelDataSetList();
                SetItemClick(id,null);
                
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
        data:{_token: CSRF_TOKEN,message:"delete_model_data_set",access_set_id:id},
        success :function(data) {
            if(data.includes("success")){
                GetModelDataSetList();
            }
            var user_id = $("#hidUserId").val();
            var user_name = $("#hidUserName").val();
            window.location="/iPD/projectAccessSetting/modelDataSet/"+user_id+"/"+user_name;
        },
        error:function(err){
            console.log(err);
        }
    });
}

function ClosePopUp(){
    $("#update_popup").hide(100);
}

function GoTo(str){
    var user_id = $("#hidUserId").val();
    var user_name = $("#hidUserName").val();
    window.location = "/iPD/projectAccessSetting/"+str+"/"+user_id+"/"+user_name;
}