
<!DOCTYPE html>
<html>
 <head>
  <title>CCC Login Account Creating </title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
  <script type="text/javascript" src="/iPD/public/js/select2/select2.min.js"></script>
  <script type="text/javascript" src="/iPD/public/js/library/maximize-select2-height.js"></script>
  <link rel="stylesheet" href="/iPD/public/css/select2.min.css">
  <link rel="stylesheet" href="/iPD/public/css/loginAccountCreating.css">
  <link rel="stylesheet" href="/iPD/public/css/jquery-ui_18.css" />
  <script type="text/javascript" src="/iPD/public/js/loginAccountCreating.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

  <style type="text/css">
  .main_div > div{
  	padding:0 0 0 0;
  	margin:7px 30px 7px 30px;
  }
  .select2-container.form-control {
     height: auto !important;
}


  </style>
  <script>
  </script>
 </head>
 <body>
	<div id="left_div" class="col-md-2">
		<ul class="list-group">
		  <li class="list-group-item active-color">ユーザー情報入力</li>
		  <li class="list-group-item">管理責任者選択</li>
		  <li class="list-group-item">利用規約確認</li>
		</ul>
	</div>
	<div class="col-md-7">
		<div>
			<h4 class="common-title">CCCログインアカウント作成</h4>
		</div>
		<div class="main_div">
			<div class="align-center" style="">
				<h4>ユーザー情報入力</h4>
			</div>
			<hr>
			<div>
				<div class="form-group col-md-6" style="padding:0 10px 0 0px;">
	                <label>氏名：姓</label>
	                <input type="text" class="form-control input-sm " id="txtFirstName" 
	                value="@if(session()->has('step1')) 
	                			@if(isset((session()->get('step1')->firstName))) 
			                	{{session()->get('step1')->firstName}}
			                	@endif
			               @endif" required>
	            </div>
	            <div class="form-group col-md-6" style="padding:0 0px 0 10px;">
	                <label>名</label>
	                <input type="text" class="form-control input-sm" id="txtLastName"
	                value="@if(session()->has('step1')) 
	                			@if(isset((session()->get('step1')->lastName))) 
				                {{session()->get('step1')->lastName}} 
				                @endif
			               @endif">
	            </div>	
			</div>
			
			<div>
				<div class="form-group col-md-6" style="padding:0 10px 0 0px;">
	                <label>氏名：姓（カナ）</label>
	                <input type="text" class="form-control input-sm " id="txtFirstNameKana" 
	                value="@if(session()->has('step1')) 
	                			@if(isset((session()->get('step1')->firstNameKana))) 
			                	{{session()->get('step1')->firstNameKana}}
			                	@endif
			               @endif" required>
	            </div>
	            <div class="form-group col-md-6" style="padding:0 0px 0 10px;">
	                <label>名（カナ）</label>
	                <input type="text" class="form-control input-sm" id="txtLastNameKana"
	                value="@if(session()->has('step1')) 
	                			@if(isset((session()->get('step1')->lastNameKana))) 
				                {{session()->get('step1')->lastNameKana}} 
				                @endif
			               @endif">
	            </div>	
			</div>
			
			<div class="form-group">
	                <label>パスワード</label>
	                <div class="input-group">
		                <input type="password" class="form-control input-sm " id="txtPassword" 
		                value="@if(session()->has('step1')) 
		                			@if(isset((session()->get('step1')->password))) 
				                	{{session()->get('step1')->password}}
				                	@endif
				               @endif">
				       <div class="input-group-addon" id="hideAndShowPass" onClick="HideAndShowPassword()">
				       	<a href="javascript:void(0)"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
			       	  </div>
			       </div>
	            </div>
             <div class="form-group">
                <label>メールアドレス</label>
                <input type="text" class="form-control input-sm" id="txtEmail"
                value="@if(session()->has('step1'))
	                		@if(isset((session()->get('step1')->email))) 
			                {{session()->get('step1')->email}}
			                @endif
		               @endif">
            </div>
            
            <input type="hidden" id="hidCompanyTypeId" 
            			value="@if(session()->has('step1'))
	                		@if (isset((session()->get('step1')->companyTypeId))) 
			                {{session()->get('step1')->companyTypeId}}
			                @endif
		               @endif"/>
            <div class="form-group" style="width:600px;">
                <label>企業種別</label>&nbsp;&nbsp;&nbsp;
                <div style="display:flex;">
                	<select class="form-control input-sm" id="txtCompanyType" style="width:50%;">
                		<option>選択してください。</option>
	                </select>
                </div>
            </div> 
            
            <input type="hidden" id="hidCompanyId" 
            			value="@if(session()->has('step1'))
	                		@if (isset((session()->get('step1')->companyId))) 
			                {{session()->get('step1')->companyId}}
			                @endif
		               @endif"/>
            <div class="form-group">
                <label>企業名</label>
                <input type="text" class="form-control input-sm" id="txtCompanyName"
                value="@if(session()->has('step1'))
	                		@if (isset((session()->get('step1')->company))) 
			                {{session()->get('step1')->company}}
			                @endif
		               @endif">
            </div>
            
            
            <div>
            	<input type="hidden" id="hidBranchId" 
            			value="@if(session()->has('step1'))
	                		@if (isset((session()->get('step1')->branchId))) 
			                {{session()->get('step1')->branchId}}
			                @endif
		               @endif"/>
	           <input type="hidden" id="hidDepartmentId" 
				value="@if(session()->has('step1'))
	        		@if (isset((session()->get('step1')->departmentId))) 
	                {{session()->get('step1')->departmentId}}
	                @endif
	           @endif"/>
	           <div class="form-group col-md-6" style="padding:0 10px 0 0px;">
	                <label>支店名</label>
	                <div style="display:flex;">
	                	<select class="form-control input-sm " id="txtBranch" style="">
	                		<option>選択してください。</option>
		                </select>
                    </div>
	                <!--<input type="text" class="form-control input-sm" id="txtBranch"-->
	                <!--value="@if(session()->has('step1'))-->
		               <!-- 		@if(isset((session()->get('step1')->branch))) -->
				            	<!--	{{session()->get('step1')->branch}}-->
				             <!--   @endif-->
			              <!-- @endif">-->
	            </div>
	           <div class="form-group col-md-6" style="padding:0 0px 0 10px;">
	                <label>組織名</label>
	                <input type="text" class="form-control input-sm" id="txtDepartment"
	                value="@if(session()->has('step1'))
				                @if(isset((session()->get('step1')->department))) 
				                {{session()->get('step1')->department}}
				                @endif
	                	 @endif">
	            </div>
        	</div>
        <div class="form-group">
            <label>電話番号</label>
            <input type="text" class="form-control input-sm" id="txtPhoneNumber"
            value="@if(session()->has('step1'))
		                @if(isset((session()->get('step1')->phone))) 
		                {{session()->get('step1')->phone}}
		                @endif
            	  @endif">
        </div>
        <div class="form-group">
            <label>勤務地</label>
            <textarea id="txtAWorkLocation" rows="5" class="form-control input-sm">
			@if(session()->has('step1')) 
				@if(isset((session()->get('step1')->workingPlace))) 
				{{session()->get('step1')->workingPlace}}
				@endif
				
			@endif
            </textarea></br>
            <span class="err_msg" id="err_message" style="margin-bottom:20px;"></span>
        </div>
	</div>
		<div>
			<button class="btn btn-custom align-center" onClick="GoTo('step2','to_session')">次へ</button>	
		</div>
	</div>
	
  
 </body>
</html>