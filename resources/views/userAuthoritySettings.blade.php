@extends('layouts.baselayout')
@section('title', 'CCC - User authority settings')

@section('head')

<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="../public/js/userAuthoritySettings.js"></script>
<link rel="stylesheet" href="../public/css/userAuthoritySettings.css">
<style>
</style>

@endsection

@section('content')
@include('layouts.loading')

<div class="main-content">
    
    <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>
    <input type="hidden" id="hidContents" name="hidContents" value="0"/>
    <input type="hidden" id="hidAuthority" name="hidAuthority" value="0"/>
    <input type="hidden" id="hidAuthorityNum" name="hidAuthorityNum" value="0"/>

    <div id="contentHeader">
        <h4 class="page-title">ユーザー権限設定</h4>
        <!--<div class="form-group has-search">-->
        <!--    <div class="selectWrap">-->
        <!--        <select id="authorityType" class="select" name="">-->
        <!--            <option value="" style="display: none;color:lightgray;">権限種別を指定してください</option>-->
        <!--        </select>-->
        <!--    </div>-->
        <!--    <input type="button" class="btn" name="btnUpdateAuthority" id="btnUpdateAuthority" value="権限設定更新" onClick=""/>-->
        <!--</div>-->
    </div>
    
    <div id="contentBody">
        <table id="tblAuthorityList">
            <thead>
                <tr>
                    <th id="theadthAuthorityType">権限種別</th>
                    <th id="theadthService">サービス</th>
                    <th id="theadthAuthority">権限</th>
                    <th class="theadBoxAuthority">BOX取込権限</th>
                    <th id="theadthUpdate">更新</th>
                    <th id="theadthDelete">削除</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    

</div>  <!--main-content-->

<!-- popup -->
<div id="createUser" class="popupOverlay">
	<div class="auPopup popupSize">
		<div class="popupHeader">
            <a class="close" href="javascript:void(0);" onClick ="ClosePopup()" style="top:2px;">&times;</a><br>
			<h3 style="text-align:center;color:dimgray;">権限作成</h3>
			
		</div>
		<div align="center">			
            <form name="createUserForm" method="post">
            <table id="tblCreateUser">
                <tr>
                    <td width="30%">権限名 : </td>
                    <td ><input type="text" class="form-control" name="txtName" id="txtName"/></td>
                </tr>
            </table>
            <div id="btnGroup">
                <input type="button" class="btn btnDesign" name="btnCreateUser" value="決定" onClick="addAuthorityType();"/>
                <input type="button" class="btn btnDesign" name="btnCancel" id="btnCancel" value="キャンセル" onClick="ClosePopup();"/>
            </div>
            </form>   		
        </div>       		
	</div>
</div>

@endsection