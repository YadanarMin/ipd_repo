@extends('layouts.baselayout')
@section('title', 'CCC - Personnel Insert')

@section('head')
<script src="../public/js/shim.js"></script>
<script src="../public/js/xlsx.full.min.js"></script>
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/personalInsert.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="../public/css/jquery-ui_18.css" />

<style>
.marginAdjust{
    width:70%;
    margin: 2vh auto;
}
@media screen and (min-width: 1200px){
    .outerTblBorder{
        border:none;
        width:100%;
        max-height: 700px;
        overflow-x: scroll;
    }
}
@media screen and (max-width: 1200px){
    .outerTblBorder{
        border:none;
        width:100%;
        max-height: 520px;
        overflow-x: scroll;
    }
}
.appIconBig{
 display: block;
 margin-left: auto;
 margin-right: auto;
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
    width:2000px;
}
/*
#tblUser td:last-child{
    text-align:center;
}
*/
input[type="checkbox"]{
    width:10%;
    height:20px;
}
#tblUser td{
    padding-bottom: 5px;
    text-align: center;
}
#btnCreateUserPopu{
    margin: 6px 0 6px 0;
    font-size: 12px;
    border: solid 1px lightgray;
}
#searchDiv{
    display: flex;
    justify-content: space-between;
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
.main_div{
    margin:10vh 50vh;
    background: white;
    border-radius: 15px;
}
.main_div > div {
    padding: 0 0 0 0;
    margin: 7px 30px 7px 30px;
}
popupOverlayPlus{
    overflow-x: scroll;
}
.btnDesignIn{
    background: #6694bb;
    color: white;
    width: 95px;
    margin: 0 0 3vh 0px;
    height: 40px;
}
#btnGroupIn{
    text-align:end;
}
#deptField{
    max-height: 100px;
    overflow-y: auto;
    overflow-x: hidden;
    padding-right: 20px;
}
.err_msg{
    display: block;
    color:red;
    text-align: left;
}
#tblUser thead tr th {
    position: sticky;
    top: 0;
}
.fixed0201{
    left: 0;
}
.fixed02{
    position: sticky;
    left: 0;
    background-color:#d9d9d9;
}
.fixed0201index{
    z-index:3;
}
.fixed02index{
    z-index:2;
}
#autoLoadPersonalData{
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.7);
    transition: opacity 500ms;
    visibility: hidden;
    opacity: 0;
}
#autoLoadInput{
    margin: 40vh 30vh;
    background: white;
    border-radius: 9px;
}
#tblUser tbody tr:hover{
    background-color:#b0c4de;
}
#tblUser tbody tr:hover + .fixed02{
    background-color:#b0c4de;
}
#tblUser td{
    padding-left: 0;
}
.autoLoadInput > div {
    padding: 0 0 0 0;
    margin: 7px 30px 7px 30px;
}
</style>

<script>
</script>

@endsection

