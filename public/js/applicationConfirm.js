var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');


var showMonth;
$(document).ready(function() {
    $.ajaxSetup({
        cache: false
    });
    $("#warningDiv").hide();

    //For Disable Month
    var date = new Date();
    var currentMonth = date.getMonth() + 1;
    showMonth = currentMonth;
    if (currentMonth <= 9) {
        currentMonth = '0' + currentMonth;
    }
    var currentYear = date.getFullYear();
    $("#hiddenSelectedMonth").val(currentMonth + "-" + currentYear);


    //For Calendar
    LoadCalendar();

})
//********Custom Key Press Event***********//


//********Custom Key Press Event***********//

function ShowCalendar(disableDateList, disableMonthList, desireDateList, decidedDateList) {

    //DisableMonthWarning
    var date = new Date();
    var curmonth = date.getMonth() + 1;
    if (curmonth <= 9) {
        curmonth = "0" + curmonth;
    }
    var curyear = date.getFullYear();
    var currentMonthAndYear = curmonth + "-" + curyear;
    if (disableMonthList.indexOf(currentMonthAndYear) >= 0) {
        $("#warningDiv").show()
    }
    //DisableMonthWarning

    $("#editCalendar").datepicker({
        dateFormat: 'yy-mm-dd',
        multidate: true,
        onChangeMonthYear: function(year, month) {
            showMonth = month;
            if (month <= 9) {
                month = "0" + month;
            }
            var thisMonth = month + "-" + year;
            $('#hiddenSelectedMonth').val(thisMonth);
            if (disableMonthList.indexOf(thisMonth) >= 0) {
                $("#warningDiv").show();
            }
            else {
                $("#warningDiv").hide();
            }
        },
        onSelect: function(dateText, inst) {
            var selectedDate = $(this).val();
            var viewDate = $.datepicker.formatDate('yy年mm月dd日(D)', new Date($(this).val()));
            $("#hiddenSelectedDate").val(selectedDate);
            ShowSelectedUserInfo(selectedDate, viewDate);
            $(this).bind()

        },
        beforeShowDay: function(date) {
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



            //   if(decidedDateList.indexOf(prettyDate) >= 0){
            //       return [true, 'myhighlightDecided', '決定日'];
            //   }
            //Change Disable Date Color
            if (disableDateList.indexOf(AM) >= 0 && disableDateList.indexOf(PM) >= 0) {
                return [true, 'myhighlightDisable', 'NG日'];
            }
            if ((decidedDateList.indexOf(AM) >= 0 && decidedDateList.indexOf(PM) >= 0) ||
                (decidedDateList.indexOf(AM) >= 0 && disableDateList.indexOf(PM) >= 0) ||
                (disableDateList.indexOf(AM) >= 0 && decidedDateList.indexOf(PM) >= 0)
            ) {
                return [true, 'myhighlightDecided', '決定日'];
            }
            else if ((desireDateList.indexOf(AM) >= 0 && desireDateList.indexOf(PM) >= 0) ||
                (desireDateList.indexOf(AM) >= 0 && decidedDateList.indexOf(PM) >= 0) ||
                (decidedDateList.indexOf(AM) >= 0 && desireDateList.indexOf(PM) >= 0) ||
                (desireDateList.indexOf(AM) >= 0 && disableDateList.indexOf(PM) >= 0) ||
                (disableDateList.indexOf(AM) >= 0 && desireDateList.indexOf(PM) >= 0) ||
                desireDateList.indexOf(AM) >= 0 ||
                desireDateList.indexOf(PM) >= 0)

            {
                if ((decidedDateList.indexOf(AM) >= 0 && desireDateList.indexOf(AM) >= 0) ||
                    (decidedDateList.indexOf(PM) >= 0 && desireDateList.indexOf(PM) >= 0)) {
                    if ((desireDateList.indexOf(AM) >= 0 && decidedDateList.indexOf(PM) >= 0) ||
                        (decidedDateList.indexOf(AM) >= 0 && desireDateList.indexOf(PM) >= 0)) {
                        return [true, 'myhighlight'];
                    }
                    else {
                        return [true, 'myhighlightDecided', '決定日'];
                    }

                }
                else {
                    return [true, 'myhighlight'];
                }

            }
            else if (decidedDateList.indexOf(AM) >= 0 || decidedDateList.indexOf(PM) >= 0) {
                return [true, 'myhighlightDecided', '決定日'];
            }
            else if (prettyDate == new Date().toJSON().slice(0, 10).replace(/-/g, '-')) {
                return [true, 'today'];
            }
            else {
                return [true];
            }

            //Change Today Color
            // if(prettyDate == new Date().toJSON().slice(0,10).replace(/-/g,'-')){
            //   return [true, 'today'];
            // }
        }
    });
}

