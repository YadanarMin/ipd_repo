/*!
  jQuery #hashtags v0.5.0
	(c) 2013 Simon Nussbaumer
	updated: 2016-25-10
	license: GNU LESSER GENERAL PUBLIC LICENSE
*/

// var hashtags = ["#構造設計",
// 	"#SS3などからのデータ連携",
// 	"#断面表の作成",
// 	"#基本設計からモデル作成",
// 	"#施主との合意形成",
// 	"#3D活用",
// 	"#意匠設計",
// 	"#協力会社設計協力",
// 	"#BIM360活用",
// 	"#点群",
// 	"#Arch-Log",
// 	"#見積・積算部",
// 	"#鉄骨数量積算",
// 	"#ヘリオス連携",
// 	"#Revitモデル積算",
// 	"#リニューアル部",
// 	"#既存モデル作成",
// 	"#仮設計画",
// 	"#ロボット",
// 	"#設備設計",
// 	"#諸元表連携",
// 	"#BIMゾーン使用",
// 	"#BIM360活用",
// 	"#品質管理部",
// 	"#杭連携",
// 	"#カット長連携",
// 	"#開口補強筋連携",
// 	"#MR",
// 	"#工事部",
// 	"#ビッグデータ活用",
// 	"#生産技術部",
// 	"#協力会社参画",
// 	"#TC検討構造設計部連携",
// 	"#仮設見積積算",
// 	"#施工ステップ",
// 	"#生産設計部",
// 	"#鉄骨モデル確認会実施(生産設計部主催)",
// 	"#フロントローディングによるモデル参画",
// 	"#デジタルモックアップ",
// 	"#ドローン",
// 	"#工事事務所(建築・生産設計含む)",
// 	"#色分け図(○○件)",
// 	"#現場数量積算",
// 	"#内装下地補強図",
// 	"#現場デジタルモックアップ",
// 	"#内装下地補強図",
// 	"#3Dビュー利用",
// 	"#施工ステップ作成",
// 	"#周辺モデル作成",
// 	"#共通仮設モデリング ",
// 	"#共通仮設数量積算",
// 	"#掘削図・モデル作成",
// 	"#掘削数量積算",
// 	"#鉄骨ピース数算出",
// 	"#鉄骨専用CADデータ連携(協力会社)",
// 	"#外部足場モデリング(職員)",
// 	"#外部足場数量算出",
// 	"#足場図面監督署提出",
// 	"#コンクリート数量算出",
// 	"#部屋色分け図",
// 	"#部屋情報数量算出",
// 	"#BIMモデル・図面計画書添付",
// 	"#金物色分け・数量算出",
// 	"#家具色分け・数量算出",
// 	"#建具色分け・数量算出",
// 	"#サテライトモデル",
// 	"#サテライトモデル(数量算出)",
// 	"#サテライトモデル(干渉チェック)",
// 	"#工事事務所(設備)",
// 	"#SpiderPlus",
// 	"#協力会社Revit使用",
// 	"#鉄骨スリーブ連携"
// ];
var active_hashtag = "";
var inputDiv = "";
var div_id = "";
//var editor = "";
var curPostion;


function renderText(text, curText, selectedTag) {
    const words = text.split(/(\s+)/);
    const output = words.map((word) => {
        if (word === curText) {
            //var start_word_position = curPostion - curText.length;
            //curPostion = curPostion + selectedTag.length;
            return selectedTag;
            return "<span class='hashtag_color'>" + selectedTag + "</span>";
        }
        else {
            return word;
        }
    })
    return output.join('');
}

