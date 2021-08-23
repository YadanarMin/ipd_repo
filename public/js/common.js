var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function(){
    $(".pop").popover({ trigger: "manual" , html: true, animation:false})
        .on("mouseenter", function () {
            var _this = this;
            $(this).popover("show");
            $(".popover").on("mouseleave", function () {
                $(_this).popover('hide');
            });
        }).on("mouseleave", function () {
            var _this = this;
            setTimeout(function () {
                if (!$(".popover:hover").length) {
                    $(_this).popover("hide");
                }
            }, 300);
    });

    $(document).on('click', '.hyper', function(){
      var pageName = $(this).closest("li").find("figcaption").text();
      SetPageNameToSession(pageName);
    });
});
function ShowLoading(){
    $(".loading").removeClass("hide");
    $(".loading").addClass("show");
}

function HideLoading(){
    $(".loading").removeClass("show");
    $(".loading").addClass("hide");
}

/**
 * ログ出力を実行する。
 * @param  {string}  [in]funcName  関数名
 * @param  {string}  [in]logString ログ出力文字列
 * @param  {int}     [in]debugLogLevel ログレベル
 * @return なし
 */
function DEBUGLOG(funcName, logString, debugLogLevel){
    
    var now = new Date();
	var year = now.getFullYear();
	var mon = now.getMonth()+1;
	var day = now.getDate();
	var hour = now.getHours();
	var min = now.getMinutes();
	var sec = now.getSeconds();
	var msec = now.getMilliseconds();
	var dateStr = "["+year+"/"+mon+"/"+day+" "+hour+":"+min+":"+sec+":"+msec+"]";

    var strFuncName = funcName != "" ? "["+funcName+"]" : "[NoName]";
    
    if(debugLogLevel == 1) {
        console.log(dateStr+"[DEBUG]"+strFuncName+logString);
    }else{
        //NOP
    }
}

function SetPageNameToSession(pageName){
    $.ajax({
          url: "../admin/setToSession",
          type: 'post',
          data:{_token: CSRF_TOKEN,pageName:pageName},
          success :function(data) {
              
            if(data.includes("success")){
              window.open('/iPD/admin/pageDescription',"pageDescription");
            }
            // window.location = '';
                                           
          },
          error : function(err){
            console.log(err);
          }
   });
}

function recordAccessHistory(personal_id,img_src,data_url,content_name){
    
    $.ajax({
        url: "/iPD/user/setAccessHistory",
        type: 'post',
        data:{_token: CSRF_TOKEN,personal_id:personal_id,img_src:img_src,data_url:data_url,content_name:content_name},
        success : function(data) {
            // console.log(data);
        },
        error : function(err){
            console.log(err);
        }
   });
    
}