@section('content')
<div class="main-content marginAdjust">  
    <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>
    <input type="hidden" id="hidPersonalData" name="hidPersonalData" value="{{json_encode($personnels)}}"/>

    <div id="searchDiv">
        <!--<h4 class="titleDesign pageTitle">ユーザー設定 </h4>-->
        <h4 class="page-title">人員情報設定</h4>
       
        <!--<input type="button" class="btn btnDesign" name="btnCreateUserPopup" id="btnCreateUserPopu" value="新規ユーザー作成" onClick="DisplayPopup();" style="float:right;margin:0;"/>-->
        <input type="button" class="btn" name="btnCreateUserPopup" id="btnCreateUserPopu" value="新規ユーザー作成" onClick="DisplayPopup();"/>
    </div>  
    <div class="outerTblBorder">
        <table id="tblUser" align="center">
            <thead>
                <tr>
                    <th id="theadName" class="fixed0201 fixed0201index" width="5%;">氏名</th>
                    <!--<th width="10%;">氏名カナ</th>-->
                    <th width="15%;">メールアドレス</th>
                    <th width="5%">個人コード</th>
                    <th width="10%">会社名</th>
                    <th width="5%">個人種別</th>
                    <!--<th width="10%">組織コード</th>-->
                    <th width="10%">支店名</th>
                    <th width="20%">組織名</th>
                    <!--<th width="10%">役職</th>-->
                    <!--<th width="10%">電話番号</th>-->
                    <!--<th width="10%">外線</th>-->
                    <!--<th width="10%">FAX</th>-->
                    <!--<th width="10%">勤務地</th>-->
                    <!--<th width="10%">本務/兼務</th>-->
                    <!--<th width="10%">派遣元会社種別</th>-->
                    <th width="10%">派遣元会社名</th>
                    <th width="5%">CCC利用権限</th>
                    <th width="5%">iPD留学経験</th>
                    <th width="5%">BIM速習利用</th>
                    <th width="5%">編集</th>
                    <th width="5%">削除</th>
                </tr> 
            </thead>
            <tbody>
            @if (count($personnels) > 0)
            @foreach($personnels as $personnnel)
                <tr>
                    <td class="fixed02 fixed02index" style="text-align:center;">{{ $personnnel["first_name"] }} {{ $personnnel["last_name"] }}</td>
                    <!--<td>{{ $personnnel["first_name_kana"] }} {{ $personnnel["last_name_kana"] }}</td>-->
                    <td>{{ $personnnel["mail"] }}</td>
                    <td>{{ $personnnel["code"] }}</td>
                    <td>{{ $personnnel["company_name"] }}</td>
                    
                    @if($personnnel["contract_type"] == 1)
                        <td style="text-align:center;">社員</td>
                    @elseif($personnnel["contract_type"] == 2)
                        <td style="text-align:center;">派遣</td>
                    @elseif($personnnel["contract_type"] == 3)
                        <td style="text-align:center;">外部</td>
                    @else
                        <td style="text-align:center;">-</td>
                    @endif

                    <!--<td>{{ $personnnel["branch_code"] }}{{ $personnnel["dept_code"] }}</td>-->
                    <td>{{ $personnnel["branch_name"] }}</td>
                    <td>{{ $personnnel["dept_name"] }}</td>
                    <!--<td>{{ $personnnel["position"] }}</td>-->
                    <!--<td>{{ $personnnel["phone"] }}</td>-->
                    <!--<td>{{ $personnnel["outsideCall"] }}</td>-->
                    <!--<td>{{ $personnnel["fax"] }}</td>-->
                    <!--<td>{{ $personnnel["work_location"] }}</td>-->
                    
                    <!--@if($personnnel["company_id"] == 1)-->
                    <!--    @if($personnnel["isAdditionalPost"] == 0)-->
                    <!--        <td>本務</td>-->
                    <!--    @else-->
                    <!--        <td>兼務</td>-->
                    <!--    @endif-->
                    <!--@else-->
                    <!--    <td>-</td>-->
                    <!--@endif-->
                    
                    <!--<td>{{ $personnnel["haken_company_type_name"] }}</td>-->
                    <td>{{ $personnnel["haken_company_name"] }}</td>
    
                    @if($personnnel["isC3User"] == 3)
                        <td style="text-align:center;">〇</td>
                    @else
                        <td style="text-align:center;">なし</td>
                    @endif
    
                    @if($personnnel["isStudyAbroad"] == 0)
                        <td style="text-align:center;">-</td>
                    @else
                        <td style="text-align:center;">〇</td>
                    @endif
    
                    @if($personnnel["isSpeedCourse"] == 0)
                        <td style="text-align:center;">-</td>
                    @else
                        <td style="text-align:center;">〇</td>
                    @endif
                    
                    <td><a href="javascript:void(0)" onClick="DisplayPopup({{$personnnel['id']}});"><img class="appIconBig" src='../public/image/edit.png' alt='' height='20' width='20' /></a></td>
                    <td><a href="javascript:void(0)" onClick="DeleteUser({{$personnnel['id']}}, '{{ $personnnel["first_name"] }} {{ $personnnel["last_name"] }}');"><img class="appIconBig" src='../public/image/trash.png' alt='' height='20' width='15' /></a></td>
                </tr>  
            @endforeach  
            @endif
            </tbody>
        </table> 
    </div>
    

</div>

