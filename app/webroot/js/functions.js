/**************
 * functions.js
 **************/

var selected = [];
var maxselect = 6;
var luckypick = false;

var selectPick = function(pick) {
	var digit = pick.html();
	luckypick = false;

	if (pick.is('.selected')) {
		pick.removeClass('selected');
		var index = selected.indexOf(digit);
		if (index > -1) { selected.splice(index, 1); }
		$('.pick-' + digit).remove();
	} else {
		if (maxselect <= selected.length) return;
		pick.addClass('selected');
		selected.push(digit);
		$('#picks').append('<span class="pick pick-' + digit + '">' + digit + '</span>');
		$('.pick-' + digit).fadeIn();
	}
	
	var rem = maxselect - selected.length;
	$('#rem var').html(rem);
	
	if (rem == 0) {
		$('#rem').hide();
		$('#btn-submit').show();
		$('.balls li').not('.selected').addClass('disabled');
	} else {
		$('#rem').show();
		$('#btn-submit').hide();
		$('.balls li').not('.selected').removeClass('disabled');
	}
}

var randomPick = function() {
	var rand = Math.floor((Math.random() * 42));
	var pick = $('.balls li').eq(rand);
	return pick;
}

var clearPick = function() {
	$('.balls li').removeClass('selected');
	$('.balls li').removeClass('disabled');
	$('#rem var').html(6);
	$('#rem').show();
	$('#btn-submit').hide();
	$('#picks').empty();
	selected = [];
}

var luckyPick = function() {
	clearPick();
	
	do {
		var pick = randomPick();
		selectPick(pick);
	} while (selected.length < maxselect);
	
	luckypick = true;
}

var submitBet = function() {
	showLoader();

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'bets/addbet',
		data: {selected: selected , luckypick: luckypick},
		success: function(data) {
			if (data.result) {
				$('.betballs').append(constructBet());
				$('.no-bet').remove();
			}
		
			clearPick();
			hideLoader();
		}
	});
}

var constructBet = function() {
	var html = '<li>';
	for (var i = 0; i < selected.length; i++) {
		html += '<span>' + selected[i] + '</span>';
	}
	html += '</li>';
	return html;
}

var showLoader = function() {
	$('#loader').show();
}

var hideLoader = function() {
	$('#loader').hide();
}

var switchTab = function(tab) {
	tab.siblings().removeClass('active');
	tab.addClass('active');
	
	var href = tab.data('href');
	$('.tabcontent').hide();
	$(href).show();
}