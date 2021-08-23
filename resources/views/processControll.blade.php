@extends('layouts.baselayout')
@section('title', 'CCC - process controll')

@section('head')
<script src="../public/js/xlsx.full.min.js"></script>
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<style>
#searchDiv{
    margin: 5vh auto 0 auto;
    width: 72vh;
}
#tbProject{
    width: 700px;
}
@media screen and (max-width: 720px) {
    #searchDiv{
        margin: 5vh auto 0 auto;
        width: 40vh;
    }
    #tbProject{
        width: 386px;
    }
}
.clear-decoration-btn{
    border: none;  /* 枠線を消す */
    outline: none; /* クリックしたときに表示される枠線を消す */
    background: transparent; /* 背景の灰色を消す */
}
</style>
<script>
    $(document).ready(function(){
    
        var login_user_id = $("#hidLoginID").val();
        var img_src = "../public/image/JPG/クレーンアイコン.jpeg";
        var url = "gantt/processControll";
        var content_name = "工程管理";
        recordAccessHistory(login_user_id,img_src,url,content_name);
    
    	$('#txtSearch').keyup(function(){
    		var textboxValue = $('#txtSearch').val();
    		$("#tbProject tr").each(function(index) {
    		    if (index !== 0) {
    		        $row = $(this);
    		        var projectName = $row.find("td:nth-child(2)").text();
    		        if(!projectName.includes(textboxValue)){
    		            $row.hide();
    		        }else{
    		            $row.show();
    		        }
    		    }
    		});
    	});
    });
    
    function ShowGantt(projectId){
        
        var projectName = "";
        
    	$("#tbProject tr").each(function(){
    		var trID = $(this).attr('id');
    		if(trID == 'undefined') return true;
    
    		if(trID == projectId){
    			projectName = $(this).find("td:eq(1)").text();
    		}
    	});
    	
        var projectType = "";
        if(projectName.includes("クレメントイン今治")){
            projectType = "clementInImabari";
        }else if(projectName.includes("ミツトヨ")){
            projectType = "mitutoyoSiwa";
        }else if(projectName.includes("博多駅")){
            projectType = "hakata4tyoume";
        }else if(projectName.includes("宮原NK")){
            projectType = "miyaharaNK";
        }else if(projectName.includes("平野町")){
            projectType = "hiranomati";
        }else if(projectName.includes("新淀屋橋")){
            projectType = "sinyodoyabasi";
        }else if(projectName.includes("資生堂")){
            projectType = "siseidou";
        }else if(projectName.includes("関西国際空港")){
            projectType = "kankuuT1phase1no1";
        }else{
            alert("工程管理データがありません。");
    	    return true;
    	}

        $.ajax({
              url: "../gantt/setProjectIdToSession",
              type: 'post',
              data:{_token: CSRF_TOKEN,projectId:projectId,projectName:projectName,projectCode:projectType},
              success :function(data) {
                if(data.includes("success")){
                  window.open('/iPD/gantt/index');
                }
              },
              error : function(err){
                console.log(err);
              }
        });    	
    }
</script>
@endsection

@section('content')
@include('layouts.loading')

<input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>
<div id="searchDiv">
    <h4 class="page-title">工程管理</h4>             
    <div class="form-group has-search">
        <span class="glyphicon glyphicon-search form-control-feedback"></span>
        <input type="text" class="form-control" id="txtSearch" placeholder="プロジェクト検索">
    </div>
</div>

<div class="projectDiv">

    <table id="tbProject" align="center">
        <thead>
            <tr>
				<th style="width:10%;">No.</th>
				<th style="width:45%;">プロジェクト名</th>
				<th style="width:10%;">工程</th>
            </tr>
        </thead>
        <tbody>
            @if (count($projects) > 0)
                @php($count=0)
                @foreach($projects as $project)
					@php($count++)
					<tr id="{{$project['id']}}">
					    <td>{{$count}}</td>
						<td>{{$project["name"]}}</td>
						<td>
                            <button class="clear-decoration-btn" onclick="ShowGantt({{$project['id']}})">
                                @if ($project["isExist"] === 0)
                                    <img id="img{{$project['id']}}" src='../public/image/ganttchart.png' style="opacity: 0.4;" alt='' height='20' width='20' />
                                @else
    						        <img id="img{{$project['id']}}" src='../public/image/ganttchart.png' alt='' height='20' width='20' />
                                @endif
                            </button>
                        </td>
					</tr>
				@endforeach
            @else
            <tr>
                <td height="20"></td>
                <td></td>
                <td></td>
            </tr>
            @endif
        </tbody>
    </table>
    
</div>

@endsection