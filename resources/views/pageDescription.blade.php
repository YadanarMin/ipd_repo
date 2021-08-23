@extends('layouts.baselayout')
@section('title', 'CCC - Page Description')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="../public/js/pageDescription.js"></script>
<script type="text/javascript" src="../public/js/jquery.flexslider.js"></script>
<link rel="stylesheet" href="../public/css/pageDescription.css">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>

<style>
#saveBtn button{
  position: absolute;
  z-index: 1;
  right: 0;
}

.theBox{

   height: 420px; 
   width: 100%; 
   max-height:850px;
   margin-top:5%;

}
.close{
    color:red;
    position:relative;
    z-index:10px !important;
}
 
.carousel-control {
    background-color:rgba(-30, 0, -20, -30);
    width:20px;
}
.item{
    marign-top:10px;
    
    background:white;
}
.carousel-inner{
    min-height:500px !important;
}

@media screen and (max-width: 1300px) {
    .description,.image{
        min-width:700px !important;
    }
    .body-part{
        display:flex;
    }
    .row{
        width:100%;
        align:center;
    }
}

.upload-part{
    min-width:600px !important;
}

.title-part{
    min-width:600px !important;
    margin-left:10%;
    
}
.page-title{
    display:inline-block;
    margin-top:8%;
}
</style>
<script>
   
</script>

@endsection

@section('content')
<main>
    <div class="row header-part">
        <div class="title-part">
        	@if(Session::has("pageName"))
            	<h4 class="page-title">サービス名：{{Session::get('pageName')}}</h4>
            @endif
        </div>
        @if(Session::has('authority') && Session::get('authority') == 1)
        <div class="upload-part">
           <form action="{{ url('admin/UploadImages') }}" method="POST" enctype="multipart/form-data">
                {{csrf_field()}}
               <div class="row">
                 <div class="col-sm-3"id="dragandrophandler">ここにドロップしてください。</div>
                 <div class="col-xs-4 saveBtn">
                     <input type="checkbox" id="chkOverwrite" name="overwrite"/>上書き保存 &nbsp;&nbsp;
                     <input class="btn btn-primary" type="button" value="Save"id="btnSaveImage" onClick="SaveUploadFiles()"/> 
                 </div>
                  
                  
               </div>
               
                
            </form> 
        </div>
        @endif
    </div>
    <div class="row body-part">
        <div class="col-xs-5 description">
        	@if(sizeof($data) > 0)
	            <table id="tblDescription">
					@foreach ($data as $row)
	                <tr>
	                    <td>
	                        <textarea id="description1"  cols="200">{{ $row["description1"]}}</textarea>
	                    </td>
	                </tr>
	                <tr>
	                    <td>
	                        <textarea id="description2"  cols="200">{{$row["description2"]}}</textarea>
	                    </td>
	                </tr>
	            </table>
	            @break
	            @endforeach
            @else
            	<table id="tblDescription">
	                <tr>
	                    <td>
	                        <textarea id="description1" cols="200"></textarea>
	                    </td>
	                </tr>
	                <tr>
	                    <td>
	                        <textarea id="description2" cols="200"></textarea>
	                    </td>
	                </tr>
	            </table>
            @endif
        </div>
        <div class="col-xs-5 image">
            <div id="carousel_pageDesp" class="carousel slide" data-ride="carousel" data-interval="false">
               <ol class="carousel-indicators" id="test">
               </ol>
              <div class="carousel-inner">
                  @foreach ($data as $key=>$row)
                  
                    	@if($row["image_path"] != "")
                    	   
                            @if(pathinfo($row["image_path"], PATHINFO_EXTENSION) == "pdf")
                                @if($key==0)
                                    <div class="item active" >
                                @else
                                 <div class="item " >
                                @endif
                                 <button onclick="DeleteImage('{{$row["image_path"]}}')" class="close AClass">
                                   <span style="position:absolute;z-index:1000px;">&times;</span>
                                </button>
                                   <iframe  src={{$row["image_path"]}} frameBorder="0"  height="450px" width="100%" ></iframe>
                                 </div>
                            @else
                                @if($key==0)
                                    <div class="item active" >
                                @else
                                 <div class="item " >
                                @endif
                                 <button onclick="DeleteImage('{{$row["image_path"]}}')" class="close AClass">
                                   <span>&times;</span>
                                </button>
                                  <img style='height: auto !important; width: 100%; object-fit: fill; 'src={{$row["image_path"]}} alt={{$row["image_path"]}}>
                                </div>
                            
                            @endif
                        @endif
                @endforeach
                
              </div>
              <a id="report-body-prev" class="left carousel-control" href="#carousel_pageDesp" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a id="report-body-next" class="right carousel-control" href="#carousel_pageDesp" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right"></span>
                <span class="sr-only">Next</span>
            </a>
            </div>
            
            <!--<div class="flex-container">
                <div class="flexslider">
                    
                    <ul class="slides">
                        
                    	@foreach ($data as $row)
                    	@if($row["image_path"] != "")
                        <li>
                            <button onclick="DeleteImage('{{$row["image_path"]}}')" class="close AClass">
                               <span>&times;</span>
                            </button>
                            @if(pathinfo($row["image_path"], PATHINFO_EXTENSION) == "pdf")
                            <div class="theBox" >
                               <iframe  src={{$row["image_path"]}} frameBorder="0"  height="450px" width="100%" ></iframe>
                             </div>
                            @else
                            <div class="theBox">
                            
                                <img style='height: 100%; width: 100%; object-fit: fill; 'src={{$row["image_path"]}} alt={{$row["image_path"]}}>
                            </div>
                            
                            @endif
                        </li>
                        @endif
                        @endforeach
                    </ul>
                    
                </div>
        	</div>-->
    	</div>
            </div>
        </div>
    </div>
  
</main>
@endsection