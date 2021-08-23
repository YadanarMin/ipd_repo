var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

function sendFileToServer(formData, status) {
    var uploadURL = "http://54.92.96.44/iPD/OBJ/upload"; //Upload URL
    var extraData = {}; //Extra Data.

    var jqXHR = $.ajax({
        xhr: function() {
            var xhrobj = $.ajaxSettings.xhr();
            if (xhrobj.upload) {

                xhrobj.upload.addEventListener('progress', function(event) {
                    var percent = 0;
                    var position = event.loaded || event.position;
                    var total = event.total;
                    if (event.lengthComputable) {
                        percent = Math.ceil(position / total * 100);
                    }
                    //Set progress
                    status.setProgress(percent);
                }, false);
            }
            return xhrobj;
        },
        url: uploadURL,
        type: "POST",
        contentType: false,
        processData: false,
        cache: false,
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        data: fd,
        success: function(data) {
            alert(data);
            status.setProgress(100);
        },
        error: function(err) {
            console.log(err);
        }
    });

    status.setAbort(jqXHR);
}

var rowCount = 0;

function createStatusbar(obj) {
    rowCount++;
    var row = "odd";
    if (rowCount % 2 == 0) row = "even";
    this.statusbar = $("<div class='statusbar " + row + "'></div>");
    this.filename = $("<div class='filename'></div>").appendTo(this.statusbar);
    this.size = $("<div class='filesize'></div>").appendTo(this.statusbar);
    this.progressBar = $("<div class='progressBar'><div></div></div>").appendTo(this.statusbar);
    this.abort = $("<div class='abort'>Abort</div>").appendTo(this.statusbar);
    obj.after(this.statusbar);

    this.setFileNameSize = function(name, size) {
        var sizeStr = "";
        var sizeKB = size / 1024;
        if (parseInt(sizeKB) > 1024) {
            var sizeMB = sizeKB / 1024;
            sizeStr = sizeMB.toFixed(2) + " MB";
        }
        else {
            sizeStr = sizeKB.toFixed(2) + " KB";
        }

        this.filename.html(name);
        this.size.html(sizeStr);
    }
    this.setProgress = function(progress) {
        var progressBarWidth = progress * this.progressBar.width() / 100;
        this.progressBar.find('div').animate({ width: progressBarWidth }, 10).html(progress + "% ");
        if (parseInt(progress) >= 100) {
            this.abort.hide();
        }
    }
    this.setAbort = function(jqxhr) {
        var sb = this.statusbar;
        this.abort.click(function() {
            jqxhr.abort();
            sb.hide();
        });
    }
}
var fd = new FormData();
var fileNames = [];

function handleFileUpload(files, obj) {

    for (var i = 0; i < files.length; i++) {
        fd.append('files[]', files[i]);
        fileNames.push(files[i].name);
        var status = new createStatusbar(obj); //Using this we can set progress.
        status.setFileNameSize(files[i].name, files[i].size);
        sendFileToServer(fd, status);

    }
}

$(document).ready(function() {

    var obj = $("#dragandrophandler");
    obj.on('dragenter', function(e) {
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border', '2px solid #0B85A1');
    });
    obj.on('dragover', function(e) {
        e.stopPropagation();
        e.preventDefault();
    });
    obj.on('drop', function(e) {

        $(this).css('border', '2px dotted #0B85A1');
        e.preventDefault();
        var files = e.originalEvent.dataTransfer.files;

        //We need to send dropped files to Server
        handleFileUpload(files, obj);
    });
    $(document).on('dragenter', function(e) {
        e.stopPropagation();
        e.preventDefault();
    });
    $(document).on('dragover', function(e) {
        e.stopPropagation();
        e.preventDefault();
        obj.css('border', '2px dotted #0B85A1');
    });
    $(document).on('drop', function(e) {
        e.stopPropagation();
        e.preventDefault();
    });


});

function ChangeOBJFile() {　 //alert("クラウドクレジットを使用するため、管理者にお知らせください。");return;
    //if($('#file option:selected').length > 1)return;
    window.location = "/iPD/OBJ/Convert/" + fileNames[0];


}

function ChangeOBJFileOld() {


    if (fileNames.length > 1 || fileNames.length == 0) return;
    ShowLoading();
    $.ajax({
        url: "../OBJ/Convert",
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "OBJ", fileName: JSON.stringify(fileNames) },
        success: function(data) {
            HideLoading();
            //alert(data);
            /*var url = '/iPD/OBJ/Convert';
            document.objForm.action = url;//multiple form submit action
            document.objForm.submit();*/
            window.location = '/iPD/OBJ/index';

        },
        error: function(err) {
            HideLoading();
            console.log("error");
            console.log(err);
        }
    });


    　
    alert("クラウドクレジットを使用するため、管理者にお知らせください。");
    return;
    if ($('#version option:selected').length > 1) return;
    ShowLoading();
    $('#version option:selected').each(function() {
        var valArr = JSON.parse($(this).val());
        var db_version_id = valArr.id;
        var forge_version_id = valArr.forge_version_id;
        var version_number = valArr.version_number;
        var item_id = valArr.item_id;
        $.ajax({
            url: "../OBJ/Convert",
            type: 'post',
            data: { _token: CSRF_TOKEN, message: "OBJ", forge_id: forge_version_id },
            success: function(data) {
                HideLoading();
                window.location = '/iPD/OBJ/index';

            },
            error: function(err) {
                HideLoading();
                console.log("error");
                console.log(err);
            }
        });
    });
}
