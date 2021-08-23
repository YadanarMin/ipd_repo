
<!DOCTYPE html>
<html>
 <head>
  <title>CCC システム管理者承認</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>

  <link rel="stylesheet" href="/iPD/public/css/loginAccountCreating.css">
  <link rel="stylesheet" href="/iPD/public/css/jquery-ui_18.css" />
  <script type="text/javascript" src="/iPD/public/js/loginAccountApproval.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

  <style type="text/css">
  
  #tblAccountInfo{
	margin: 0 20px 0 20px;
  }
  #tblAccountInfo td:nth-child(1),#tblChiefAdminInfo td:nth-child(1){
	  background:#b0c4de	;
	  width:25%;
	  text-align:right;
	  padding:5px 5px 5px 20px;
	  font-weight:bold;
	  border:1px solid #fff;
  }
  #tblAccountInfo td:nth-child(2),#tblChiefAdminInfo td:nth-child(2){
	  background:whitesmoke;
	  text-align:left;
	  padding:5px;
	  font-weight:bold;
	  border:1px solid #fff;
  }
  .highlight-text{
	color:darkblue;
	font-weight:bold;
  }
  .margin-left{
	margin-left:20px;
  }
  .btn-approve-color{
	background : #6c757d !important;
	color:#fff !important;
   }

  </style>
  <script></script>
 </head>
 <body>
	<div id="left_div" class="col-md-2">
		<ul class="list-group">
		  <li class="list-group-item disabled">ユーザーからの登録申請</li>
		  <li class="list-group-item disabled">管理責任者の承認</li>
		  <li class="list-group-item active-color">システム管理者の承認</li>
		</ul>
	</div>
	<div class="col-md-7 margin-left">
		<div>
			<h4 class="common-title">システム管理者の承認画面</h4>
			
		</div>
		<div>
			<p>
				<span class="highlight-text">@if(isset($userInfo["name"])) {{$userInfo["name"]}} @endif さん（登録申請者）</span>が、CCC(Central Command Center)への登録を申請しています。</br>
				<span class="highlight-text">@if(isset($chiefAdmin["name"])) {{$chiefAdmin["name"]}} @endif さん（管理責任者）</span>は承認済です。
			</p>
		</div><br>
		<div class="form-group">
            <label>ユーザー権限</label>
            <input type="hidden" id="hidAuthoritId" value="">
            <input type="text" class="form-control input-sm" id="txtAuthority" value="">
        </div>
		<div>
			
			<hr>
			<div style = "display:flex;">
				<div style="width:700px;">
					<div class="align-center" style="font-weight:bold !important;">
						<h4><span class="highlight-text">登録申請者情報</span></h4>
					</div>
					<table id="tblAccountInfo" width="">
						<tr>
							<td>ユーザー名</td>
							<td>@if(isset($userInfo["name"])) {{$userInfo["name"]}} @endif</td>
						</tr>
						<tr>
							<td>メールアドレス</td>
							<td>@if(isset($userInfo["mail"])) {{$userInfo["mail"]}} @endif</td>
						</tr>
						<tr>
							<td>企業名</td>
							<td>@if(isset($userInfo["companyName"])) {{$userInfo["companyName"]}} @endif</td>
						</tr>
						<tr>
							<td>支店名</td>
							<td>@if(isset($userInfo["branch"])) {{$userInfo["branch"]}} @endif</td>
						</tr>
						<tr>
							<td>組織</td>
							<td>@if(isset($userInfo["dept"])) {{$userInfo["dept"]}} @endif</td>
						</tr>
						<tr>
							<td>電話番号</td>
							<td>@if(isset($userInfo["phone"])) {{$userInfo["phone"]}} @endif</td>
						</tr>
						<tr>
							<td>勤務地</td>
							<td>@if(isset($userInfo["work_location"])) {{$userInfo["work_location"]}} @endif</td>
						</tr>
					</table>
				</div>
				<div style="width:600px;">
					<div class="align-center" style="font-weight:bold !important;">
						<h4><span class="highlight-text">管理責任者情報</span></h4>
					</div>
					<table id="tblChiefAdminInfo" width="">
						<tr>
							<td>ユーザー名</td>
							<td>@if(isset($chiefAdmin["name"])) {{$chiefAdmin["name"]}} @endif</td>
						</tr>
						<tr>
							<td>メールアドレス</td>
							<td>@if(isset($chiefAdmin["mail"])) {{$chiefAdmin["mail"]}} @endif</td>
						</tr>
						<tr>
							<td>企業名</td>
							<td>@if(isset($chiefAdmin["companyName"])) {{$chiefAdmin["companyName"]}} @endif</td>
						</tr>
						<tr>
							<td>支店名</td>
							<td>@if(isset($chiefAdmin["branch"])) {{$chiefAdmin["branch"]}} @endif</td>
						</tr>
						<tr>
							<td>組織</td>
							<td>@if(isset($chiefAdmin["dept"])) {{$chiefAdmin["dept"]}} @endif</td>
						</tr>
						<tr>
							<td>電話番号</td>
							<td>@if(isset($chiefAdmin["phone"])) {{$chiefAdmin["phone"]}} @endif</td>
						</tr>
						<tr>
							<td>勤務地</td>
							<td>@if(isset($chiefAdmin["work_location"])) {{$chiefAdmin["work_location"]}} @endif</td>
						</tr>
					</table>	
				</div>
				
			</div>
			
		<span class="err_msg" id="err_message" style="margin-top:20px;"></span>
		</div><hr>
		
		</br>
		
		<div style="display:flex;">
			<button class="btn btn-custom align-center btn-approve-color" onClick="ApproveByCCCAdmin(@if(isset($userInfo["id"])) {{$userInfo["id"]}} @endif)">承認</button>
			<button class="btn btn-custom align-center" onClick="Decline(@if(isset($userInfo["id"])) {{$userInfo["id"]}} @endif,@if(isset($userInfo["companyId"])) {{$userInfo["companyId"]}} @endif)">却下</button>	
		</div>
	</div>
	
  
 </body>
</html>