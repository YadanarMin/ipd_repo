var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function () {
    $.ajaxSetup({
        cache:false
    });
    $("#disableMonthWarning").hide();
    //For Calendar
    LoadDecidedDateList();
    //LoadSelectedCalendar();
    ShowSuggestedData();
})

function ShowCalendar( disableDateList, disableMonthList){
        
        var decidedDateList=[];
        var decidedDateListStr = $("#hiddenDecidedDateList").val();
        console.log(decidedDateListStr);
        var dateArray = decidedDateListStr.split(",");
        for(var i=0; i<dateArray.length ; i++){
            decidedDateList.push(dateArray[i].trim());
        } 
        console.log("De"+decidedDateList);
        var result = [];
        var selectedDateList = [];
        
        //DisableMonthWarning
        var date = new Date();
        var month = date.getMonth()+1;
        if(month<=9){
            month="0"+month;
        }
        var year = date.getFullYear();
        var currentMonthAndYear = month+"-"+year;
        if(disableMonthList.indexOf(currentMonthAndYear) >=0){
            $("#disableMonthWarning").show()
        }
        //DisableMonthWarning
        
        var desireDate = $("#hiddenDate").val();
        if(desireDate){
            var dateArray = desireDate.split(",");
            for(var i=0; i<dateArray.length ; i++){
                selectedDateList.push(dateArray[i].trim());
            }
            $("#calendar").datepicker({
                dateFormat: 'yy-mm-dd',
                onChangeMonthYear : function(year, month){
                    if(month<=9){
                        month = "0"+month;
                    }
                    var onChangeYearAndMonth = month+"-"+year;
                    if(disableMonthList.indexOf(onChangeYearAndMonth) >=0){
                        $("#disableMonthWarning").show();
                    }else{
                        $("#disableMonthWarning").hide()
                    }
                    
                },
                onSelect: function (dateText, inst) {
                    var date = $(this).val();
                    var selectedDate = $(this).val();
                    if ($("#popup").length){
                      $("#popup").remove();
                    }
                    var dialog ="<div id='popup' style='border :1px solid black; height: 100px; padding:20px'>"+
                                "<label>"+date+"</label><br>"+
                                "<label>午前</label>"+
                                "<input type='checkbox' id='"+  date +   "AM' value='" + date+ 
                                "(AM)'"+ ((selectedDateList.indexOf(date+'(AM)') >=0 ? 'checked' : ' '))+
                                ((decidedDateList.indexOf(date+'(AM)') >=0 || disableDateList.indexOf(date+'(AM)') >=0  ? ' disabled' : ' '))+"/>&nbsp;"+
                                "<label>午後</label>"+
                                "<input type='checkbox' id='"+  date +   "PM' value='" + date+ 
                                "(PM)'"+ ((selectedDateList.indexOf(date+'(PM)') >=0 ? 'checked' : ' ')) + 
                                ((decidedDateList.indexOf(date+'(PM)') >=0 || disableDateList.indexOf(date+'(PM)') >=0  ? ' disabled' : ' '))+ "/>"+
                                "</div>";
                    //$(".calendarAndWarining").append(dialog);
                    $(dialog).insertBefore("#disableMonthWarning");
                    
                    $("#"+date+"AM").on('change', function() {
                        if($("#"+date+"AM").is(":checked")){
                            var value = $("#"+date+"AM").val();
                            if(selectedDateList.indexOf(value) <0){
                                selectedDateList.push(value);
                            }    
                            $("#selectedDesireDate").html(selectedDateList.toString());
                            $("#hiddenDate").val(selectedDateList.toString())
                        }else{
                            var index = selectedDateList.indexOf($("#"+date+"AM").val());
                            selectedDateList.splice(index,1);
                            $("#selectedDesireDate").html(selectedDateList.toString());
                            $("#hiddenDate").val(selectedDateList.toString())
                        }
                        
                    });
                    $("#"+date+"PM").on('change', function(){
                        if($("#"+date+"PM").is(":checked")){
                            var value = $("#"+date+"PM").val();
                            if(selectedDateList.indexOf(value) <0){
                                selectedDateList.push(value);
                            } 
                            $("#selectedDesireDate").html(selectedDateList.toString());
                            $("#hiddenDate").val(selectedDateList.toString())
                        }else{
                            var index = selectedDateList.indexOf($("#"+date+"PM").val());
                            selectedDateList.splice(index,1);
                            $("#selectedDesireDate").html(selectedDateList.toString());
                            $("#hiddenDate").val(selectedDateList.toString())
                        }
                        
                    });
                },
                beforeShowDay :    function (date){
                    
                                    if(date < new Date()){
                                        return [false];
                                    }
                                    var year = date.getFullYear();
                                    
                                   var month = date.getMonth() + 1;
                                   if(month <= 9)
                                        month = '0'+month;
                                    
                                    var day= date.getDate();
                                    if(day <= 9)
                                        day = '0'+day;
                                    
                                   var prettyDate = year +'-'+ month +'-'+ day;
                                   var AM = prettyDate+"(AM)";
                                    var PM = prettyDate+"(PM)";
                                    
                                   for(var i=0; i<disableMonthList.length ; i++){
                                       var monthAndYear = disableMonthList[i];
                                       m = monthAndYear.substring(0, monthAndYear.indexOf('-'));
                                       y = monthAndYear.substring(monthAndYear.indexOf('-')+1);
                                      
                                       if(month == m && year == y){
                                           return[false];
                                       }
                                    }
                                   
                                   //Change Today Color
                                   if(prettyDate == new Date().toJSON().slice(0,10).replace(/-/g,'-')){
                                       return [true, 'today'];
                                   }
                                //   if(disableDateList.indexOf(prettyDate) >= 0){
                                //       return [false, '', 'NG日'];
                                //   }
                                   
                                //   if(decidedDateList.indexOf(prettyDate) >= 0){
                                //       return [true, 'myhighlightDecided', '決定日'];
                                //   } else {
                                //         return [true];
                                //     }  
                                    if(disableDateList.indexOf(AM) >= 0 && disableDateList.indexOf(PM)>=0){
                                      return [true, 'myhighlightDisable', 'NG日'];
                                    }
                                    
                                    if((decidedDateList.indexOf(AM) >= 0 && decidedDateList.indexOf(PM) >=0)||
                                        (decidedDateList.indexOf(AM) >= 0 && disableDateList.indexOf(PM) >=0)||
                                        (disableDateList.indexOf(AM) >= 0 && decidedDateList.indexOf(PM) >=0)||
                                        decidedDateList.indexOf(AM) >= 0 ||
                                        decidedDateList.indexOf(PM) >=0
                                    ) {
                                        return [true, 'myhighlightDecided', '決定日'];
                                    }else{
                                        return [true];
                                    }
                            }
            });
        }else{
            $("#calendar").datepicker({
                dateFormat: 'yy-mm-dd',
                onChangeMonthYear : function(year, month){
                    if(month<=9){
                        month = "0"+month;
                    }
                    var onChangeYearAndMonth = month+"-"+year;
                    if(disableMonthList.indexOf(onChangeYearAndMonth) >=0){
                        $("#disableMonthWarning").show();
                    }else{
                        $("#disableMonthWarning").hide()
                    }
                    
                },
                onSelect: function (dateText, inst) {
                    var date = $(this).val();
                    var selectedDate = $(this).val();
                    if ($("#popup").length){
                      $("#popup").remove();
                    }
                    var dialog ="<div id='popup' style='border :1px solid black; height: 100px; padding:20px'>"+
                                "<label>"+date+"</label><br>"+
                                "<label>午前</label>"+
                                "<input type='checkbox' id='"+  date +   "AM' value='" + date+ 
                                "(AM)'"+ ((selectedDateList.indexOf(date+'(AM)') >=0 ? 'checked' : ' '))+ 
                                ((decidedDateList.indexOf(date+'(AM)') >=0 || disableDateList.indexOf(date+'(AM)') >=0  ? ' disabled' : ' '))+ "/>&nbsp;"+
                                "<label>午後</label>"+
                                "<input type='checkbox' id='"+  date +   "PM' value='" + date+ 
                                "(PM)'"+ ((selectedDateList.indexOf(date+'(PM)') >=0 ? 'checked' : ' ')) + 
                                ((decidedDateList.indexOf(date+'(PM)') >=0 || disableDateList.indexOf(date+'(PM)') >=0  ? ' disabled' : ' '))+ "/>"+
                                "</div>";
                    //$(".calendarAndWarining").append(dialog);
                    $(dialog).insertBefore("#disableMonthWarning");
                    
                    $("#"+date+"AM").on('change', function() {
                        if($("#"+date+"AM").is(":checked")){
                            var value = $("#"+date+"AM").val();
                            if(selectedDateList.indexOf(value) <0){
                                selectedDateList.push(value);
                            }    
                            $("#selectedDesireDate").html(selectedDateList.toString());
                            $("#hiddenDate").val(selectedDateList.toString())
                        }else{
                            var index = selectedDateList.indexOf($("#"+date+"AM").val());
                            selectedDateList.splice(index,1);
                            $("#selectedDesireDate").html(selectedDateList.toString());
                            $("#hiddenDate").val(selectedDateList.toString())
                        }
                        
                    });
                    $("#"+date+"PM").on('change', function(){
                        if($("#"+date+"PM").is(":checked")){
                            var value = $("#"+date+"PM").val();
                            if(selectedDateList.indexOf(value) <0){
                                selectedDateList.push(value);
                            } 
                            $("#selectedDesireDate").html(selectedDateList.toString());
                            $("#hiddenDate").val(selectedDateList.toString())
                        }else{
                            var index = selectedDateList.indexOf($("#"+date+"PM").val());
                            selectedDateList.splice(index,1);
                            $("#selectedDesireDate").html(selectedDateList.toString());
                            $("#hiddenDate").val(selectedDateList.toString())
                        }
                        
                    });
                },
                beforeShowDay :    function (date){
                    
                                    if(date < new Date()){
                                        return [false];
                                    }
                                    
                                    var year = date.getFullYear();
                                    
                                   var month = date.getMonth() + 1;
                                   if(month <= 9)
                                        month = '0'+month;
                                    
                                    var day= date.getDate();
                                    if(day <= 9)
                                        day = '0'+day;
                                    
                                   var prettyDate = year +'-'+ month +'-'+ day;
                                   var AM = prettyDate+"(AM)";
                                    var PM = prettyDate+"(PM)";
                                    
                                   for(var i=0; i<disableMonthList.length ; i++){
                                       var monthAndYear = disableMonthList[i];
                                       m = monthAndYear.substring(0, monthAndYear.indexOf('-'));
                                       y = monthAndYear.substring(monthAndYear.indexOf('-')+1);
                                      
                                       if(month == m && year == y){
                                           return[false];
                                       }
                                    }
                                   
                                   //Change Today Color
                                   if(prettyDate == new Date().toJSON().slice(0,10).replace(/-/g,'-')){
                                       return [true, 'today'];
                                   }
                                //   if(disableDateList.indexOf(prettyDate) >= 0){
                                //       return [false, '', 'NG日'];
                                //   }
                                   
                                //   if(decidedDateList.indexOf(prettyDate) >= 0){
                                //       return [true, 'myhighlightDecided', '決定日'];
                                //   } else {
                                //         return [true];
                                //     }  
                                    if(disableDateList.indexOf(AM) >= 0 && disableDateList.indexOf(PM)>=0){
                                      return [true, 'myhighlightDisable', 'NG日'];
                                    }
                                    
                                    if((decidedDateList.indexOf(AM) >= 0 && decidedDateList.indexOf(PM) >=0)||
                                        (decidedDateList.indexOf(AM) >= 0 && disableDateList.indexOf(PM) >=0)||
                                        (disableDateList.indexOf(AM) >= 0 && decidedDateList.indexOf(PM) >=0)||
                                        decidedDateList.indexOf(AM) >= 0 ||
                                        decidedDateList.indexOf(PM) >=0
                                    ) {
                                        return [true, 'myhighlightDecided', '決定日'];
                                    }else{
                                        return [true];
                                    }
                            }
            });
        }
        
        
}  

