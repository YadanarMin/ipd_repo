@extends('layouts.baselayout')
@section('title', 'CCC - Company settings')

@section('head')
<script type="text/javascript" src="/iPD/public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="/iPD/public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="/iPD/public/js/company.js"></script>
<script type="text/javascript" src="/iPD/public/js/easyselectbox.min.js"></script>
<script src='https://kit.fontawesome.com/a076d05399.js'></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/redmond/jquery-ui.css" >
<style>
.marginAdjust{
    width:85%;
    margin: 2vh auto;
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
    width:10%;
    height:20px;
}
.outerBorder{
    border:none;
    width:100%;
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
th,td{
/*border:1px solid;*/
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
</style>
@endsection

@section('content')
<div class="main-content marginAdjust">  
    <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>
    <input type="hidden" id="hidCompanyData" name="hidCompanyData" value="{{json_encode($companyList)}}"/>
    <input type="hidden" id="hidBranchData" name="hidBranchData" value="{{json_encode($branchList)}}"/>

    <div id="searchDiv">
        <h4 class="page-title">企業情報設定</h4>
       
        <input type="button" class="btn" name="btnCreateUserPopup" id="btnCreateUserPopu" value="新規企業登録" onClick="DisplayPopup();"/>
    </div>  
    <div class="outerBorder">
        <table id="tblUser">
            <thead>
            <tr>
                <th style="width:30px;">ID</th>
                <th style="width:350px;">企業名</th>
                <th style="width:170px;">企業種別</th>
                <th style="width:170px;">職種</th>
                <th style="width:100px;">支店ｺｰﾄﾞ</th>
                <th style="width:170px;">支店名</th>
                <th style="width:170px;">郵便番号</th>
                <th style="width:350px;">住所</th>
                <th style="width:50px;">編集</th>
                <th style="width:50px;">削除</th>
            </tr> 
            </thead>
            <tbody>
            @if (count($companyList) > 0)
            @foreach($companyList as $company)
            <tr class="trCompany">
                <td style="width:30px;">{{ $company["id"] }}</td>
                <td style="width:350px;">
                    <div style="display:flex;margin:0 5px 0 0;position:relative;">
                        
                    @php
                      $branch_num = 0;
                    @endphp
                    
                    @if (count($branchList) > 0)
                        @foreach($branchList as $branch)
                            @if ($branch["company_id"] == $company["id"])
                                @php
                                  $branch_num = $branch_num + 1;
                                @endphp
                            @endif
                        @endforeach  
                    @endif
                        
                    @if ($branch_num > 0)
                        <div style="position: absolute;right: 0;"><img src="../public/image/drop_down.png" alt="dropdown" align="dropdown" style="width:16px;" onclick="toggleServiceList({{$company['id']}}, '{{ $company["name"] }}')"></div>
                    @else
                        <div style="position: absolute;right: 0;"></div>
                    @endif
                        <div class="companyTitle">{{ $company["name"] }}</div>
                    </div>
                </td>
                <td style="width:170px;">{{ $company["company_type_name"] }}</td>
                <td style="width:170px;">{{ $company["industry_type"] }}</td>
                <td style="width:100px;"></td>
                <td style="width:170px;"></td>
                <td style="width:170px;"></td>
                <td style="width:350px;"></td>
                <td style="width:100px;"><a href="javascript:void(0)" onClick="DisplayPopup({{$company['id']}});"><img class="appIconBig" src='/iPD/public/image/edit.png' alt='' height='20' width='20' /></a></td>
                <td style="width:100px;"><a href="javascript:void(0)" onClick="DeleteUser({{$company['id']}}, '{{ $company["name"] }}');"><img class="appIconBig" src='/iPD/public/image/trash.png' alt='' height='20' width='15' /></a></td>
            </tr>
            
                @if (count($branchList) > 0)
                @foreach($branchList as $branch)
                
                @if ($branch["company_id"] == $company["id"])
                <tr class="trBranch" style="display:none;">
                    <td style="width:30px;"></td>
                    <td style="width:350px;"></td>
                    <td style="width:170px;"></td>
                    <td style="width:170px;"></td>
                    <td style="width:100px;">{{ $branch["code"] }}</td>
                    <td style="width:170px;">{{ $branch["name"] }}</td>
                    <td style="width:170px;">{{ $branch["postal_code"] }}</td>
                    <td style="width:350px;">{{ $branch["address"] }}</td>
                    <td style="width:100px;"></td>
                    <td style="width:100px;"><a href="javascript:void(0)" onClick="DeleteBranch({{ $branch["id"] }}, '{{ $branch["name"] }}');"><img class="appIconBig" src='/iPD/public/image/trash.png' alt='' height='20' width='15' /></a></td>
                </tr>
                @endif
                
                @endforeach  
                @endif           
            
            @endforeach  
            @endif 
            
            </tbody>
        </table> 
    </div>
    

</div>

<!-- popup -->
<div id="createUser" class="popupOverlay">
	<div class="popup popupSize">
	    <div><a class="close" href="javascript:void(0);" onClick ="ClosePopup()" style="top:0px;">&times;</a><br></div>

		<div align="center">			
            <form name="createCompanyForm" method="post">
            <h4 style="text-align:center;color:dimgray;font-weight: bold;">企業情報登録</h4>
            <table id="tblCreateUser">
                <tr>
                    <td width="15%">企業名 : </td>
                    <td >
                        <input type="text" class="form-control" name="txtName" id="txtName" required />
                        <span class="warning_text" id="txtName_err"></span>
                    </td>
                </tr>
                <tr>
                    <td>企業種別 : </td>
                    <td>
                        <select class="form-control" name="companyTypeSelect" id="companyTypeSelect" required></select>
                        <span class="warning_text" id="companyTypeSelect_err"></span>
                    </td>
                </tr> 
                <tr>
                    <td>職種 : </td>
                    <td><input type="text" class="form-control" name="txtIndustryType" id="txtIndustryType"/></td>
                </tr>
                <tr>
                    <td>支店 : </td>
                    <td>
                        <input type="text" class="form-control" name="txtBranch" id="txtBranch"/>
                        <span class="warning_text" id="branchSelect_err"></span>
                    </td>
                </tr> 
                <tr>
                    <td>支店コード : </td>
                    <td><input type="text" class="form-control" name="txtCode" id="txtCode" disabled/></td>
                </tr>
                <tr>
                    <td>郵便番号 : </td>
                    <td><input type="text" class="form-control" name="txtPostalCode" id="txtPostalCode" placeholder="000-0000" disabled/></td>
                </tr>
                <tr>
                    <td>所在地 : </td>
                    <td><input type="text" class="form-control" name="txtAddress" id="txtAddress"  disabled/></td>
                </tr>
            </table>
            <div id="btnGroup">
                <input type="button" class="btn btnDesign" name="btnCreateUser" value="作成" onClick="CreateUser();"/>
                <input type="button" class="btn btnDesign" name="btnCancel" id="btnCancel" value="キャンセル" onClick="ClosePopup();"/>
            </div>
            </form>   		
        </div>       		
	</div>
</div> 

@endsection