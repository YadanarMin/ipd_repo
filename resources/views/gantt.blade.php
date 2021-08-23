<html>
<head>
<title>CCC - Process control[{{$processPjName}}]</title>
<!--<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE"/>-->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

<link rel=stylesheet href="../public/js/gantt/platform.css" type="text/css">
<link rel=stylesheet href="../public/js/gantt/libs/jquery/dateField/jquery.dateField.css" type="text/css">

<link rel=stylesheet href="../public/js/gantt/gantt.css" type="text/css">
<link rel=stylesheet href="../public/js/gantt/ganttPrint.css" type="text/css" media="print">
<link rel=stylesheet href="../public/js/gantt/libs/jquery/valueSlider/mb.slider.css" type="text/css" media="print">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>-->
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<!--<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>-->
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript" src="../public/js/jquery.csv.min.js"></script>

<script src="../public/js/gantt/libs/jquery/jquery.livequery.1.1.1.min.js"></script>
<script src="../public/js/gantt/libs/jquery/jquery.timers.js"></script>

<script src="../public/js/gantt/libs/utilities.js"></script>
<script src="../public/js/gantt/libs/forms.js"></script>
<script src="../public/js/gantt/libs/date.js"></script>
<script src="../public/js/gantt/libs/dialogs.js"></script>
<script src="../public/js/gantt/libs/layout.js"></script>
<script src="../public/js/gantt/libs/i18nJs.js"></script>
<script src="../public/js/gantt/libs/jquery/dateField/jquery.dateField.js"></script>
<script src="../public/js/gantt/libs/jquery/JST/jquery.JST.js"></script>
<script src="../public/js/gantt/libs/jquery/valueSlider/jquery.mb.slider.js"></script>

<script type="text/javascript" src="../public/js/gantt/libs/jquery/svg/jquery.svg.min.js"></script>
<script type="text/javascript" src="../public/js/gantt/libs/jquery/svg/jquery.svgdom.1.8.js"></script>

<script src="../public/js/gantt/ganttUtilities.js"></script>
<script src="../public/js/gantt/ganttTask.js"></script>
<script src="../public/js/gantt/ganttDrawerSVG.js"></script>
<script src="../public/js/gantt/ganttZoom.js"></script>
<script src="../public/js/gantt/ganttGridEditor.js"></script>
<script src="../public/js/gantt/ganttMaster.js"></script>  

<!--<script src="libs/profiling.js"></script>-->
<!--<script type="text/javascript" src="ganttTestSuite.js"></script>-->

<link rel="stylesheet" href="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/style.min.css" type="text/css">
<script src="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/viewer3D.min.js"></script>
<script type="text/javascript" src="../public/js/library/toolbarExtension.js"></script>
<script type="text/javascript" src="../public/js/common.js"></script>
<script type="text/javascript" src="../public/js/gantt.js"></script>
<script type="text/javascript" src="../public/js/modelViewer.js"></script>
<script type="text/javascript" src="../public/js/select2/select2.min.js"></script>
<script type="text/javascript" src="../public/js/select2/i18n/ja.js"></script>
<link rel="stylesheet" href="../public/css/select2.min.css">
<link rel="stylesheet" href="../public/css/gantt.css">

</head>

<!--Loading-->
<div class="loading hide">
    <div class="lds-spinner">
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      </div>
</div>
<body style="background-color: #fff;">


<!--<div style="position:absolute;right:5px;top:5px;width:378px;padding:5px;background-color: #FFF5E6; border:1px solid #F9A22F; font-size:12px">-->
<div style="position:absolute;right:5px;top:5px;width:135px;padding:5px; font-size:12px">
    <a href="https://gantt.twproject.com/"><img src="../public/js/gantt/res/twGanttLogo.png" alt="Twproject" align="absmiddle" style="max-width: 136px; padding-right: 15px"></a>
    <!--This Gantt editor is free thanks to <a href="http://twproject.com" target="_blank">Twproject</a> where it can be used on a complete and flexible project management solution.<br> Get your projects done! Give <a href="http://twproject.com" target="_blank">Twproject a try now</a>.-->
</div>
<div style="margin:0 5px 5px 5px;">
    <div style="display:flex;">
        <div id="workSpace" style="width:60%;padding:0px; overflow-y:auto; overflow-x:hidden;border:1px solid #e5e5e5;position:relative;margin:0 5px"></div>
        <div id="modelViewer" style="width:40%;height:99vh;position:relative;margin:5px 0 5px 0;"><span id="spanText"></span></div>
    </div>
</div>
<style>
.select2-container{
    display:none;
}
</style>

