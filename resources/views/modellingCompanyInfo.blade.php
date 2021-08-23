@extends('layouts.baselayout')
@section('title', 'CCC - モデリング会社管理')

<!--CSS and JS file-->
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="../public/js/select2/select2.min.js"></script>
<script type="text/javascript" src="../public/js/modellingCompany.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="../public/css/select2.min.css" />
<link rel="stylesheet" href="../public/css/jquery-ui_18.css" />
<link rel="stylesheet" href="../public/css/partnerCompany.css" />
<style>
.main-content{
	/*background-color: #f2f3f3;*/
	margin: 0 auto;
	width:90%;
	display : center;
}
.heading{
    display: flex;
    margin: 0 0 0 3%;
    justify-content: space-between;
    align-items: flex-end;
}
.partnerCompanyContactView{
    width: 100%;
    margin: 1% 3% 3% 2%;
    display: inline-flex;
    justify-content: space-between;
}
#partnerCompanyContactList{
    width : 30%;
    border : 1px solid #eee;
    border-radius : 3px;
    padding : 10px;
    box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
}
#partnerCompanyContactForm{
    width  : 65%;
    border : 1px solid #eee;
    border-radius : 3px;
    padding : 10px;
    box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
}
label{
    font-size : 18px;
    font-weight : normal;
}
.form-inline-btn-group{
    display: flex;
    justify-content: flex-end;
}

