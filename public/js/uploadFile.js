  /* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content'); 
    function sendFileToServer(formData,status)
    {
        var uploadURL ="/iPD/admin/UploadImages"; //Upload URL
        var extraData ={}; //Extra Data.

        var jqXHR=$.ajax({
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
            data:fd,
            success: function(data){
                status.setProgress(100); 
            },
            error:function(err){
                console.log(err);
            }
        }); 
    
        status.setAbort(jqXHR);
    }
    
    var rowCount=0;
    function createStatusbar(obj)
    {
        rowCount++;
        var row="odd";
        if(rowCount %2 ==0) row ="even";
        this.statusbar = $("<div class='statusbar "+row+"'></div>");
        this.filename = $("<div class='filename'></div>").appendTo(this.statusbar);
        this.size = $("<div class='filesize'></div>").appendTo(this.statusbar);
        this.progressBar = $("<div class='progressBar'><div></div></div>").appendTo(this.statusbar);
        this.abort = $("<div class='abort'>Abort</div>").appendTo(this.statusbar);
        obj.after(this.statusbar);
    
        this.setFileNameSize = function(name,size)
        {
            var sizeStr="";
            var sizeKB = size/1024;
            if(parseInt(sizeKB) > 1024)
            {
                var sizeMB = sizeKB/1024;
                sizeStr = sizeMB.toFixed(2)+" MB";
            }
            else
            {
                sizeStr = sizeKB.toFixed(2)+" KB";
            }
    
            this.filename.html(name);
            this.size.html(sizeStr);
        }
        this.setProgress = function(progress)
        {       
            var progressBarWidth =progress*this.progressBar.width()/ 100;  
            this.progressBar.find('div').animate({ width: progressBarWidth }, 10).html(progress + "% ");
            if(parseInt(progress) >= 100)
            {
                this.abort.hide();
            }
        }
        this.setAbort = function(jqxhr)
        {
            var sb = this.statusbar;
            this.abort.click(function()
            {
                jqxhr.abort();
                sb.hide();
            });
        }
    }
    var fd = new FormData();
    var fileNames = [];
    function handleFileUpload(files,obj)
    {
        
        for (var i = 0; i < files.length; i++) 
        {
                fd.append('files[]', files[i]);
                fileNames.push(files[i].name);
                var status = new createStatusbar(obj); //Using this we can set progress.
                status.setFileNameSize(files[i].name,files[i].size);
                sendFileToServer(fd,status);
        
        }
    }

    $(document).ready(function()
    {
        var obj = $("#dragandrophandler");
        obj.on('dragenter', function (e) 
        {
            e.stopPropagation();
            e.preventDefault();
            $(this).css('border', '2px solid #0B85A1');
        });
        obj.on('dragover', function (e) 
        {
            e.stopPropagation();
            e.preventDefault();
        });
        obj.on('drop', function (e) 
        {
        
            $(this).css('border', '2px dotted #0B85A1');
            e.preventDefault();
            var files = e.originalEvent.dataTransfer.files;
        
            //We need to send dropped files to Server
            handleFileUpload(files,obj);
        });
        $(document).on('dragenter', function (e) 
        {
            e.stopPropagation();
            e.preventDefault();
        });
        $(document).on('dragover', function (e) 
        {
        e.stopPropagation();
        e.preventDefault();
        obj.css('border', '2px dotted #0B85A1');
        });
        $(document).on('drop', function (e) 
        {
            e.stopPropagation();
            e.preventDefault();
        });
    
    });
    
    function UploadFiles(){

        $.ajax({
        url: "../common/uploadFiles",
        type: 'post',
        data:{_token: CSRF_TOKEN,message:"upload_file","fileNames":JSON.stringify(fileNames)},
        success :function(data) {
            //alert(JSON.stringify(data));
            if(data.includes("success")){
                location.reload();
            }
            
        },
        error:function(err){
            console.log(err);
        }
        });
    }
    
    
    function DeleteFile(fileName) {
        
        $.ajax({
            url: "../common/uploadFiles",
            type: 'post',
            data:{_token: CSRF_TOKEN,message:"delete_file",fileName:fileName},
            success :function(data) {
                //alert(JSON.stringify(data));
                if(data.includes("success")){
                    location.reload();
                }
                
            },
            error:function(err){
                console.log(err);
            }
        });
        
    }
    