function UpdateUserInfo(){
    var id = $("#hiddenUserId").val();
    // var username = $("#username").val();
    // var place = $("#place").val();
    // var obayashi = $("#obayashi").val();
    // var hakenplace = $("#hakenplace").val();
    // var code = $("#code").val();
    // var job = $("#job").val();
    // var mail = $("#mail").val();
    // var inviter = $("#inviter").val();
    var classType = $("input[type='radio']:checked").val();
    var desireDate = $("#hiddenDate").val();
    console.log(desireDate)
    //return;
    // var userInfo = {
    //     'username' : username,
    //     'place' : place,
    //     'obayashi' : obayashi,
    //     'hakenplace' : hakenplace,
    //     'code' : code,
    //     'job' : job,
    //     'mail' : mail,
    //     'inviter' : inviter,
    //     'classType' : classType,
    //     'hiddenSelectedDate' : hiddenSelectedDate
    // };
    
    $.ajax({
      type     : "post",
      url      : "../../application/updateData",
      data     : {_token: CSRF_TOKEN, message : "updateData", desireDate : desireDate, id : id, classType :classType},
      success  : function(data){
                alert("変更しました！")
                window.location.href="../../application/edit"
      },
      error    : function(err){
            console.log(err);
        }
  });
}

function LoadSelectedCalendar(){
    ShowLoading();
    var decidedDateList = [];
    var disableDateList = [];
    var disableMonthList = [];
//     $.ajax({
//       type     : "post",
//       url      : "../../application/getData",
//       data     : {_token: CSRF_TOKEN,message : "getDate"},
//       success  : function(data){
//                 console.log("h" + JSON.stringify(data));
//                 $.each(data,function(key,value){
//                     $.each(value,function(key, value) {
//                         if(value['decidedDate']){
//                             var decidedDate = value['decidedDate'];
//                             var dateArray = decidedDate.split(",");
//                             for(var i=0; i<dateArray.length ; i++){
//                             if(!decidedDateList.includes(dateArray[i].trim())){
//                                 decidedDateList.push(dateArray[i].trim());
//                             }
                            
//                         }
//                         }
//                         if(value['disableDate']){
//                             var disableDate = value['disableDate'];
//                             var disableDateArray = disableDate.split(",");
//                             for(var i=0; i<disableDateArray.length ; i++){
//                             if(!disableDateList.includes(disableDateArray[i].trim())){
//                                 disableDateList.push(disableDateArray[i].trim());
//                             }
                            
//                         }
//                         }
                        
//                         if(value['disableMonth']){
//                             var disableMonth = value['disableMonth'];
//                             var dateArray = disableMonth.split(",");
//                             for(var i=0; i<dateArray.length ; i++){
//                             if(!disableMonthList.includes(dateArray[i].trim())){
//                                 disableMonthList.push(dateArray[i].trim());
                                
//                             }
                            
//                         }
//                         }
//                     })
//                 })
//                 console.log(decidedDateList)
//                 console.log(disableDateList)
//                 ShowCalendar(decidedDateList, disableDateList,disableMonthList);
//                 HideLoading();
//       },
//       error    : function(err){
//             console.log(err);
//             HideLoading();
//         }
//   });
    $.ajax({
      type     : "post",
      url      : "../../application/getData",
      data     : {_token: CSRF_TOKEN,message : "getDateFromBimCalendar"},
      success  : function(data){
                console.log("h" + JSON.stringify(data));
                $.each(data,function(key,value){
                    $.each(value,function(key, value) {
                        if(value['disableDate']){
                            var disableDate = value['disableDate'];
                            var disableDateArray = disableDate.split(",");
                            for(var i=0; i<disableDateArray.length ; i++){
                            if(!disableDateList.includes(disableDateArray[i].trim())){
                                disableDateList.push(disableDateArray[i].trim());
                            }
                            
                        }
                        }
                        if(value['disableMonth']){
                            var disableMonth = value['disableMonth'];
                            var dateArray = disableMonth.split(",");
                            for(var i=0; i<dateArray.length ; i++){
                            if(!disableMonthList.includes(dateArray[i].trim())){
                                disableMonthList.push(dateArray[i].trim());
                                
                            }
                            
                        }
                        }
                    })
                })
                console.log(disableDateList)
                console.log(disableMonthList)
                $("#hiddenDisableDateList").val(disableDateList.toString());
                ShowCalendar(disableDateList, disableMonthList);
                HideLoading();
      },
      error    : function(err){
            console.log(err);
            HideLoading();
        }
  });
}