.form-inline-btn-group button{
    font-size : 16px;
    padding-left: 20px;
    padding-right: 20px;
}
.inline-label{
    display : inline-flex;
}
.personalView{
    border: 1px solid #eee;
    padding: 20px;
    margin: 10px;
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
    
    <input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>
    
    <div class="heading">
        <h3>モデリング会社情報管理</h3>
        <div class="btn-group customBtnGroup" role="group" aria-label="...">
          <button type="button" class="btn btn-default" id="insertBtn1" onclick="InsertBtn();" >
              <span class="glyphicon glyphicon-pencil">　入力</span>
          </button>
          <button type="button" class="btn btn-default" id="showBtn1" onclick="ShowBtn();">
              <span class="glyphicon glyphicon-th-list"> 一覧</span>
        　</button>
        </div>
    </div>
    <hr>
    <div class="partnerCompanyContactView">
        <div id="partnerCompanyContactList">
            <div class="panel panel-default">
              <div class="panel-heading" style="color: white; background-color: gray;"><h4>モデリング会社名</h4></div>
              
                  <table class="table table-hover" id="partnerCompanyNameList">
                      <tbody>
                          
                      </tbody>
                  </table>
              
            </div>
        </div>
        <div id="partnerCompanyContactForm">
            <div class="form-inline-btn-group">
                <button class="btn btn-primary" style="margin-right : 20px" onclick="createNewModellingCompany()">新規作成</button>
                <button class="btn btn-info" style="margin-right : 20px"  onclick="updateModellingCompany()">変更</button>
                <button class="btn btn-danger" onclick="deleteModellingCompany()">削除</button>
            </div>
            <input name="companyId" value="" id="companyId" type="hidden">
            <input name="hidCompanyId" value="" id="hidCompanyId" type="hidden">
            <p id="errorMsg" style="font-size: 14px; color: red"></p>
            <form action="" method="">
                <input name="_token" value="{{ csrf_token() }}" type="hidden">
                
                <div class="form-group">
                    <div class="checkbox">
                        <label><input type="checkbox" value="" name="isPartnerCompany" id="isPartnerCompany">パートナー会社</label>
                    </div>
                </div>
                
                <!--<div class="form-group">-->
                <!--    <div class="inline-label">-->
                <!--        <label>会社名</label>-->
                <!--        <p id="companyNameWarning" style="color: red;display :none " >       *（株式会社との間は半角スペース）</p>-->
                <!--    </div>-->
                <!--    <input type="text" class="form-control" name="partnerCompanyName" id="partnerCompanyName" required>-->
                <!--</div>-->
                
                <div class="form-group">
                    <label>会社名</label>
                    <input type="text" class="form-control" name="txtCompanyName" id="txtCompanyName" style="display:none">
                    <div style="display : flex" id="cName">
                        <select class="form-control input-sm" name="companyName" id="companyName" style="width:75%;"></select>
                        
                        &nbsp;&nbsp;&nbsp;
                        <a href="javascript:void(0)" id="updateInsertedCompany" onclick="ShowSuggestedCompany();" style="margin-top:5px;"><img class="appIconBig" src="../public/image/update.png" alt="" height="17" width="17"></a>
                        &nbsp;&nbsp;&nbsp;
                        <a id="insertNewCompany" onClick="DisplayPopup()" style="margin-top: 4px">新規企業追加</a>
                    </div>
                    <button type="button" class="btn btn-default" id="getCompanyInfo" style="margin-top: 10px" onclick="GetCompanyInfo();">会社名から情報取得<span class="glyphicon glyphicon-play-circle"></span></button>
                </div>
                
                <div class="form-group">
                    <label>職種</label>
                    <input type="text" class="form-control" name="jobType" id="jobType" disabled />
                </div>
                
                <div class="form-group">
                    <label>支店</label>
                    <!--<select class="form-control branchCompany" name="branch" id="branch">-->
                    <!--  <option>選択してください。</option> -->
                    <!--  <option>大阪</option>-->
                    <!--  <option>広島</option>-->
                    <!--  <option>九州</option>-->
                    <!--  <option>名古屋</option>-->
                    <!--  <option>海外</option>-->
                    <!--  <option>四国</option>-->
                    <!--</select>-->
                    <input type="text" class="form-control" name="branch" id="branch" disabled >
                </div>
                
                <div class="form-group">
                    <div class="inline-label">
                        <label>郵便番号</label>
                        <p id="mailCodeWarning" style="color: red;" > *(〒抜き)</p>
                    </div>
                    <input type="text" class="form-control" name="postalCode" id="postalCode" placeholder="000-0000の形で入力すること" disabled />
                </div>
                <div class="form-group">
                    <div class="inline-label">
                        <label>会社住所</label>
                        <p id="addressWarning" style="color: red;" > *(都道府県から)</p>
                    </div>
                    <input type="text" class="form-control" name="address" id="address" disabled />
                </div>
                
                <div class="form-group">
                    <label>担当者人数</label>
                    <input type="number" id="numOfIncharge" min="1" value="1"/>
                </div>
                <div id="personalInfo">
                    <div class="personalView" id="personalView1">
                        <div>
                            <div class="form-group col-md-6" style="padding: 0">
                                <label>担当者氏名：姓</label>
                                <input type="text" class="form-control" name="firstName1" id="firstName1" />
                            </div>
                            <div class="form-group col-md-6" style = "padding-right : 0">
                                <label>担当者名</label>
                                <input type="text" class="form-control" name="lastName1" id="lastName1" />
                            </div>
                        </div>
                    
                        <div class="form-group">
                            <label>電話番号「携帯」</label>
                            <input type="text" class="form-control" name="phone1" id="phone1" />
                        </div>
                        <div class="form-group">
                            <label>電話番号「外線」</label>
                            <input type="text" class="form-control" name="outsideCall1" id="outsideCall1" />
                        </div>
                        <div class="form-group">
                            <label>メール</label>
                            <input type="email" class="form-control" name="email1" id="email1" />
                        </div>
                    </div>
                
                </div>
                
                
                <!--<div class="form-group">-->
                <!--    <div class="inline-label">-->
                <!--        <label>担当者名</label>-->
                <!--        <p id="inchargeNameWarning" style="color: red;" > *(氏名間は全角スペース)</p>-->
                <!--    </div>-->
                <!--    <input type="text" class="form-control" name="partnerInchargeName" id="partnerInchargeName"  />-->
                <!--</div>-->
                <!--<div class="form-group">-->
                <!--    <label>電話番号</label>-->
                <!--    <input type="text" class="form-control" name="partnerPhone" id="partnerPhone" />-->
                <!--</div>-->
                <!--<div class="form-group">-->
                <!--    <label>メール</label>-->
                <!--    <input type="email" class="form-control" name="partnerEmail" id="partnerEmail" />-->
                <!--</div>-->
            </form>
            <div class="form-group">
                    <button class="form-control btn btn-primary" style="font-size : 16px;" id="saveModellingCompany" onclick="insertModellingCompany()">登録</button>
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
                <input type="button" class="btn btnDesign" name="btnCreateUser" value="作成" onClick="CreateCompany();"/>
                <input type="button" class="btn btnDesign" name="btnCancel" id="btnCancel" value="キャンセル" onClick="ClosePopup();"/>
            </div>
            </form>   		
        </div>       		
	</div>
</div> 


<script>
    $(document).ready(function() {
       $( "#partnerCompanyName" ).keydown(function() {
          var text = $("#partnerCompanyName").val();
          if(text.includes("株式会社")){
          　for(var i=0; i<text.length; i++){
              if(text.indexOf(' ') >0){
                $("#companyNameWarning").css({"display":"none"});
              }else{
                $("#companyNameWarning").css({"display":"block"});
              }
            }
          }
        });
        $("#branchCompany").select2();
        
        
        
    });
</script>
 

@endsection