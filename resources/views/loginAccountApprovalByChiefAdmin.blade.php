
<!DOCTYPE html>
<html>
 <head>
  <title>CCC 管理責任者承認</title>
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
  #tblAccountInfo td:nth-child(1){
	  background:#b0c4de	;
	  width:20%;
	  text-align:right;
	  padding:5px 5px 5px 20px;
	  font-weight:bold;
	  border:1px solid #fff;
  }
  #tblAccountInfo td:nth-child(2){
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
		  <li class="list-group-item active-color">管理責任者の承認</li>
		  <li class="list-group-item disabled">システム管理者の承認</li>
		</ul>
	</div>
	<div class="col-md-7 margin-left">
		<div>
			<h4 class="common-title">管理責任者の承認画面</h4>
			
		</div>
		<div>
			<p>
				<span class="highlight-text">@if(isset($userInfo["name"])) {{$userInfo["name"]}} @endif さん</span>(登録申請者)があなたを管理者として、CCC(Central Command Center)への登録を申請しています。​
				以下の利用規約について登録申請者が理解したことをご確認の上、承認してください。
			</p>
		</div>
		<div>
			<div class="align-center" style="font-weight:bold !important;">
				<h4 class="highlight-text">登録申請者情報</h4>
			</div>
			<hr>
			<table id="tblAccountInfo" width="95%;">
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
		
		</div><hr>
		<div>
			<p>
				はじめに</br>
				当サイトの利用につきましては、必ず本利用規約をよく読み、必ず同意の上ご利用ください。
			</p>
		</div></br>
		<div>
			<p>
			1.各本支店BIMマネ課所属（兼務含む）及びiPDセンターBIMマネ統括課（兼務含む）所属者は、</br>各自を責任者とする。</br></br>
			2.各本支店所属者の責任者は、所属する店のBIMマネ課所属者とする。</br></br>
			3.本社所属者の責任者は、BIMマネ統括課所属者とする。</br></br>
			4.当該ユーザー登録によりアクセス可能となるCCC及びTeamsチャネルには関係者外秘の情報が含まれていることを認識し、
			 自部門で得る事ができない情報については取り扱わないこと。</br></br>
			5.登録申請者は規約同意前に、管理責任者からCCCの利用に関する詳細説明を受けて理解しておくこと。</br></br>
			</p>
		</div>
		</br>
		<div>
			<input type="checkbox" id="chkAccept"><label>&nbsp;規約に同意しCCCの利用申請を行う</label><!--(管理責任者にメールが自動送信され、承認されることで登録が完了します)-->
			<span class="err_msg" id="err_message" style="margin-top:20px;"></span>
		</div><hr>
		<div style="display:flex;">
			<button class="btn btn-custom align-center btn-approve-color" onClick="ApproveByChiefAdmin(@if(isset($userInfo["id"])) {{$userInfo["id"]}} @endif)">承認</button>
			<button class="btn btn-custom align-center" onClick="Decline(@if(isset($userInfo["id"])) {{$userInfo["id"]}} @endif,@if(isset($userInfo["companyId"])) {{$userInfo["companyId"]}} @endif)">却下</button>	
		</div>
	</div>
	
  
 </body>
</html>