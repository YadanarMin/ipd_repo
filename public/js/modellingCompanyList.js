var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

var loginUser;
$(document).ready(function () {
    $.ajaxSetup({
        cache:false
    });
    
    loginUser = $("#loginUserNameForContactView").val();
    
    $("#showBtn").addClass("disabledBtn");
    
    //Load SelectBox Data
    ShowSuggestedJobType();
    
     //SearchFunction
    $("#searchPartnerCompany").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#searchableCompanyContactList tr").filter(function() {
              var companyName = $(this).find($("td:nth-child(2)")).text().toLowerCase();
              var jobType = $(this).find($("td:nth-child(3)")).text().toLowerCase();
              var branch = $(this).find($("td:nth-child(4)")).text().toLowerCase();
              var inCharge = $(this).find($("td:nth-child(7)")).text().toLowerCase();
              $(this).toggle(companyName.indexOf(value) > -1 || jobType.indexOf(value) > -1 || inCharge.indexOf(value) > -1 || branch.indexOf(value) > -1 );
              $(this).find('td').children("table").show();
              
            });
            
            
    });
    
    $(".partnerJobTypeSelect").select2({
        placeholder : "職種で検索"
    });
    
    //Search Select Box multiple value
    $(".partnerJobTypeSelect").on("change",function(e){
    　  var value = $('.partnerJobTypeSelect').val().toString().toLowerCase();
    　  console.log("val" + value);
    　  if(!value){
    　     $("#searchableCompanyContactList tr").show();
    　  }else{
        　  $("#searchableCompanyContactList tr").filter(function() {
                var jobType = $(this).find($("td:nth-child(3)")).text().trim().toLowerCase();
               if (jobType && !value.includes(",")) {
                  $(this).toggle(value == jobType);
                } else if (jobType) {
                  $(this).toggle(value.indexOf(jobType) > -1)
                } else {
                   $(this).hide();
                }
             });
    　  
    　  }
    　  
    });
    
    $("#minimizeBtn2").on("click", function(){
           $( ".second").toggle();
           if($(".second").is(":visible")){
                $("#minimizeBtn2").removeClass("glyphicon glyphicon-plus");
                $("#minimizeBtn2").addClass("glyphicon glyphicon-minus");
                ShowView("isPartnerJobTypeShow",loginUser);
            } else{
                $("#minimizeBtn2").removeClass("glyphicon glyphicon-minus");
                $("#minimizeBtn2").addClass("glyphicon glyphicon-plus");
                HideView("isPartnerJobTypeShow",loginUser);
            }
       });
       
    $("#minimizeBtn3").on("click", function(){
           $( ".third").toggle();
           if($(".third").is(":visible")){
                $("#minimizeBtn3").removeClass("glyphicon glyphicon-plus");
                $("#minimizeBtn3").addClass("glyphicon glyphicon-minus");
                ShowView("isPartnerCompanyBranchShow",loginUser);
            } else{
                $("#minimizeBtn3").removeClass("glyphicon glyphicon-minus");
                $("#minimizeBtn3").addClass("glyphicon glyphicon-plus");
                 HideView("isPartnerCompanyBranchShow",loginUser);
            }
       });
       
    $("#minimizeBtn4").on("click", function(){
           $( ".fouth").toggle();
           if($(".fouth").is(":visible")){
                $("#minimizeBtn4").removeClass("glyphicon glyphicon-plus");
                $("#minimizeBtn4").addClass("glyphicon glyphicon-minus");
                ShowView("isPartnerMailCodeShow",loginUser);
            } else{
                $("#minimizeBtn4").removeClass("glyphicon glyphicon-minus");
                $("#minimizeBtn4").addClass("glyphicon glyphicon-plus");
                HideView("isPartnerMailCodeShow",loginUser);
            }
       });
       
    $("#minimizeBtn5").on("click", function(){
           $( ".fifth").toggle();
           if($(".fifth").is(":visible")){
                $("#minimizeBtn5").removeClass("glyphicon glyphicon-plus");
                $("#minimizeBtn5").addClass("glyphicon glyphicon-minus");
                ShowView("isPartnerCompanyAddressShow",loginUser);
            } else{
                $("#minimizeBtn5").removeClass("glyphicon glyphicon-minus");
                $("#minimizeBtn5").addClass("glyphicon glyphicon-plus");
                HideView("isPartnerCompanyAddressShow",loginUser);
            }
       });
       
    $("#minimizeBtn6").on("click", function(){
           $( ".sixth").toggle();
           if($(".sixth").is(":visible")){
                $("#minimizeBtn6").removeClass("glyphicon glyphicon-plus");
                $("#minimizeBtn6").addClass("glyphicon glyphicon-minus");
                ShowView("isPartnerInchargeNameShow",loginUser);
            } else{
                $("#minimizeBtn6").removeClass("glyphicon glyphicon-minus");
                $("#minimizeBtn6").addClass("glyphicon glyphicon-plus");
                HideView("isPartnerInchargeNameShow",loginUser);
            }
       });
       
    $("#minimizeBtn7").on("click", function(){
           $( ".seven").toggle();
           if($(".seven").is(":visible")){
                $("#minimizeBtn7").removeClass("glyphicon glyphicon-plus");
                $("#minimizeBtn7").addClass("glyphicon glyphicon-minus");
                ShowView("isPartnerPhoneShow",loginUser);
            } else{
                $("#minimizeBtn7").removeClass("glyphicon glyphicon-minus");
                $("#minimizeBtn7").addClass("glyphicon glyphicon-plus");
                HideView("isPartnerPhoneShow",loginUser);
            }
       });
       
    $("#minimizeBtn8").on("click", function(){
           $( ".eight").toggle();
           if($(".eight").is(":visible")){
                $("#minimizeBtn8").removeClass("glyphicon glyphicon-plus");
                $("#minimizeBtn8").addClass("glyphicon glyphicon-minus");
                ShowView("isPartnerEmailShow",loginUser);
            } else{
                $("#minimizeBtn8").removeClass("glyphicon glyphicon-minus");
                $("#minimizeBtn8").addClass("glyphicon glyphicon-plus");
                HideView("isPartnerEmailShow",loginUser);
            }
       });
});

