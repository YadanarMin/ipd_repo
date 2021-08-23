var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
$(document).ready(function() {

    getAllstoreManagementInfo();

    //Update display estimate flag
    $("#tblCheckbox").on('change', "input[type='checkbox']", function(e) {
        var pj_code = $(this).val();
        var check_id = $(this).attr("id");

        if (check_id === "displayEstimateFlag") {
            var flag = 0;
            if ($(this).prop("checked") == true) {
                flag = 1;
            }
            UpdateDisplayEstimateFlag(pj_code, flag);

        }
    });

    //Search Function
    $("#txtiPDCodeSearch").keyup(delay(function(e) {
        var value = $(this).val().trim().toLowerCase();
        $("#searchableProjectList tr,#searchableCheckBoxList tr").each(function(index) {
            $row = $(this);
            var id = $row.find("td:eq(1)").text().toLowerCase();
            if (id.includes(value)) {
                $row.show();
            }
            else {
                $row.hide();
            }

        });
    }, 2000));
    $("#txtProjectNameSearch").keyup(delay(function(e) {
        var value = $(this).val().trim().toLowerCase();
        $("#searchableProjectList tr,#searchableCheckBoxList tr").each(function(index) {
            $row = $(this);
            var project = $row.find("td:eq(2)").text().toLowerCase();
            if (project.includes(value)) {
                $row.show();
            }
            else {
                $row.hide();
            }

        });
    }, 2000));
    $("#txtBranchSearch").keyup(delay(function(e) {
        var value = $(this).val().trim().toLowerCase();
        $("#searchableProjectList tr,#searchableCheckBoxList tr").each(function(index) {
            $row = $(this);
            var branch = $row.find("td:eq(3)").text().toLowerCase();

            if (branch.includes(value)) {
                $row.show();
            }
            else {
                $row.hide();
            }

        });
    }, 2000));
    $("#txtKoujiTypeSearch").keyup(delay(function(e) {
        var value = $(this).val().trim().toLowerCase();
        $("#searchableProjectList tr,#searchableCheckBoxList tr").each(function(index) {
            $row = $(this);
            var kouji = $row.find("td:eq(4)").text().toLowerCase();
            if (kouji.includes(value)) {
                $row.show();
            }
            else {
                $row.hide();
            }

        });
    }, 2000));
    $("#txtiPDinChargeSearch").keyup(delay(function(e) {
        var value = $(this).val().trim().toLowerCase();
        $("#searchableProjectList tr,#searchableCheckBoxList tr").each(function(index) {
            $row = $(this);

            var tantou = $row.find("td:eq(5)").text().toLowerCase();

            if (tantou.includes(value)) {
                $row.show();
            }
            else {
                $row.hide();
            }

        });
    }, 2000));


});

function getAllstoreManagementInfo() {
    ShowLoading();
    $.ajax({
        url: "/iPD/allstore/getData",
        type: 'post',
        data: { _token: CSRF_TOKEN },
        success: function(data) {
            if (data != null) {
                console.log(data);
                displayAllstoreManagementInfo(data);
                HideLoading();
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function displayAllstoreManagementInfo(data) {
    var row = "";
    var checkRow = "";
    $.each(data, function(key, value) {
        row += "<tr>" +
            "<td style='display:none'>" + value['id'] + "</td>" +
            "<td style='height :30px'>" + value['a_pj_code'] + "</td>" +
            "<td style='height :30px'>" + value['a_pj_name'] + "</td>" +
            "<td style='height :30px'>" + value['a_shiten'] + "</td>" +
            "<td style='height :30px'>" + value['a_kouji_type'] + "</td>" +
            "<td style='height :30px'>" + value['b_ipd_center_tantou'] + "</td>" +
            "</tr>";
        checkRow += "<tr>" +
            "<td style='display:none'>" + value['id'] + "</td>" +
            "<td style='display:none'>" + value['a_pj_code'] + "</td>" +
            "<td style='display:none'>" + value['a_pj_name'] + "</td>" +
            "<td style='display:none'>" + value['a_shiten'] + "</td>" +
            "<td style='display:none'>" + value['a_kouji_type'] + "</td>" +
            "<td style='display:none'>" + value['b_ipd_center_tantou'] + "</td>";

        if (value['display_estimate_flag']) {
            checkRow += "<td style='height :30px'><input type='checkbox' id='displayEstimateFlag' value='" + value['a_pj_code'] + "' checked></td>";
        }
        else {
            checkRow += "<td style='height :30px'><input type='checkbox' id='displayEstimateFlag' value='" + value['a_pj_code'] + "'></td>";
        }

        checkRow += "</tr>";


    });
    $("#tbProjectList tbody").empty();
    $("#tblCheckbox tbody").empty();
    $("#tbProjectList tbody").append(row);
    $("#tblCheckbox tbody").append(checkRow);

}

function UpdateDisplayEstimateFlag(pjCode, flag) {
    console.log("Update Estimate Flag")
    $.ajax({
        url: "/iPD/allstore/updateFlag",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "update_estimate_flag", projectCode: pjCode, flag: flag },
        success: function(data) {
            if (data.includes("success")) {
                console.log("succssfully updated!");
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function delay(callback, ms) {
    var timer = 0;
    return function() {
        var context = this,
            args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function() {
            callback.apply(context, args);
        }, ms || 0);
    };
}
