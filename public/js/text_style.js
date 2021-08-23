let selection;
$(document).ready(function() {
    $('.ql-tooltip').hide();

    // var containers = document.querySelectorAll('.tbl_report');
    // var toolbars = document.querySelectorAll('.reports_header');
    // console.log(toolbars);
    // containers.forEach(function(b, index) {
    //     var editor = new Quill(b, {
    //         modules: { toolbar: toolbars[index] },
    //         theme: 'snow'
    //     });
    // })

    return;

    var ipd_boldBtn = document.querySelectorAll('.ipd_bold');
    var ipd_underlineBtn = document.querySelectorAll('.ipd_underline');
    var ipd_strikeBtn = document.querySelectorAll('.ipd_strike');
    var ipd_bgcolorBtn = document.querySelectorAll(".ipd_background_color");
    var ipd_bgcolorColorPicker = document.querySelectorAll('.ipd_bg_colorpicker');
    var ipd_txtcolorBtn = document.querySelectorAll('.ipd_text_color');
    var ipd_txtColorPicker = document.querySelectorAll('.ipd_text_colorpicker');

    document.onselectionchange = function() {
        selection = document.getSelection();
        var style = selection.getRangeAt(0).commonAncestorContainer.parentNode.style.cssText;
        console.log(style)
    };

    ipd_boldBtn.forEach(function(b) {
        b.addEventListener('click', BoldEventHandler);
    });

    ipd_underlineBtn.forEach(function(b) {
        b.addEventListener('click', UnderlineEventHandler);
    });

    ipd_strikeBtn.forEach(function(b) {
        b.addEventListener('click', StrikeEventHandler);
    });

    ipd_bgcolorBtn.forEach(function(b) {
        b.addEventListener('click', function() {
            console.log($(this).children($('.ipd_bgcolorColorPicker')))
            $(this).children($('.ipd_bgcolorColorPicker')).click();
            // ipd_bgcolorColorPicker.addEventListener('input', BackgroundColorEventHandler, false);
        })
    })

    ipd_bgcolorColorPicker.forEach(function(b) {
        b.addEventListener('input', BackgroundColorEventHandler, false);
    })

    ipd_txtcolorBtn.forEach(function(b) {
        b.addEventListener('click', function() {
            $(this).children().click();
        })
    });

    ipd_txtColorPicker.forEach(function(b) {
        b.addEventListener('input', TextColorEventHandler, false);
    })
})

function BoldEventHandler(event) {
    var inlineStyle = "";
    var style = selection.getRangeAt(0).commonAncestorContainer.parentNode.style.cssText;
    console.log(style);
    if (selection.rangeCount) {
        if (style.includes('bold')) {
            //selection.getRangeAt(0).commonAncestorContainer.parentNode.style.fontWeight = "normal";
            var e = document.createElement('span');
            e.innerHTML = selection.toString();
            if (style.includes('underline') && style.includes('line-through')) {
                inlineStyle += "text-decoration:underline; text-decoration :line-through;";
            }
            else if (style.includes('underline')) {
                inlineStyle += "text-decoration:underline;";
            }
            else if (style.includes('line-through')) {
                inlineStyle += "text-decoration :line-through;";
            }

            inlineStyle += "font-weight : normal;";
            e.style = inlineStyle;
            var range = selection.getRangeAt(0);
            range.deleteContents();
            range.insertNode(e);
        }
        else {
            var e = document.createElement('span');
            e.innerHTML = selection.toString();
            if (style.includes('underline') && style.includes('line-through')) {
                inlineStyle += "text-decoration:underline; text-decoration :line-through;";
            }
            else if (style.includes('underline')) {
                inlineStyle += "text-decoration:underline;";
            }
            else if (style.includes('line-through')) {
                inlineStyle += "text-decoration :line-through;";
            }

            inlineStyle += "font-weight : bold;";
            e.style = inlineStyle;
            var range = selection.getRangeAt(0);
            range.deleteContents();
            range.insertNode(e);
        }


    }
}

