
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

var colors = ['#c73b0b', '#e7a136', '#978e43', '#9b2f10', '#731115'];
$('a.collection').each(function(i) {
	$(this).prepend('#' + i++);
	$(this).css('background-color', colors[i]);
})