function ShowSelectedUserInfo(selectedDate, viewDate) {
    ShowLoading();
    var disableDateList = [];
    var decidedDateList = [];
    var desireDateList = [];
    $("#selectedDateAM").html(viewDate);
    $("#selectedDatePM").html(viewDate);
    var disableDateListStr = $("#hiddenDisableDateList").val();
    var decidedDateListStr = $("#hiddenDecidedDateList").val();

    disableDateList = disableDateListStr.split(",");
    decidedDateList = decidedDateListStr.split(",");
    console.log("List" + disableDateList)
    console.log("List" + decidedDateList)
    $.ajax({
        type: "post",
        url: "../application/getData",
        data: { _token: CSRF_TOKEN, message: "getUserInfoBySelectedDate", selectedDate: selectedDate },
        success: function(data) {
            console.log("Data" + JSON.stringify(data, null, 2));

            var resultSet = data['UserInfoBySelectedDate'].length;
            console.log(resultSet)
            var rowAM = "";
            var rowPM = "";
            var btnAM = "";
            var btnPM = "";
            var labelAM = "";
            var labelPM = "";
            var rowCount = 0;
            var rowAMCount = 0;
            var rowPMCount = 0;
            if (resultSet) {
                $.each(data, function(key, value) {
                    $.each(value, function(key, value) {
                        var classType;
                        if (value['classType']) {
                            if (value['classType'] == 1) {
                                classType = "オンライン";
                            }
                            else if (value['classType'] == 2) {
                                classType = "対面(大阪)";
                            }
                            else if (value['classType'] == 3) {
                                classType = "対面（その他）";
                            }
                        }

                        //Show Table
                        var desireDate = value['desireDate'];
                        var a = desireDate.split(",");
                        for (var i = 0; i < a.length; i++) {
                            if (desireDateList.indexOf(a[i]) < 0) {
                                desireDateList.push(a[i]);
                            }
                        }
                        console.log("DesireDate" + desireDateList);
                        if (desireDate.indexOf(selectedDate + "(AM)") >= 0 && desireDate.indexOf(selectedDate + "(PM)") >= 0) {
                            var dataAM = selectedDate + "(AM)";
                            rowAM += "<tr>" +
                                "<td>" + value['firstName'] + " " + value['lastName'] + "</td>" +
                                "<td>" + (value['companyType'] ? value['companyType'] : ' ') + "</td>" +
                                "<td>" + (value['company'] ? value['company'] : ' ') + "</td>" +
                                "<td>" + (value['dept'] ? value['dept'] : ' ') + "</td>" +
                                "<td>" + (value['branch'] ? value['branch'] : ' ') + "</td>" +
                                "<td>" + (value['code'] ? value['code'] : ' ') + "</td>" +
                                "<td>" + (value['position'] ? value['position'] : ' ') + "</td>" +
                                "<td>" + (value['mail'] ? value['mail'] : ' ') + "</td>" +
                                "<td>" + (value['inviter'] ? value['inviter'] : ' ') + "</td>" +
                                "<td>" + classType + "</td>" +
                                "<td>" + (value['desireDate'] ? value['desireDate'] : ' ') + "</td>" +
                                "</tr>";
                            rowAMCount++;
                            var dataPM = selectedDate + "(PM)";
                            rowPM += "<tr>" +
                                "<td>" + value['firstName'] + " " + value['lastName'] + "</td>" +
                                "<td>" + (value['companyType'] ? value['companyType'] : ' ') + "</td>" +
                                "<td>" + (value['company'] ? value['company'] : ' ') + "</td>" +
                                "<td>" + (value['dept'] ? value['dept'] : ' ') + "</td>" +
                                "<td>" + (value['branch'] ? value['branch'] : ' ') + "</td>" +
                                "<td>" + (value['code'] ? value['code'] : ' ') + "</td>" +
                                "<td>" + (value['position'] ? value['position'] : ' ') + "</td>" +
                                "<td>" + (value['mail'] ? value['mail'] : ' ') + "</td>" +
                                "<td>" + (value['inviter'] ? value['inviter'] : ' ') + "</td>" +
                                "<td>" + classType + "</td>" +
                                "<td>" + (value['desireDate'] ? value['desireDate'] : ' ') + "</td>" +
                                "</tr>";
                            rowPMCount++;
                        }
                        else if (desireDate.indexOf(selectedDate + "(AM)") >= 0) {
                            var dataAM = selectedDate + "(AM)";
                            rowAM += "<tr>" +
                                "<td>" + value['firstName'] + " " + value['lastName'] + "</td>" +
                                "<td>" + (value['companyType'] ? value['companyType'] : ' ') + "</td>" +
                                "<td>" + (value['company'] ? value['company'] : ' ') + "</td>" +
                                "<td>" + (value['dept'] ? value['dept'] : ' ') + "</td>" +
                                "<td>" + (value['branch'] ? value['branch'] : ' ') + "</td>" +
                                "<td>" + (value['code'] ? value['code'] : ' ') + "</td>" +
                                "<td>" + (value['position'] ? value['position'] : ' ') + "</td>" +
                                "<td>" + (value['mail'] ? value['mail'] : ' ') + "</td>" +
                                "<td>" + (value['inviter'] ? value['inviter'] : ' ') + "</td>" +
                                "<td>" + classType + "</td>" +
                                "<td>" + (value['desireDate'] ? value['desireDate'] : ' ') + "</td>" +
                                "</tr>";
                            rowAMCount++;

                        }
                        else if (desireDate.indexOf(selectedDate + "(PM)") >= 0) {
                            var dataPM = selectedDate + "(PM)";
                            rowPM += "<tr>" +
                                "<td>" + value['firstName'] + " " + value['lastName'] + "</td>" +
                                "<td>" + (value['companyType'] ? value['companyType'] : ' ') + "</td>" +
                                "<td>" + (value['company'] ? value['company'] : ' ') + "</td>" +
                                "<td>" + (value['dept'] ? value['dept'] : ' ') + "</td>" +
                                "<td>" + (value['branch'] ? value['branch'] : ' ') + "</td>" +
                                "<td>" + (value['code'] ? value['code'] : ' ') + "</td>" +
                                "<td>" + (value['position'] ? value['position'] : ' ') + "</td>" +
                                "<td>" + (value['mail'] ? value['mail'] : ' ') + "</td>" +
                                "<td>" + (value['inviter'] ? value['inviter'] : ' ') + "</td>" +
                                "<td>" + classType + "</td>" +
                                "<td>" + (value['desireDate'] ? value['desireDate'] : ' ') + "</td>" +
                                "</tr>";
                            rowPMCount++;

                        }
                        //Show Table
                    })
                })

            }
            else {
                var paramAM = selectedDate + "(AM)";
                var paramPM = selectedDate + "(PM)";
                btnAM = "<button class='btn btn-danger' onclick='DisableDate(\"" + paramAM + "\")'>NG日にする</button>";
                btnPM = "<button class='btn btn-danger' onclick='DisableDate(\"" + paramPM + "\")'>NG日にする</button>";

            }
            console.log("DesireDate" + desireDateList);
            $("#selectedUserInfoTableAM tbody").empty();
            $("#selectedUserInfoTablePM tbody").empty();
            $("#selectedUserInfoTableAM tbody").append(rowAM);
            $("#selectedUserInfoTablePM tbody").append(rowPM);
            $("#numOfApplicantsAM").html(rowAMCount);
            $("#numOfApplicantsPM").html(rowPMCount);

            //Button Display
            var checkAM = selectedDate + "(AM)";
            var checkPM = selectedDate + "(PM)";

            if (decidedDateList.indexOf(checkAM) >= 0) {
                btnAM = "<h5 style='font-size:15px; color:green;font-weight:bold;margin:0'>決定日</h5>";
                btnAM += "<button class='btn btn-primary' onclick='ClearDecidedDate(\"" + checkAM + "\")'>クリア</button>";
            }
            else if (desireDateList.indexOf(checkAM) >= 0) {
                btnAM = "<button style='margin-bottom: 7px' class='btn btn-success' onclick='DecideDate(\"" + checkAM + "\")'>決定</button><br>" +
                    "<button class='btn btn-primary' onclick='ClearDesireDate(\"" + checkAM + "\")'>クリア</button>";
            }
            else if (disableDateList.indexOf(checkAM) >= 0) {
                btnAM = "<h5 style='font-size:15px; color:red;font-weight:bold;margin:0'>NG日</h5>";
                btnAM += "<button class='btn btn-primary' onclick='ClearDisableDate(\"" + checkAM + "\")'>クリア</button>";
            }
            else {
                btnAM = "<button class='btn btn-danger' onclick='DisableDate(\"" + checkAM + "\")'>NG日にする</button>";
            }

            //PM
            if (decidedDateList.indexOf(checkPM) >= 0) {
                btnPM = "<h5 style='font-size:15px; color:green;font-weight:bold;margin:0'>決定日</h5>";
                btnPM += "<button class='btn btn-primary' onclick='ClearDecidedDate(\"" + checkPM + "\")'>クリア</button>";

            }
            else if (desireDateList.indexOf(checkPM) >= 0) {
                btnPM = "<button style='margin-bottom: 7px' class='btn btn-success' onclick='DecideDate(\"" + checkPM + "\")'>決定</button><br>" +
                    "<button class='btn btn-primary' onclick='ClearDesireDate(\"" + checkPM + "\")'>クリア</button>";
            }
            else if (disableDateList.indexOf(checkPM) >= 0) {
                btnPM = "<h5 style='font-size:15px; color:red;font-weight:bold;margin:0'>NG日</h5>";
                btnPM += "<button class='btn btn-primary' onclick='ClearDisableDate(\"" + checkPM + "\")'>クリア</button>";
            }
            else {
                btnPM = "<button class='btn btn-danger' onclick='DisableDate(\"" + checkPM + "\")'>NG日にする</button>";
            }
            $(".updateDateBtnAM").empty();
            $(".updateDateBtnPM").empty();
            $(".updateDateBtnAM").append(btnAM);
            $(".updateDateBtnPM").append(btnPM);
            HideLoading();



        },
        error: function(err) {
            console.log(err);
        }
    });
}