function UnderlineEventHandler(event) {
    var inlineStyle = "";
    var style = selection.getRangeAt(0).commonAncestorContainer.parentNode.style.cssText;
    console.log(style);
    if (selection.rangeCount) {
        if (style.includes('underline')) {
            // selection.getRangeAt(0).commonAncestorContainer.parentNode.style.textDecoration = "none";
            // if (style.includes('line-through')) {
            //     //selection.getRangeAt(0).commonAncestorContainer.parentNode.style.textDecoration = "line-through";
            // }
            var e = document.createElement('span');
            e.innerHTML = selection.toString();

            if (style.includes('bold') && style.includes('line-through')) {
                inlineStyle += "font-weight:bold; text-decoration :line-through;";
            }
            else if (style.includes('bold')) {
                inlineStyle += "font-weight :bold;";
            }
            else if (style.includes('line-through')) {
                inlineStyle += "text-decoration :line-through;";
            }
            else {
                inlineStyle += "text-decoration : none;";
            }
            inlineStyle += "display : inline-block;";
            e.style = inlineStyle;


            var range = selection.getRangeAt(0);
            range.deleteContents();
            range.insertNode(e);
        }
        else {
            var e = document.createElement('span');
            e.innerHTML = selection.toString();

            if (style.includes('bold') && style.includes('line-through')) {
                inlineStyle += "font-weight:bold; text-decoration :line-through underline;";
            }
            else if (style.includes('bold')) {
                inlineStyle += "font-weight :bold; text-decoration :underline;";
            }
            else if (style.includes('line-through')) {
                inlineStyle += "text-decoration :line-through underline;";
            }
            else {
                inlineStyle += "text-decoration : underline;";
            }
            inlineStyle += "display : inline-block;";
            e.style = inlineStyle;


            var range = selection.getRangeAt(0);
            range.deleteContents();
            range.insertNode(e);
        }


    }
}

function StrikeEventHandler(event) {
    var inlineStyle = "";
    var style = selection.getRangeAt(0).commonAncestorContainer.parentNode.style.cssText;
    console.log(style);
    if (selection.rangeCount) {
        if (style.includes('line-through')) {
            // selection.getRangeAt(0).commonAncestorContainer.parentNode.style.textDecoration = "none";
            // if (style.includes('underline')) {
            //     selection.getRangeAt(0).commonAncestorContainer.parentNode.style.textDecoration = "underline";
            // }
            var e = document.createElement('span');
            e.innerHTML = selection.toString();

            if (style.includes('bold') && style.includes('underline')) {
                inlineStyle += "font-weight:bold; text-decoration :underline;";
            }
            else if (style.includes('bold')) {
                inlineStyle += "font-weight :bold;";
            }
            else if (style.includes('underline')) {
                inlineStyle += "text-decoration :underline;";
            }
            else {
                inlineStyle += "text-decoration : none;";
            }
            inlineStyle += "display : inline-block;";
            e.style = inlineStyle;


            var range = selection.getRangeAt(0);
            range.deleteContents();
            range.insertNode(e);

        }
        else {
            var e = document.createElement('span');
            e.innerHTML = selection.toString();

            if (style.includes('bold') && style.includes('underline')) {
                inlineStyle += "font-weight:bold; text-decoration :underline line-through;";
            }
            else if (style.includes('bold')) {
                inlineStyle += "font-weight :bold; text-decoration :line-through;";
            }
            else if (style.includes('underline')) {
                inlineStyle += "text-decoration :underline line-through;";
            }
            else {
                inlineStyle += "text-decoration : line-through;";
            }
            inlineStyle += "display : inline-block;";
            e.style = inlineStyle;


            var range = selection.getRangeAt(0);
            range.deleteContents();
            range.insertNode(e);
        }


    }
}

function BackgroundColorEventHandler(event) {
    //$(this).parent().css('backgroundColor', event.target.value);
    console.log('BG Color');
    console.log(selection.toString());
    console.log(event.target.value)
    var inlineStyle = "";
    var style = selection.getRangeAt(0).commonAncestorContainer.parentNode.style.cssText;
    if (selection.rangeCount) {
        var e = document.createElement('span');
        e.innerHTML = selection.toString();
        e.style.backgroundColor = event.target.value;

        var range = selection.getRangeAt(0);
        range.deleteContents();
        range.insertNode(e);
    }
}

function TextColorEventHandler(event) {
    //$(this).parent().css('backgroundColor', event.target.value);
    console.log('Text Color');
    console.log(selection.toString());
    console.log(event.target.value)
    var inlineStyle = "";
    var style = selection.getRangeAt(0).commonAncestorContainer.parentNode.style.cssText;
    if (selection.rangeCount) {
        var e = document.createElement('span');
        e.innerHTML = selection.toString();
        e.style.color = event.target.value;

        var range = selection.getRangeAt(0);
        range.deleteContents();
        range.insertNode(e);
    }
}

function ToggleState(event) {
    var button = event.target;
    var currentState = button.getAttribute('aria-pressed');
    var newState = 'true';
    if (currentState === 'true') {
        newState = 'false';
    }
    button.setAttribute('aria-pressed', newState);
}
