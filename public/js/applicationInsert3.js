var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function() {
    $.ajaxSetup({
        cache: false
    });

    var userInfoList = JSON.parse($("#hidUserInfoList").val());
    console.log(userInfoList);
})

function SaveAppliedUserInfo() {

    var idList = [];
    var nameList = [];
    var lastNameList = [];
    var mailList = [];
    var companyTypeList = [];
    var companyTypeIdList = [];
    var companyNameList = [];
    var companyIdList = [];
    var deptList = [];
    var deptIdList = [];
    var branchList = [];
    var branchIdList = [];
    var codeList = [];
    var positionTypeList = [];
    var isStudyAbroadList = [];
    var isC3UserList = [];
    var isAdditionalPostList = [];
    var contractTypeList = [];
    var applicants = $("#userInfoTableBody tr").length;
    console.log(applicants)
    for (var i = 1; i <= applicants; i++) {
        idList.push($("#userInfoTableBody #" + i).html());
        nameList.push($("#username" + i).html());
        lastNameList.push($("#lastname" + i).html());
        mailList.push($("#email" + i).html());
        companyTypeList.push($("#companyType" + i).html());
        companyTypeIdList.push($("#companyTypeId" + i).html());
        companyNameList.push($("#companyName" + i).html());
        companyIdList.push($("#companyId" + i).html());
        deptList.push($("#dept" + i).html());
        deptIdList.push($("#deptId" + i).html());
        branchList.push($("#branch" + i).html());
        branchIdList.push($("#branchId" + i).html());
        codeList.push($("#code" + i).html());
        positionTypeList.push($("#position" + i).html());
        isStudyAbroadList.push($("#isStudyAbroad" + i).html());
        isC3UserList.push($("#isC3User" + i).html());
        isAdditionalPostList.push($("#isAdditionalPost" + i).html());
        contractTypeList.push($("#hidContractType" + i).html());

    }
    console.log(idList)
    console.log(nameList)
    console.log(lastNameList)
    console.log(mailList)
    console.log(companyTypeList)
    console.log(companyTypeIdList)
    console.log(companyNameList)
    console.log(companyIdList)
    console.log(deptList)
    console.log(deptIdList)
    console.log(branchList)
    console.log(branchIdList)
    console.log(codeList)
    console.log(positionTypeList)
    console.log(contractTypeList)

    var userInfoList = idList.map((id, index) => {
        return {
            id: id,
            name: nameList[index],
            lastname: lastNameList[index],
            email: mailList[index],
            companyType: companyTypeList[index],
            companyTypeId: companyTypeIdList[index],
            companyName: companyNameList[index],
            companyId: companyIdList[index],
            dept: deptList[index],
            deptId: deptIdList[index],
            branch: branchList[index],
            branchId: branchIdList[index],
            code: codeList[index],
            position: positionTypeList[index],
            isStudyAbroad: isStudyAbroadList[index],
            isC3User: isC3UserList[index],
            isAdditionalPost: isAdditionalPostList[index],
            contractType: contractTypeList[index],
        }
    });
    var desireDate = $("#desireDate").val();
    var inviter = $("#inviter").val();
    var classType = $("#classType").val();
    console.log(userInfoList);
    console.log(desireDate);
    console.log(inviter);
    $.ajax({
        type: "post",
        url: "/iPD/application/saveInsertData",
        data: { _token: CSRF_TOKEN, message: "insertData", userInfoList: userInfoList, desireDate: desireDate, inviter: inviter, classType: classType },
        success: function(data) {
            console.log(data);

            alert("情報入力しました！");
            window.location.href = "/iPD/application/index";

        },
        error: function(err) {
            console.log(err);
        }
    });

}