<!-- popup -->
<div id="createUser" class="popupOverlay" style="overflow-y:scroll;">
    <div class="main_div">
        <div><a class="close" href="javascript:void(0);" onClick ="ClosePopup()" style="top:0px;padding-top:3vh;padding-left:2vh;">&times;</a><br></div>
    	<div class="align-center" style="">
    	    <div style="display:flex;justify-content:space-between">
        		<h4>人員情報入力</h4>
        		<input type="button" class="btn" name="btnAutoLoadPopup" id="btnAutoLoadPopup" value="自動読み込み" onClick="AutoLoadPopup();"/>
    	    </div>
    		<span class="err_msg" id="err_message"></span>
    	</div>
    	<hr>

        <div class="form-group">
            <label>メールアドレス</label>
            <input type="text" class="form-control input-sm" id="txtEmail" value="">
        </div>

    	<div>
    		<div class="form-group col-md-6" style="padding:0 10px 0 0px;">
                <label>氏名：姓</label>
                <input type="text" class="form-control input-sm " id="txtFirstName" value="" required>
            </div>
            <div class="form-group col-md-6" style="padding:0 0px 0 10px;">
                <label>名</label>
                <input type="text" class="form-control input-sm" id="txtLastName" value="">
            </div>	
    	</div>
    	
    	<div>
    		<div class="form-group col-md-6" style="padding:0 10px 0 0px;">
                <label>氏名：姓（カナ）</label>
                <input type="text" class="form-control input-sm " id="txtFirstNameKana" value="" required>
            </div>
            <div class="form-group col-md-6" style="padding:0 0px 0 10px;">
                <label>名（カナ）</label>
                <input type="text" class="form-control input-sm" id="txtLastNameKana" value="">
            </div>	
    	</div>

        <div class="form-group">
            <label>個人コード</label>
            <input type="text" class="form-control input-sm" id="txtCode" value="">
        </div>
        
        <input type="hidden" id="hidCompanyTypeList" value="{{json_encode($companyTypeList)}}"/>
        <input type="hidden" id="hidCompanyList" value="{{json_encode($companyList)}}"/>
        <div class="form-group">
            <label>企業種別</label>
            <div style="display:flex;">
            	<select class="form-control input-sm" id="companyTypeSelect">
            		<option value="">選択してください</option>
            		@foreach($companyTypeList as $curCompanyType)
            		    <option value="{{ $curCompanyType['id'] }}">{{ $curCompanyType['name'] }}</option>
            		@endforeach
            	</select>
                <span class="warning_text" id="companyTypeSelect_err"></span>
            </div>
        </div>
        <div class="form-group">
            <label>企業名</label>
            <div style="display:flex;">
            	<select class="form-control input-sm" id="companyNameSelect" style="width:75%;">
            		<option value="">選択してください</option>
            		@foreach($companyList as $curCompany)
            		    <option value="{{ $curCompany['id'] }}">{{ $curCompany['name'] }}</option>
            		@endforeach
                </select>
                <!--<input type="text" class="form-control input-sm" id="txtCompanyName" value="" style="width:75%;">-->
                &nbsp;&nbsp;&nbsp;
                <a href="javascript:void(0)" onClick="UpdateCompanyNameSelect();" style="margin-top:5px;"><img class="appIconBig" src="../public/image/update.png" alt="" height="17" width="17" /></a>
                &nbsp;&nbsp;&nbsp;
                <a href="/iPD/company/index/is_other_page" target="_blank" rel="noopener noreferrer" style="margin-top:4px;">新規企業追加</a>
            </div>
        </div>
        <div class="form-group">
            <label>個人種別</label>
            <div style="display:flex;">
            	<select class="form-control input-sm" id="contractTypeSelect">
            		<option value="0">選択してください</option>
        		    <option value="1">社員</option>
        		    <option value="2">派遣</option>
        		    <option value="3">外部</option>
            	</select>
                <span class="warning_text" id="contractTypeSelect_err"></span>
            </div>
        </div>
        
        <div>
            <input type="hidden" id="hidBranchList" value="{{json_encode($branchList)}}"/>
            <div class="form-group col-md-6" style="padding:0 10px 0 0px;">
                <label>支店名</label>
                <div style="display:flex;">
                	<select class="form-control input-sm" id="branchNameSelect">
                		<option value=''>選択してください</option>
                		@foreach($branchList as $curBranch)
                		    <option value='{{ $curBranch['id'] }}'>{{ $curBranch['name'] }}</option>
                		@endforeach
                    </select>
                </div>
            </div>
            <input type="hidden" id="hidDeptList" value="{{json_encode($deptList)}}"/>
            <div id="deptField" class="form-group col-md-6" style="padding:0 0px 0 10px;">
                <label>組織名</label>
                <input type="text" class="form-control input-sm" id="txtDepartment" value="">
            </div>
        </div>
        
        <div class="form-group">
            <label>本務/兼務</label>&nbsp;&nbsp;&nbsp;
            <div style="display:flex;">
            	<select class="form-control input-sm" id="isAdditionalPostSelect">
            		<option>選択してください</option>
        		    <option>本務</option>
        		    <option>兼務</option>
                </select>
            </div>
        </div> 

        <div class="form-group">
            <label>役職</label>
            <input type="text" class="form-control input-sm" id="txtPosition" value="">
        </div>
        
        <div class="form-group">
            <label>内線</label>
            <input type="text" class="form-control input-sm" id="txtPhoneNumber" value="">
        </div>
        <div class="form-group">
            <label>外線</label>
            <input type="text" class="form-control input-sm" id="txtOutsideCall" value="">
        </div>
        <div class="form-group">
            <label>FAX</label>
            <input type="text" class="form-control input-sm" id="txtFAX" value="">
        </div>

        <div class="form-group">
            <label>派遣元企業種別</label>
            <div style="display:flex;">
            	<select class="form-control input-sm" id="hakenCompanyTypeSelect">
            		<option value="">選択してください</option>
            		@foreach($companyTypeList as $curCompanyType)
            		    @if($curCompanyType['id'] != 1)
            		    <option value="{{ $curCompanyType['id'] }}">{{ $curCompanyType['name'] }}</option>
            		    @endif
            		@endforeach
            	</select>
                <span class="warning_text" id="hakenCompanyTypeSelect_err"></span>
            </div>
        </div>

        <div class="form-group">
            <label>派遣元企業名</label>

            <div style="display:flex;">
            	<select class="form-control input-sm" id="hakenCompanyNameSelect" style="width:75%;">
            		<option value="">選択してください</option>
            		@foreach($companyList as $curCompany)
            		    @if($curCompany['id'] != 1)
            		    <option value="{{ $curCompany['id'] }}">{{ $curCompany['name'] }}</option>
            		    @endif
            		@endforeach
                </select>
                &nbsp;&nbsp;&nbsp;
                <a href="javascript:void(0)" onClick="UpdateCompanyNameSelect();" style="margin-top:5px;"><img class="appIconBig" src="../public/image/update.png" alt="" height="17" width="17" /></a>
                &nbsp;&nbsp;&nbsp;
                <a href="/iPD/company/index/is_other_page" target="_blank" rel="noopener noreferrer" style="margin-top:4px;">新規企業追加</a>
            </div>
        </div>

        <div class="form-group" style="padding-bottom:20px;">
            <label>勤務地</label>
            <textarea id="txtAWorkLocation" rows="5" class="form-control input-sm"></textarea>
        </div>
        
        <div id="btnGroupIn">
            <input type="button" class="btn btnDesignIn" style="margin:0 0 3vh 0;" name="btnCreateUser" value="作成" onClick="CreateUser();"/>
            <input type="button" class="btn btnDesignIn" style="margin:0 0 3vh 0;" name="btnCancel" id="btnCancel" value="キャンセル" onClick="ClosePopup();"/>
        </div>

    </div>
</div>

<div id="autoLoadPersonalData">
    <div id="autoLoadInput">
        
        <div><a class="close" href="javascript:void(0);" onClick ="CloseAutoLoad()" style="top:0px;padding-top:3vh;padding-right:3vh;">&times;</a><br></div>
    	<div class="align-center" style="margin-left: 2vh;">
    		<h4>電話帳データ自動読み込み</h4>
    		<span class="err_msg" id="auto_load_err_message"></span>
    	</div>
    	<hr style="margin-top:10px;margin-bottom:10px;">

        <div class="form-group" style="margin: 7px 30px 7px 30px;height: 100px;">
            <label>指定された形式で張り付けてください</label>
            <input type="text" class="form-control input-sm" id="txtAutoLoad" value="">
            <input type="button" class="btn" name="btnLoadAutoLoadString" id="btnLoadAutoLoadString" value="Load" onClick="LoadAutoLoadString();"/>
        </div>
        
    </div>
</div>

@endsection