var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');


$(document).ready(function() {
    var loginId = $('#hidLoginID').val();

    //Bim速習コースの管理者
    if (loginId == 6 || loginId == 46) {
        console.log("BimCourse Incharge");
        LoadAllBimCourseInfo();
    }
    else {
        LoadBimCourseInfoById(loginId);
    }
})

function LoadAllBimCourseInfo() {
    console.log("=======Load All Bim Course Info ======");
    $.ajax({
        url: "/iPD/application/getData/courseInfo",
        async: false,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "get_all_course_info" },
        success: function(data) {
            console.log(data);
            ShowAllNotification(data);
        },
        error: function(err) {
            console.log(err);
        }
    })
}

function LoadBimCourseInfoById(loginId) {
    console.log("=======Load Bim Course Info By Inviter Id=====");
    var loginName = $("#hiddenLoginUser").val();
    console.log(loginId);
    $.ajax({
        url: "/iPD/application/getData/courseInfo",
        async: false,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "get_course_info_by_inviter", login_id: loginId },
        success: function(data) {
            if (data.length) {
                console.log(data);
                ShowNotification(data, loginName);
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function ShowNotification(data, loginName) {
    var info = {};
    $.each(data, function(key, value) {
        if (info[loginName]) {
            info[loginName]['numOfStudent']++;
            if (!info[loginName]['desireDate'].includes(value['desireDate'])) {
                info[loginName]['desireDate'] += "," + value['desireDate'];
            }
        }
        else {
            var obj = {
                'numOfStudent': 1,
                'desireDate': value['desireDate']
            }
            info[loginName] = obj;
        }
    })
    var notiBox = "<div class='alert alert-info'>" +
        "<strong class='cus-link-color'>" + loginName + "さん</strong>がBim速習コースの受講者<strong>" + info[loginName]['numOfStudent'] + "人</strong>を入力しました。" +
        "<a href='/iPD/applicationConfirm/edit' class = 'alert-link cus-link-color' > 確認します </a>か？" + "<br>" +
        "【" + info[loginName]['desireDate'] + "】" +
        "</div>";
    $('.noti-area').append(notiBox);
}

function ShowAllNotification(data) {
    console.log(data)
    var inviterList = [];
    var info = {};
    $.each(data, function(key, value) {
        if (!inviterList.includes(value['username'])) {
            inviterList.push(value['username']);
        }
    })
    console.log(inviterList);
    for (var i = 0; i < inviterList.length; i++) {
        $.each(data, function(key, value) {
            if (inviterList[i] == value['username']) {
                if (info[inviterList[i]]) {

                    info[inviterList[i]]['numOfStudent']++;
                    if (!info[inviterList[i]]['desireDate'].includes(value['desireDate'])) {
                        info[inviterList[i]]['desireDate'] += value['desireDate'];
                    }
                }
                else {
                    var obj = {
                        'numOfStudent': 1,
                        'desireDate': value['desireDate']
                    }
                    info[inviterList[i]] = obj;
                }
            }
        })
    }
    console.log(info)
    $.each(info, function(key, value) {
        var notiBox = "<div class='alert alert-info'>" +
            "<strong class='cus-link-color'>" + key + "さん</strong>がBim速習コースの受講者<strong>" + value['numOfStudent'] + "人</strong>を入力しました。" +
            "<a href='/iPD/applicationConfirm/edit' class = 'alert-link cus-link-color' > 確認します </a>か？" + "<br>" +
            "【" + value['desireDate'] + "】" +
            "</div>";
        $('.noti-area').append(notiBox);
    })
}