//希望日と決定日(tb-bimcourse-info)、NG日と今月しめぎり(tb-bimcourse-calendar)
function LoadCalendar() {
    ShowLoading();
    var desireDateList = [];
    var decidedDateList = [];
    var disableDateList = [];
    var disableMonthList = [];
    //     $.ajax({
    //       type     : "post",
    //       url      : "../application/getData",
    //       data     : {_token: CSRF_TOKEN,message : "getDate"},
    //       success  : function(data){
    //                 console.log("h" + JSON.stringify(data));
    //                 $.each(data,function(key,value){
    //                     $.each(value,function(key, value) {
    //                         if(value['desireDate']){
    //                             var desireDate = value['desireDate'];
    //                             var dateArray = desireDate.split(",");
    //                             for(var i=0; i<dateArray.length ; i++){
    //                             if(!desireDateList.includes(dateArray[i].trim())){
    //                                 desireDateList.push(dateArray[i].trim());
    //                             }

    //                         }
    //                         }
    //                         if(value['decidedDate']){
    //                             var decidedDate = value['decidedDate'];
    //                             var decidedDateArray = decidedDate.split(",");
    //                             for(var i=0; i<decidedDateArray.length ; i++){
    //                                 if(!decidedDateList.includes(decidedDateArray[i].trim())){
    //                                     decidedDateList.push(decidedDateArray[i].trim());
    //                                 }

    //                             }
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
    //                 console.log(desireDateList)
    //                 console.log(decidedDateList)
    //                 console.log(disableDateList)
    //                 console.log(disableMonthList)
    //                 ShowCalendar(desireDateList, decidedDateList, disableMonthList);
    //                 HideLoading();
    //       },
    //       error    : function(err){
    //             console.log(err);
    //             HideLoading();
    //         }
    //   });
    $.ajax({
        type: "post",
        url: "../application/getData",
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
            url: "../application/getData",
            data: { _token: CSRF_TOKEN, message: "getDateFromBimCourseInfo" },
            success: function(data) {
                console.log("BimCourseInfo" + JSON.stringify(data));
                $.each(data, function(key, value) {
                    $.each(value, function(key, value) {
                        if (value['desireDate']) {
                            var desireDate = value['desireDate'];
                            var desireDateArray = desireDate.split(",");
                            for (var i = 0; i < desireDateArray.length; i++) {
                                if (!desireDateList.includes(desireDateArray[i].trim())) {
                                    desireDateList.push(desireDateArray[i].trim());
                                }

                            }
                        }
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
                console.log(desireDateList)
                console.log(decidedDateList)
                $("#hiddenDisableDateList").val(disableDateList.toString());
                $("#hiddenDecidedDateList").val(decidedDateList.toString());
                ShowCalendar(disableDateList, disableMonthList, desireDateList, decidedDateList);
                HideLoading();
            },
            error: function(err) {
                console.log(err);
                HideLoading();
            }
        })
    });
    //Get Desiredate and decidedDate by LoginUser
}



function searchPlace() {
    var idList = [];
    var miseList = [];
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();
    console.log(startDate);
    console.log(endDate);
    $.ajax({
        type: "post",
        url: "../application/getData",
        data: { _token: CSRF_TOKEN, message: "searchMise", startDate: startDate, endDate: endDate },
        success: function(data) {
            console.log(data)

            if (data.length > 0) {
                $.each(data, function(key, value) {
                    console.log(value);
                    miseList.push(value);
                    //return;
                    // $.each(value, function(key, value) {
                    //     if(value['id']){
                    //         if(!idList.includes(value['id'])){
                    //             idList.push(value['id']);
                    //             if(value['place']){
                    //                 miseList.push(value['place']);
                    //             }

                    //         }
                    //     }
                    // })
                })
            }
            else {
                alert("No data");
                location.reload()
            }

            console.log(idList)
            console.log(miseList)
            ShowMiseList(miseList);

        },
        error: function(err) {
            console.log(err);
        }

    });

}

function ShowMiseList(miseList) {
    var li = '';
    var place = {};
    for (const key of miseList) {
        if (key in place) {
            place[key]++;
        }
        else {
            place[key] = 1;
        }
    }
    console.log(place)
    for (const key in place) {
        console.log(key)
        if (key) {
            li += "<li class='list-group-item'>" + key + "<span class='badge'>" + place[key] + "</span></li>";
        }


    }
    $("#listOfMise").empty();
    $("#listOfMise").append(li);


}

function CloseApplicationForMonth() {
    var disableMonth = $('#hiddenSelectedMonth').val();
    console.log(disableMonth)
    $.ajax({
        type: "post",
        url: "../applicationConfirm/update",
        data: { _token: CSRF_TOKEN, message: "updateDisableMonth", disableMonth: disableMonth },
        success: function(data) {
            console.log("h" + JSON.stringify(data));
            // $("#warning").html(showMonth+"月の受付を締め切りました。")
            // $("#warning").show();
            alert(showMonth + "月の受付を締め切りました。")
            window.location = "../applicationConfirm/edit";
        },
        error: function(err) {
            console.log(err);
        }
    });

}

function ReopenApplicationForMonth() {
    var disableMonth = $('#hiddenSelectedMonth').val();
    console.log(disableMonth)
    $.ajax({
        type: "post",
        url: "../applicationConfirm/update",
        data: { _token: CSRF_TOKEN, message: "deleteDisableMonth", disableMonth: disableMonth },
        success: function(data) {
            console.log("h" + JSON.stringify(data));
            // $("#warning").html(showMonth+"月の受付締め切りを解除しました。")
            // $("#warning").show();
            alert(showMonth + "月の受付締め切りを解除しました。")
            window.location = "../applicationConfirm/edit";
        },
        error: function(err) {
            console.log(err);
        }
    });
}



//NG日にするボタン
function DisableDate(param) {
    var confirmResult = confirm("NG日にしますか。");
    if (confirmResult) {
        var ngday = param;
        $.ajax({
            type: "post",
            url: "../applicationConfirm/update",
            data: { _token: CSRF_TOKEN, message: "updateDisableDate", disableDate: ngday },
            success: function(data) {
                console.log("h" + JSON.stringify(data));
                window.location = "../applicationConfirm/edit";
            },
            error: function(err) {
                console.log(err);
            }
        });
    }
    else {

    }
}

//決定日にするボタン
function DecideDate(param) {
    var decidedDate = param;
    console.log(decidedDate)
    var confirmResult = confirm("決定日にしますか。");
    if (confirmResult) {
        $.ajax({
            type: "post",
            url: "../applicationConfirm/update",
            data: { _token: CSRF_TOKEN, message: "updateDecidedDate", decidedDate: decidedDate },
            success: function(data) {
                console.log("h" + JSON.stringify(data));
                window.location = "../applicationConfirm/edit"
            },
            error: function(err) {
                console.log(err);
            }
        });
    }
    else {

    }

}

//希望日クリアボタン
function ClearDesireDate(param) {
    var confirmResult = confirm("この希望日を消してよろしいですか。");
    if (confirmResult) {
        var cleardesiredate = param;
        $.ajax({
            type: "post",
            url: "../applicationConfirm/update",
            data: { _token: CSRF_TOKEN, message: "clearDesireDate", clearDate: cleardesiredate },
            success: function(data) {
                console.log("h" + JSON.stringify(data));
                window.location = "../applicationConfirm/edit";
            },
            error: function(err) {
                console.log(err);
            }
        });
    }
    else {

    }
}

//NG日クリアボタン
function ClearDisableDate(param) {
    var confirmResult = confirm("このNG日を消してよろしいですか。");
    if (confirmResult) {
        var ngday = param;
        $.ajax({
            type: "post",
            url: "../applicationConfirm/update",
            data: { _token: CSRF_TOKEN, message: "deleteDisableDate", disableDate: ngday },
            success: function(data) {
                console.log("h" + JSON.stringify(data));
                window.location = "../applicationConfirm/edit";
            },
            error: function(err) {
                console.log(err);
            }
        });
    }
    else {

    }
}

function ClearDecidedDate(param) {
    var confirmResult = confirm("この決定日を消してよろしいですか。");
    if (confirmResult) {
        var decidedDate = param;
        console.log(decidedDate);
        $.ajax({
            type: "post",
            url: "../applicationConfirm/update",
            data: { _token: CSRF_TOKEN, message: "deleteDecidedDate", decidedDate: decidedDate },
            success: function(data) {
                console.log("h" + JSON.stringify(data));
                window.location = "../applicationConfirm/edit";
            },
            error: function(err) {
                console.log(err);
            }
        });
    }
    else {

    }
}

function excelExport() {
    console.log("Excel Export Starting");
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();
    if (startDate && endDate) {
        $.ajax({
            type: "post",
            url: "../application/getData",
            data: { _token: CSRF_TOKEN, message: "excelExportData", startDate: startDate, endDate: endDate },
            success: function(data) {
                var row = "";
                var gridData = [];
                $.each(data, function(key, value) {
                    $.each(value, function(key, value) {
                        var classType;
                        if (value['classType']) {
                            if (value['classType'] == 1) {
                                classType = "オンライン";
                            }
                            else if (value['classType'] == 2) {
                                classType = "対面(大阪)";
                            }
                            else if (value['classType'] == 3) {
                                classType = "対面（その他）";
                            }
                        }
                        var name = value['firstName'] + " " + value['lastName'];
                        var companyType = value['companyTypeName'];
                        var company = value['companyName'];
                        var dept = value['dept'];
                        var branch = value['branch'];
                        var code = value['code'];
                        var position = value['position'];
                        var mail = value['mail'];
                        var inviter = value['inviter'];
                        var dateStr = value['date'];
                        var dateArray = dateStr.split(",");
                        for (var i = 0; i < dateArray.length; i++) {
                            var date = dateArray[i];
                            var obj = {
                                日付　: date,
                                氏名　: name,
                                企業名: companyType,
                                会社名: company,
                                所属　: dept,
                                支店　: branch,
                                社員コード　: code,
                                役職　: position,
                                メールアドレス　: mail,
                                招待者　: inviter,
                                講習形態　: classType

                            }
                            gridData.push(obj);
                        }
                    })
                });
                gridData.sort(function(a, b) {
                    var keyA = new Date(a['日付']),
                        keyB = new Date(b['日付']);
                    // Compare the 2 dates
                    if (keyA < keyB) return -1;
                    if (keyA > keyB) return 1;
                    return 0;
                });

                console.log(gridData);
                // ********* XLS Export ******************
                const xls = new XlsExport(gridData, "受講者情報");
                xls.exportToXLS('export.xls');
                // ********* XLS Export ******************

                // ********* XLSX Export ******************
                // var wb = XLSX.utils.book_new();
                // wb.Props = {
                //         Title: "受講者一覧"
                // };

                // wb.SheetNames.push("Sheet1");
                // var ws_data = [gridData];
                // var ws = XLSX.utils.aoa_to_sheet(ws_data);
                // wb.Sheets["Sheet1"] = ws;
                // var wbout = XLSX.write(wb, {bookType:'xlsx',  type: 'binary'});
                // function s2ab(s) {

                //     var buf = new ArrayBuffer(s.length);
                //     var view = new Uint8Array(buf);
                //     for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
                //     return buf;

                // }
                // saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), 'test.xlsx');
                // ********* XLSX Export ******************
                // $.each(data,function(key, value) {
                //     $.each(value,function(key, value) {
                //         row+="<tr>"+
                //              "<td>"+  value["date"]  + "</td>"+
                //              "<td>"+  value["firstName"] + " "+  value["lastName"] + "</td>"+
                //              "<td>"+  value["companyTypeName"] + "</td>"+
                //              "<td>"+  value["companyName"]  + "</td>"+
                //              "<td>"+  value["dept"]  + "</td>"+
                //              "<td>"+  value["branch"] + "</td>"+
                //              "<td>"+  value["code"]  + "</td>"+
                //              "<td>"+  value["position"]  + "</td>"+
                //              "<td>"+  value["mail"]  + "</td>"+
                //                 "</tr>";

                //     });
                //     $("#exportDataTable tbody").empty();
                //     $("#exportDataTable tbody").append(row);

                // })
            },
            error: function(err) {
                console.log(err);
            }
        });
    }
    else {
        console.log("Nothing");
    }
}




//テーブル変更前
// function DecideDate(){
//     var decidedDate =  $("#hiddenSelectedDate").val();
//     console.log(decidedDate)
//     $.ajax({
//       type     : "post",
//       url      : "../applicationConfirm/update",
//       data     : {_token: CSRF_TOKEN,message : "updateDecidedDate", decidedDate : decidedDate},
//       success  : function(data){
//                 console.log("h" + JSON.stringify(data));
//                 window.location="../applicationConfirm/edit"
//       },
//       error    : function(err){
//             console.log(err);
//         }
//   });
// }

// function DisableDate(){
//     var disableDate =  $("#hiddenSelectedDate").val();
//     console.log(disableDate)
//     $.ajax({
//       type     : "post",
//       url      : "../applicationConfirm/update",
//       data     : {_token: CSRF_TOKEN,message : "updateDisableDate", disableDate : disableDate},
//       success  : function(data){
//                 console.log("h" + JSON.stringify(data));
//                 window.location="../applicationConfirm/edit";
//       },
//       error    : function(err){
//             console.log(err);
//         }
//   });
// }

// function ClearDesireDate(){
//     var clearDate =  $("#hiddenSelectedDate").val();
//     console.log("h"+clearDate)
//     $.ajax({
//       type     : "post",
//       url      : "../applicationConfirm/update",
//       data     : {_token: CSRF_TOKEN,message : "clearDesireDate", clearDate : clearDate},
//       success  : function(data){
//                 console.log("h" + JSON.stringify(data));
//                 window.location="../applicationConfirm/edit";
//       },
//       error    : function(err){
//             console.log(err);
//         }
//   });
// }
// $( "#dialog1" ).dialog({
//     autoOpen: false,
// });
// $( "#dialog2" ).dialog({ autoOpen: false });

//RightClick
//Test
// if(decidedDateList.indexOf(selectedDate) >= 0){
//     $(this).unbind();
//     $( "#dialog1" ).dialog( "close" );
//     $( "#dialog2" ).dialog( "close" );
// }

// if (desireDateList.indexOf(selectedDate) >= 0) {
//     if(decidedDateList.indexOf(selectedDate) >= 0){
//         $(this).unbind();
//         $( "#dialog1" ).dialog( "close" );
//         $( "#dialog2" ).dialog( "close" );
//     }else{
//         $(this).bind("contextmenu", function(e){
//             $( "#dialog1" ).dialog( "open" );
//             $( "#dialog2" ).dialog( "close" );
//             return false;
//         }); 
//     }

// }else{
//     if(disableDateList.indexOf(selectedDate) >= 0){
//         $(this).unbind();
//         $( "#dialog1" ).dialog( "close" );
//         $( "#dialog2" ).dialog( "close" );
//     }else{
//         $(this).bind("contextmenu", function(e){
//       $( "#dialog1" ).dialog( "close" );
//       $( "#dialog2" ).dialog( "open" );
//       return false;
//     }); 
//     }

// }
//Test
