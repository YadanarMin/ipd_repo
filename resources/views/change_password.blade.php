
<!DOCTYPE html>
<html>
 <head>
  <title>Change Password </title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
  <script type="text/javascript" src="../public/js/user.js"></script>
  <style type="text/css">
   #changePasswordDiv{
    width:500px;
    margin:0 auto;
    border:1px solid #ccc;
    border-radius:5px;
    margin-top:5vh;
    padding-top:2vh;
    min-height:40vh !important;
    height:auto;
   }
   .form-control{
    width:80%;
   }
   .custom-size{
    width:80%;
   }
   .txt-design{
     text-align: left;
     margin: 8px 0px 8px;
    }
    
    #btnGroup{
     margin-top:2vh;
     margin-bottom:2vh;
    }
    #txtWarning{
     color:red;
    }
  </style>
 </head>
 <body>

 	<div class="popup popupSize">
 		   <div class="popupHeader">
 			<h3 style="text-align:center;color:dimgray;">パスワード更新</h3>
 			
 		</div>
 		      <div align="center">			
         <form name="changePasswordForm" method="post">
          {{ csrf_field() }}
              <div id="changePasswordDiv">
               <span id="txtWarning"></span>
                <div class="custom-size" id="show_hide_password">
                    <div class="txt-design">現在パスワード :</div>
                    <div class="input-group">
                       <input type="text" class="form-control" name="txtPassword" id="txtPassword" value='{{ Session::get("current_password")}}'/>
                       <div class="input-group-addon"><a href=""><i class="fa fa-eye" aria-hidden="true"></i></a></div>
                    </div>
                    
                </div>
                <div class="custom-size" id="show_hide_newpassword"> 
                    <div class="txt-design">新パスワード :</div>
                    <div class="input-group">
                       <input type="password" class="form-control" name="txtNewPassword" id="txtNewPassword" />
                       <div class="input-group-addon"><a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a></div>
                    </div>
                </div>
                <div class="custom-size" id="show_hide_confirmpassword">
                   <div class="txt-design">新パスワード確認 :</div>
                   <div class="input-group">
                      <input type="password" class="form-control" name="txtConfirmNewPassword" id="txtConfirmNewPassword" />
                      <div class="input-group-addon"><a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a></div>
                   </div>
                </div> 
                <div id="btnGroup">
                    <input type="button" class="btn btn-primary" name="btnUpdatePassword" value="更新" onClick="ChangePassword();"/>
                </div>
              </div>
           </form>   
          </div>       		
 	    </div>
  </div> 
 </body>
</html>