function InsertBtn(){
    $("#insertBtn").addClass("disabledBtn");
    window.location='../modellingCompany/index';
}

//Data in jobType Select box
function ShowSuggestedJobType(){
    var jobTypeList =[];
    $.ajax({
        url: "../company/getData",
        async:true,
        type: 'post',
        data:{_token: CSRF_TOKEN, message:"getIndustryType"},
        success :function(data) {
            console.log(data);
            if(data){
                $.each(data, function(key,value){
                  console.log(value)
                  if(value['industry_type']){
                            if(!jobTypeList.includes(value['industry_type'])){
                                jobTypeList.push(value['industry_type']);
                                var option = "<option>"+value['industry_type']+"</option>";
                                $(".partnerJobTypeSelect").append(option);
                            }
                            
                    }  
                        
                    
                    
                });
              
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function HideView(columnName,loginUser){
    $.ajax({
        url: "../modellingCompany/updateData",
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
        url: "../modellingCompany/updateData",
        async:true,
        type: 'post',
        data:{_token: CSRF_TOKEN, message:"showDisplay", columnName : columnName, loginUser : loginUser},
        success :function(data) {
            
        },
        error:function(err){
            console.log(err);
            
        }
    }
        
        );
}

//Suggested Data For JobType
// function ShowSuggestedPartnerJobtype(){
//     var jobTypeList = [];
//     $.ajax({
//         url: "../modellingCompany/getData",
//         async:true,
//         type: 'post',
//         data:{_token: CSRF_TOKEN, message:"getAllPartnerCompanyContact"},
//         success :function(data) {
//             if(data){
//                 $.each(data, function(key,value){
//                     $.each(value, function(tname, tdata){
//                         if(tdata['partnerJobType']){
//                             if(!jobTypeList.includes(tdata['partnerJobType'])){
//                                 jobTypeList.push(tdata['partnerJobType']);
//                                 var option = "<option>"+tdata['partnerJobType']+"</option>";
//                                 $(".partnerJobTypeSelect").append(option);
//                             }
//                         }
//                     });
                    
//                 });
//             }
//         },
//         error:function(err){
//             console.log(err);
//         }
//     });
// }