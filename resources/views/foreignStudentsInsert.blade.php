@extends('layouts.baselayout')
@section('title', 'CCC - 留学生入力')

<!--CSS and JS file-->
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">  
<link rel="stylesheet" href="../public/css/foreignStudentsInsert.css" />
<link rel="stylesheet" href="../public/css/jquery-ui_18.css" />
<script type="text/javascript" src="../public/js/select2/select2.min.js"></script>
<script type="text/javascript" src="../public/js/foreignStudentsInsert.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<link rel="stylesheet" href="../public/css/select2.min.css" />

<style>
#automaticInput{
    margin-top : 1%;
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

#btnCreateUserPopu{
    margin: 6px 0 6px 0;
    font-size: 12px;
    border: solid 1px lightgray;
}

#btnGroup{
    margin-left: 220px;
}

.popup{
    height:auto;
}

.warning_text{
    color:red;
    font-size:1em;
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
@include('layouts.loading')
<div class="main-content">
    
    <div class="btn-group customBtn" role="group" aria-label="...">
      <button type="button" class="btn btn-default" id="insertBtn" onclick="InsertBtn();" ><h5>留学生入力</h5></button>
      <button type="button" class="btn btn-default" id="showBtn" onclick="ShowBtn();"><h5>留学生情報確認</h5></button>
      
    </div>
    
    
    <div class="input-group" style="margin: 1% 1% 0% 3%;">
          <span class="input-group-addon"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></span>
          <input type="text" id="userSearch1" class="form-control" placeholder="氏名・社員コード・分野・派遣元所属で検索">
    </div>
        
    <div class="studentViewer">
        
        
        <div id="sideBar">
            <div class="panel">
                <h3>登録済留学生</h3>
                <hr>
                <div class="form-check">
                    
                    <input type="checkbox" class="form-check-input" id="all" checked>
                    <label class="form-check-label">All &nbsp;</label>
                    
                    <input type="checkbox" class="form-check-input" id="notfinished">
                    <label class="form-check-label">留学中 &nbsp;</label>
                    
                    <input type="checkbox" class="form-check-input" id="notyet">
                    <label class="form-check-label">留学予定 &nbsp;</label>
                    
                    <input type="checkbox" class="form-check-input" id="finished">
                    <label class="form-check-label">留学終了 &nbsp;</label>
                </div>
                
                
                <table class="table table-bordered table-hover" id="studentNameList">
                    <thead>
                        <tr class="info">
                            <th>氏名</th>
                        </tr>
                    </thead>        
                    <tbody id="searchableUserList1">
                        
                    </tbody>
                </table>
            </div>
            
            
            
        </div>
        
        <div id="studentInfo">
            
            <div class="row" style="float: right; margin-right : 5px;" >
                
                <input type="button" name="btnCreateNewStudent" id="btnCreateNewStudent" class="btn btn-primary" value="新規作成" onclick="CreateNewStudent()"/>&nbsp;&nbsp;
                <input type="button" name="btnSaveStudent" class="btn btn-info" id="btnSaveStudent" value="変更" onclick="UpdateStudent()"/>&nbsp;&nbsp;
                <input type="button" name="btnDeleteStudent" id="btnDeleteStudent" class="btn btn-danger" value="留学履歴の削除" onclick="DeleteStudent()"/>
            </div>
            
            
            <div class="row">
                <form action="" method="post" >
                <input name="_token" value="{{ csrf_token() }}" type="hidden">
                <input name="studentId" value="" id="studentId" type="hidden">
                
                <!--<div class="form-group col-xs-10 col-sm-12 col-md-12 col-lg-12">-->
                <!--    <label>氏名</label>-->
                <!--    <input type="text" class="form-control" id="username" name="username" placeholder="Enter name" required>-->
                <!--</div>-->
                <p style="margin-left:2%; color:red; font-size: 14px" id="err_message"></p>
                <div class="clearfix"></div>
        
                <!--氏名-->
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <label>氏名：姓</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" >
                </div>
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <label>名</label>
                    <input type="text" class="form-control" id="lastname" name="lastname"  >
                </div>
                <div class="clearfix"></div>
                
                <!--氏名(Kana)-->
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <label>氏名：姓（カナ）</label>
                    <input type="text" class="form-control" id="firstnameKana" name="firstnameKana" >
                </div>
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <label>名（カナ）</label>
                    <input type="text" class="form-control" id="lastnameKana" name="lastnameKana"  >
                </div>
                <div class="clearfix"></div>
                
                <div class="form-group col-xs-10 col-sm-12 col-md-12 col-lg-12">
                    <label>メールアドレス</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                    <botton type="button" id="automaticInput" onclick="getUserAttribute()" class="btn btn-default">留学生情報を取得<span class="glyphicon glyphicon-play-circle"></span></botton>
                </div>
                
    
                <div class="clearfix"></div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <h4><u><b>留学時</b></u></h4>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <h4><u><b>現在</b></u></h4>
                </div>
                 <div class="clearfix"></div>
                
                
                <!--支店名-->
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6　ui-widget">
                    <label for="s-place">支店名</label>
                    <select class="form-control" id="s-place" name="s-place" disabled></select>
                    <!--<input type="text" class="form-control" id="s-place" name="s-place" placeholder="Enter 留学時の店">-->
                </div>
                
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <label>支店名</label>
                    <select class="form-control" id="e-place" name="e-place" disabled></select>
                    <!--<input type="text" class="form-control" id="e-place" name="e-place" placeholder="Enter 現在の店">-->
                </div>
                <div class="clearfix"></div>
                
                <!--大林組所属-->
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <label>大林組所属</label>
                    <input type="text" class="form-control" id="s-obayashi" name="s-obayashi" disabled >
                </div>
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <span class="glyphicon glyphicon-link chainLink" ></span>
                    <label>大林組所属</label>
                    <input type="text" class="form-control" id="e-obayashi" name="e-obayashi" disabled>
                </div>
                <div class="clearfix"></div>
                
                <!--社員コード-->
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <label>社員コード</label>
                    <input type="text" class="form-control" id="s-code" name="s-code" disabled>
                </div>
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <span class="glyphicon glyphicon-link chainLink" ></span>
                    <label>社員コード</label>
                    <input type="text" class="form-control" id="e-code" name="e-code" disabled>
                </div>
                <div class="clearfix"></div>
                
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <label>企業種別</label>
                    <select name="companyTypeSelect" class="companyTypeSelect" id="s-companyType" disabled></select>
                    <a id="s-newCompany" onClick="DisplayPopup()" disabled>新規企業追加</a>
                </div>
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <label>企業種別</label>
                    <select name="companyTypeSelect" class="companyTypeSelect" id="e-companyType" disabled></select>
                    <a id="e-newCompany" onClick="DisplayPopup()" disabled>新規企業追加</a>
                </div>
                <div class="clearfix"></div>
                
                <!--派遣元所属-->
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <label>派遣元所属</label>
                    <input type="text" class="form-control" id="s-hakenplace" name="s-hakenplace" disabled>
                    <select  class="form-control" id="s-hakenplaceSelectBox" name="s-hakenplaceSelectBox" style ="display :none">
                    </select>
                </div>
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <span class="glyphicon glyphicon-link chainLink" ></span>
                    <label>派遣元所属</label>
                    <input type="text" class="form-control" id="e-hakenplace" name="e-hakenplace" disabled>
                    <select class="form-control" id="e-hakenplaceSelectBox" name="e-hakenplaceSelectBox" style="display :none">
                    </select>
                </div>
                <div class="clearfix"></div>
                
                <!--スキル-->
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <label>スキル</label>
                    <input type="text" class="form-control" id="s-skill" name="s-skill" disabled>
                </div>
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <span class="glyphicon glyphicon-link chainLink" ></span>
                    <label>スキル</label>
                    <input type="text" class="form-control" id="e-skill" name="e-skill" disabled>
                </div>
                <div class="clearfix"></div>
                
                <!--分野-->
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <label>分野</label>
                    <input type="text" class="form-control" id="s-field" name="s-field" disabled>
                </div>
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <span class="glyphicon glyphicon-link chainLink"></span>
                    <label>分野</label>
                    <input type="text" class="form-control" id="e-field" name="e-field" disabled >
                </div>
                <div class="clearfix"></div>
                
                <!--留学種別-->
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <label>留学種別</label>
                    <input type="text" class="form-control" id="s-type" name="s-type" disabled>
                </div>
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <span class="glyphicon glyphicon-link chainLink"></span> 
                    <label>留学種別</label>
                    <input type="text" class="form-control" id="e-type" name="e-type" disabled>
                </div>
                <div class="clearfix"></div>
                
                <!--留学開始-->
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <label>留学開始</label>
                    <input type="date" class="form-control" id="startDate" name="startDate" disabled>
                </div>
                <div class="clearfix"></div>
                
                <!--留学終了​-->
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <label>留学終了​</label>
                    <input type="date" class="form-control" id="endDate" name="endDate" disabled>
                </div>
                <div class="clearfix"></div>
                
                <!--プチ留学1​-->
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <label>プチ留学1​</label>
                    <input type="date" class="form-control" id="puchi1" name="puchi1" disabled>
                </div>
                <div class="clearfix"></div>
                
                <!--プチ留学2​-->
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <label>プチ留学2​</label>
                    <input type="date" class="form-control" id="puchi2" name="puchi2" disabled>
                </div>
                <div class="clearfix"></div>
                
                <!--プチ留学3​-->
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <label>プチ留学3​</label>
                    <input type="date" class="form-control" id="puchi3" name="puchi3" disabled>
                </div>
                <div class="clearfix"></div>
                
                <!--プチ留学4​-->
                <div class="form-group col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <label>プチ留学4​</label>
                    <input type="date" class="form-control" id="puchi4" name="puchi4" disabled>
                </div>
                <div class="clearfix"></div>
                
                <!--Kind of Users-->
                <input type="hidden" id="isSpeedCourse" />
                <input type="hidden" id="isC3User" />
                <input type="hidden" id="position" />
                <input type="hidden" id="companyId" />
                
                
            </form>
                <div class="col-xs-10 col-sm-6 col-md-6 col-lg-6">
                    <button id="saveStudent" class="btn btn-primary form-control" onclick="SaveStudent()">登録</button>
                </div>
            </div>
            
            
        </div>
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
                    <td ><input type="text" class="form-control" name="txtName" id="txtName" required />
                    <span class="warning_text" id="txtName_err"></span></td>
                </tr>
                <tr>
                    <td>企業種別 : </td>
                    <td>
                        <select name="companyTypeSelect" id="companyType" required></select>
                        <span class="warning_text" id="companyTypeSelect_err"></span>
                    </td>
                </tr> 
                <tr>
                    <td>職種 : </td>
                    <td><input type="text" class="form-control" name="txtIndustryType" id="txtIndustryType"/></td>
                </tr>
                <tr>
                    <td>郵便番号 : </td>
                    <td><input type="text" class="form-control" name="txtPostalCode" id="txtPostalCode" placeholder="000-0000"/></td>
                </tr>
                <tr>
                    <td>所在地 : </td>
                    <td><input type="text" class="form-control" name="txtAddress" id="txtAddress"/></td>
                </tr>
            </table>
            <div id="btnGroup">
                <input type="button" class="btn btnDesign" name="btnCreateUser" value="作成" onClick="CreateCompany();"/>
                <input type="button" class="btn btnDesign" name="btnCancel" id="btnCancel" value="キャンセル" onClick="ClosePopup();"/>
            </div>
            </form>   		
        </div>       		
	</div>
</div> 
<script>
$(document).ready(function(){
  $('.chainLink').on('click',function(){
    var that = this;
    var prevText = $(that).parent().prev().find('input').val();
    var nextText = $(that).nextAll().last().val();
    
    if(prevText){
        $(that).nextAll().last().val('');
        $(that).nextAll().last().val(prevText);
    }else{
        $(that).parent().prev().find('input').val('');
        $(that).parent().prev().find('input').val(nextText);
    }
    
  });
  
  
  $('#notfinished, #finished, #notyet').click(function(){
    $('#all').removeAttr('checked');
    var isNotFinished = $("#notfinished").is(":checked");
    var isFinished = $("#finished").is(":checked");
    var isNotYet = $("#notyet").is(":checked");
    
    if(isNotFinished && isFinished && isNotYet){
        ClearAllStudents();
        LoadAllStudents();
    }else if(isNotFinished && isNotYet){
        ClearAllStudents();
        LoadNotFinishedAndNotYetStudent();
    }else if(isNotFinished && isFinished ){
        ClearAllStudents();
        LoadNotFinishedAndFinishedStudent();
    }else if(isNotYet && isFinished ){
        ClearAllStudents();
        LoadNotYetAndFinishedStudent();
    }else if(isNotFinished){
        ClearAllStudents();
        LoadNotFinishedStudent();
    }else if(isNotYet){
        ClearAllStudents();
        LoadNotYetStudent();
    }else if(isFinished){
        ClearAllStudents();
        LoadFinishedStudent();
    }else{
        ClearAllStudents();
    }
    
  });
  
  $('#all').click(function(){
    
    window.location.reload();
  });
  
  
});
</script>
@endsection