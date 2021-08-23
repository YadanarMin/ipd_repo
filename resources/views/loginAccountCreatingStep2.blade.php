
<!DOCTYPE html>
<html>
 <head>
  <title>CCC Login Account Creating </title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
  
  <link rel="stylesheet" href="/iPD/public/css/loginAccountCreating.css">
  <link rel="stylesheet" href="/iPD/public/css/jquery-ui_18.css" />
  <script type="text/javascript" src="/iPD/public/js/loginAccountCreating.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  
  <style type="text/css">
  textarea{
	resize:none !important;
  }

  </style>
  
  <script></script>
 </head>
 <body>
	<div id="left_div" class="col-md-2">
		<ul class="list-group">
		  <li class="list-group-item">ユーザー情報入力</li>
		  <li class="list-group-item active-color">管理責任者選択</li>
		  <li class="list-group-item">利用規約確認</li>
		</ul>
	</div>
	<div class="col-md-7">
		<div>
			<h4 class="common-title">CCCログインアカウント作成</h4>
		</div>
		<div class="main_div">
			<div class="align-center" style="">
				<h4>管理責任者選択</h4>
			</div>
			<hr>
			<div><label>管理責任者を選択してください。</label></div>
			<div>
				<input type="hidden" id="hidChiefAdminId" 
            			value="@if(session()->has('chiefAdminId'))
			                {{session()->get('chiefAdminId')}}
		            		@endif"/>
				<input type="text" class="form-control input-sm" name="txt" id="txtChiefAdmin"
				value="@if(session()->has('step2')) 
						{{session()->get('step2')}} 
						@endif">
			</div><br>
			<div><label>以下の基準を参考に、管理責任者に事前通知したうえで選択してください。</label></div>
			<div>
				<textarea class="form-control" id="txtA1" rows="11">

				管理責任者設定基準
			
1.各本支店BIMマネ課所属（兼務含む）及びiPDセンターBIMマネ統括課（兼務含む）所属者は、
  各自を責任者とする。

2.各本支店所属者の責任者は、所属する店のBIMマネ課所属者とする。

3.本社所属者の責任者は、BIMマネ統括課所属者とする
					
				</textarea>
				<span class="err_msg" id="err_message" style="margin-top:20px;"></span>
			</div>
            
		
		
	</div>
		<div style="display:flex;">
			<button class="btn btn-custom align-center" onClick="GoTo('step1')">前へ</button>
			<button class="btn btn-custom align-center" onClick="GoTo('step3','to_session')">次へ</button>	
		</div>
	</div>
	
  
 </body>
</html>