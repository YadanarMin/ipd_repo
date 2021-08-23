var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

var loginUser;
$(document).ready(function () {
    $.ajaxSetup({
        cache:false
    });
    
    loginUser = $("#loginUserName").val();
    
    $("#showBtn").addClass("disabledBtn");
    
    //SearchFunction
    $("#searchCompany").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#searchableCompanyList tr").filter(function() {
              var companyName = $(this).find($("td:nth-child(1)")).text().toLowerCase();
              var jobType = $(this).find($("td:nth-child(2)")).text().toLowerCase();
              var inCharge = $(this).find($("td:nth-child(9)")).text().toLowerCase();
              var pjName = $(this).find($("td:nth-child(7)")).text().toLowerCase();
                  $(this).toggle(companyName.indexOf(value) > -1 || jobType.indexOf(value) > -1 || inCharge.indexOf(value) > -1 || pjName.indexOf(value) > -1 );
              
            });
    });

    
    ShowSuggestedJobType();
    
    //Search Btn By JobType
    $('.jobTypeSelect').select2({
        placeholder : "職種で検索"
    });
    
    //Search Select Box multiple value
    $(".jobTypeSelect").on("change",function(e){
    　  var value = $('.jobTypeSelect').val().toString().toLowerCase();
    　  if(!value){
    　      $("#searchableCompanyList tr").show();
    　  }else{
        　  $("#searchableCompanyList tr").filter(function() {
                  var jobType = $(this).find($("td:nth-child(2)")).text().trim().toLowerCase();
                  if (jobType && !value.includes(",")) {
                      $(this).toggle(value == jobType);
                  } else if (jobType) {
                      $(this).toggle(value.includes(jobType))
                  } else {
                       $(this).hide();
                  }
             });
    　  
    　  }
    　  
    　});
    
    //MaruShowFirstYaruki　
    $(".maruShowFirstYaruki").on("click",function(e) {
        e.stopPropagation();
        $("#searchableCompanyList").empty();
        DisplayMaruShowFirst("yaruki");
        
    })
    
    //MaruShowFIrstRevit
    $(".maruShowFirstRevit").on("click",function(e) {
        e.stopPropagation();
        $("#searchableCompanyList").empty();
        DisplayMaruShowFirst("revit");
    })
    
    //MaruShowFirstIpd
    $(".maruShowFirstIpd").on("click",function(e) {
        e.stopPropagation();
        $("#searchableCompanyList").empty();
        DisplayMaruShowFirst("iPDStudent");
    })
    
    //MaruShowFirstSatelite
    $(".maruShowFirstSatellite").on("click",function(e) {
        e.stopPropagation();
        $("#searchableCompanyList").empty();
        DisplayMaruShowFirst("satelliteExp");
    })
    
    
    
    $("#minimizeBtn2").on("click", function(){
           $( ".second").toggle();
           if($(".second").is(":visible")){
                $("#minimizeBtn2").removeClass("glyphicon glyphicon-plus");
                $("#minimizeBtn2").addClass("glyphicon glyphicon-minus");
                ShowView("isJobTypeShow",loginUser);
            } else{
                $("#minimizeBtn2").removeClass("glyphicon glyphicon-minus");
                $("#minimizeBtn2").addClass("glyphicon glyphicon-plus");
                HideView("isJobTypeShow",loginUser);
            }
       });
       
       $("#minimizeBtn3").on("click", function(){
           $( ".third").toggle();
           if($(".third").is(":visible")){
                $("#minimizeBtn3").removeClass("glyphicon glyphicon-plus");
                $("#minimizeBtn3").addClass("glyphicon glyphicon-minus");
                ShowView("isYarukiShow",loginUser);
            } else{
                $("#minimizeBtn3").removeClass("glyphicon glyphicon-minus");
                $("#minimizeBtn3").addClass("glyphicon glyphicon-plus");
                 HideView("isYarukiShow",loginUser);
            }
       });
       
       $("#minimizeBtn4").on("click", function(){
           $( ".fouth").toggle();
           if($(".fouth").is(":visible")){
                $("#minimizeBtn4").removeClass("glyphicon glyphicon-plus");
                $("#minimizeBtn4").addClass("glyphicon glyphicon-minus");
                ShowView("isRevitShow",loginUser);
            } else{
                $("#minimizeBtn4").removeClass("glyphicon glyphicon-minus");
                $("#minimizeBtn4").addClass("glyphicon glyphicon-plus");
                HideView("isRevitShow",loginUser);
            }
       });
       
       $("#minimizeBtn5").on("click", function(){
           $( ".fifth").toggle();
           if($(".fifth").is(":visible")){
                $("#minimizeBtn5").removeClass("glyphicon glyphicon-plus");
                $("#minimizeBtn5").addClass("glyphicon glyphicon-minus");
                ShowView("isIpdShow",loginUser);
            } else{
                $("#minimizeBtn5").removeClass("glyphicon glyphicon-minus");
                $("#minimizeBtn5").addClass("glyphicon glyphicon-plus");
                HideView("isIpdShow",loginUser);
            }
       });
       
       $("#minimizeBtn6").on("click", function(){
           $( ".sixth").toggle();
           if($(".sixth").is(":visible")){
                $("#minimizeBtn6").removeClass("glyphicon glyphicon-plus");
                $("#minimizeBtn6").addClass("glyphicon glyphicon-minus");
                ShowView("isSatelliteExpShow",loginUser);
            } else{
                $("#minimizeBtn6").removeClass("glyphicon glyphicon-minus");
                $("#minimizeBtn6").addClass("glyphicon glyphicon-plus");
                HideView("isSatelliteExpShow",loginUser);
            }
       });
       
       $("#minimizeBtn7").on("click", function(){
           $( ".seven").toggle();
           if($(".seven").is(":visible")){
                $("#minimizeBtn7").removeClass("glyphicon glyphicon-plus");
                $("#minimizeBtn7").addClass("glyphicon glyphicon-minus");
                ShowView("isSatelliteProjNameShow",loginUser);
            } else{
                $("#minimizeBtn7").removeClass("glyphicon glyphicon-minus");
                $("#minimizeBtn7").addClass("glyphicon glyphicon-plus");
                HideView("isSatelliteProjNameShow",loginUser);
            }
       });
       
       $("#minimizeBtn8").on("click", function(){
           $( ".eight").toggle();
           if($(".eight").is(":visible")){
                $("#minimizeBtn8").removeClass("glyphicon glyphicon-plus");
                $("#minimizeBtn8").addClass("glyphicon glyphicon-minus");
                ShowView("isRemarkShow",loginUser);
            } else{
                $("#minimizeBtn8").removeClass("glyphicon glyphicon-minus");
                $("#minimizeBtn8").addClass("glyphicon glyphicon-plus");
                HideView("isRemarkShow",loginUser);
            }
       });
       
       $("#minimizeBtn9").on("click", function(){
           $( ".nine").toggle();
           if($(".nine").is(":visible")){
                $("#minimizeBtn9").removeClass("glyphicon glyphicon-plus");
                $("#minimizeBtn9").addClass("glyphicon glyphicon-minus");
                ShowView("isInchargeNameShow",loginUser);
            } else{
                $("#minimizeBtn9").removeClass("glyphicon glyphicon-minus");
                $("#minimizeBtn9").addClass("glyphicon glyphicon-plus");
                HideView("isInchargeNameShow",loginUser);
            }
       });
       
       $("#minimizeBtn10").on("click", function(){
           $( ".ten").toggle();
           if($(".ten").is(":visible")){
                $("#minimizeBtn10").removeClass("glyphicon glyphicon-plus");
                $("#minimizeBtn10").addClass("glyphicon glyphicon-minus");
                ShowView("isPhoneShow",loginUser);
            } else{
                $("#minimizeBtn10").removeClass("glyphicon glyphicon-minus");
                $("#minimizeBtn10").addClass("glyphicon glyphicon-plus");
                HideView("isPhoneShow",loginUser);
            }
       });
       
       $("#minimizeBtn11").on("click", function(){
           $( ".eleven").toggle();
           if($(".eleven").is(":visible")){
                $("#minimizeBtn11").removeClass("glyphicon glyphicon-plus");
                $("#minimizeBtn11").addClass("glyphicon glyphicon-minus");
                ShowView("isEmailShow",loginUser);
            } else{
                $("#minimizeBtn11").removeClass("glyphicon glyphicon-minus");
                $("#minimizeBtn11").addClass("glyphicon glyphicon-plus");
                HideView("isEmailShow",loginUser);
            }
       });
       
    
});

