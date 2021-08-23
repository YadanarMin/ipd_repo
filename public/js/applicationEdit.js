var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function() {
    $.ajaxSetup({
        cache: false
    });

    //For Calendar
    //LoadDecidedDateList();
    LoadCalendar();

})


function ShowCalendar(disableDateList, disableMonthList, desireDateList, decidedDateList) {
    console.log("DisableDateList" + disableDateList)

    $("#editCalendar").datepicker({
        dateFormat: 'yy-mm-dd',
        onSelect: function(dateText, inst) {
            var selectedDate = $(this).val();
            var viewDate = $.datepicker.formatDate('yy年mm月dd日(D)', new Date($(this).val()));
            ShowSelectedUserInfo(selectedDate, viewDate);

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

            //       //Change Today Color
            //   if(prettyDate == new Date().toJSON().slice(0,10).replace(/-/g,'-')){
            //       return [true, 'today'];
            //   }


        }
    });
}


function ShowSelectedUserInfo(selectedDate, viewDate) {
    ShowLoading();
    $("#selectedDateAM").html(viewDate);
    $("#selectedDatePM").html(viewDate);
    var decidedDateList = [];
    var decidedDateListStr = $("#hiddenDecidedDateList").val();
    var dateArray = decidedDateListStr.split(",");
    for (var i = 0; i < dateArray.length; i++) {
        decidedDateList.push(dateArray[i].trim());
    }
    console.log(decidedDateList)
    var loginUser = $("#hiddenLoginUser").val();
    var loginId = $("hiddenLoginId").val();
    $.ajax({
        type: "post",
        url: "../application/getData",
        data: { _token: CSRF_TOKEN, message: "getUserInfoBySelectedDateAndLoginUser", selectedDate: selectedDate, loginUser: loginUser },
        success: function(data) {
            console.log("DD" + JSON.stringify(data, null, 2));
            var rowAM = "";
            var rowPM = "";
            var btnAM = "";
            var btnPM = "";
            var rowAMCount = 0;
            var rowPMCount = 0;
            $.each(data, function(key, value) {
                $.each(value, function(key, value) {
                    var classType;
                    var inviter = value['inviter'];
                    console.log(inviter);
                    console.log(loginUser != inviter)
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

                    //Test
                    var desireDate = value['desireDate'];
                    if (desireDate.indexOf(selectedDate + "(AM)") >= 0 && desireDate.indexOf(selectedDate + "(PM)") >= 0) {
                        var dataAM = selectedDate + "(AM)";
                        rowAM += "<tr>" +
                            "<td style='display:none'>" + "<input type='hidden' id='" + value['id'] + "' value='" + dataAM + "'>" +
                            "<td style='display:none'>" + "<input type='hidden' id=''　value='" + value['companyTypeId'] + "'>" +
                            "<td style='display:none'>" + "<input type='hidden' id='' value='" + value['companyId'] + "'>" +
                            "<td>" + "<a class='btn btn-danger' " +
                            ((decidedDateList.indexOf(dataAM) >= 0 || loginUser != inviter) ? 'disabled' : "onclick='ClearDate(\"" + value['id'] + "\")'") + " >クリア</a>" + "</td>" +
                            "<td>" + "<a class='btn btn-primary' " +
                            ((decidedDateList.indexOf(dataAM) >= 0 || loginUser != inviter) ? 'disabled' : "href='../application/edit/" + value['id'] + "'") + ">編集</a>" + "</td>" +
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
                        //Add New Person
                        if (decidedDateList.indexOf(dataAM) < 0 && new Date(selectedDate) > new Date()) {
                            btnAM = "<button type='button' class='btn btn-default newBtnAM' onclick='AddNewPerson(\"" + dataAM + "\")'>追加</buton>";
                        }
                        //Add New Person
                        var dataPM = selectedDate + "(PM)";
                        rowPM += "<tr>" +
                            "<td style='display:none'>" + "<input type='hidden' id='" + value['id'] + "' value='" + dataPM + "'>" +
                            "<td style='display:none'>" + "<input type='hidden' id=''　value='" + value['companyTypeId'] + "'>" +
                            "<td style='display:none'>" + "<input type='hidden' id='' value='" + value['companyId'] + "'>" +
                            "<td>" + "<a class='btn btn-danger' " +
                            ((decidedDateList.indexOf(dataPM) >= 0 || loginUser != inviter) ? 'disabled' : "onclick='ClearDate(\"" + value['id'] + "\")'") + ">クリア</a>" + "</td>" +
                            "<td>" + "<a class='btn btn-primary' " +
                            ((decidedDateList.indexOf(dataPM) >= 0 || loginUser != inviter) ? 'disabled' : "href='../application/edit/" + value['id'] + "'") + ">編集</a>" + "</td>" +
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
                        //Add New Person
                        if (decidedDateList.indexOf(dataPM) < 0 && new Date(selectedDate) > new Date()) {
                            btnPM = "<button type='button' class='btn btn-default newBtnPM' onclick='AddNewPerson(\"" + dataPM + "\");'>追加</buton>";
                        }

                        //Add New Person
                    }
                    else if (desireDate.indexOf(selectedDate + "(AM)") >= 0) {
                        var dataAM = selectedDate + "(AM)";
                        rowAM += "<tr>" +
                            "<td style='display:none'>" + "<input type='hidden' id='" + value['id'] + "' value='" + dataAM + "'>" +
                            "<td style='display:none'>" + "<input type='hidden' id=''　value='" + value['companyTypeId'] + "'>" +
                            "<td style='display:none'>" + "<input type='hidden' id='' value='" + value['companyId'] + "'>" +
                            "<td>" + "<a class='btn btn-danger' " +
                            ((decidedDateList.indexOf(dataAM) >= 0 || loginUser != inviter) ? 'disabled' : "onclick='ClearDate(\"" + value['id'] + "\")'") + ">クリア</a>" + "</td>" +
                            "<td>" + "<a class='btn btn-primary' " +
                            ((decidedDateList.indexOf(dataAM) >= 0 || loginUser != inviter) ? 'disabled' : "href='../application/edit/" + value['id'] + "'") + ">編集</a>" + "</td>" +
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
                        //Add New Person
                        if (decidedDateList.indexOf(dataAM) < 0 && new Date(selectedDate) > new Date()) {

                            btnAM = "<button type='button' class='btn btn-default newBtnAM' onclick='AddNewPerson(\"" + dataAM + "\");'>追加</button>";
                        }

                        //Add New Person
                    }
                    else if (desireDate.indexOf(selectedDate + "(PM)") >= 0) {
                        var dataPM = selectedDate + "(PM)";
                        rowPM += "<tr>" +
                            "<td style='display:none'>" + "<input type='hidden' id='" + value['id'] + "' value='" + dataPM + "'>" +
                            "<td style='display:none'>" + "<input type='hidden' id=''　value='" + value['companyTypeId'] + "'>" +
                            "<td style='display:none'>" + "<input type='hidden' id='' value='" + value['companyId'] + "'>" +
                            "<td>" + "<a class='btn btn-danger' " +
                            ((decidedDateList.indexOf(dataPM) >= 0 || loginUser != inviter) ? 'disabled' : "onclick='ClearDate(\"" + value['id'] + "\")'") + ">クリア</a>" + "</td>" +
                            "<td>" + "<a class='btn btn-primary'" +
                            ((decidedDateList.indexOf(dataPM) >= 0 || loginUser != inviter) ? 'disabled' : "href='../application/edit/" + value['id'] + "'") + ">編集</a>" + "</td>" +
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
                        //Add New Person
                        if (decidedDateList.indexOf(dataPM) < 0 && new Date(selectedDate) > new Date()) {
                            btnPM = "<button type='button' class='btn btn-default newBtnPM' onclick='AddNewPerson(\"" + dataPM + "\");'>追加</buton>";
                        }

                        //Add New Person
                    }
                    //Test

                })
            })
            $("#selectedUserInfoTableAM tbody").empty();
            $("#selectedUserInfoTablePM tbody").empty();
            $(".newBtnAM").remove();
            $(".newBtnPM").remove();
            $("#selectedUserInfoTableAM tbody").append(rowAM);
            $("#selectedUserInfoTablePM tbody").append(rowPM);
            $("#AMDiv").append(btnAM);
            $("#PMDiv").append(btnPM);
            $("#numOfApplicantsAM").html(rowAMCount);
            $("#numOfApplicantsPM").html(rowPMCount);
            HideLoading();


        },
        error: function(err) {
            console.log(err);
        }
    });
}

