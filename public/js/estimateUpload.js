  /* ajax通信トークン定義 */
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  let allstore_pj_code = [];
  let fix_label = { "": "", "per": "パース：per", "ext": "外観：ext", "int": "内観：int", "sit": "現場：sit", "vr": "VR：vr", "mr": "MR：mr", "mtg": "打合せ風景：mtg", "otr": "他：otr" };
  let SelectedFiles = [];
  $(document).ready(function() {
    InitialLoadData();
    TextSearch();

    $(document).on('change', 'select', function() {

      var rowIndex = $(this).closest('tr').index();
      console.log(rowIndex);
      MakeNewFileName(rowIndex);
    });

    $(document).on('change', '.txtInput', function() {

      var rowIndex = $(this).closest('tr').index();
      MakeNewFileName(rowIndex);
    });

    $('#file').change(function() {
      var login_email = $("#login_email").val() ? $("#login_email").val() : "";
      if (login_email.includes('@')) {
        login_email = login_email.split('@')[0];
      }
      var appendDiv = "";
      var index = 0;
      SelectedFiles = $(this)[0].files;
      console.log($(this));
      console.log(SelectedFiles);

      appendDiv += "<tr>";
      appendDiv += "<th class='td-wd-1'>元ファイル名</th>";
      appendDiv += "<th class='td-wd-0'></th>";
      appendDiv += "<th class='td-wd-3'>プロジェクトコード</th>";
      appendDiv += "<th class='td-wd-1'>登録者イニシャル</th>";
      appendDiv += "<th class='td-wd-1'>自由記述欄</th>";
      appendDiv += "<th class='td-wd-2'>ラベル選択</th>";
      appendDiv += "<th class='td-wd-0'></th>";
      appendDiv += "<th class='td-wd-3'>新ファイル名</th>";
      appendDiv += "</tr>";

      $.each(SelectedFiles, function(key, file) {
        index++;
        appendDiv += "<tr>";
        appendDiv += "<td class='td-wd-1'><input type='text' id='txtOrgName" + index + "' class='form-control input-sm' value='" + file.name + "' disabled/></td>";
        appendDiv += "<td class='td-wd-0 txt-bold'>⇒</td>";
        appendDiv += "<td class='td-wd-3'><select id='cmb" + index + "' class='cmb form-control input-sm'>";
        appendDiv += "<option></option>";
        $.each(allstore_pj_code, function(ind, item) {
          appendDiv += "<option value='" + item["pj_code"] + "'>" + item["pj_name"] + "</option>";
        });
        appendDiv += "</select></td>";
        appendDiv += "<td class='td-wd-1'><input type='text' id='txtLoginEmail" + index + "' class='form-control input-sm txtInput' value='" + login_email + "'/></td>";
        appendDiv += "<td class='td-wd-1'><input type='text' id='txtCustom" + index + "' class='form-control input-sm txtInput' value=''/></td>";
        appendDiv += "<td class='td-wd-2'><select id='cmbFix" + index + "' class='cmbFix form-control input-sm'>";
        $.each(fix_label, function(k, fix_str) {
          appendDiv += "<option value='" + k + "'>" + fix_str + "</option>";
        });
        appendDiv += "</select></td>";
        appendDiv += "<td class='td-wd-0 txt-bold'>＝</td>";
        appendDiv += "<td class='td-wd-3'><input type='text' id='txtNewFileName" + index + "' class='form-control input-sm' value=''/></td>";
        appendDiv += "</tr>";

      });

      $("#tblRename tr").remove();
      $("#tblRename").append(appendDiv);

      $(".cmb").select2();
      $(".cmbFix").select2();
      //alert(JSON.stringify(selectedFiles[0].name));
    });
  });

  function MakeNewFileName(rowIdx) {
    var oldFileName = $("#txtOrgName" + rowIdx).val();
    var extension = "";
    if (oldFileName != undefined && oldFileName.includes('.')) {
      var temp = oldFileName.split('.');
      var lastIndex = temp.length - 1;
      extension = "." + temp[lastIndex];
    }
    var pj_code = $("#cmb" + rowIdx).val();
    var creator = $("#txtLoginEmail" + rowIdx).val();
    var custom_text = $("#txtCustom" + rowIdx).val();
    var label = $("#cmbFix" + rowIdx).val();
    var newFileName = pj_code + "_" + creator + "_" + custom_text + "_" + label + extension;
    $("#txtNewFileName" + rowIdx).val(newFileName);
  }

  function TextSearch() {
    $('#txtSearch').keyup(function() {
      var textboxValue = $('#txtSearch').val();
      $("#tblUpload tbody tr").each(function() {
        var fileName = $(this).find("td:eq(1)").html();
        if (!fileName.includes(textboxValue)) {
          $(this).hide();
        }
        else {
          $(this).show();
        }
      });

    });
  }

  function InitialLoadData() {
    LoadAllstoreProjectCode();
    var isBoxLogin = CheckBoxLogin();
    if (isBoxLogin) {

    }
  }

  function LoadReportImageFromBox() {
    $.ajax({
      url: "../report/getReportImage",
      type: 'post',
      async: false,
      data: { _token: CSRF_TOKEN, message: "get_all_item" },
      success: function(data) {
        if (data["report_images"] != "" && data["report_images"] != undefined) {
          CreatTableBody(data["report_images"]);
        }

      },
      error: function(err) {

        if (err.responseText.includes("401 Unauthorized")) {
          $("#box_login_warning").html("BOX TOKEN の有効期限が切れたため、処理できません。BOXに再ログインしてください。")
          return;
        }
        console.log(err);
      }
    });
  }

  function LoadAllstoreProjectCode() {

    $.ajax({
      url: "../report/getData",
      type: 'post',
      async: false,
      data: { _token: CSRF_TOKEN, message: "get_allstore_projectcode" },
      success: function(data) {
        if (data != "" && data.length > 0) {
          allstore_pj_code = data;
        }

      },
      error: function(err) {

        console.log(err);
      }
    });
  }

  function UploadImages() { //upload to server not using now

    /*$.ajax({
    url: "../report/uploadImages",
    type: 'post',
    data:{_token: CSRF_TOKEN,message:"upload_image","fileNames":JSON.stringify(fileNames)},
    success :function(data) {
        //alert(JSON.stringify(data));
        if(data.includes("success")){
            location.reload();
        }
        
    },
    error:function(err){
        console.log(err);
    }
    });*/
  }


  function DeleteFile(fileName) {
    //alert();
    $.ajax({
      url: "../report/uploadImages",
      type: 'post',
      data: { _token: CSRF_TOKEN, message: "delete_image", fileName: fileName },
      success: function(data) {
        //alert(JSON.stringify(data));
        if (data.includes("success")) {
          location.reload();
        }

      },
      error: function(err) {
        console.log(err);
      }
    });

  }

  function CreatTableBody(data) {
    var row = "";
    var count = 0;
    $.each(data, function(key, value) {
      count++;
      row += "<tr>";
      row += "<td width='10%'>" + count + "</td>";
      row += "<td>" + value + "</td>";
      row += "</tr>";
    });
    if (row != "") {
      $("#tblUpload tbody tr").remove();
      $("#tblUpload tbody").append(row);

    }
  }

  function CheckBoxLogin() {
    var access_token = $("#access_token").val();

    if (access_token == undefined) {
      $("#box_login_warning").html("Boxにログインされていないため、処理できません。");
      return false;
    }
    return true;
  }
  