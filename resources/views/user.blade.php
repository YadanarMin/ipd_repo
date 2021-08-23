@extends('layouts.baselayout')
@section('title', 'CCC - User settings')

@section('head')
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="../public/js/user.js"></script>
<script type="text/javascript" src="../public/js/easyselectbox.min.js"></script>
<script src='https://kit.fontawesome.com/a076d05399.js'></script>
<style>
.marginAdjust{
    /*width:60%;*/
    margin: 2vh 5%;
}
#tblCreateUser td{
    padding-top:20px; 
    color:dimgray;
    font-weight: bold;
    font-size:0.9em;
}
#tblCreateUser{
   width:90%;

}
#tblCreateUser td:first-child{
    width:140px;
    padding-right:5px;
}
#tblUser{
    width:100%;
}
#tblUser td:last-child{
    text-align:center;
}
input[type="checkbox"]{
    <!--width:10%;-->
    <!--height:20px;-->
}
.outerBorder{
    border:none;
    min-width:1100px;
    width:auto !important;
    min-height:30vh;
    height:auto;

}
.appIconBig{
 display: block;
 margin-left: auto;
 margin-right: auto;
}
#tblUser td{
    padding-bottom: 5px;
    text-align: left;
}
#btnCreateUserPopu{
    margin-left: auto;
    order: 2;
    <!--margin: 6px 0 6px 0;-->
    <!--font-size: 12px;-->
    <!--border: solid 1px lightgray;-->
}
#searchDiv{
    display: flex;
    justify-content: right;
}
#btnGroup{
    margin-left: 220px;
}
#btnAutoPassword{
    font-size:0.3em;
    float:right;
}
.popup{
    height:auto;
}
#tblUser th,#tblUser td{
border:1px solid #ddd;
}
.warning_text{
    color:red;
    font-size:0.5em;
}
.popupSize{
    width:500px;
}
.popupHeader{
    padding:0px;
    margin:0px;
}
.btn-secondary{
    background:#6c757d !important;
}
.td-sm{
 width :50px;
}
.td-md{
 width :100px;
}
.td-lg{
 width :200px;
}
</style>
@endsection

@section('content')
<div class="main-content marginAdjust">  
    <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>
    <input type="hidden" id="hidNotC3Users" name="hidNotC3Users" value="{{json_encode($notC3Users)}}"/>

    
    <div class="outerBorder">
        <div id="searchDiv">
            <h4 class="page-title">ログイン情報設定</h4>
            <input type="button" class="btn btn-primary" name="btnCreateUserPopup" id="btnCreateUserPopu" value="ログイン情報付与" onClick="DisplayPopup();"/>
        </div>  
        <table id="tblUser">
            <tr>
                <!--<th width="10%;">ユーザコード</th>-->
                <th class="td-sm">CCCマスター</th>
                <th class="td-sm">管理責任者</th>
                <th class="td-md">ユーザ名</th>
                <th class="td-md">パスワード</th>
                <th class="td-lg">メールアドレス</th>
                <th class="td-lg">組織名</th>
                <th class="td-md">権限</th>
                <th class="td-md">管理責任者名</th>
                <th class="td-sm">編集</th>
                <th class="td-sm">削除</th>
                <th class="td-md">管理責任者</br>承認</th>
                <th class="td-md">CCC管理者</br>承認</th>
            </tr> 
            @if (count($users) > 0)
            @foreach($users as $user)
            <tr>
                <!--<td></td>-->
                <td>
                    @if($user['isCCCMaster'] == 1)
                        <input class="form-check-input" type="checkbox" id="chkCCCMaster" name="chkCCCMaster" value="{{$user['personal_id']}}" checked/>
                    @else
                        <input class="form-check-input" type="checkbox" id="chkCCCMaster" name="chkCCCMaster" value="{{$user['personal_id']}}"/>
                    @endif
                </td>
                <td>
                    @if($user['isChiefAdmin'] == 1)
                        <input class="form-check-input" type="checkbox" id="chkChiefAdmin" name="chkChiefAdmin" value="{{$user['personal_id']}}" checked/>
                    @else
                        <input class="form-check-input" type="checkbox" id="chkChiefAdmin" name="chkChiefAdmin" value="{{$user['personal_id']}}"/>
                    @endif
                </td>
                <td>{{ $user["name"] }}</td>
                <td>{{ $user["password"] }}</td>
                <td>{{ $user["mail"] }}</td>
                <td>{{ $user["branch"] }} {{ $user["department"] }}</td>
                <td>{{ $user["authority_name"] }}</td>
                <td>{{ $user["chief_admin_name"] }}</td>
                <td><a href="javascript:void(0)" onClick="DisplayPopup({{$user['personal_id']}});"><img class="appIconBig" src='../public/image/edit.png' alt='' height='20' width='20' /></a></td>
                <td><a href="javascript:void(0)" onClick="DeleteUser({{$user['personal_id']}}, '{{ $user["name"] }}');"><img class="appIconBig" src='../public/image/trash.png' alt='' height='20' width='15' /></a></td>
                <td>
                    @if($user["isC3User"] >= 2)
                        <input type="button" class="btn btn-secondary btn-sm"  value="承認済み" disabled="disabled"/> <!--onClick="ApproveByChiefAdmin({{$user['personal_id']}});"-->
                    @elseif($user["isC3User"] == 1)
                        <input type="button" class="btn btn-info btn-sm"  value="承認待ち"/><!--onClick="ApproveByChiefAdmin({{$user['personal_id']}});"-->
                    @endif
                </td>
                <td>
                    @if($user["isC3User"] == 3)
                        <input type="button" class="btn btn-secondary btn-sm"  value="承認済み" disabled="disabled"/>
                    @elseif($user["isC3User"] == 2)
                        <input type="button" class="btn btn-primary btn-sm"  value="承認待ち"/><!--onClick="ApproveByCCCAdmin({{$user['personal_id']}},{{$user['chief_admin_id']}});"-->
                    @endif
                    
                </td>
            </tr>  
            @endforeach  
            @endif           
        </table> 
    </div>
    

