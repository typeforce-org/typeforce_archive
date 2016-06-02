// FBSage - Firebelly 2015
/*jshint latedef:false*/

// Good Design for Good Reason for Good Namespace
var FBSage = (function($) {

  var screen_width = 0,
      breakpoint_small = false,
      breakpoint_medium = false,
      breakpoint_large = false,
      breakpoint_array = [480,768,1200],
      $document,
      $sidebar,
      loadingTimer,
      page_at;

  function _init() {
    // touch-friendly fast clicks
    FastClick.attach(document.body);

    // Cache some common DOM queries
    $document = $(document);
    $('body').addClass('loaded');

    // Set screen size vars
    _resize();

    // Fit them vids!
    $('main').fitVids();

    _initNav();
    _initSearch();
    _initLoadMore();
    // Set up the show/hide exhibition functionality;
    // _initExhibition();

    // Inject all of our svgs so we can grab them throughout the page with <use xlink:href="#..."> commands.
    _injectSvgSprite();

    // Give mouse over behavior to exhibit listings (e.g. duo images and other elements fade away)
    _exhibitListingMouseOver();

    _initSliders();

    //give the header a .scrolled class when past a certain point
    _fixHeaderOnScroll();


    _initImages();

    //add classes to make up for the lack of hover event on touch devices;
    _touchOnly();

    // Esc handlers
    $(document).keyup(function(e) {
      if (e.keyCode === 27) {
        _hideNav();
        _closeSearch();
      }
    });

    // Smoothscroll links
    $('a.smoothscroll').click(function(e) {
      e.preventDefault();
      var href = $(this).attr('href');
      _scrollBody($(href));
    });

    // Scroll down to hash afer page load
    $(window).load(function() {
      if (window.location.hash) {
        _scrollBody($(window.location.hash));
      }
    });

  } // end init()

  function _scrollBody(element, duration, delay) {
    if ($('#wpadminbar').length) {
      wpOffset = $('#wpadminbar').height();
    } else {
      wpOffset = 0;
    }
    element.velocity("scroll", {
      duration: duration,
      delay: delay,
      offset: -wpOffset
    }, "easeOutSine");
  }


  function _openSearch() {
    $('.site-header').addClass('search-active');
    $('.search-field:first').focus();
  }
  function _closeSearch() {
    $('.site-header').removeClass('search-active');
  }

 function _initSearch() {
  $('<svg class="icon-search" role="img"><use xlink:href="#icon-search"></use></svg>')
    .prependTo('.search-toggle');
  $('.search-toggle').on('click', function (e) {
    if ( $('.site-header').hasClass('search-active') ) {
      $('.search-form').submit();
    } else {
      e.preventDefault();
      _openSearch();
    }
  });
  // $('.search-form:not(.mobile-search) .search-submit').on('click', function (e) {
  //   if ($('.search-form').hasClass('active')) {

  //   } else {
  //     e.preventDefault();
  //     $('.search').addClass('active');
  //     $('.search-field:first').focus();
  //   }
  // });
  // $('.search-form .close-button').on('click', function() {
  //   _hideSearch();
  //   _hideMobileNav();
  // });
 }


  function _resizeNav() {

    var $navWidth = 100 + (breakpoint_medium ? 75 : 25); //50 is a buffer, 75/25 is site padding; 
    $('.site-header .site-nav .menu-item a').each(function() {
      $navWidth += $(this).outerWidth()*2;
    });
    var $titleWidth = $('.site-header .title').outerWidth();
    var $windowWidth = $( window ).width();
    //console.log('nav '+$navWidth+' title '+$titleWidth+' window '+$windowWidth);
    if (($navWidth+$titleWidth)<$windowWidth){
      $('.site-nav, .menu-toggle, .search-toggle').addClass('full-nav');
      //console.log('open');
    } else {
      $('.site-nav, .menu-toggle, .search-toggle').removeClass('full-nav');
      //console.log('close');
    }
  }


  // Handles main nav
  function _initNav() {
    //SEO-useless nav togglers
    $('<svg class="menu-toggle menu-toggle-close icon-x" role="img"><use xlink:href="#icon-x"></use></svg>')
      .prependTo('.site-nav')
      .on('click', function(e) {
        _hideNav();
      });
    $('<svg class="menu-toggle menu-toggle-open icon-hamburger" role="img"><use xlink:href="#icon-hamburger"></use></svg>')
      .prependTo('.site-header .wrapper')
      .on('click', function(e) {
        _showNav();
      });

    _resizeNav();
  }

  function _showNav() {
    $('.menu-toggle').addClass('menu-active');
    $('.site-nav').addClass('menu-active');
  }

  function _hideNav() {
    $('.menu-toggle').removeClass('menu-active');
    $('.site-nav').removeClass('menu-active');
  }

  function _touchOnly() {
    if(Modernizr.touchevents) {
      var $exhibits = $('.exhibit-list .exhibit .exhibit-listing-info');
      $(window).scroll(function() {
        var wintop = $(window).scrollTop();
        var winhalf = wintop + $(window).height()/2;
        $exhibits.each(function() {
          var top = $(this).offset().top;
          var height = parseInt($(this).closest('.exhibit').css('padding-bottom'));
          var bottom = height + top;
          // console.log('wintop '+wintop);
          // console.log('winhalf '+winhalf);
          // console.log('top '+top);
          // console.log('height '+height);
          // console.log('bottom '+bottom);
          if (winhalf > top && winhalf < bottom) {
            $(this).addClass('hover');
            // console.log('in');
          } else {
            $(this).removeClass('hover');  
            // console.log('out');        
          }
        });
      });
      
      console.log('touch screen');
    }
  }

  function _initImages() {
    if (breakpoint_medium) {
      $('.lazy').each( function() {
        var bigUrl = $(this).attr('data-big-img-url');
        if( bigUrl && $(this).attr('data-current-img-size')==='tiny'){
          // console.log($(this));
          $(this).attr('data-current-img-size','big');
          $(this).removeClass('loaded');
          $(this).attr('data-original', bigUrl);
        }
      });
    }
    $('.lazy').lazyload({
      threshold: 800,
      failure_limit : 10,
      load : function() {
        $(this).addClass('loaded');
      }
    });
    $('.slide-item .lazy').trigger('appear'); //force load of sliders;
  }

  function _initLoadMore() {
    $('<svg class="icon-load-more" role="img"><use xlink:href="#icon-load-more"></use></svg>')
      .prependTo('.load-more a');

    // Hide load more if starting on last page
    $('.load-more').each(function() {
      $load_more = $(this);
      var page = parseInt($load_more.attr('data-page-at'));
      var tot_pages = parseInt($load_more.attr('data-total-pages'));
      if ( tot_pages <= page ) {
          $load_more.addClass('hide');
      }
    });

    $document.on('click', '.load-more a', function(e) {
      e.preventDefault();
      var $load_more = $(this).closest('.load-more');
      var post_type = $load_more.attr('data-post-type') ? $load_more.attr('data-post-type') : 'exhibit';
      var exhibition_id = $load_more.attr('data-exhibition-id');
      var search_query = $load_more.attr('data-search-query');
      var orderby = $load_more.attr('data-orderby');
      var page = parseInt($load_more.attr('data-page-at'));
      var per_page = parseInt($load_more.attr('data-per-page'));
      var $more_container = $load_more.parents('main').find('.load-more-container');
      loadingTimer = setTimeout(function() { $more_container.addClass('loading'); }, 0);


      var post__not_in = [];
      //We want to exclude the posts that are currently displayed if we are a random query.
      if(orderby === 'rand') {
        $('.load-more-container .exhibit .exhibit-listing-info').each(function() {
          post__not_in.push( $(this).data('id') );
        });
      }

      $.ajax({
        url: wp_ajax_url,
        method: 'post',
        data: {
          action: 'load_more_posts',
          post_type: post_type,
          exhibition_id: exhibition_id,
          search_query: search_query,
          post__not_in: post__not_in,
          orderby: orderby,
          page: page+1,
          per_page: per_page,
        },
        success: function(data) {
          var $data = $(data);
          if (loadingTimer) { clearTimeout(loadingTimer); }
          $more_container.append($data).removeClass('loading');
          $load_more.attr('data-page-at', page+1);

          // Hide load more if last page
          if ($load_more.attr('data-total-pages') <= page + 1 ) {
              $load_more.addClass('hide');
          }

          // lazyload the new images
          _initImages();
        }
      });
    });
  }

  function _fixHeaderOnScroll() {
    $(window).scroll(function() {
      var wintop = $(window).scrollTop();
      var distance = 30; //distance = breakpoint_medium ? 50 : 30;
      if (wintop > distance) {
        $('.home .site-header').addClass('scrolled');
      } else {
        $('.home .site-header').removeClass('scrolled');
      }
    });
  }


  function _resizeSliders() {
    if($('.home').length) {

      //get height of headlines
      var headlineHeight = $('.headline').outerHeight(true);

      //Find the tallest slide content
      var maxSlideContentHeight = 0;
      $slideContent = $('.slide-item .content, .slide-item .update').each(function() {
        myHeight = $(this).outerHeight(true);
        maxSlideContentHeight = Math.max(maxSlideContentHeight,myHeight);
      });

      //the total hieght we need to be safe
      var totalHeight = headlineHeight + maxSlideContentHeight; 

      //assign
      $('.intro-slider .slide-item').css('min-height',totalHeight);
      $('.intro-content').css('min-height',totalHeight);

    }
    if($('.single-exhibit').length) {

      //find the widest width to height ratio for the images from data attr
      var widestRatio = 1;
      $('.slide-item').each(function() {
        var ratio = $(this).data('width-height-ratio');
        widestRatio = Math.max(ratio,widestRatio);
      });

      //calculate maximum height and width
      var maxWidth = $(window).width()*0.8;
      var maxHeightFromRatio = maxWidth * (1/widestRatio);
      var maxHeightFromScreen = $(window).height()*0.5;
      var maxHeight = Math.max(maxHeightFromScreen,maxHeightFromRatio);

      //apply
      $('.slide-item').css('max-height',maxHeight+'px');
      $('.slide-item').css('max-width',maxWidth+'px');
      
      //give slides proper width (they do not have this on their own)
      $('.slide-item').each(function() {
        var ratio = $(this).data('width-height-ratio');
        var h = $(this).height();
        var w = h*ratio;
        $(this).css('width',w+'px');
      });

      //re-goto the slide we are on already (this will recenter the track)
      if($('.slider.slick-slider').length){
        var currentSlide = $('.slider.slick-slider').slick('slickCurrentSlide');
        console.log(currentSlide);
        $('.slider.slick-slider').slick('slickGoTo',currentSlide);
      }

    }
  }

  function _sliderArrowKeys() {

   $(document).keyup(function(e) {
      if (e.keyCode === 37) { //left arrow
        // $('.slide-item.slick-center').removeClass('slick-center');
        $('.slider').slick('slickPrev');

      }
      if (e.keyCode === 39) { //right arrow
        // $('.slide-item.slick-center').removeClass('slick-center');
        $('.slider').slick('slickNext');
      }
    });

  }


  function _initSliders(){

    $('.slide-item').addClass('site-just-loaded');

    $(window).load(function() {
      $('.slider').slick({
        slide: '.slide-item',
        centerMode: true,
        centerPadding: '0',
        slidesToShow: 1,
        accessibility: true,
        // autoplay: true,
        autoplaySpeed: 5000,
        speed: 500,
        variableWidth: true,
        // draggable: false,
        // touchMove: false,
        prevArrow: '<div class="slider-nav-left"><svg class="icon-caret-left" role="img"><use xlink:href="#icon-caret-left"></use></svg></div>',
        nextArrow: '<div class="slider-nav-right"><svg class="icon-caret-right" role="img"><use xlink:href="#icon-caret-right"></use></svg></div>',   
      }).on('beforeChange', function(event, slick, currentSlide, nextSlide){
          $(window).scroll();
      });
      $('.slider-nav-left').each(function() {
          $mySlider = $(this).closest('.slider');
          $(this).detach().appendTo($mySlider);
        }
      );

      _resizeSliders();
      window.setTimeout(function() {
        $('.site-just-loaded').removeClass('site-just-loaded');
      },0);
      _sliderArrowKeys();
    }); 


  }

  function _injectSvgSprite() {
    boomsvgloader.load('/app/themes/typeforce/assets/svgs/build/svgs-defs.svg'); 
  }

  function _exhibitListingMouseOver() {
    // For exhibit listings in the slider...
    $('.intro-slider').each(function() {
      //... the desired behavior is to hide ...
      var $this = $(this);
      var $link = $this.find('.intro-link a');
      //... the duotone image of the slide, and the global headline and update...
      var $duo = $this.find('.duo');
      var $headline = $('.headline');
      var $update = $('.update');
      var $toDisappear = $duo.add($update).add($headline);
      //... on mouseover.
      $link.mouseenter(function() {
        $toDisappear.addClass('disappeared');
      }).mouseleave(function() {
        $toDisappear.removeClass('disappeared');
      });
    });

  }

  // Track ajax pages in Analytics
  function _trackPage() {
    if (typeof ga !== 'undefined') { ga('send', 'pageview', document.location.href); }
  }

  // Track events in Analytics
  function _trackEvent(category, action) {
    if (typeof ga !== 'undefined') { ga('send', 'event', category, action); }
  }

  // Called in quick succession as window is resized
  function _resize() {
    screenWidth = document.documentElement.clientWidth;
    breakpoint_small = (screenWidth >= breakpoint_array[0]);
    breakpoint_medium = (screenWidth >= breakpoint_array[1]);
    breakpoint_large = (screenWidth >= breakpoint_array[2]);

    _resizeSliders(); 
    _resizeNav();
    _initImages();

  }

  // Called on scroll
  // function _scroll(dir) {
  //   var wintop = $(window).scrollTop();
  // }

  // Public functions
  return {
    init: _init,
    resize: _resize,
    scrollBody: function(section, duration, delay) {
      _scrollBody(section, duration, delay);
    }
  };

})(jQuery);

// Fire up the mothership
jQuery(document).ready(FBSage.init);

// Zig-zag the mothership
jQuery(window).resize(FBSage.resize);
