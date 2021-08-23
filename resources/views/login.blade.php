
<!DOCTYPE html>
<html>
 <head>
  <title>CCC Login </title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
 
  <style type="text/css">
   .box{
    width:500px;
    margin:0 auto;
    border:1px solid #ccc;
    border-radius:5px;
    margin-top:10vh;
   }
   
   .form-group{
    width:80%;
    margin-left:10%;
   }
   .form-control{
    height:35px;
   }
   
  </style>
  <script>
   $(function(){
    $("#show_hide_password a").on("click",function(event){
        event.preventDefault();
        var id = $(this).parent().siblings().closest('input').attr('id');

        if($(this).find('i').hasClass("fa-eye")){//current is show,need to hide
            $('#'+id).attr('type', 'password');
            $(this).find('i').removeClass("fa-eye");
            $(this).find('i').addClass("fa-eye-slash")
        }else{

            $('#'+id).attr('type', 'text');
            $(this).find('i').removeClass("fa-eye-slash");
            $(this).find('i').addClass("fa-eye")
        }
    })
   });
  </script>
 </head>
 <body>
  <br />
  <div class="container box">
   <h3 align="center">ログイン</h3><br />

   @if ($message = Session::get('error'))
   <div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
   </div>
   @endif

   @if (count($errors) > 0)
    <div class="alert alert-danger">
     <ul>
     @foreach($errors->all() as $error)
      <li>{{ $error }}</li>
     @endforeach
     </ul>
    </div>
   @endif

   <form method="post" action="{{ url('/login/checklogin') }}">
    {{ csrf_field() }}
    <div class="form-group">
     <label>ログインID</label>
     <input type="name" name="username" class="form-control" placeholder="ユーザー名 or メールアドレス"/>
    </div>

    <div class="form-group" id="show_hide_password"> 
          <div class="txt-design">パスワード</div>
          <div class="input-group">
             <input type="password" class="form-control" name="password" id="password" />
             <div class="input-group-addon"><a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a></div>
          </div>
    </div>
    <div class="form-group">
     <input type="submit" name="login" class="btn btn-primary" value="ログイン" />
    </div>
    <hr>
    <a href="{{ url('login/create/step1') }}" onClick="OpenAccountCreationForm()" style="display: block;text-align:center;padding-bottom:20px;">新しいアカウントを作成</a>
   </form>
  </div>
  
 </body>
</html>