function InsertBtn(){
    $("#insertBtn").addClass("disabledBtn");
    window.location='../partnerCompany/index';
}

function HideView(columnName,loginUser){
    $.ajax({
        url: "../partnerCompany/updateData",
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
        url: "../partnerCompany/updateData",
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
                                $(".jobTypeSelect").append(option);
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

function DisplayMaruShowFirst(columnName){
    $.ajax({
        url: "../partnerCompany/getData",
        async:true,
        type: 'post',
        data:{_token: CSRF_TOKEN, message:"getMaruFirst", columnName : columnName},
        success :function(data) {
            console.log(data)
            if(data){
                ShowMaruFirstTable(data);
              
            }
        },
        error:function(err){
            console.log(err);
        }
    });
}

function ShowMaruFirstTable(data){
        $.each(data,function(tname, tdata) {
            var row = "<tr>"+
                      "<td>" + tdata['name'] + "</td>"+
                      "<td><div class='second'>" + (tdata['industry_type'] ? tdata['industry_type'] : ' ' ) + "</div></td>"+
                      "<td><div class='third'>" + ((tdata['yaruki'] == 'NULL') ? ' ' : tdata['yaruki'] ) + "</div></td>"+
                      "<td><div class='fouth'>" + ((tdata['revit'] == 'NULL') ? ' ' : tdata['revit'] ) + "</div></td>"+
                      "<td><div class='fifth'>" + ((tdata['iPDStudent'] == 'NULL') ? ' ' : tdata['iPDStudent'] )+ "</div></td>"+
                      "<td><div class='sixth'>" + ((tdata['satelliteExp'] == 'NULL') ? ' ' : tdata['satelliteExp'] ) + "</div></td>"+
                      "<td><div class='seven'>" + ((tdata['satelliteName'] == 'NULL') ? ' ' : tdata['satelliteName'] ) + "</div></td>"+
                      "<td><div class='eight'>" + ((tdata['remark'] == 'NULL') ? ' ' : tdata['remark'] ) + "</div></td>";
            if(tdata['incharge'].length > 0){
                row+= "<td><table>";
                for(var i=0; i<tdata['incharge'].length ; i++ ){
                    row+= "<tr><td><div class='nine'>" + tdata['incharge'][i]['name']  +"</div></td></tr>";
                }
                row+= "</table></td>";
                
                row+= "<td><table>";
                for(var i=0; i<tdata['incharge'].length ; i++ ){
                    row+= "<tr><td><div class='ten'>" + tdata['incharge'][i]['phone']  +"</div></td></tr>";
                }
                row+= "</table></td>";
                
                row+= "<td><table>";
                for(var i=0; i<tdata['incharge'].length ; i++ ){
                    row+= "<tr><td><div class='eleven'>" + tdata['incharge'][i]['mail']  +"</div></td></tr>";
                }
                row+= "</table></td>";
                
                row+= "</tr>";
            }else{
                row+= "<td><div class='nine'></div></td>"+
                      "<td><div class='ten'></div></td>"+
                      "<td><div class='eleven'></div></td>"+
                      "</tr>";
            }
                      
            $("#searchableCompanyList").append(row);
        })
    
    
}

//Sorting Table
function sortTable(){
  var rows = $('#searchableCompanyList  tr').get();

  rows.sort(function(a, b) {

  var A = $(a).children('td').eq(1).text().toUpperCase();
  var B = $(b).children('td').eq(1).text().toUpperCase();

  if(A < B) {
    return -1;
  }

  if(A > B) {
    return 1;
  }

  return 0;

  });

  $.each(rows, function(index, row) {
    //$('#mytable').children('tbody').append(row);
    $('#searchableCompanyList').append(row);
  });
}

sortTable();