function LoadDecidedDateList(){
    var loginUser = $("#hiddenLoginUser").val();
    var decidedDateList=[];
    $.ajax({
          type     : "post",
          url      : "../../application/getData",
          data     : {_token: CSRF_TOKEN,message : "getDateFromBimCourseInfo"},
          success  : function(data){
                    console.log("BimCourseInfo" + JSON.stringify(data));
                    $.each(data,function(key,value){
                        $.each(value,function(key, value) {
                            if(value['decidedDate']){
                                var decidedDate = value['decidedDate'];
                                var dateArray = decidedDate.split(",");
                                for(var i=0; i<dateArray.length ; i++){
                                if(!decidedDateList.includes(dateArray[i].trim())){
                                    decidedDateList.push(dateArray[i].trim());
                                    
                                }
                                
                            }
                            }
                        })
                    })
                    console.log(decidedDateList)
                    $("#hiddenDecidedDateList").val(decidedDateList.toString());
                   
          },
          error    : function(err){
                console.log(err);
               
            }
    }).done(function(){
       LoadSelectedCalendar();
    });
}
function ShowSuggestedData(){
    var mise = [];
    var obayashi =[];
    var hakenplace = [];
    var job =[];
    var applicants = $("#applicants").val();
    $.ajax({
        url: "../../application/getData",
        async:true,
        type: 'post',
        data:{_token: CSRF_TOKEN, message:"applicantsInfo"},
        success :function(data) {
            console.log(data)
            if(data){
                $.each(data, function(key,value){
                    $.each(value, function(tname, tdata){
                        if(tdata['place']){
                            if(!mise.includes(tdata['place'])){
                                 mise.push(tdata['place']);
                            }
                        }
                        if(tdata['obayashi']){
                            if(!obayashi.includes(tdata['obayashi'])){
                                 obayashi.push(tdata['obayashi']);
                            }
                        }
                        if(tdata['hakenplace']){
                            if(!hakenplace.includes(tdata['hakenplace'])){
                                 hakenplace.push(tdata['hakenplace']);
                            }
                        }
                        if(tdata['job']){
                            if(!job.includes(tdata['job'])){
                                 job.push(tdata['job']);
                            }
                        }
                    });
                    
                });
                console.log("Mise " + JSON.stringify(mise));
                
                
                    $("#place").autocomplete({
                        source: mise,
                        minLength:0
                    }).bind('focus', function(){ $(this).autocomplete("search"); } );
                    $("#obayashi").autocomplete({
                        source: obayashi,
                        minLength:0
                    }).bind('focus', function(){ $(this).autocomplete("search"); } );
                    $("#hakenplace").autocomplete({
                        source: hakenplace,
                        minLength:0
                    }).bind('focus', function(){ $(this).autocomplete("search"); } );
                    $("#job").autocomplete({
                        source: job,
                        minLength:0
                    }).bind('focus', function(){ $(this).autocomplete("search"); } );
                
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}