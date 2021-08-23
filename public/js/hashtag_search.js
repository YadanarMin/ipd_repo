/* ajax通信トークン定義 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
var hashtags = [];
var display_record = 0;
var total_record = 0;
var offset = 0;
var result = [];
$(document).ready(function() {

    var login_user_id = $("#hidLoginID").val();
    var img_src = "/iPD/public/image/JPG/会員証のアイコン素材.jpeg";
    var url = "hashtag/index";
    var content_name = "ハッシュタグ検索";
    recordAccessHistory(login_user_id, img_src, url, content_name);


    $('#tab1').on('click', function() {
        var className = $(this).attr('class');
        if (!className) {
            $('#tab2').removeClass('tab-item');
            $(this).addClass('tab-item');
            $('.post').show();
            $('.images').hide();
        }
    })

    $('#tab2').on('click', function() {
        var className = $(this).attr('class');
        if (!className) {
            $('#tab1').removeClass('tab-item');
            $(this).addClass('tab-item');
            $('.post').hide();
            $('.images').show();
        }
    })

    $(".closeSign1").on('click', function() {
        console.log($(this).siblings().find($(".hashtag1")).html());
        $(this).siblings().find($(".hashtag1")).html("");
        $(this).siblings().find($(".highlighter")).html('');
    })

    LoadHashtagList();

});

function LoadHashtagList() {
    ShowLoading();
    var result = [];
    // $.ajax({
    //     url: "/iPD/hashtag/getData",
    //     type: 'post',
    //     data: { _token: CSRF_TOKEN, message: "get_hashtag_list" },
    //     success: function(data) {
    //         if (data) {
    //             console.log(data);
    //             $.each(data, function(key, value) {
    //                 var hashtagStr = value['hashtags'];
    //                 if (hashtagStr) {
    //                     var arr = hashtagStr.split(',');
    //                     for (var key of arr) {
    //                         if (key in hashtagList) {
    //                             hashtagList[key]++;
    //                         }
    //                         else {
    //                             hashtagList[key] = 1;
    //                         }
    //                     }

    //                 }
    //             })

    //             let keys = Object.keys(hashtagList);
    //             keys.sort(function(a, b) { return hashtagList[b] - hashtagList[a] });
    //             for (var i of keys) {
    //                 hashtags.push(i);
    //             }
    //             console.log(hashtags)
    //             ShowHashTag(hashtags);
    //             HideLoading();

    //         }
    //     },
    //     error: function(err) {
    //         console.log(err);
    //         HideLoading();
    //     }
    // });
    $.ajax({
        url: "/iPD/report/getData",
        async: false,
        type: 'post',
        data: { _token: CSRF_TOKEN, message: "get_hashtags" },
        success: function(data) {
            if (data != null) {
                $.each(data, function(k, row) {
                    result.push({ "id": row["id"], "value": row["name"] });
                })
                ShowHashTag(result)
                HideLoading();

            }
        },
        error: function(err) {
            console.log(err);
            console.log("load hashtags ===> error");
            HideLoading();
            //alert("データロードに失敗しました。\n管理者に問い合わせてください。");
        }
    });
}

function ShowHashTag(data) {
    var optionStr = "";
    // for (var i = 0; i < data.length; i++) {
    //     optionStr += "<option value='" + data[i] + "'>" + data[i] + "</option>";
    // }
    $.each(data, function(k, row) {
        optionStr += "<option value='" + row['value'] + "'>" + row['value'] + "</option>";
    })
    $(".hashtag1").append(optionStr);
    $(".hashtag1").select2();

}

function SearchReport() {
    ShowLoading();
    var validate = Validation();
    if (validate) {
        var search_logic = $('#search_logic').val();
        var hashtagList = $('.hashtag1').val();
        console.log(hashtagList)
        hashtags = hashtagList.toString().split(",");
        if (!hashtagList.length) {
            HideLoading();
        }
        console.log(hashtagList);
        $.ajax({
            url: "/iPD/hashtag/getData",
            type: 'post',
            data: { _token: CSRF_TOKEN, message: "get_report_data", hashtag: hashtagList, search_logic: search_logic },
            success: function(data) {
                if (data) {
                    result = data;
                    offset = 50;
                    ShowData(data);

                    if (data.length < 50) {
                        display_record = data.length;
                        total_record = data.length;
                        ShowData(data);
                        HideLoading();
                    }
                    else {
                        total_record = data.length;
                        offset = 50;
                        getResultByRowIncrease(result, offset);
                        HideLoading();

                    }

                }
                else {
                    $('#tblPosts tbody').empty();
                }
            },
            error: function(err) {
                console.log(err);
            }
        });



    }
    else {
        alert('検索したいハッシュタグを入力してください。')
    }
}

function Validation() {
    var searchTxt1 = $('.hashtag1').html().trim();

    if (!searchTxt1) {
        return false;
    }
    return true;
}

function ShowData(data) {
    console.log(hashtags)

    var row = '';
    $.each(data, function(key, value) {
        row += "<tr>" +
            "<td>" + value['name'] + "</td>" +
            "<td>" + value['dept_name'] + "</td>" +
            "<td><div class='report'>" + value['report'] + "</div></td>" +
            "<td>" + value['saved_date'] + "</td>" +
            "</tr>";
    });

    $('#tblPosts tbody').empty();
    $('#tblPosts tbody').append(row);

    $(".report").each(function() {
        MakeHighlightHashTag($(this), $(this).html());
    });

    $('#display_record').html(display_record);
    $('#total_record').html(total_record);
}


function getResultByRowIncrease() {

    var row = '';
    if (offset < result.length) {
        for (var i = 0; i < offset; i++) {
            row += "<tr>" +
                "<td>" + result[i].name + "</td>" +
                "<td>" + result[i].dept_name + "</td>" +
                "<td><div class='report'>" + result[i].report + "</div></td>" +
                "<td>" + result[i].saved_date + "</td>" +
                "</tr>";
        }
        $('#tblPosts tbody').empty();
        $('#tblPosts tbody').append(row);
        $(".report").each(function() {
            MakeHighlightHashTag($(this), $(this).html());
        });

        $('#display_record').html(offset);
        $('#total_record').html(total_record);
        offset += 50;
    }
    else {
        for (var i = 0; i < result.length; i++) {
            row += "<tr>" +
                "<td>" + result[i].name + "</td>" +
                "<td>" + result[i].dept_name + "</td>" +
                "<td><div class='report'>" + result[i].report + "</div></td>" +
                "<td>" + result[i].saved_date + "</td>" +
                "</tr>";
        }
        $('#tblPosts tbody').empty();
        $('#tblPosts tbody').append(row);
        $(".report").each(function() {
            MakeHighlightHashTag($(this), $(this).html());
        });
        $('#display_record').html(total_record);
        $('#total_record').html(total_record);
    }

}

function getAllResult() {
    var row = '';
    $.each(result, function(key, value) {
        row += "<tr>" +
            "<td>" + value['name'] + "</td>" +
            "<td>" + value['dept_name'] + "</td>" +
            "<td><div class='report'>" + value['report'] + "</div></td>" +
            "<td>" + value['saved_date'] + "</td>" +
            "</tr>";
    });

    $('#tblPosts tbody').empty();
    $('#tblPosts tbody').append(row);

    $(".report").each(function() {
        MakeHighlightHashTag($(this), $(this).html());
    });

    $('#display_record').html(result.length);
    $('#total_record').html(result.length);
}

function MakeHighlightHashTag(ele, str) {
    if (!str.match(/(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?#([a-zA-Z0-9]+)/g) && !str.match(/(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?@([a-zA-Z0-9]+)/g) && !str.match(/(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?#([\u0600-\u06FF]+)/g) && !str.match(/(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?@([\u0600-\u06FF]+)/g)) {
        if (!str.match(/#(([_a-zA-Z0-9]+)|([\u0600-\u06FF]+)|([ㄱ-ㅎㅏ-ㅣ가-힣]+)|([ぁ-んァ-ン]+)|([一-龯]+))#/g)) {
            str = str.replace(/#(([_a-zA-Z0-9ぁ-んァ-ン一-龯\(\)\（\）\.\・\-\ー]+)|()|([\u0600-\u06FF]+)|([ㄱ-ㅎㅏ-ㅣ가-힣]+)|([ぁ-んァ-ン]+)|([一-龯]+))/g, '<span class="hashtag">#$1</span>');
        }
        else {
            str = str.replace(/#(([_a-zA-Z0-9ぁ-んァ-ン一-龯\(\)\（\）\.\・\-]+)|()|([\u0600-\u06FF]+)|([ㄱ-ㅎㅏ-ㅣ가-힣]+)|([ぁ-んァ-ン]+)|([一-龯]+))#(([_a-zA-Z0-9]+)|([\u0600-\u06FF]+)|([ㄱ-ㅎㅏ-ㅣ가-힣]+)|([ぁ-んァ-ン]+)|([一-龯]+))/g, '<span class="hashtag">#$1</span>');
        }
    }

    $(ele).html(str);
}
