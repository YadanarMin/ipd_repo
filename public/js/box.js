var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

function GetThreeLeggedBoxAuth() {
    var btnText = $("#btnBoxLogin").text();
    $.ajax({
        url: "/iPD/box/login",
        type: 'post',
        data: { _token: CSRF_TOKEN, btnText: btnText },
        success: function(data) {
            //location.href=data;
            if (data.includes("LOGIN")) {
                window.location.href = "/iPD/login/successlogin";
            }
            else {
                location.href = data;
            }

        },
        error: function(err) {
            console.log(err);
        }
    });
}

function GetBoxData() {

    $.ajax({
        url: "../box/getData",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "get_top_folder" },
        success: function(data) {
            //console.log(JSON.parse(data));
            console.log(data);
            if (data["files"] != undefined || data["files"] != "") {
                CreateBoxFileContent(data["files"], data["token"]);
            }

        },
        error: function(err) {
            console.log(err);
        }
    });
}

function UploadFilesToBox() {

    var isBoxLogin = CheckBoxLogin();
    if (!isBoxLogin) return;

    var old_new_filename_pair = RenameSelectedFiles();
    console.log(old_new_filename_pair);
    // $.each(SelectedFiles,function(key,file){

    //     file.name = old_new_file_pair[file.name];
    // });

    var form = $("#box-upload-form"); // $("#template-form").serialize();
    var form_data = new FormData(form[0]);
    form_data.append("old_new_file_pair", JSON.stringify(old_new_filename_pair));
    console.log(form_data);
    ShowLoading();
    $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        url: "../report/boxUpload",
        type: 'post',
        enctype: 'multipart/form-data',
        processData: false, // Important!
        contentType: false,
        cache: false,
        data: form_data,
        success: function(data) {
            HideLoading();
            console.log("==========uploaded=======");
            console.log(data);
            if (data.includes("success")) {

                location.reload();
            }
            else if (data.includes("401failed")) {
                $("#box_login_warning").html("BOX TOKEN の有効期限が切れたため、失敗しました。BOXに再ログインしてください。")
            }
            else if (data.includes("409failed")) {
                $("#box_login_warning").html("アップロードファイルが既にあります。失敗しました。");
            }
        },
        error: function(err) {
            HideLoading();
            console.log(err);
            alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
}

function RenameSelectedFiles() {
    var old_new_file_pair = {};
    $("#tblRename tr").each(function() {
        var rowIndex = $(this).index();
        if (rowIndex == 0) return; //skip th row
        var old_fileName = $("#txtOrgName" + rowIndex).val();
        var new_fileName = $("#txtNewFileName" + rowIndex).val();
        new_fileName = (new_fileName == undefined || new_fileName == "") ? old_fileName : new_fileName;
        old_new_file_pair[old_fileName] = new_fileName;
    });

    return old_new_file_pair;
}

function CreateBoxFileContent(fileIds, token, containder_id, active_file_id = null, changed_type = null) {
    //alert(fileIds.toString());
    var preview = new Box.Preview();

    preview.addListener("load", data => {
        var fileName = data.file.name;
        var freedomPart = fileName.split('_')[2] == undefined ? "" : fileName.split('_')[2];
        var lastPart = fileName.split('_')[3] == undefined ? "" : fileName.split('_')[3];

        if (containder_id.includes("img1")) {
            if (fileName.includes(changed_type)) {
                $("#text1").html(freedomPart);
            }
        }
        else if (containder_id.includes("img2")) {
            if (fileName.includes(changed_type)) {
                $("#text2").html(freedomPart);
            }
        }
        else if (containder_id.includes("img3")) {
            if (fileName.includes(changed_type)) {
                $("#text3").html(freedomPart);
            }
        }

    });
    //alert(containder_id);
    var active_id = active_file_id == null ? fileIds[0] : active_file_id;
    preview.show(active_id, token, {
        container: containder_id,
        header: 'none',
        crossOrigin: "anonymous",
        startAt: {
            unit: 'seconds',
            value: 2,
        },
        // Comment out the following if you are using your own access token and file ID
        collection: fileIds
    });

}