</div>

<!-- popup -->
<div id="createUser" class="popupOverlay">
	<div class="popup popupSize">
	    <div><a class="close" href="javascript:void(0);" onClick ="ClosePopup()" style="top:0px;">&times;</a><br></div>

		<div align="center">			
            <form name="createUserForm" method="post">
            <h4 style="text-align:center;color:dimgray;font-weight: bold;">ログイン情報</h4>
            <input type="hidden" name="hidPersonalId" id="hidPersonalId" required />
            <input type="hidden" name="hidIsC3User" id="hidIsC3User" required />
            <input type="hidden" name="hidOldAuthorityID" id="hidOldAuthorityID" required />
            <table id="tblCreateUser">
                <!--<tr>-->
                <!--    <td width="15%">ユーザコード : </td>-->
                <!--    <td ><input type="text" class="form-control" name="txtCode" id="txtCode" required /><span class="warning_text" id="txtCode_err"></span></td>-->
                <!--</tr>-->
                <tr>
                    <td>メールアドレス : </td>
                    <td>
                        <select class="form-control" name="emailSelect" id="emailSelect" disabled></select>
                        <span class="warning_text" id="emailSelect_err"></span>
                    </td>
                    <!--<td><input type="text" class="form-control" name="txtEmail" id="txtEmail" disabled/><span class="warning_text" id="txtEmail_err"></span></td>-->
                </tr>
                <tr>
                    <td width="15%">ユーザ名 : </td>
                    <td ><input type="text" class="form-control" name="txtName" id="txtName" disabled/><span class="warning_text" id="txtName_err"></span></td>
                </tr>
                <tr>
                    <td>組織名 : </td>
                    <td><input type="text" class="form-control" name="txtDeptName" id="txtDeptName" disabled></td>
                </tr>
                <tr>
                    <td>パスワード : <input type="button" id="btnAutoPassword" class="btn　btn-sm" onClick="CreateRandomPassword()" value="自動生成"></td>
                    <td><input type="text" class="form-control" name="txtPassword" id="txtPassword" required/><span class="warning_text" id="txtPassword_err"></span></td>
                </tr>
                <tr>
                    <td>権限 : </td>
                    <td>
                        <select class="form-control" name="authoritySelect" id="authoritySelect" required></select>
                        <span class="warning_text" id="authoritySelect_err"></span>
                    </td>
                </tr> 
                <!--<tr>-->
                <!--    <td>会社種別 : </td>-->
                <!--    <td>-->
                <!--        <select name="deptSelect" id="deptSelect" disabled></select>-->
                <!--        <span class="warning_text" id="deptSelect_err"></span>-->
                <!--    </td>-->
                <!--</tr>-->
                <!--<tr>-->
                <!--    <td>電話番号 : </td>-->
                <!--    <td><input type="text" class="form-control" name="txtPhone" id="txtPhone" disabled></td>-->
                <!--</tr>-->
                
                <!--<tr>-->
                <!--    <td>勤務地 : </td>-->
                <!--    <td><textarea class="form-control md-textarea" name="txtAddress" id="txtAddress" rows="3" disabled></textarea></td>-->
                <!--</tr>                                      -->
            </table>
            <div id="btnGroup">
                <input type="button" class="btn btnDesign" name="btnCreateUser" value="更新" onClick="CreateUser();"/>
                <input type="button" class="btn btnDesign" name="btnCancel" id="btnCancel" value="キャンセル" onClick="ClosePopup();"/>
            </div>
            </form>   		
        </div>       		
	</div>
</div> 
@endsection