(function($) {
    $.fn.hashtags = function() {
        $(this).wrap('<div class="jqueryHashtags"><div class="highlighter"></div></div>').unwrap().before('<div class="highlighter"></div>').wrap('<div class="typehead"></div></div>');
        $(this).addClass("theSelector");
        $(this).on("keyup", function(evnt) {

            if (evnt.keyCode == 13) { //enter
                $("#hashtag_popup").css({ display: "none" });
            }
            else if (evnt.keyCode == 32) { //,space bar
                $("#hashtag_popup").css({ display: "none" });
            }

            inputDiv = $(this);
            var str = $(this).html();
            div_id = $(this).attr('id');
            MakeHighlight($(this), str);
            // $(this).parent().parent().find(".highlighter").css({ "width": $(this).css("width"), "height": $(this).css("height") });
            // if (!str.match(/(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?#([a-zA-Z0-9]+)/g) && !str.match(/(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?@([a-zA-Z0-9]+)/g) && !str.match(/(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?#([\u0600-\u06FF]+)/g) && !str.match(/(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?@([\u0600-\u06FF]+)/g)) {
            // 	if (!str.match(/#(([_a-zA-Z0-9]+)|([\u0600-\u06FF]+)|([ㄱ-ㅎㅏ-ㅣ가-힣]+)|([ぁ-んァ-ン]+)|([一-龯]+))#/g)) { //arabic support, CJK support
            // 		str = str.replace(/#(([_a-zA-Z0-9ぁ-んァ-ン一-龯\(\)\（\）\.\・]+)|([\u0600-\u06FF]+)|([ㄱ-ㅎㅏ-ㅣ가-힣]+)|([ぁ-んァ-ン]+)|([一-龯]+))/g, '<span class="hashtag">#$1</span>');
            // 	}
            // 	else {
            // 		str = str.replace(/#(([_a-zA-Z0-9ぁ-んァ-ン一-龯\(\)\（\）\. \・]+)|([\u0600-\u06FF]+)|([ㄱ-ㅎㅏ-ㅣ가-힣]+)|([ぁ-んァ-ン]+)|([一-龯]+))#(([_a-zA-Z0-9]+)|([\u0600-\u06FF]+)|([ㄱ-ㅎㅏ-ㅣ가-힣]+)|([ぁ-んァ-ン]+)|([一-龯]+))/g, '<span class="hashtag">#$1</span>');
            // 	}

            // }

            // $(this).parent().parent().find(".highlighter").html(str);

            curPostion = $(this).getCursorPosition();
            //var selectionRange = saveSelection();

            $(this).parent().parent().find(".highlighter span").each(function() {
                // $(this).html($(this).html().trim()); //trim space
                var hashtag_text = $(this).html();

                //inputDiv.caretTo(hashtag_text);
                var start_hashtag_text = inputDiv.getCursorPosition();
                var end_hashtag_text = start_hashtag_text + hashtag_text.length;
                //alert(curPostion + "\n" + start_hashtag_text + "\n" + end_hashtag_text);
                if (curPostion >= start_hashtag_text && curPostion <= end_hashtag_text) {
                    //alert(curPostion + "\n" + start_hashtag_text + "\n" + end_hashtag_text);
                    $(".highlighter span").removeAttr('id');
                    $(this).attr('id', "active_hashtag");
                    var y = $(this).offset().top;
                    var x = $(this).offset().left;
                    CreateHashTagPopup(x, y, hashtag_text);
                    active_hashtag = $(this);
                    //alert(start_hashtag_text+"\n"+end_hashtag_text+"\n"+curPostion);
                }
                //inputDiv.caretTo(curPostion);

            });

        });

        $(this).parent().prev().on('click', function() {
            $(this).parent().find(".theSelector").focus();
        });

    };
})(jQuery);


