@extends('layouts.baselayout')
@section('title', 'CCC - Integrated Personal Manegement')

@section('head')
<script src="../public/js/shim.js"></script>
<script src="../public/js/xlsx.full.min.js"></script>
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/personal.js"></script>

<style>
.main-content{
    min-height:0;
}
#personalField{
    margin-left: 20px;
    <!--border : 1px solid rgb(224, 224, 209);-->
    padding: 30px;
    display:flex;
}
.disabledBtn{
    background-color: #cccccc;
}
.selected{
    color : dodgerblue;
    text-decoration: underline;
}
.chainLink{
    margin-top: 30px;
    position: absolute;
    margin-left: -20px;
}
#sideBar{
    width : 400px;
    padding : 20px;
    border : 1px solid rgb(224, 224, 209);
    border-radius: 10px;
    max-height:543px;
}
.personalViewer{
    width : 100%;
    height:552px;
    display : inline-flex;
}
.customBtn{
    margin : 1% -2% 0% -1%;
    display : center;
}
thead, tbody {
  display: block;
}
tbody {
  overflow-x: hidden;
  overflow-y: scroll;
  height: 545px;
}
.tab_switch_on{
    background-color:silver;
}
.personalOverview{
    display:flex;
    height:234px;
    margin:0 10px 0 10px;
}
.personalDetail{
    display:flex;
    height:246px;
    margin:10px 10px 10px 25px;
}
ul {
  list-style: none;
  padding-left: 10px;
}
.personalData{
    border: 2px solid rgb(224, 224, 209);
    border-radius: 10px;
    margin-bottom:10px;
    margin-right: 10px;
    width:521px;
}
hr{
    margin-top:10px;
    margin-bottom:10px;
    width:95%;
}
.pimage{
    width:30%;
    height:100%;
    <!--background-color:gray;-->
}
.pfiguimage{
    text-align:center;
    margin:5px 131px 5px 0;
}
.pfigcimage{
    margin-top:21px;
}
.pworkstatus{
    width:70%;
    height:100%;
}
.pbuilding{
    width:480px;
    height:100%;
}
.pstudyabload{
    width:360px;
    height:100%;
    margin-left: 15px;
}
.pStudyAbloadList{
    overflow:auto;
}
.pbuilding tbody{
    overflow-x: hidden;
    overflow-y: scroll;
    height: 200px;
}
.pstudyabload tbody{
    overflow-x: hidden;
    overflow-y: scroll;
    height: 187px;
}
#personalNameList th{
    border-right-width: none;
}
#personalNameList td{
    border-right-width: none;
}
#personalNameList tbody{
    min-height: 298px;
    max-height: 298px;
}
.cardScroll{
    overflow-x: scroll;
    overflow-y: scroll;
}
.detailDisplayNone{
    display:none;
}
#detailTable thead tr {
  background-color: #b0c4de;
}
.detailTableField{
    overflow: auto;
    white-space: nowrap;
    max-height: 650px;
    width: 1200px;
    margin: 10px auto 0px auto;
}
</style>

<script>
</script>

@endsection

@section('content')
<div class="main-content">

	<input type="hidden" id="hidLoginID" name="hidLoginID" value="{{Session::get('login_user_id')}}"/>
	<div style="display:flex;justify-content: space-between;align-items: flex-end;">
	    <h3>人員管理</h3>
	    <div>
    	    <input type="button" class="btn" name="showPersonalDetail" id="showPersonalDetail" style="margin:10px 0 10px 150px;" value="選択表示" onClick="ShowPersonalDetail();"/>
    	    <input type="button" class="btn" name="switchDetailTypeTable" id="switchDetailTypeTable" style="margin:10px 0 10px 150px;" value="TABLE" onClick="SwitchDetailTypeTable();"/>
    	    <input type="button" class="btn" name="switchDetailTypeCard" id="switchDetailTypeCard" style="margin:10px 0 10px 150px;" value="CARD" onClick="SwitchDetailTypeCard();"/>
    	    <input type="button" class="btn" name="btnResetPersonal" id="btnResetPersonal" style="margin:10px 0 10px 9px;" value="リセット" onClick="ResetPersonalDetail();"/>
    	</div>
	</div>
	
    <div class="personalViewer">
        <div id="sideBar">
            <div class="panel" style="margin-bottom:0;">
                <div id="userCategorySelect" class="btn-group customBtn" role="group" aria-label="...">
                    <button type="button" class="btn btn-default" id="allperson" style="height:40px;outline: none;">全て</button>
                    <button type="button" class="btn btn-default" id="employee" style="height:40px;outline: none;">社員</button>
                    <button type="button" class="btn btn-default" id="excStudent" style="height:40px;outline: none;">留学生</button>
                    <button type="button" class="btn btn-default" id="subcon" style="height:40px;outline: none;">協力業者</button>
                    <button type="button" class="btn btn-default" id="partner" style="height:40px;outline: none;">ﾊﾟｰﾄﾅｰ業者</button>
                </div>
                <hr>
                <table class="table table-bordered table-hover" id="personalNameList" rules="rows">
                    <thead>
                        <tr class="info">
                            <th style="width:300px;">氏名</th>
                            <th style="width:40px;"></select></th>
                        </tr>
                    </thead>        
                    <tbody id="userList">
                    </tbody>
                </table>
            </div>
        </div>
        
        <div id="personalList">
            <input name="loginUserNameForContactView" value="{{Session::get('userName')}}" id="loginUserNameForContactView" type="hidden">
            <ul id="personalField">
            </ul>
            <div class="detailTableField">
                <table id="detailTable" class="table table-bordered table-hover" rules="rows" style="margin:0 0 0 10px;">
                </table>
            </div>
        </div>
    </div>

</div>
@endsection