function AddNewPerson(date) {
    console.log(date);
    var win = window.open("/iPD/application/insertUpdate/" + date, '_blank');
    win.focus();
}

function LoadCalendar() {
    ShowLoading();
    var loginUser = $("#hiddenLoginUser").val();
    var desireDateList = [];
    var decidedDateList = [];
    var disableDateList = [];
    var disableMonthList = [];

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
            // data: { _token: CSRF_TOKEN, message: "getDateFromBimCourseInfoByLoginUser", loginUser: loginUser },
            data: { _token: CSRF_TOKEN, message: "getDateFromBimCourseInfo" },
            success: function(data) {
                console.log("DesireDateByLoginUser" + JSON.stringify(data));
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
                            var decidedDateArray = decidedDate.split(",");
                            for (var i = 0; i < decidedDateArray.length; i++) {
                                if (!decidedDateList.includes(decidedDateArray[i].trim())) {
                                    decidedDateList.push(decidedDateArray[i].trim());
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

}

function LoadDecidedDateList() {
    var decidedDateList = [];
    $.ajax({
        type: "post",
        url: "../application/getData",
        data: { _token: CSRF_TOKEN, message: "getDateFromBimCourseInfo" },
        success: function(data) {
            console.log("BimCourseInfo" + JSON.stringify(data));
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
            console.log(decidedDateList)
            $("#hiddenDecidedDateList").val(decidedDateList.toString());

        },
        error: function(err) {
            console.log(err);

        }
    })
}

function ClearDate(id) {
    console.log(id);

    var r = confirm("この希望日を消してよろしいですか？");
    if (r == true) {
        var clearDesireDate = $("#" + id).val();　
        console.log(clearDesireDate)　
        $.ajax({
            type: "post",
            url: "../application/deleteData",
            data: { _token: CSRF_TOKEN, message: "deleteDesireDate", id: id, desireDate: clearDesireDate },
            success: function(data) {
                window.location = "../application/edit"
            },
            error: function(err) {
                console.log(err);
            }
        });
    }
    else {

    }

}


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
//                             var dateArray1 = decidedDate.split(",");
//                             for(var i=0; i<dateArray1.length ; i++){
//                             if(!decidedDateList.includes(dateArray1[i].trim())){
//                                 decidedDateList.push(dateArray1[i].trim());
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
//                     })
//                 })

//       },
//       error    : function(err){
//             console.log(err);
//         }
//   }).done(function(){
//       $.ajax({
//       type     : "post",
//       url      : "../application/getData",
//       data     : {_token: CSRF_TOKEN,message : "getDesireDate", loginUser : loginUser},
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
//                     })
//                 })
//                 console.log(desireDateList)
//                 console.log(decidedDateList)
//                 console.log(disableDateList)
//                 ShowCalendar(desireDateList,decidedDateList, disableDateList);
//                 HideLoading();
//       },
//       error    : function(err){
//             console.log(err);
//             HideLoading();
//         }
//   });
// })
