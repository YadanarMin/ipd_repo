
<!DOCTYPE html>
<html>
 <head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>CCC Login Account Creating </title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
  <script type="text/javascript" src="/iPD/public/js/loginAccountCreating.js"></script>
  <link rel="stylesheet" href="/iPD/public/css/loginAccountCreating.css">
  <style type="text/css">
 
  </style>
  <script></script>
 </head>
 <body>
	<div id="left_div" class="col-md-2">
		<ul class="list-group">
		  <li class="list-group-item">ユーザー情報入力</li>
		  <li class="list-group-item">管理責任者選択</li>
		  <li class="list-group-item active-color">利用規約確認</li>
		</ul>
	</div>
	<div class="col-md-7">
		<div>
			<h4 class="common-title">CCCログインアカウント作成</h4>
		</div>
		<div class="main_div">
			
			<div class="align-center" style="">
				<h4>利用規約確認</h4>
			</div>
			<hr>
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
			</div>
			
			
		
	</div>
		<div style="display:flex;">
			<button class="btn btn-custom align-center" onClick="GoTo('step2')">前へ</button>
			<button class="btn btn-custom align-center" onClick="CreateUserAccount()">OK</button>	
		</div>
	</div>
	
  
 </body>
</html>