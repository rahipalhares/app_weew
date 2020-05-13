/*! Pushy - v1.0.0 - 2016-3-1
* Pushy is a responsive off-canvas navigation menu using CSS transforms & transitions.
* https://github.com/christophery/pushy/
* by Christopher Yee */

(function ($) {
	var pushy2 = $('.pushy2'), //menu css class
		body = $('body'),
		container = $('#container'), //container css class
		push2 = $('.push2'), //css class to add pushy capability
		pushy2Left = 'pushy2-left', //css class for left menu position
		pushy2OpenLeft = 'pushy2-open-left', //css class when menu is open (left position)
		pushy2OpenRight = 'pushy2-open-right', //css class when menu is open (right position)
		site2Overlay = $('.site2-overlay'), //site overlay
		menuBtn2 = $('.menu2-btn, .pushy2-link'), //css classes to toggle the menu
		menuSpeed = 200, //jQuery fallback menu speed
		menuWidth = pushy2.width() + 'px', //jQuery fallback menu width
		submenu2Class = '.pushy2-submenu',
		submenu2OpenClass = 'pushy2-submenu-open',
		submenu2ClosedClass = 'pushy2-submenu-closed',
		submenu2 = $(submenu2Class);

	function togglePushy2(){
		//add class to body based on menu position
		if( pushy2.hasClass(pushy2Left) ){
			body.toggleClass(pushy2OpenLeft);
		}else{
			body.toggleClass(pushy2OpenRight);
		}
	}

	function openPushy2Fallback(){		

		//animate menu position based on CSS class
		if( pushy2.hasClass(pushy2Left) ){
			body.addClass(pushy2OpenLeft);
			pushy2.animate({left: "0px"}, menuSpeed);
			container.animate({left: menuWidth}, menuSpeed);
			//css class to add pushy capability
			push2.animate({left: menuWidth}, menuSpeed);
		}else{
			body.addClass(pushy2OpenRight);
			pushy2.animate({right: '0px'}, menuSpeed);
			container.animate({right: menuWidth}, menuSpeed);
			push2.animate({right: menuWidth}, menuSpeed);
		}

	}

	function closePushy2Fallback(){

		//animate menu position based on CSS class
		if( pushy2.hasClass(pushy2Left) ){
			body.removeClass(pushy2OpenLeft);
			pushy2.animate({left: "-" + menuWidth}, menuSpeed);
			container.animate({left: "0px"}, menuSpeed);
			//css class to add pushy capability
			push2.animate({left: "0px"}, menuSpeed);
		}else{
			body.removeClass(pushy2OpenRight);
			pushy2.animate({right: "-" + menuWidth}, menuSpeed);
			container.animate({right: "0px"}, menuSpeed);
			push2.animate({right: "0px"}, menuSpeed);
		}

	}

	function toggleSubmenu2(){
		//hide submenu by default
		$(submenu2Class).addClass(submenu2ClosedClass);

		$(submenu2Class).on('click', function(){
	        var selected = $(this);

	        if( selected.hasClass(submenu2ClosedClass) ) {
	            //hide opened submenus
	            $(submenu2Class).addClass(submenu2ClosedClass).removeClass(submenu2OpenClass);
	            //show submenu
	            selected.removeClass(submenu2ClosedClass).addClass(submenu2OpenClass);
	        }else{
	            //hide submenu
	            selected.addClass(submenu2ClosedClass).removeClass(submenu2OpenClass);
	        }
	    });
	}
	
    function toggleSubmenu2Fallback(){
    	//hide submenu by default
    	$(submenu2Class).addClass(submenu2ClosedClass);
    	
    	submenu2.children('a').on('click', function(event){
    		event.preventDefault();
    		$(this).toggleClass(submenu2OpenClass)
    			   .next('.pushy2-submenu ul').slideToggle(200)
    			   .end().parent(submenu2Class)
    			   .siblings(submenu2Class).children('a')
    			   .removeClass(submenu2OpenClass)
    			   .next('.pushy2-submenu ul').slideUp(200);
    	});
    }

	//checks if 3d transforms are supported removing the modernizr dependency
	var cssTransforms3d = (function csstransforms3d(){
		var el = document.createElement('p'),
		supported = false,
		transforms = {
		    'webkitTransform':'-webkit-transform',
		    'OTransform':'-o-transform',
		    'msTransform':'-ms-transform',
		    'MozTransform':'-moz-transform',
		    'transform':'transform'
		};

		// Add it to the body to get the computed style
		document.body.insertBefore(el, null);

		for(var t in transforms){
		    if( el.style[t] !== undefined ){
		        el.style[t] = 'translate3d(1px,1px,1px)';
		        supported = window.getComputedStyle(el).getPropertyValue(transforms[t]);
		    }
		}

		document.body.removeChild(el);

		return (supported !== undefined && supported.length > 0 && supported !== "none");
	})();

	if(cssTransforms3d){
		//make menu visible
		pushy2.css({'visibility': 'visible'});

		//toggle submenu
		toggleSubmenu2();

		//toggle menu
		menuBtn2.on('click', function(){
			togglePushy2();
		});
		//close menu when clicking site overlay
		site2Overlay.on('click', function(){
			togglePushy2();
		});
	}else{
		//add css class to body
		body.addClass('no-csstransforms3d');

		//hide menu by default
		if( pushy2.hasClass(pushy2Left) ){
			pushy2.css({left: "-" + menuWidth});
		}else{
			pushy2.css({right: "-" + menuWidth});
		}

		//make menu visible
		pushy2.css({'visibility': 'visible'}); 
		//fixes IE scrollbar issue
		container.css({"overflow-x": "hidden"});

		//keep track of menu state (open/close)
		var opened = false;

		//toggle submenu
		toggleSubmenu2Fallback();

		//toggle menu
		menuBtn2.on('click', function(){
			if (opened) {
				closePushy2Fallback();
				opened = false;
			} else {
				openPushy2Fallback();
				opened = true;
			}
		});

		//close menu when clicking site overlay
		site2Overlay.on('click', function(){
			if (opened) {
				closePushy2Fallback();
				opened = false;
			} else {
				openPushy2Fallback();
				opened = true;
			}
		});
	}
}(jQuery));