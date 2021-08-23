<style>

.header-dropdown {
  position: relative;
  display:inline-block;
  margin-top:10%;
}

#dropdown-content {
  display: none;
  position: absolute;
  background-color: #f1f1f1;
  min-width: 200px;
  max-height:300px;
  overflow-y:auto;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 5000;
  padding:10px;
}

.content-item{
    display:block;
    cursor:pointer;
}
#btn-content{
    display:flex;
    margin:10px;
}
.btn{
 /*margin-left:5px;*/
}
.header-dropdown:hover #dropdown-content{display: block;}
.header-dropdown-content:hover{display: block;}
.content-item:hover{background-color: #ddd;}


header{
    background-color: #232f3e;
}
.outer{
    width: 100%;
    height: 90px;
    align-items: center;
    background-color: #232f3e;
    }
.inner{
    position: relative;
    left:0;
    width: 20%;
    height: 90px;
    margin: 0 auto;
    background:#232f3e;
    float: left;
    padding-top: 0px;
    display:flex;

}
.head1{
    background-color: #232f3e;
    width:58%;
    float: left;
}
.LOGIN{
    background-color: #232f3e;
    height: 40px;
    float: right;
    width: 23%;
    height:auto;
    margin-right:0px;
    margin-top: -30px;
    position:relative;
}
.head1 h2{
    background-color: #232f3e;
    max-width: 1250px ;
    text-align: center;
    font-size: 25px;
    color: floralwhite;
    position:relative;
    top: 35px;
    height:55px;
    margin: 0 auto 0 0;
    line-height: 20px;
}
#loginUser{
  color:#fff;
  margin-right:10px;
}

#fileDownload{
    color:#D3D3D3;
    font-size:1em;
    z-index:0;
    margin: 38px 0px 0px 20px;
}
.page-header-btn{
    margin-left:5px;
    /*font-size:0.9em;*/
}
.LOGIN{
 display:flex;
 flex-direction:row !important;

}
  
@media screen and (min-width:1500px) and (max-width:1600px){ 
    .LOGIN{
        width:26% !important;
        margin-top: -30px;
        position:relative;
    }
    .page-header-btn{
        font-size:0.9em;
    }
}
@media screen and (min-width:1350px) and (max-width:1499px){
     .LOGIN{
      width:26.5%;
    }
    .LOGIN{
        margin-top: -30px;
        position:relative;
  
     }

    .col-sm{
        margin-bottom:5px;
    }
    .inner{
        width:25%;
    }
    .page-header-btn{
        font-size:0.9em;
    }
    
}

@media screen and (min-width:200px) and (max-width:1349px){
 .LOGIN{
      width:10%;
    }
    .LOGIN{
        margin-top: 0px;
        flex-direction:column !important;
    }
    .page-header-btn{
        font-size:0.45em;
        padding:3px 7px !important;
    }
    .col-sm{
        margin-bottom:1px;
    }
}

@media screen and (min-width:200px) and (max-width:920px){
    .LOGIN{
      width:15%;
    }
}

</style>
<script>
    $(function() {
        var w = $(window).width();
        var x = 500;
        if (w <= x) {
            document.getElementById("head1Title").innerText = "CCC";
        }else{
            document.getElementById("head1Title").innerText = "Central Command Center";
        }
    });
    
    $(window).resize(function(){
        var w = $(window).width();
        var x = 500;
        if (w <= x) {
            document.getElementById("head1Title").innerText = "CCC";
        } else {
            document.getElementById("head1Title").innerText = "Central Command Center";
        }
    });
</script>

<header>
       
    <div class = "outer">      
        <div class="inner">
            <h1>
                <a href="{{ url('login/successlogin') }}"><img src="/iPD/public/image/JPG/index.png" style="width:40px;margin-left:20px;"  alt="大林ロゴ" /></a>
            </h1>
            <!--<a href="{{url('admin/fileDownload')}}" id="fileDownload" data-toggle="tooltip" data-placement="bottom" title=""> 資料ダウンロード</a>-->

             
             <div class="header-dropdown">
                <a href="javascript:void(0)" id="fileDownload"> 資料ダウンロード</a>
                <div id="dropdown-content">
                    @if(Session::has('DownloadFiles'))
                        @foreach(Session::get('DownloadFiles') as $file)
                        <div class="content-item">
                           <input type="checkbox"/><label>{{$file}}</label> 
                        </div>
                        @endforeach
                        <div id="btn-content">
                           <input type="button" class="btn btn-info btn-xs" id="btnDownload" value="ダウンロード" onclick="DownloadFiles()">&nbsp;&nbsp;
                           @if(Session::has("authority") && Session::get("authority") == 1)
                           <input type="button" class="btn btn-info btn-xs" id="btnUpload" value="アップロード" onclick="UploadFiles()"> 
                           @endif
                        </div>
                    @endif
                </div>
            </div>
             
        </div>
        <div class="head1">
            <p3>
            <h2 id="head1Title"></h2>
            </p3>
        </div>
        <div class="LOGIN row flex-column">
                <span id="loginUser">
                @if(Session::has('userName'))
                    {{Session::get('userName')}}
                @else
                    {{''}}
                @endif
                </span>
                <div class="col-sm">
                    <button type="button" class="btn btn1 page-header-btn" onclick="Logout()">
                        @if(Session::has('userName'))
                            LOGOUT
                        @else
                            LOGIN
                        @endif
                    </button>
                </div>
                <div class="col-sm">
                    <button type="button" class="btn page-header-btn" id="btnForgeLogin" onclick="GetThreeLeggedAuth()">
                        @if(Session::has('authCode'))
                          FORGE LOGOUT
                        @else
                          FORGE LOGIN
                        @endif
                    </button>
                </div>
                <div class="col-sm">
                    <button type="button" class="btn page-header-btn" id="btnBoxLogin" onclick="GetThreeLeggedBoxAuth()">
                        @if(Session::has('access_token'))
                          BOX LOGOUT
                        @else
                          BOX LOGIN
                        @endif
                    </button>
                </div>
        </div>
    </div>
</header>
