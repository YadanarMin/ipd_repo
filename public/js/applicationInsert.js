var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

var selectedDateAMPM;
$(document).ready(function() {
    $.ajaxSetup({
        cache: false
    });

    $("#disableMonthWarning").hide();
    $("#dialog").dialog({
        autoOpen: false,
    });
    //Check data from Edit Page
    var selectedDate = $("#hiddenSelectedDate1").html();
    if (selectedDate) {

        $("#hiddenSelectedDate").val(selectedDate);
        $("#calendar1").datepicker("option", "disabled", true);
    }


    //For Calendar
    LoadCalendar();




});

function SaveSessionAndGoToPage2() {
    var numOfApplicants = $("#applicants").val();
    var desireDate = $("#hiddenSelectedDate").val();
    var classType = $("input[type='radio']:checked").val();
    $.ajax({
        type: "post",
        url: "/iPD/application/saveInsertData",
        data: { _token: CSRF_TOKEN, message: "saveInsertData", numOfApplicants: numOfApplicants, desireDate: desireDate, classType: classType },
        success: function(data) {
            window.location.href = "/iPD/application/insert/page2"
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function ShowCalendar(disableDateList, disableMonthList, decidedDateList) {

    var result = [];
    var selectedDateList = [];
    var session = $("#hiddenSelectedDate").val();

    //DisableMonthWarning
    var date = new Date();
    var month = date.getMonth() + 1;
    if (month <= 9) {
        month = "0" + month;
    }
    var year = date.getFullYear();
    var currentMonthAndYear = month + "-" + year;
    if (disableMonthList.indexOf(currentMonthAndYear) >= 0) {
        $("#disableMonthWarning").show()
    }
    //DisableMonthWarning

    //pre selected date between page transfer
    if (session) {
        var dateArray = session.split(",");
        for (var i = 0; i < dateArray.length; i++) {
            selectedDateList.push(dateArray[i].trim());
        }
        $("#hiddenSelectedDate1").html(selectedDateList.toString());
        $("#calendar1").datepicker({
            dateFormat: 'yy-mm-dd',
            onChangeMonthYear: function(year, month) {
                if (month <= 9) {
                    month = "0" + month;
                }
                var onChangeYearAndMonth = month + "-" + year;
                if (disableMonthList.indexOf(onChangeYearAndMonth) >= 0) {
                    $("#disableMonthWarning").show();
                }
                else {
                    $("#disableMonthWarning").hide()
                }

            },
            onSelect: function(dateText, inst) {

                var date = $(this).val();
                var selectedDate = $(this).val();
                if ($("#popup").length) {
                    $("#popup").remove();
                }
                var dialog = "<div id='popup' style='border :1px solid black; height: 100px; padding:20px'>" +
                    "<label>" + date + "</label><br>" +
                    "<label>午前</label>" +
                    "<input type='checkbox' id='" + date + "AM' value='" + date +
                    "(AM)'" + ((selectedDateList.indexOf(date + '(AM)') >= 0 ? 'checked' : ' ')) +
                    ((decidedDateList.indexOf(date + '(AM)') >= 0 || disableDateList.indexOf(date + '(AM)') >= 0 ? 'disabled' : ' ')) + "/>&nbsp;" +
                    "<label>午後</label>" +
                    "<input type='checkbox' id='" + date + "PM' value='" + date +
                    "(PM)'" + ((selectedDateList.indexOf(date + '(PM)') >= 0 ? 'checked' : ' ')) +
                    ((decidedDateList.indexOf(date + '(PM)') >= 0 || disableDateList.indexOf(date + '(PM)') >= 0 ? 'disabled' : ' ')) + "/>" +
                    "</div>";
                //$(".calendarAndWarining").append(dialog);
                $(dialog).insertBefore("#disableMonthWarning");

                $("#" + date + "AM").on('change', function() {
                    if ($("#" + date + "AM").is(":checked")) {
                        var value = $("#" + date + "AM").val();
                        if (selectedDateList.indexOf(value) < 0) {
                            selectedDateList.push(value);
                        }
                        $("#hiddenSelectedDate1").html(selectedDateList.toString());
                        $("#hiddenSelectedDate").val(selectedDateList.toString())
                    }
                    else {
                        var index = selectedDateList.indexOf($("#" + date + "AM").val());
                        selectedDateList.splice(index, 1);
                        $("#hiddenSelectedDate1").html(selectedDateList.toString());
                        $("#hiddenSelectedDate").val(selectedDateList.toString())
                    }

                });
                $("#" + date + "PM").on('change', function() {
                    if ($("#" + date + "PM").is(":checked")) {
                        var value = $("#" + date + "PM").val();
                        if (selectedDateList.indexOf(value) < 0) {
                            selectedDateList.push(value);
                        }
                        $("#hiddenSelectedDate1").html(selectedDateList.toString());
                        $("#hiddenSelectedDate").val(selectedDateList.toString())
                    }
                    else {
                        var index = selectedDateList.indexOf($("#" + date + "PM").val());
                        selectedDateList.splice(index, 1);
                        $("#hiddenSelectedDate1").html(selectedDateList.toString());
                        $("#hiddenSelectedDate").val(selectedDateList.toString())
                    }

                });
            },

            beforeShowDay: function(date) {
                if (date < new Date()) {
                    return [false];
                }
                var m;
                var y;
                var year = date.getFullYear();
                var month = date.getMonth() + 1;
                if (month <= 9)
                    month = '0' + month;
                var day = date.getDate();
                if (day <= 9)
                    day = '0' + day;

                var prettyDate = year + '-' + month + '-' + day;
                var AM = prettyDate + "(AM)";
                var PM = prettyDate + "(PM)";
                for (var i = 0; i < disableMonthList.length; i++) {
                    var monthAndYear = disableMonthList[i];
                    m = monthAndYear.substring(0, monthAndYear.indexOf('-'));
                    y = monthAndYear.substring(monthAndYear.indexOf('-') + 1);

                    if (month == m && year == y) {
                        return [false];
                    }
                }


                if (prettyDate == new Date().toJSON().slice(0, 10).replace(/-/g, '-')) {
                    return [true, 'today'];
                }

                if (disableDateList.indexOf(AM) >= 0 && disableDateList.indexOf(PM) >= 0) {
                    return [false];
                }
                else {
                    return [true];
                }


            }
        });
    }
    else {
        // pre loaded calendar with disable date
        $("#calendar1").datepicker({
            dateFormat: 'yy-mm-dd',
            onChangeMonthYear: function(year, month) {

                if (month <= 9) {
                    month = "0" + month;
                }
                var onChangeYearAndMonth = month + "-" + year;
                if (disableMonthList.indexOf(onChangeYearAndMonth) >= 0) {
                    $("#disableMonthWarning").show();
                }
                else {
                    $("#disableMonthWarning").hide()
                }
            },
            onSelect: function(dateText, inst) {

                var date = $(this).val();
                var selectedDate = $(this).val();
                if ($("#popup").length) {
                    $("#popup").remove();
                }
                var dialog = "<div id='popup' style='border :1px solid black; height: 100px; padding:20px'>" +
                    "<label>" + date + "</label><br>" +
                    "<label>午前</label>" +
                    "<input type='checkbox' id='" + date + "AM' value='" + date +
                    "(AM)'" + ((selectedDateList.indexOf(date + '(AM)') >= 0 ? 'checked' : ' ')) +
                    ((decidedDateList.indexOf(date + '(AM)') >= 0 || disableDateList.indexOf(date + '(AM)') >= 0 ? 'disabled' : ' ')) + "/>&nbsp;" +
                    "<label>午後</label>" +
                    "<input type='checkbox' id='" + date + "PM' value='" + date +
                    "(PM)'" + ((selectedDateList.indexOf(date + '(PM)') >= 0 ? 'checked' : ' ')) +
                    ((decidedDateList.indexOf(date + '(PM)') >= 0 || disableDateList.indexOf(date + '(PM)') >= 0 ? 'disabled' : ' ')) + "/>" +
                    "</div>";
                //$(".calendarAndWarining").append(dialog);
                $(dialog).insertBefore("#disableMonthWarning");

                $("#" + date + "AM").on('change', function() {
                    if ($("#" + date + "AM").is(":checked")) {
                        var value = $("#" + date + "AM").val();
                        if (selectedDateList.indexOf(value) < 0) {
                            selectedDateList.push(value);
                        }
                        $("#hiddenSelectedDate1").html(selectedDateList.toString());
                        $("#hiddenSelectedDate").val(selectedDateList.toString())
                    }
                    else {
                        var index = selectedDateList.indexOf($("#" + date + "AM").val());
                        selectedDateList.splice(index, 1);
                        $("#hiddenSelectedDate1").html(selectedDateList.toString());
                        $("#hiddenSelectedDate").val(selectedDateList.toString())
                    }

                });
                $("#" + date + "PM").on('change', function() {
                    if ($("#" + date + "PM").is(":checked")) {
                        var value = $("#" + date + "PM").val();
                        if (selectedDateList.indexOf(value) < 0) {
                            selectedDateList.push(value);
                        }
                        $("#hiddenSelectedDate1").html(selectedDateList.toString());
                        $("#hiddenSelectedDate").val(selectedDateList.toString())
                    }
                    else {
                        var index = selectedDateList.indexOf($("#" + date + "PM").val());
                        selectedDateList.splice(index, 1);
                        $("#hiddenSelectedDate1").html(selectedDateList.toString());
                        $("#hiddenSelectedDate").val(selectedDateList.toString())
                    }

                });
            },
            beforeShowDay: function(date) {

                if (date < new Date()) {
                    return [false];
                }
                var year = date.getFullYear();

                var month = date.getMonth() + 1;
                if (month <= 9)
                    month = '0' + month;

                var day = date.getDate();
                if (day <= 9)
                    day = '0' + day;

                var prettyDate = year + '-' + month + '-' + day;
                var AM = prettyDate + "(AM)";
                var PM = prettyDate + "(PM)";
                for (var i = 0; i < disableMonthList.length; i++) {
                    var monthAndYear = disableMonthList[i];
                    m = monthAndYear.substring(0, monthAndYear.indexOf('-'));
                    y = monthAndYear.substring(monthAndYear.indexOf('-') + 1);

                    if (month == m && year == y) {
                        return [false];
                    }
                }
                if (prettyDate == new Date().toJSON().slice(0, 10).replace(/-/g, '-')) {
                    return [true, 'today'];
                }
                if (disableDateList.indexOf(AM) >= 0 && disableDateList.indexOf(PM) >= 0) {
                    return [false];
                }
                else {
                    return [true];
                }

                // if(decidedDateList.indexOf(prettyDate) >= 0 || disableDateList.indexOf(prettyDate) >= 0){
                //   return [false, '', 'Unavailable'];
                // } else {
                //     return [true];
                // }      
            }
        });

    }
}


function LoadCalendar() {
    ShowLoading();
    var decidedDateList = [];
    var disableDateList = [];
    var disableMonthList = [];
    $.ajax({
        type: "post",
        url: "/iPD/application/getData",
        data: { _token: CSRF_TOKEN, message: "getDateFromBimCalendar" },
        success: function(data) {
            console.log("h" + JSON.stringify(data));
            $.each(data, function(key, value) {
                $.each(value, function(key, value) {
                    if (value['disableDate']) {
                        var disableDate = value['disableDate'];
                        var disableDateArray = disableDate.split(",");
                        for (var i = 0; i < disableDateArray.length; i++) {
                            if (!disableDateList.includes(disableDateArray[i].trim())) {
                                disableDateList.push(disableDateArray[i].trim());
                            }

                        }
                    }
                    if (value['disableMonth']) {
                        var disableMonth = value['disableMonth'];
                        var dateArray = disableMonth.split(",");
                        for (var i = 0; i < dateArray.length; i++) {
                            if (!disableMonthList.includes(dateArray[i].trim())) {
                                disableMonthList.push(dateArray[i].trim());

                            }

                        }
                    }
                })
            })

        },
        error: function(err) {
            console.log(err);
            HideLoading();
        }
    }).done(function() {
        $.ajax({
            type: "post",
            url: "/iPD/application/getData",
            data: { _token: CSRF_TOKEN, message: "getDateFromBimCourseInfo" },
            success: function(data) {
                console.log("h" + JSON.stringify(data));
                $.each(data, function(key, value) {
                    $.each(value, function(key, value) {
                        if (value['decidedDate']) {
                            var decidedDate = value['decidedDate'];
                            var dateArray = decidedDate.split(",");
                            for (var i = 0; i < dateArray.length; i++) {
                                if (!decidedDateList.includes(dateArray[i].trim())) {
                                    decidedDateList.push(dateArray[i].trim());

                                }

                            }
                        }

                    })
                })
                console.log(disableDateList)
                console.log(disableMonthList)
                console.log(decidedDateList)
                ShowCalendar(disableDateList, disableMonthList, decidedDateList);
                HideLoading();
            },
            error: function(err) {
                console.log(err);
                HideLoading();
            }
        });
    });
}




// <!--function myCheck(date){-->
// <!--    var year = date.getFullYear();-->

// <!--    var month = date.getMonth() + 1;-->
// <!--    if(month <= 9)-->
// <!--        month = '0'+month;-->

// <!--    var day= date.getDate();-->
// <!--    if(day <= 9)-->
// <!--        day = '0'+day;-->

// <!--    var prettyDate = year +'-'+ month +'-'+ day;-->
// <!--    console.log(result)-->
// <!--    var r =  ["2021-05-27", "2021-05-29"];-->
// <!--    if (result.indexOf(prettyDate) >= 0) {-->
// <!--        return [true, ''];-->
// <!--    } else {-->
// <!--        return [true];-->
// <!--    }-->



// <!--}-->

// function LoadCalendar(){
//     ShowLoading();
//     var decidedDateList = [];
//     var disableDateList = [];
//     var disableMonthList = [];
//     $.ajax({
//       type     : "post",
//       url      : "../application/getData",
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
//                             var dateArray = disableDate.split(",");
//                             for(var i=0; i<dateArray.length ; i++){
//                             if(!disableDateList.includes(dateArray[i].trim())){
//                                 disableDateList.push(dateArray[i].trim());
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
//                 console.log(disableMonthList)
//                 ShowCalendar(decidedDateList, disableDateList, disableMonthList);
//                 HideLoading();
//       },
//       error    : function(err){
//             console.log(err);
//             HideLoading();
//         }
//   });
// }