<form id="gimmeBack" style="display:none;" action="../gimmeBack.jsp" method="post" target="_blank"><input type="hidden" name="prj" id="gimBaPrj"></form>

<script type="text/javascript">
</script>





<div id="gantEditorTemplates" style="display:none;">
<div class="__template__" type="GANTBUTTONS">
    <!--
    <input type="hidden" id="hidProjectCode" name="hidProjectCode" value="{{$processPjCode}}"/>
    <input type="hidden" id="hidProjectName" name="hidProjectName" value="{{$processPjName}}"/>
    <h2 class="htextidkosaka" align="left" style="margin:10px 0 0 0;">&nbsp;{{$processPjName}}</h2>
    <select id="version" style="display:none;visibility:hidden;" class="select-1column-dtl" multiple="multiple"></select>
    <div class="ganttButtonBar noprint">
        <div class="buttons">
            <a href="{{ url('login/successlogin') }}"><img src="../public/image/home.png" alt="Twproject" align="absmiddle" style="width:30px;"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            
            <button onclick="$('#workSpace').trigger('undo.gantt');return false;" class="button textual icon requireCanWrite" title="undo"><span class="teamworkIcon">&#39;</span></button>
            <button onclick="$('#workSpace').trigger('redo.gantt');return false;" class="button textual icon requireCanWrite" title="redo"><span class="teamworkIcon">&middot;</span></button>
            <span class="ganttButtonSeparator requireCanWrite requireCanAdd"></span>
            <button onclick="$('#workSpace').trigger('addAboveCurrentTask.gantt');return false;" class="button textual icon requireCanWrite requireCanAdd" title="insert above"><span class="teamworkIcon">l</span></button>
            <button onclick="$('#workSpace').trigger('addBelowCurrentTask.gantt');return false;" class="button textual icon requireCanWrite requireCanAdd" title="insert below"><span class="teamworkIcon">X</span></button>
            <span class="ganttButtonSeparator requireCanWrite requireCanInOutdent"></span>
            <button onclick="$('#workSpace').trigger('outdentCurrentTask.gantt');return false;" class="button textual icon requireCanWrite requireCanInOutdent" title="un-indent task"><span class="teamworkIcon">.</span></button>
            <button onclick="$('#workSpace').trigger('indentCurrentTask.gantt');return false;" class="button textual icon requireCanWrite requireCanInOutdent" title="indent task"><span class="teamworkIcon">:</span></button>
            <span class="ganttButtonSeparator requireCanWrite requireCanMoveUpDown"></span>
            <button onclick="$('#workSpace').trigger('moveUpCurrentTask.gantt');return false;" class="button textual icon requireCanWrite requireCanMoveUpDown" title="move up"><span class="teamworkIcon">k</span></button>
            <button onclick="$('#workSpace').trigger('moveDownCurrentTask.gantt');return false;" class="button textual icon requireCanWrite requireCanMoveUpDown" title="move down"><span class="teamworkIcon">j</span></button>
            <span class="ganttButtonSeparator requireCanWrite requireCanDelete"></span>
            <button onclick="$('#workSpace').trigger('deleteFocused.gantt');return false;" class="button textual icon delete requireCanWrite" title="Elimina"><span class="teamworkIcon">&cent;</span></button>
            <span class="ganttButtonSeparator"></span>
            <button onclick="$('#workSpace').trigger('expandAll.gantt');return false;" class="button textual icon " title="EXPAND_ALL"><span class="teamworkIcon">6</span></button>
            <button onclick="$('#workSpace').trigger('collapseAll.gantt'); return false;" class="button textual icon " title="COLLAPSE_ALL"><span class="teamworkIcon">5</span></button>
        
            <span class="ganttButtonSeparator"></span>
                <button onclick="$('#workSpace').trigger('zoomMinus.gantt'); return false;" class="button textual icon " title="zoom out"><span class="teamworkIcon">)</span></button>
                <button onclick="$('#workSpace').trigger('zoomPlus.gantt');return false;" class="button textual icon " title="zoom in"><span class="teamworkIcon">(</span></button>
            <span class="ganttButtonSeparator"></span>
                <button onclick="$('#workSpace').trigger('print.gantt');return false;" class="button textual icon " title="Print"><span class="teamworkIcon">p</span></button>
            <span class="ganttButtonSeparator"></span>
                <button onclick="ge.gantt.showCriticalPath=!ge.gantt.showCriticalPath; ge.redraw();return false;" class="button textual icon requireCanSeeCriticalPath" title="CRITICAL_PATH"><span class="teamworkIcon">&pound;</span></button>
            <span class="ganttButtonSeparator requireCanSeeCriticalPath"></span>
                <button onclick="ge.splitter.resize(.1);return false;" class="button textual icon" ><span class="teamworkIcon">F</span></button>
                <button onclick="ge.splitter.resize(50);return false;" class="button textual icon" ><span class="teamworkIcon">O</span></button>
                <button onclick="ge.splitter.resize(100);return false;" class="button textual icon"><span class="teamworkIcon">R</span></button>
                <span class="ganttButtonSeparator"></span>
                <button onclick="$('#workSpace').trigger('fullScreen.gantt');return false;" class="button textual icon" title="FULLSCREEN" id="fullscrbtn"><span class="teamworkIcon">@</span></button>
                <button onclick="ge.element.toggleClass('colorByStatus' );return false;" class="button textual icon"><span class="teamworkIcon">&sect;</span></button>
            
            <button onclick="editResources();" class="button textual requireWrite" title="edit resources"><span class="teamworkIcon">M</span></button>
            &nbsp; &nbsp; &nbsp; &nbsp;
        </div>
        
        <div>
            <input type="file" name="load-file" id="load-file">
            <label for="load-file">Load</label>
            <button onclick="exportCSVGanttOnServer();" class="button first big requireWrite" title="Export">Export(CSV)</button>
            <button onclick="exportJSONGanttOnServer();" class="button first big requireWrite" title="Export">Export(JSON)</button>
            <button onclick="saveGanttOnServer();" class="button first big requireWrite" title="Save">Save</button>
            <button onclick='newProject();' class='button requireWrite newproject'><em>clear project</em></button>
            <button class="button login" title="login/enroll" onclick="loginEnroll($(this));" style="display:none;">login/enroll</button>
            <button class="button opt collab" title="Start with Twproject" onclick="collaborate($(this));" style="display:none;"><em>collaborate</em></button>
        </div>
    </div>
    -->
</div>

<div class="__template__" type="TASKSEDITHEAD"><!--
    <table class="gdfTable" cellspacing="0" cellpadding="0">
        <thead>
        <tr style="height:40px">
            <th class="gdfColHeader" style="width:35px; border-right: none"></th>
            <th class="gdfColHeader" style="width:25px;"></th>
            <th class="gdfColHeader gdfResizable" style="width:100px;">code/short name</th>
            <th class="gdfColHeader gdfResizable" style="width:300px;">name</th>
            <th class="gdfColHeader"  align="center" style="width:17px;" title="Start date is a milestone."><span class="teamworkIcon" style="font-size: 8px;">^</span></th>
            <th class="gdfColHeader gdfResizable" style="width:80px;">start</th>
            <th class="gdfColHeader"  align="center" style="width:17px;" title="End date is a milestone."><span class="teamworkIcon" style="font-size: 8px;">^</span></th>
            <th class="gdfColHeader gdfResizable" style="width:80px;">End</th>
            <th class="gdfColHeader gdfResizable" style="width:50px;">dur.</th>
            <th class="gdfColHeader gdfResizable" style="width:20px;">%</th>
            <th class="gdfColHeader gdfResizable requireCanSeeDep" style="width:50px;">depe.</th>
            <th class="gdfColHeader gdfResizable" style="width:1000px; text-align: left; padding-left: 10px;">assignees</th>
        </tr>
        </thead>
    </table>
    --></div>

<div class="__template__" type="TASKROW"><!--
    <tr id="tid_(#=obj.id#)" taskId="(#=obj.id#)" class="taskEditRow (#=obj.isParent()?'isParent':''#) (#=obj.collapsed?'collapsed':''#)" level="(#=level#)">
        <th class="gdfCell edit" align="right" style="cursor:pointer;"><span class="taskRowIndex">(#=obj.getRow()+1#)</span> <span class="teamworkIcon" style="font-size:12px;" >e</span></th>
        <td class="gdfCell noClip" align="center"><div class="taskStatus cvcColorSquare" status="(#=obj.status#)"></div></td>
        <td class="gdfCell"><input type="text" name="code" value="(#=obj.code?obj.code:''#)" placeholder="code/short name"></td>
        <td class="gdfCell indentCell" style="padding-left:(#=obj.level*10+18#)px;">
            <div class="exp-controller" align="center"></div>
            <input type="text" name="name" value="(#=obj.name#)" placeholder="name">
        </td>
        <td class="gdfCell" align="center"><input type="checkbox" name="startIsMilestone"></td>
        <td class="gdfCell"><input type="text" name="start"  value="" class="date"></td>
        <td class="gdfCell" align="center"><input type="checkbox" name="endIsMilestone"></td>
        <td class="gdfCell"><input type="text" name="end" value="" class="date"></td>
        <td class="gdfCell"><input type="text" name="duration" autocomplete="off" value="(#=obj.duration#)"></td>
        <td class="gdfCell"><input type="text" name="progress" class="validated" entrytype="PERCENTILE" autocomplete="off" value="(#=obj.progress?obj.progress:''#)" (#=obj.progressByWorklog?"readOnly":""#)></td>
        <td class="gdfCell requireCanSeeDep"><input type="text" name="depends" autocomplete="off" value="(#=obj.depends#)" (#=obj.hasExternalDep?"readonly":""#)></td>
        <td class="gdfCell taskAssigs">(#=obj.getAssigsString()#)</td>
    </tr>
    --></div>

<div class="__template__" type="TASKEMPTYROW"><!--
    <tr class="taskEditRow emptyRow" >
        <th class="gdfCell" align="right"></th>
        <td class="gdfCell noClip" align="center"></td>
        <td class="gdfCell"></td>
        <td class="gdfCell"></td>
        <td class="gdfCell"></td>
        <td class="gdfCell"></td>
        <td class="gdfCell"></td>
        <td class="gdfCell"></td>
        <td class="gdfCell"></td>
        <td class="gdfCell"></td>
        <td class="gdfCell requireCanSeeDep"></td>
        <td class="gdfCell"></td>
    </tr>
    --></div>

<div class="__template__" type="TASKBAR"><!--
    <div class="taskBox taskBoxDiv" taskId="(#=obj.id#)" >
        <div class="layout (#=obj.hasExternalDep?'extDep':''#)">
            <div class="taskStatus" status="(#=obj.status#)"></div>
            <div class="taskProgress" style="width:(#=obj.progress>100?100:obj.progress#)%; background-color:(#=obj.progress>100?'red':'rgb(153,255,51);'#);"></div>
            <div class="milestone (#=obj.startIsMilestone?'active':''#)" ></div>
            
            <div class="taskLabel"></div>
            <div class="milestone end (#=obj.endIsMilestone?'active':''#)" ></div>
        </div>
    </div>
  --></div>


<div class="__template__" type="CHANGE_STATUS"><!--
    <div class="taskStatusBox">
    <div class="taskStatus cvcColorSquare" status="STATUS_ACTIVE" title="Active"></div>
    <div class="taskStatus cvcColorSquare" status="STATUS_DONE" title="Completed"></div>
    <div class="taskStatus cvcColorSquare" status="STATUS_FAILED" title="Failed"></div>
    <div class="taskStatus cvcColorSquare" status="STATUS_SUSPENDED" title="Suspended"></div>
    <div class="taskStatus cvcColorSquare" status="STATUS_WAITING" title="Waiting" style="display: none;"></div>
    <div class="taskStatus cvcColorSquare" status="STATUS_UNDEFINED" title="Undefined"></div>
    </div>
    --></div>




<div class="__template__" type="TASK_EDITOR"><!--
    <div class="ganttTaskEditor">
        <h2 class="taskData">Task editor</h2>
        <table  cellspacing="1" cellpadding="5" width="100%" class="taskData table" border="0">
            <tr>
                <td width="200" style="height: 80px"  valign="top">
                    <label for="code">code/short name</label><br>
                    <input type="text" name="code" id="code" value="" size=15 class="formElements" autocomplete='off' maxlength=255 style='width:100%' oldvalue="1">
                </td>
                <td colspan="3" valign="top"><label for="name" class="required">name</label><br><input type="text" name="name" id="name"class="formElements" autocomplete='off' maxlength=255 style='width:100%' value="" required="true" oldvalue="1">
                </td>
            </tr>
            <tr class="dateRow">
                <td nowrap="">
                    <div style="position:relative">
                        <label for="start">start</label>&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" id="startIsMilestone" name="startIsMilestone" value="yes"> &nbsp;<label for="startIsMilestone">is milestone</label>&nbsp;
                        <br><input type="text" name="start" id="start" size="8" class="formElements dateField validated date" autocomplete="off" maxlength="255" value="" oldvalue="1" entrytype="DATE">
                        <span title="calendar" id="starts_inputDate" class="teamworkIcon openCalendar" onclick="$(this).dateField({inputField:$(this).prevAll(':input:first'),isSearchField:false});">m</span>
                    </div>
                </td>
                <td nowrap="">
                    <label for="end">End</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" id="endIsMilestone" name="endIsMilestone" value="yes"> &nbsp;<label for="endIsMilestone">is milestone</label>&nbsp;
                    <br><input type="text" name="end" id="end" size="8" class="formElements dateField validated date" autocomplete="off" maxlength="255" value="" oldvalue="1" entrytype="DATE">
                    <span title="calendar" id="ends_inputDate" class="teamworkIcon openCalendar" onclick="$(this).dateField({inputField:$(this).prevAll(':input:first'),isSearchField:false});">m</span>
                </td>
                <td nowrap="" >
                    <label for="duration" class=" ">Days</label><br>
                    <input type="text" name="duration" id="duration" size="4" class="formElements validated durationdays" title="Duration is in working days." autocomplete="off" maxlength="255" value="" oldvalue="1" entrytype="DURATIONDAYS">&nbsp;
                </td>
            </tr>

            <tr>
                <td  colspan="2">
                <label for="status" class=" ">status</label><br>
                <select id="status" name="status" class="taskStatus" status="(#=obj.status#)"  onchange="$(this).attr('STATUS',$(this).val());">
                    <option value="STATUS_ACTIVE" class="taskStatus" status="STATUS_ACTIVE" >active</option>
                    <option value="STATUS_WAITING" class="taskStatus" status="STATUS_WAITING" >suspended</option>
                    <option value="STATUS_SUSPENDED" class="taskStatus" status="STATUS_SUSPENDED" >suspended</option>
                    <option value="STATUS_DONE" class="taskStatus" status="STATUS_DONE" >completed</option>
                    <option value="STATUS_FAILED" class="taskStatus" status="STATUS_FAILED" >failed</option>
                    <option value="STATUS_UNDEFINED" class="taskStatus" status="STATUS_UNDEFINED" >undefined</option>
                </select>
                </td>
        
                <td valign="top" nowrap>
                    <label>progress</label><br>
                    <input type="text" name="progress" id="progress" size="7" class="formElements validated percentile" autocomplete="off" maxlength="255" value="" oldvalue="1" entrytype="PERCENTILE">
                </td>
            </tr>
            </tr>
            <tr>
                <td colspan="4">
                    <label for="description">Description</label><br>
                    <textarea rows="3" cols="30" id="description" name="description" class="formElements" style="width:100%"></textarea>
                </td>
            </tr>
        </table>

        <h2>Assignments</h2>
        <table  cellspacing="1" cellpadding="0" width="100%" id="assigsTable">
        <tr>
            <th style="width:100px;">name</th>
            <th style="width:70px;">Role</th>
            <th style="width:30px;">est.wklg.</th>
            <th style="width:30px;" id="addAssig"><span class="teamworkIcon" style="cursor: pointer">+</span></th>
        </tr>
        </table>

        <div style="text-align: right; padding-top: 20px">
            <span id="saveButton" class="button first" onClick="$(this).trigger('saveFullEditor.gantt');">Save</span>
        </div>

    </div>
    --></div>



<div class="__template__" type="ASSIGNMENT_ROW"><!--
    <tr taskId="(#=obj.task.id#)" assId="(#=obj.assig.id#)" class="assigEditRow" >
        <td ><select name="resourceId"  class="formElements" (#=obj.assig.id.indexOf("tmp_")==0?"":"disabled"#) ></select></td>
        <td ><select type="select" name="roleId"  class="formElements"></select></td>
        <td ><input type="text" name="effort" value="(#=getMillisInHoursMinutes(obj.assig.effort)#)" size="5" class="formElements"></td>
        <td align="center"><span class="teamworkIcon delAssig del" style="cursor: pointer">d</span></td>
    </tr>
    --></div>



<div class="__template__" type="RESOURCE_EDITOR"><!--
    <div class="resourceEditor" style="padding: 5px;">
    
        <h2>Project team</h2>
        <table  cellspacing="1" cellpadding="0" width="100%" id="resourcesTable">
            <tr>
                <th style="width:100px;">name</th>
                <th style="width:30px;" id="addResource"><span class="teamworkIcon" style="cursor: pointer">+</span></th>
            </tr>
        </table>
        
        <div style="text-align: right; padding-top: 20px"><button id="resSaveButton" class="button big">Save</button></div>
    </div>
    --></div>



<div class="__template__" type="RESOURCE_ROW"><!--
    <tr resId="(#=obj.id#)" class="resRow" >
        <td ><input type="text" name="name" value="(#=obj.name#)" style="width:100%;" class="formElements"></td>
        <td align="center"><span class="teamworkIcon delRes del" style="cursor: pointer">d</span></td>
    </tr>
    --></div>


</div>
<script type="text/javascript">
</script>

</body>
</html>