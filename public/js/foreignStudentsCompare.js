var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');


$(document).ready(function(){
    
    var loginUser = $("#loggingInUser").val();
    console.log("Login" + loginUser)
    
    $(".chechBoxDiv input[type='checkbox']").click(function(){
        var now =  $("#now").is(":checked");
        var student = $("#student").is(":checked");
        if(now && !student){
            $(".now").show();
            $(".past").hide();
        }else if(student && !now){
            $(".past").show();
            $(".now").hide();
        }else if(student && now){
            $(".past").show();
            $(".now").show();
        }else{
            $(".past").hide();
            $(".now").hide();
        }
    });
    
    $("#minimizeMise").on("click", function(){
        $(".mise").toggle();
        if($(".mise").is(":visible")){
            $(".miseRow").attr('rowspan', '2');
            $(this).closest('tr').next('tr').show();
            $("#minimizeMise").removeClass("glyphicon glyphicon-plus");
            $("#minimizeMise").addClass("glyphicon glyphicon-minus");
            ShowView("isMiseShow",loginUser);
        }else{
            $(".miseRow").attr('rowspan', '1');
            $(this).closest('tr').next('tr').hide();
            $("#minimizeMise").removeClass("glyphicon glyphicon-minus");
            $("#minimizeMise").addClass("glyphicon glyphicon-plus");
            HideView("isMiseShow",loginUser);
        }
    });
    
    $("#minimizeSkill").on("click", function(){
        $(".skill").toggle();
        if($(".skill").is(":visible")){
            $(".skillRow").attr('rowspan', '2');
            $(this).closest('tr').next('tr').show();
            $("#minimizeSkill").removeClass("glyphicon glyphicon-plus");
            $("#minimizeSkill").addClass("glyphicon glyphicon-minus");
            ShowView("isSkillShow",loginUser);
        }else{
            $(".skillRow").attr('rowspan', '1');
            $(this).closest('tr').next('tr').hide();
            $("#minimizeSkill").removeClass("glyphicon glyphicon-minus");
            $("#minimizeSkill").addClass("glyphicon glyphicon-plus");
            HideView("isSkillShow",loginUser);
        }
    });
    
    $("#minimizeField").on("click", function(){
        $(".field").toggle();
        if($(".field").is(":visible")){
            $(".fieldRow").attr('rowspan', '2');
            $(this).closest('tr').next('tr').show();
            $("#minimizeField").removeClass("glyphicon glyphicon-plus");
            $("#minimizeField").addClass("glyphicon glyphicon-minus");
            ShowView("isFieldShow",loginUser);
        }else{
            $(".fieldRow").attr('rowspan', '1');
            $(this).closest('tr').next('tr').hide();
            $("#minimizeField").removeClass("glyphicon glyphicon-minus");
            $("#minimizeField").addClass("glyphicon glyphicon-plus");
            HideView("isFieldShow",loginUser);
        }
    });
    
    $("#minimizeHaken").on("click", function(){
        $(".haken").toggle();
        if($(".haken").is(":visible")){
            $(".hakenRow").attr('rowspan', '2');
            $(this).closest('tr').next('tr').show();
            $("#minimizeHaken").removeClass("glyphicon glyphicon-plus");
            $("#minimizeHaken").addClass("glyphicon glyphicon-minus");
            ShowView("isHakenShow",loginUser);
        }else{
            $(".hakenRow").attr('rowspan', '1');
            $(this).closest('tr').next('tr').hide();
            $("#minimizeHaken").removeClass("glyphicon glyphicon-minus");
            $("#minimizeHaken").addClass("glyphicon glyphicon-plus");
            HideView("isHakenShow",loginUser);
        }
    });
    
    $("#minimizeObayashi").on("click", function(){
        $(".obayashi").toggle();
        if($(".obayashi").is(":visible")){
            $(".obayashiRow").attr('rowspan', '2');
            $(this).closest('tr').next('tr').show();
            $("#minimizeObayashi").removeClass("glyphicon glyphicon-plus");
            $("#minimizeObayashi").addClass("glyphicon glyphicon-minus");
            ShowView("isObayashiShow",loginUser);
        }else{
            $(".obayashiRow").attr('rowspan', '1');
            $(this).closest('tr').next('tr').hide();
            $("#minimizeObayashi").removeClass("glyphicon glyphicon-minus");
            $("#minimizeObayashi").addClass("glyphicon glyphicon-plus");
            HideView("isObayashiShow",loginUser);
        }
    });
    
    $("#minimizeCode").on("click", function(){
        $(".code").toggle();
        if($(".code").is(":visible")){
            $(".codeRow").attr('rowspan', '2');
            $(this).closest('tr').next('tr').show();
            $("#minimizeCode").removeClass("glyphicon glyphicon-plus");
            $("#minimizeCode").addClass("glyphicon glyphicon-minus");
            ShowView("isCodeShow",loginUser);
        }else{
            $(".codeRow").attr('rowspan', '1');
            $(this).closest('tr').next('tr').hide();
            $("#minimizeCode").removeClass("glyphicon glyphicon-minus");
            $("#minimizeCode").addClass("glyphicon glyphicon-plus");
            HideView("isCodeShow",loginUser);
        }
    });
    
    $("#minimizeType").on("click", function(){
        $(".type").toggle();
        if($(".type").is(":visible")){
            $(".typeRow").attr('rowspan', '2');
            $(this).closest('tr').next('tr').show();
            $("#minimizeType").removeClass("glyphicon glyphicon-plus");
            $("#minimizeType").addClass("glyphicon glyphicon-minus");
            ShowView("isTypeShow",loginUser);
        }else{
            $(".typeRow").attr('rowspan', '1');
            $(this).closest('tr').next('tr').hide();
            $("#minimizeType").removeClass("glyphicon glyphicon-minus");
            $("#minimizeType").addClass("glyphicon glyphicon-plus");
            HideView("isTypeShow",loginUser);
        }
    });
    
    
});

function HideView(columnName,loginUser){
    $.ajax({
        url: "../updateData",
        async:true,
        type: 'post',
        data:{_token: CSRF_TOKEN, message:"hideDisplay", columnName : columnName, loginUser : loginUser},
        success :function(data) {
            
        },
        error:function(err){
            console.log(err);
            
        }
    }
        
        );
}

function ShowView(columnName,loginUser){
    $.ajax({
        url: "../updateData",
        async:true,
        type: 'post',
        data:{_token: CSRF_TOKEN, message:"showDisplay", columnName : columnName, loginUser : loginUser},
        success :function(data) {
            
        },
        error:function(err){
            console.log(err);
            
        }
    });
}
