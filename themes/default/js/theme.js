$(function () {
	responsiveNav ();

	$('ul.main-nav a').each(function () {
		if (this.href === location.href) $(this).parent().addClass('active');
	});

});

function responsiveNav () {
	var html = '';
	
	var cloned = $('.main-nav > li').clone ();
	
	var container = $('<div>', { id: 'responsive-nav' });
	var items = $('<ul>', { id: 'responsive-nav-items' });
	var trigger = $('<div>', { id: 'responsive-nav-trigger', text: 'Navigate...' });
	
	container.appendTo ('#nav .container');
	items.appendTo (container);
		
	items.append (cloned);
	
	items.find ('li').removeClass ('dropdown');
	items.find ('ul').removeClass ('dropdown-menu');
	items.find ('.caret').remove ();
	
	items.append (html);
	
	trigger.bind ('click', function (e) {
		items.slideToggle ();
		trigger.toggleClass ('open');
	});;
	
	trigger.prependTo (container);
}
