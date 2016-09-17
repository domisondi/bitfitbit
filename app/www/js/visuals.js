$(function() {
    $('.center').css({
        'position' : 'absolute',
        'left' : '50%',
        'top' : '50%',
        'margin-left' : function() {return -$(this).outerWidth()/2},
        'margin-top' : function() {return -$(this).outerHeight()/2}
    });
});

$('a.collection').height(.5 * $('a.collection').width());

var colors = ['#00a0b0', '#cc333f', '#eb6841', '#edc951'];
$('a.collection').each(function(i) {
	$(this).prepend('<span>#' + (i+1).toString() + '</span>');
	$(this).css('background-color', colors[i % colors.length]);
});