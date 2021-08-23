jQuery.fn.focusEnd = function(){return this.each(function(){this.selectionStart=this.selectionEnd=this[$(this).focus().is('input,textarea')?'value':'innerHTML'].length})};
