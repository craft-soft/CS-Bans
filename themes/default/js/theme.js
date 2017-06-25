$(function () {
	var html = '',
        cloned = $('.main-nav > li').clone (),
        container = $('<div>', { id: 'responsive-nav' }),
        items = $('<ul>', { id: 'responsive-nav-items' }),
        trigger = $('<div>', { id: 'responsive-nav-trigger', text: 'Navigate...' });
	
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
    
	$('ul.main-nav a').each(function () {
		if (this.href === location.href) $(this).parent().addClass('active');
	});
    window.getCsrfParam = function() {
        return $('meta[name=csrf-param]').attr('content');
    };
    window.getCsrfToken = function() {
        return $('meta[name=csrf-token]').attr('content');
    };
});
