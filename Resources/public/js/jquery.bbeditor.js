<!--
/*
 * This file is part of the CCDN CommonBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/> 
 * 
 * Available on github <http://www.github.com/codeconsortium/CommonBundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Plugin jQuery.BBEditor
 *
 * @author Reece Fowell <reece at codeconsortium dot com>
 *
 * BBCode Editor for textareas on sites using the BBCode engine.
 *
 * Requires JQuery, make sure to have JQuery included in your JS to use this.
 * JQuery needs to be loaded before this script in order for it to work.
 * @link http://jquery.com/
 *
 * Based on jQuery.BBCode plugin 
 * @link http://www.webcheatsheet.com/javascript/bbcode_editor.php
 * @link http://www.kamaikinproject.ru
 */

function getSelectedText(el) {
    if (typeof el.selectionStart == "number") {
        return el.value.slice(el.selectionStart, el.selectionEnd);
    } else if (typeof document.selection != "undefined") {
        var range = document.selection.createRange();
        if (range.parentElement() == el) {
            return range.text;
        }
    }
    return "";
}

function getLeftOfSel(el) {
	if (typeof el.selectionStart == "number") {
        return el.value.slice(0, el.selectionStart);
    } else if (typeof document.selection != "undefined") {
        var range = document.selection.createRange();
        if (range.parentElement() == el) {
			range.setEnd(el, range.getStart());
			range.setStart(el, 0);
            return range.text;
        }
    }
    return "";
}

function getRightOfSel(el) {
	if (typeof el.selectionStart == "number") {
        return el.value.slice(el.selectionEnd, (el.length - el.selectionEnd));
    } else if (typeof document.selection != "undefined") {
        var range = document.selection.createRange();
        if (range.parentElement() == el) {
			range.setStart(el, range.getEnd());
			range.setEnd(el, (el.length - range.getEnd()));
            return range.text;
        }
    }
    return "";
}

function insert(start, input, end, element) {
	
	var sel = getSelectedText(target);
	var los = getLeftOfSel(target);
	var ros = getRightOfSel(target);
	
	if (sel.length < 1) {
		element.val(los + start + input + end + ros);
	}
}

function addBlock(target) {
	var src = document.getElementById(target);
	
//	console.log(target.id);
//	console.log('' + getSelectedText(target) + '');
//	console.log('event: ' + $(event.target).data('tag'));
	
	var btn = $(event.target);
	
	var param = (btn.data('ask-param')) ? '=' + prompt(btn.data('ask-param')) : (btn.data('use-param')) ? '=' + btn.data('use-param') : '';
	var value = (btn.data('ask-value')) ? prompt(btn.data('ask-value')) : '';
	
	var start = '[' + $(this).data('tag') + param + ']';
	var end = '[/' + $(this).data('tag') + ']';
	
	this.insert(start, value, end, $(target.id));
	
	return true;
}

function addSmiley(target) {
	var src = document.getElementById(target);
	var btn = $(event.target);

	var start = '[' + $(this).data('tag') + ']';
	var end = '';

	this.insert(start, value, end, $(target.id));

	return true;
}


// -->
