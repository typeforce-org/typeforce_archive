// Typeforce Archive - Firebelly
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

    // Fire up the Slick sliders
    _initSliders();

    // Give the header a .scrolled class when past a certain point
    _fixHeaderOnScroll();

    // Init lazyload images
    _initImages();

    // Add classes to make up for the lack of hover event on touch devices;
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
    $('.search-mask').addClass('search-active');
  }
  function _closeSearch() {
    $('.site-header').removeClass('search-active');
    $('.search-mask').removeClass('search-active');
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
  $('.site-header').before('<div class="search-mask"></div>');
  $('.search-mask').click(function() {
    if($(this).hasClass('search-active')) {
      _closeSearch();
    }
  });
 }

  // Unfortunately the transition to hamburger nav cannot be made smoothely with breakpoints because of the variable number of menu items.
  // We'll use this function instead to check if the menu can fit in this screen size.
  function _resizeNav() {

    //If it's open, close it.
    _hideNav();

    //How wide am I?
    var $navWidth = 100 + $('.site-header .site-nav .menu-item').length*75 + (breakpoint_medium ? 75 : 25); // Num menu-items * 75px + 75/25 site padding

    //Compare sum of that and title's width to determine hamburger or no
    var $titleWidth = $('.site-header .title').outerWidth(); 
    var $windowWidth = $( window ).width();
    if (($navWidth+$titleWidth)<$windowWidth){
      $('.site-nav, .menu-toggle, .search-toggle').addClass('full-nav');
    } else {
      $('.site-nav, .menu-toggle, .search-toggle').removeClass('full-nav');
    }
  }

  function _showNav() {
    $('.menu-toggle').addClass('nav-active');
    $('.site-nav').addClass('nav-active');
    $('.nav-mask').addClass('nav-active');
  }

  function _hideNav() {
    $('.menu-toggle').removeClass('nav-active');
    $('.site-nav').removeClass('nav-active');
    $('.nav-mask').removeClass('nav-active');
  }

  // Handles main nav
  function _initNav() {
    // SEO-useless nav togglers
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
      $('.site-nav').before('<div class="nav-mask"></div>');
      $('.nav-mask').click(function() {
        if($(this).hasClass('nav-active')) {
          _hideNav();
        }
      });
  }

  
  // Create good experience for people with no mouse's to hover by triggering a faux 'hover' class as an exhibit in the grid passes to screen center
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
          if (winhalf > top && winhalf < bottom) {
            $(this).addClass('hover');
          } else {
            $(this).removeClass('hover');
          }
        });
      });
    }
  }

  function _initImages() {
    if (breakpoint_medium) {
      $('.lazy').each( function() {
        var bigUrl = $(this).attr('data-big-img-url');
        if( bigUrl && $(this).attr('data-current-img-size')==='tiny'){
          $(this).attr('data-current-img-size','big');
          $(this).removeClass('loaded');
          $(this).attr('data-original', bigUrl);
        }
      });
    }
    $('.lazy').lazyload({
      threshold: 200,
      failure_limit : 10,
      load : function() {
        $(this).addClass('loaded');
      }
    });
    $('.slide-item .lazy').trigger('appear'); // Force load of sliders
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
          post__not_in.push( $(this).attr('data-id') );
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
      //find the widest width to height ratio for the images from data attr
      var widestRatio = 1;
      $('.slide-item').each(function() {
        var ratio = $(this).attr('data-width-height-ratio');
        widestRatio = Math.max(ratio,widestRatio);
      });

      //calculate maximum height and width
      var maxWidth = $(window).width()*0.8;
      var maxHeightFromRatio = maxWidth * (1/widestRatio);
      var maxHeightFromScreen = $(window).height()*0.5;
      var maxHeight = Math.max(maxHeightFromScreen,maxHeightFromRatio);

      //apply
      $('.slide-item, .intro-content').css('max-height',maxHeight+'px');
      $('.slide-item').css('max-width',maxWidth+'px');

      //give slides proper width (they do not have this on their own)
      $('.slide-item').each(function() {
        var ratio = $(this).attr('data-width-height-ratio');
        var h = $(this).height();
        var w = h*ratio;
        $(this).css('width',w+'px');
      });

      //re-goto the slide we are on already (this will recenter the track)
      if($('.slider.slick-slider').length){
        var currentSlide = $('.slider.slick-slider').slick('slickCurrentSlide');
        $('.slider.slick-slider').slick('slickGoTo',currentSlide);
      }
  }

  function _sliderArrowKeys() {
    $(document).keyup(function(e) {
      if (e.keyCode === 37) { // Left arrow
        // $('.slide-item.slick-center').removeClass('slick-center');
        $('.slider').slick('slickPrev');

      }
      if (e.keyCode === 39) { // Right arrow
        // $('.slide-item.slick-center').removeClass('slick-center');
        $('.slider').slick('slickNext');
      }
    });
  }

  function _initSliders(){
    $('.slide-item').addClass('site-just-loaded');
    $('.slider').slick({
      slide: '.slide-item',
      centerMode: true,
      centerPadding: '0',
      slidesToShow: 1,
      accessibility: true,
      autoplay: true,
      autoplaySpeed: 5000,
      speed: 500,
      variableWidth: true,
      prevArrow: '<div class="slider-nav-left"><svg class="icon-caret-left" role="img"><use xlink:href="#icon-caret-left"></use></svg></div>',
      nextArrow: '<div class="slider-nav-right"><svg class="icon-caret-right" role="img"><use xlink:href="#icon-caret-right"></use></svg></div>',
    }).on('beforeChange', function(event, slick, currentSlide, nextSlide){
        $(window).scroll();
    });
    _resizeSliders();

    $(window).load(function() {
      // Move slider-nav-left to after the track, so it can appear
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