function MakeHighlight(ele, str) {
    $(ele).parent().parent().find(".highlighter").css({ "width": $(ele).css("width"), "height": $(ele).css("height") });
    if (!str.match(/(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?#([a-zA-Z0-9]+)/g) && !str.match(/(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?@([a-zA-Z0-9]+)/g) && !str.match(/(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?#([\u0600-\u06FF]+)/g) && !str.match(/(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?@([\u0600-\u06FF]+)/g)) {
        if (!str.match(/#(([_a-zA-Z0-9]+)|([\u0600-\u06FF]+)|([ㄱ-ㅎㅏ-ㅣ가-힣]+)|([ぁ-んァ-ン]+)|([一-龯]+))#/g)) { //arabic support, CJK support
            str = str.replace(/#(([_a-zA-Z0-9ぁ-んァ-ン一-龯\(\)\（\）\.\・\-\ー]+)|()|([\u0600-\u06FF]+)|([ㄱ-ㅎㅏ-ㅣ가-힣]+)|([ぁ-んァ-ン]+)|([一-龯]+))/g, '<span class="hashtag">#$1</span>');
        }
        else {
            str = str.replace(/#(([_a-zA-Z0-9ぁ-んァ-ン一-龯\(\)\（\）\.\・\-]+)|()|([\u0600-\u06FF]+)|([ㄱ-ㅎㅏ-ㅣ가-힣]+)|([ぁ-んァ-ン]+)|([一-龯]+))#(([_a-zA-Z0-9]+)|([\u0600-\u06FF]+)|([ㄱ-ㅎㅏ-ㅣ가-힣]+)|([ぁ-んァ-ン]+)|([一-龯]+))/g, '<span class="hashtag">#$1</span>');
        }

    }

    $(ele).parent().parent().find(".highlighter").html(str);
}

// Set caret position easily in jQuery
// Written by and Copyright of Luke Morton, 2011
// Licensed under MIT
(function($) {
    // Behind the scenes method deals with browser
    // idiosyncrasies and such
    $.caretTo = function(el, index) {
        if (el.createTextRange) {
            var range = el.createTextRange();
            range.move("character", index);
            range.select();
        }
        else if (el.selectionStart != null) {
            el.focus();
            el.setSelectionRange(index, index);
        }
        else {
            //el.focus(index, index);
        }
    };

    // The following methods are queued under fx for more
    // flexibility when combining with $.fn.delay() and
    // jQuery effects.

    // Set caret to a particular index
    $.fn.caretTo = function(index, offset) {
        return this.queue(function(next) {
            if (isNaN(index)) {
                var i = $(this).val().indexOf(index);

                if (offset === true) {
                    i += index.length;
                }
                else if (offset) {
                    i += offset;
                }

                $.caretTo(this, i);
            }
            else {
                $.caretTo(this, index);
            }

            next();
        });
    };

    // Set caret to beginning of an element
    $.fn.caretToStart = function() {
        return this.caretTo(0);
    };

    // Set caret to the end of an element
    $.fn.caretToEnd = function() {
        return this.queue(function(next) {
            $.caretTo(this, $(this).val().length);
            next();
        });
    };
}(jQuery));

(function($) {
    $.fn.getCursorPosition = function() {
        var el = $(this).get(0);
        var pos = 0;
        if ('selectionStart' in el) {
            pos = el.selectionStart;
        }
        else if ('selection' in document) {
            el.focus();
            var Sel = document.selection.createRange();
            var SelLength = document.selection.createRange().text.length;
            Sel.moveStart('character', -el.value.length);
            pos = Sel.text.length - SelLength;
        }
        else {
            pos = window.getSelection().anchorOffset;
        }
        return pos;
    }
})(jQuery);


function CreateHashTagPopup(xPos, yPos, searchText) {
    var str = "";

    $.each(hashtags, function(index, hashtag) {
        if (hashtag["name"].includes(searchText) && hashtag["name"] != searchText　) {
            //alert(searchText);
            str += "<li class='list-group-item' onClick='SetHashtag(this,\"" + hashtag["name"] + "\")'>" + hashtag["name"] + "<span style='float:right;color:#ccc;'>" + hashtag["used_count"] + " posts</span></li>";
        }

    });

    $("#hashtag_popup li").remove();
    $("#hashtag_popup").append(str);
    $("#hashtag_popup").css({
        display: "block",
        top: yPos + 10 + "px",
        left: xPos + "px"
    });
}

function SetHashtag(ele, selectedTag) {
    // debugger;
    console.log(selectedTag)
    //var selectedTag = $(ele).html();
    var current_text = $("#active_hashtag").html();
    //alert(selectedTag+"\n"+current_text);
    var str = inputDiv.html();
    console.log(str)

    var span_text = current_text.replace(current_text, selectedTag);
    var tag_y = $("#active_hashtag").offset().top;
    //$("#active_hashtag2").html(inputDiv_text);
    var nodes = inputDiv[0].childNodes;
    $.each(nodes, function(k, node) {
        if (node.nodeType == Node.TEXT_NODE) {
            var words = renderText(node.nodeValue, current_text, selectedTag);
            node.nodeValue = words;
            //node.caretTo(curPostion);
        }
    })

    $("#" + div_id + "> div").each(function() {
        var div_y = $(this).offset().top;
        var tag_y = $("#active_hashtag").offset().top;
        //alert(current_text + "\n" + div_y + "\n" + tag_y);
        if ($(this).html().includes(current_text) && Math.abs(div_y - tag_y) < 5) {
            //alert(div_y);
            //alert($("#active_hashtag").parent().text());
            var words = renderText($(this).text(), current_text, selectedTag);

            //alert(words);
            // var textBefore = words.substring(0, curPostion);
            // textBefore = selectedTag;
            // var textAfter = words.substring(curPostion, words.length);
            // alert(textBefore + textAfter);
            //$(this).html(textBefore + textAfter);
            console.log(words)
            $(this).html(words);
        }
    });
    console.log(span_text)
    $("#active_hashtag").html(span_text);

    var highlighter_html = $("#" + div_id + "> .highlighter").html();
    $("#hashtag_popup").css({ display: "none" });
}

function saveSelection() {
    if (window.getSelection) {
        var sel = window.getSelection();
        if (sel.getRangeAt && sel.rangeCount) {
            return sel.getRangeAt(0);
        }
    }
    else if (document.selection && document.selection.createRange) {
        return document.selection.createRange();
    }
    return null;
}

function restoreSelectionRange(range) {
    if (range) {
        if (window.getSelection) {
            var sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        }
        else if (document.selection && range.select) {
            range.select();
        }
    }
}
