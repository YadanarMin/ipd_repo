/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
var numOfCompareStudent = 0;
var compareStudentIdList = [];
var count = 0;

$(document).ready(function(){
    $("#showBtn").addClass("disabledBtn");
    $("#compareStudent").hide();
    
    var all = $("#all").is(":checked");
    if(all){
        LoadAllStudents($(".changeTimeline input[type='radio']:checked").val());
        // LoadAllTimelines();
    }
    
    //Checkbox for timeline
    $(".changeTimeline input[name='time']").click( function() {
        
        var all =  $("#all").is(":checked");
        var isNotFinished = $("#notfinished").is(":checked");
        var isFinished = $("#finished").is(":checked");
        var isNotYet = $("#notyet").is(":checked");
        ClearAllStudents();
        
        console.log("Radio" + $(".changeTimeline input[type='radio']:checked").val());
        if(all){
            ClearAllStudents();
            LoadAllStudents($(".changeTimeline input[type='radio']:checked").val());
        }
        else if(isNotFinished && isFinished && isNotYet){
            ClearAllStudents();
            LoadAllStudents($(".changeTimeline input[type='radio']:checked").val());
            
        }else if(isNotFinished && isNotYet){
            ClearAllStudents();
            LoadNotFinishedAndNotYetStudent($(".changeTimeline input[type='radio']:checked").val());
            
        }else if(isNotFinished && isFinished ){
            ClearAllStudents();
            LoadNotFinishedAndFinishedStudent($(".changeTimeline input[type='radio']:checked").val());
            
        }else if(isNotYet && isFinished ){
            ClearAllStudents();
            LoadNotYetAndFinishedStudent($(".changeTimeline input[type='radio']:checked").val());
            
        }else if(isNotFinished){
            ClearAllStudents();
            LoadNotFinishedStudent($(".changeTimeline input[type='radio']:checked").val());
            
        }else if(isNotYet){
            ClearAllStudents();
            LoadNotYetStudent($(".changeTimeline input[type='radio']:checked").val());
            
        }else if(isFinished){
            ClearAllStudents();
            LoadFinishedStudent($(".changeTimeline input[type='radio']:checked").val());
        }else{
            ClearAllStudents();
            ClearAllTimeline();
        }
    })
    
    //SearchFunction
    $("#userSearch").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#searchableUserList tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
        $("#searchableTimelineList tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
     
     });
     
    //ShowSelectedStudentInTable
    $("#usernameList tbody").on("click","tr", function(e){
        var id = $(this).find($('input[type=hidden]')).val();
        $(this).addClass("selected").siblings().removeClass("selected");
        ShowSelectedStudent(id);
    });
    
    //CompareStudentByClickingCheckboxInTable
    $("#usernameList tbody").on('click', 'input[type=checkbox]', function(e) {
        e.stopPropagation();
        var id =$(this).parent().siblings('input[type=hidden]').val();
        var tdText = $(this).parent().siblings().closest('tr').find('td:eq(0)').text();
        var name = tdText.substr(0,tdText.indexOf("比較"));
        
        var isChecked = $(".compareCheckbox").is(":checked");
        if(isChecked){
            if($("#"+id).is(":checked")){
                AddCompareStudentList(id,name);
            }else{
                RemoveFromCompareStudentList(id,name);
            }
            $("#compareStudent").show(500);
        }else{
            numOfCompareStudent = 0;
            $(".displayCompareStudentList").empty();
            $("#compareStudent").hide(500);
        }
        
    });
    
    //CloseCompareDiv
    $(".closeSign").on("click", function(){
        $("#compareStudent").hide(500);
    })
    
    //ShowOrHideCardInCompareDiv
    $(".displayCompareStudentList").on("click","span", function(e){
        e.stopPropagation();
        numOfCompareStudent--;
        $("#numOfCompareStudent").text(numOfCompareStudent);
        var id = $(this).children('input[type=hidden]').val();
        $("input:checkbox#"+id).prop("checked", false);
        $(this).parent().remove();
        var index = compareStudentIdList.indexOf(id);
        compareStudentIdList.splice(index,1);
        
        if(numOfCompareStudent==0){
            $("#compareStudent").hide(500);
        }
        
    });
    
    //NextArrow
    $(".next").on("click", function(e) {
        e.stopPropagation();
        count++;
        var left = count*(-120) 
        $(".displayCompareStudentList").animate({
            "margin-left" : left
            
        });
 
    });
    
    //PreviousArrow
    $(".previous").on("click", function(e) {
        e.stopPropagation();
        count=0;
        $(".displayCompareStudentList").animate({
            "margin-left" : "0px"
            
        });
 
    });
    
    
});

function AddCompareStudentList(id,name){
    numOfCompareStudent++;
    compareStudentIdList.push(id);
    $("#numOfCompareStudent").text(numOfCompareStudent);
    var div = "<div class='studentRemoveCard' id='" + id + "'>"+"<span class='glyphicon glyphicon-remove-circle removeStudent'><input type='hidden' value='"+ id +"'/></span>"+ name +"</div>";
    $(".displayCompareStudentList").append(div);
    
}

function RemoveFromCompareStudentList(id,name){
    numOfCompareStudent--;
    var index = compareStudentIdList.indexOf(id);
    compareStudentIdList.splice(index,1);
    $("#numOfCompareStudent").text(numOfCompareStudent);
    $(".displayCompareStudentList").children("#"+id).remove();
}

function ShowSelectedStudent(id){
    $.ajax({
        url: "../foreignStudents/getData",
        async:true,
        type: 'post',
        data:{_token: CSRF_TOKEN, message:"getStudentById", id : id},
        success :function(data) {
            console.log("StudentById" + JSON.stringify(data,null,4));
            if(data){
                ShowStudentTableById(data);
            }
        },
        error:function(err){
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
    
}

//ShowSelectedStudentInTable
function ShowStudentTableById(data){
    $.each(data, function(key,value){
        $.each(value, function(tname, tdata) {
            var id = tdata['id'];
            var username = tdata['first_name']+ " "+tdata['last_name'];
            var s_place = tdata['s_branch_name'];
            var s_skill = tdata['s_skill'];
            var s_field = tdata['s_field'];
            var s_haken_department = tdata['s_haken_company_name'];
            var s_obayashi_department = tdata['s_dept_name'];
            var s_code = tdata['s_code'];
            var s_type = tdata['s_type'];
            var genzai_place = tdata['branch_name'];
            var genzai_skill = tdata['genzai_skill'];
            var genzai_field = tdata['genzai_field'];
            var genzai_haken_department = tdata['genzai_haken_company_name'];
            var genzai_obayashi_department = tdata['dept_name'];
            var genzai_code = tdata['code'];
            var genzai_type = tdata['genzai_type'];
            var s_startDate = (tdata['startDate'] == '0000-00-00') ? " " : tdata['startDate'] ;
            var s_endDate = (tdata['endDate'] == '0000-00-00') ? " " : tdata['endDate'] ;
            var puchi1 = (tdata['puchi1'] == '0000-00-00') ? " " : tdata['puchi1'] ;
            var puchi2 = (tdata['puchi2'] == '0000-00-00') ? " " : tdata['puchi2'] ;
            var puchi3 = (tdata['puchi3'] == '0000-00-00') ? " " : tdata['puchi3'] ;
            var puchi4 = (tdata['puchi4'] == '0000-00-00') ? " " : tdata['puchi4'] ;
            
            $("#s-place").html(s_place);
            $("#s-skill").html(s_skill);
            $("#s-field").html(s_field);
            $("#s-haken").html(s_haken_department);
            $("#s-obayashi").html(s_obayashi_department);
            $("#s-code").html(s_code);
            $("#s-type").html(s_type);
            $("#s-startDate").html(s_startDate);
            $("#s-endDate").html(s_endDate);
            $("#s-puchi1").html(puchi1);
            $("#s-puchi2").html(puchi2);
            $("#s-puchi3").html(puchi3);
            $("#s-puchi4").html(puchi4);
            
            $("#e-place").html(genzai_place);
            $("#e-skill").html(genzai_skill);
            $("#e-field").html(genzai_field);
            $("#e-haken").html(genzai_haken_department);
            $("#e-obayashi").html(genzai_obayashi_department);
            $("#e-code").html(genzai_code);
            $("#e-type").html(genzai_type);
            $("#e-startDate").html("");
            $("#e-endDate").html("");
            $("#e-puchi1").html(puchi1);
            $("#e-puchi2").html(puchi2);
            $("#e-puchi3").html(puchi3);
            $("#e-puchi4").html(puchi4);
            
            
        });
        
    });
}

function InsertBtn(){
    $("#insertBtn").addClass("disabledBtn");
    window.location='../foreignStudents/insert';
}

//比較ボタンを押す時
function CompareStudent(){
    console.log("List" + compareStudentIdList);
    window.location.href='../foreignStudents/compare/'+compareStudentIdList;
    // $.ajax({
    //     url: "../foreignStudents/compare",
    //     async:true,
    //     type: 'post',
    //     data:{_token: CSRF_TOKEN, message:"getCompareStudentData", list : compareStudentIdList},
    //     success :function(data) {
    //         console.log("CompareData" + JSON.stringify(data,null,4));
            
            
            
    //     },
    //     error:function(err){
    //         console.log(err);
    //         alert("データロードに失敗しました。\n管理者に問い合わせてください。");
    //     }
    // });
}

function LoadAllStudents(time){
    $.ajax({
        url: "../foreignStudents/getData",
        async:true,
        type: 'post',
        data:{_token: CSRF_TOKEN, message:"getAllStudentsByStartDateDesc"},
        success :function(data) {
            console.log("ListOfStudents" + JSON.stringify(data,null,4));
            if(data){
                ClearAllTimeline()
                ShowAllStudents(data,time);
            }
        },
        error:function(err){
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
}

function LoadFinishedStudent(time){
    $.ajax({
        url: "../foreignStudents/getData",
        async:true,
        type: 'post',
        data:{_token: CSRF_TOKEN, message:"getFinishedStudents"},
        success :function(data) {
            console.log("ListOfStudents" + JSON.stringify(data,null,4));
            if(data){
                ClearAllTimeline()
                ShowAllStudents(data,time);
            }
        },
        error:function(err){
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
}

// function LoadFinishedStudentTimeline(){
//     $.ajax({
//         url: "../foreignStudents/getData",
//         async:true,
//         type: 'post',
//         data:{_token: CSRF_TOKEN, message:"getFinishedStudentsTimeline"},
//         success :function(data) {
//             if(data){
//                 ShowAllTimelines(data);
//             }
//         },
//         error:function(err){
//             console.log(err);
//             alert("データロードに失敗しました。\n管理者に問い合わせてください。");
//         }
//     });
// }

//留学中の学生
function LoadNotFinishedStudent(time){
    $.ajax({
        url: "../foreignStudents/getData",
        async:true,
        type: 'post',
        data:{_token: CSRF_TOKEN, message:"getNotFinishedStudents"},
        success :function(data) {
            console.log("ListOfStudents" + JSON.stringify(data,null,4));
            if(data){
                ClearAllTimeline()
                ShowAllStudents(data,time);
            }
        },
        error:function(err){
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
}

// function LoadNotFinishedStudentTimeline(){
//     $.ajax({
//         url: "../foreignStudents/getData",
//         async:true,
//         type: 'post',
//         data:{_token: CSRF_TOKEN, message:"getNotFinishedStudentsTimeline"},
//         success :function(data) {
//             console.log("ListOfStudents" + JSON.stringify(data,null,4));
//             if(data){
//                 ShowAllTimelines(data);
//             }
//         },
//         error:function(err){
//             console.log(err);
//             alert("データロードに失敗しました。\n管理者に問い合わせてください。");
//         }
//     });
// }

//留学予定
function LoadNotYetStudent(time){
    $.ajax({
        url: "../foreignStudents/getData",
        async:true,
        type: 'post',
        data:{_token: CSRF_TOKEN, message:"getNotYetStudents"},
        success :function(data) {
            console.log("ListOfStudents" + JSON.stringify(data,null,4));
            if(data){
                ClearAllTimeline()
                ShowAllStudents(data,time);
            }
        },
        error:function(err){
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
}

// function LoadNotYetStudentTimeline(){
//     $.ajax({
//         url: "../foreignStudents/getData",
//         async:true,
//         type: 'post',
//         data:{_token: CSRF_TOKEN, message:"getNotYetStudentsTimeline"},
//         success :function(data) {
//             if(data){
//                 ShowAllTimelines(data);
//             }
//         },
//         error:function(err){
//             console.log(err);
//             alert("データロードに失敗しました。\n管理者に問い合わせてください。");
//         }
//     });
// }

//留学中と留学終了 LoadNotFinishedAndNotYetStudent
function LoadNotFinishedAndFinishedStudent(time){
    $.ajax({
        url: "../foreignStudents/getData",
        async:true,
        type: 'post',
        data:{_token: CSRF_TOKEN, message:"getNotFinishedAndFinishedStudents"},
        success :function(data) {
            console.log("ListOfStudents" + JSON.stringify(data,null,4));
            if(data){
                ClearAllTimeline()
                ShowAllStudents(data,time);
            }
        },
        error:function(err){
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
}

// function LoadNotFinishedAndFinishedStudentTimeline(){
//     $.ajax({
//         url: "../foreignStudents/getData",
//         async:true,
//         type: 'post',
//         data:{_token: CSRF_TOKEN, message:"getNotFinishedAndFinishedStudentsTimeline"},
//         success :function(data) {
            
//             if(data){
//                 ShowAllTimelines(data);
//             }
//         },
//         error:function(err){
//             console.log(err);
//             alert("データロードに失敗しました。\n管理者に問い合わせてください。");
//         }
//     });
// }

//留学中と留学予定
function LoadNotFinishedAndNotYetStudent(time){
    $.ajax({
        url: "../foreignStudents/getData",
        async:true,
        type: 'post',
        data:{_token: CSRF_TOKEN, message:"getNotFinishedAndNotYetStudents"},
        success :function(data) {
            console.log("ListOfStudents" + JSON.stringify(data,null,4));
            if(data){
                ClearAllTimeline()
                ShowAllStudents(data,time);
            }
        },
        error:function(err){
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
}

// function LoadNotFinishedAndNotYetStudentTimeline(){
//     $.ajax({
//         url: "../foreignStudents/getData",
//         async:true,
//         type: 'post',
//         data:{_token: CSRF_TOKEN, message:"getNotFinishedAndNotYetStudentsTimeline"},
//         success :function(data) {
            
//             if(data){
//                 ShowAllTimelines(data);
//             }
//         },
//         error:function(err){
//             console.log(err);
//             alert("データロードに失敗しました。\n管理者に問い合わせてください。");
//         }
//     });
// }

//留学終了と留学予定
function LoadNotYetAndFinishedStudent(time){
    $.ajax({
        url: "../foreignStudents/getData",
        async:true,
        type: 'post',
        data:{_token: CSRF_TOKEN, message:"getNotYetAndFinishedStudents"},
        success :function(data) {
            console.log("ListOfStudents" + JSON.stringify(data,null,4));
            if(data){
                ClearAllTimeline()
                ShowAllStudents(data,time);
            }
        },
        error:function(err){
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
}

// function LoadNotYetAndFinishedStudentTimeline(){
//     $.ajax({
//         url: "../foreignStudents/getData",
//         async:true,
//         type: 'post',
//         data:{_token: CSRF_TOKEN, message:"getNotYetAndFinishedStudentsTimeline"},
//         success :function(data) {
            
//             if(data){
//                 ShowAllTimelines(data);
//             }
//         },
//         error:function(err){
//             console.log(err);
//             alert("データロードに失敗しました。\n管理者に問い合わせてください。");
//         }
//     });
// }

function ClearAllStudents(){
    $("#usernameList tbody").empty();
}
function ClearAllTimeline(){
    $("#timelineList tbody").empty();
}

function ShowAllStudents(data, time) {
    $.each(data, function(key,value){
        $.each(value, function(tname, tdata){
            var row =  "<tr>"+
                        "<td>" + tdata['first_name'] + " " + tdata['last_name']  + "<input type='hidden' value='" + tdata['id'] + "'/>"+
                        "<div style='float:right; margin-top: -5px'><input type='checkbox' class='compareCheckbox'  value='" + tdata['user_id'] + "' id='" + tdata['id'] + "'/>"+
                        "<p style='padding: 0;margin: 0;font-size: 12px;margin-top: -5px;'>比較</p></div>"+
                        
                        "</td>" +
                       "<td style='display:none;'>" + tdata['code']  + "</td>" +
                       "<td style='display:none;'>" + tdata['s_code']  + "</td>" +
                       "<td style='display:none;'>" + tdata['s_skill']  + "</td>" +
                       "<td style='display:none;'>" + tdata['genzai_skill']  + "</td>" +
                       "<td style='display:none;'>" + tdata['s_field']  + "</td>" +
                       "<td style='display:none;'>" + tdata['genzai_field']  + "</td>" +
                       "<td style='display:none;'>" + tdata['s_type']  + "</td>" +
                       "<td style='display:none;'>" + tdata['genzai_type']  + "</td>" +
                       "<td style='display:none;'>" + tdata['s_haken_company_name']  + "</td>" +
                       "<td style='display:none;'>" + tdata['genzai_haken_company_name']  + "</td>" +
                       "<td style='display:none;'>" + tdata['genzai_dept_name']  + "</td>" +
                       "<td style='display:none;'>" + tdata['genzai_branch_name']  + "</td>" +
                       "<td style='display:none;'>" + tdata['s_dept_name']  + "</td>" +
                       "<td style='display:none;'>" + tdata['s_branch_name']  + "</td>" +
                        "</tr>";
            
            $("#usernameList tbody").append(row);
            
            var line_margin;
            var line_height;
            var today;
            var start;
            var end;
            var thHeight = $(".info").css("height");
            var trHeight = $("#searchableUserList tr:nth-child(2)").height();
            
            
            if(time){
                var t = parseInt(time/2);
                var startTimeline = new Date(Date.now() - t * 24*60*60*1000);
                var endTimeline = new Date(Date.now() + t * 24*60*60*1000);
            }else{
                var startTimeline = new Date(Date.now() - 548 * 24*60*60*1000);
                var endTimeline = new Date(Date.now() + 548 * 24*60*60*1000);
            }
            
            var Difference_In_Time = endTimeline.getTime() - startTimeline.getTime();
            var duration = Difference_In_Time / (1000 * 3600 * 24);
            
            var i = tdata['id'];
            var startDate = tdata['startDate'];
            var endDate = tdata['endDate'];
            if(!endDate){
                endDate = "";
            }
            var timelineRow = "<tr style='height :" +trHeight +"px'><td>"+
                        "<span style='font-size: 12px; padding: 0px'>" + startDate +" - "+ endDate+ "</span>"+
                        "<span style='display:none;'>" + tdata['first_name'] + " "+ tdata['last_name']  + "</span>" +
                        "<span style='display:none;'>" + tdata['s_code']  + "</span>" +
                        "<span style='display:none;'>" + tdata['code']  + "</span>" +
                        "<span style='display:none;'>" + tdata['s_haken_company_name']  + "</span>" +
                        "<span style='display:none;'>" +tdata['genzai_haken_company_name']  + "</span>" +
                        "<span style='display:none;'>" + tdata['genzai_dept_name']  + "</span>" +
                        "<span style='display:none;'>" +tdata['s_dept_name']  + "</span>" +
                        "<span style='display:none;'>" + tdata['s_type']  + "</span>" +
                        "<span style='display:none;'>" + tdata['genzai_type']  + "</span>" +
                        "<span style='display:none;'>" + tdata['genzai_branch_name']  + "</span>" +
                        "<span style='display:none;'>" + tdata['s_branch_name']  + "</span>" +
                        "<span style='display:none;'>" + tdata['s_skill']  + "</span>" +
                        "<span style='display:none;'>" + tdata['genzai_skill']  + "</span>" +
                        "<span style='display:none;'>" + tdata['genzai_field']  + "</span>" +
                        "<span style='display:none;'>" + tdata['s_field']  + "</span>" +
                        
                        "<div id='slider"+ i + "' style='padding: 0px'></div>"+
                        "</td></tr>";
            $("#timelineList tbody").append(timelineRow);
            startDate = new Date(startDate);
            endDate = new Date(endDate);
            start = new Date(startDate-startTimeline);
            end = new Date(endDate - startTimeline);
            
            start = start.getTime();
            end = end.getTime();
            
            console.log("Start" + start);
            console.log("End" + end);
            $( "#slider"+i ).slider({
                values:[start,end],
                min:0,
                max:Difference_In_Time,
                step : 1,
                range: true,
            });
            
            $(".timelineHead").css("height",thHeight);
            today = new Date(Date.now()-startTimeline);
            var numOfROw = $("#timelineList tr").length;
            var rowHeight = $("#timelineList tr").height();
            line_height = 47*(numOfROw-1);
            console.log("Row" + rowHeight);
            var swidth = $(".ui-slider").css("width");
            console.log("Row" + swidth);
            var index = swidth.search("px");
            //var width = swidth.slice(0,index);
            var width = parseInt(swidth);
            line_margin = Math.round((width/(endTimeline.getTime()-startTimeline.getTime())) * ( new Date().getTime() - startTimeline.getTime()));
            console.log("Row" + line_margin);
            $("#vline").css({"height" : line_height});
            $("#vline").css({"margin-left" : line_margin});
            $("#today").css({"margin-left" : line_margin-7});
        });
        
    });
    
}

// function LoadAllTimelines(){
//     $.ajax({
//         url: "../foreignStudents/getData",
//         async:true,
//         type: 'post',
//         data:{_token: CSRF_TOKEN, message:"getAllStudentsDuration"},
//         success :function(data) {
//             console.log("ListOfStudentsDuration" + JSON.stringify(data,null,4));
//             if(data){
//                 ShowAllTimelines(data);
//             }
//         },
//         error:function(err){
//             console.log(err);
//             alert("データロードに失敗しました。\n管理者に問い合わせてください。");
//         }
//     });
// }

// function LoadTimelineById(id){
//     $.ajax({
//         url: "../foreignStudents/getData",
//         async:true,
//         type: 'post',
//         data:{_token: CSRF_TOKEN, message:"getStudentById", id: id},
//         success :function(data) {
//             console.log("ListOfStudentsDurationById" + JSON.stringify(data,null,4));
//             if(data){
//                 ShowAllTimelines(data);
//             }
//         },
//         error:function(err){
//             console.log(err);
//             alert("データロードに失敗しました。\n管理者に問い合わせてください。");
//         }
//     });
// }

// function ShowAllTimelines(data){
//     var line_margin;
//     var line_height;
//     var today;
//     var start;
//     var end;
//     var thHeight = $(".info").css("height");
//     var trHeight = $("#searchableUserList tr").height();
//     console.log("hello" +JSON.stringify(data))
//     var startTimeline = new Date(Date.now() - 15 * 24*60*60*1000);
//     var endTimeline = new Date(Date.now() + 30 * 24*60*60*1000);
//     var Difference_In_Time = endTimeline.getTime() - startTimeline.getTime();
//     var duration = Difference_In_Time / (1000 * 3600 * 24);
    
//     $.each(data, function(key,value){
//         $.each(value, function(tname, tdata){
//             var i = tdata['id'];
//             var startDate = tdata['s_startDate'];
//             var endDate = tdata['s_endDate'];
//             console.log("StartDate" + startDate);
//             console.log("EndDate" + endDate);
            
//             if(!endDate){
//                 endDate = "";
//             }
            
//             var row = "<tr style='height :" +trHeight +"px'><td>"+
//                         "<span style='font-size: 12px; padding: 0px'>" + startDate +" - "+ endDate+ "</span>"+
//                         "<span style='display:none;'>" + tdata['username']  + "</span>" +
//                         "<span style='display:none;'>" + tdata['s_code']  + "</span>" +
//                         "<span style='display:none;'>" + tdata['s_field']  + "</span>" +
//                         "<span style='display:none;'>" + tdata['s_haken_department']  + "</span>" +
//                         "<span style='display:none;'>" + tdata['s_obayashi_department']  + "</span>" +
//                         "<span style='display:none;'>" + tdata['s_type']  + "</span>" +
//                         "<span style='display:none;'>" + tdata['s_place']  + "</span>" +
//                         "<span style='display:none;'>" + tdata['s_skill']  + "</span>" +
                        
//                         "<div id='slider"+ i + "' style='padding: 0px'></div>"+
//                         "</td></tr>";
//             $("#timelineList tbody").append(row);
            
//             startDate = new Date(startDate);
//             endDate = new Date(endDate);
//             start = new Date(startDate-startTimeline);
//             end = new Date(endDate - startTimeline);
//             // start = Math.round(start.getTime()/(1000*3600*24));
//             // end = Math.round(end.getTime()/(1000*3600*24));
//             start = start.getTime();
//             end = end.getTime();
            
//             console.log("Start" + start);
//             console.log("End" + end);
//             $( "#slider"+i ).slider({
//                 values:[start,end],
//                 min:0,
//                 max:Difference_In_Time,
//                 step : 1,
//                 range: true,
//             });
            
//         });
        
//     });
//     $(".timelineHead").css("height",thHeight);
//     today = new Date(Date.now()-startTimeline);
//     var numOfROw = $("#timelineList tr").length;
//     var rowHeight = $("#timelineList tr").height();
//     line_height = 47*(numOfROw-1);
//     console.log("Row" + rowHeight);
//     var swidth = $(".ui-slider").css("width");
//     console.log("Row" + swidth);
//     var index = swidth.search("px");
//     //var width = swidth.slice(0,index);
//     var width = parseInt(swidth);
//     line_margin = Math.round((width/(endTimeline.getTime()-startTimeline.getTime())) * ( new Date().getTime() - startTimeline.getTime()));
//     console.log("Row" + line_margin);
    
//     // // var width = $(".ui-slider").width();
//     // // console.log("Width" + (width/duration)*15);
//     // // today = new Date(startTimeline-Date.now());
//     // // today = Math.abs(today.getTime()/(1000*3600*24));
//     // // line_margin = (width/100)*today;
    
//     $("#vline").css({"height" : line_height});
//     $("#vline").css({"margin-left" : line_margin});
//     // $("#today").val("g");
// }

// function CheckToday(){
//     var d = new Date();
//     var year = d.getFullYear();
//     var month = d.getMonth()+1;
//     var day = d.getDate();
//     var currentDate = year + "-" + month + "-"+day;
//     return currentDate;
// }

// function CheckNumOfDay (month, year) { // Use 1 for January, 2 for February, etc.
//   return new Date(year, month, 0).getDate();
// }

