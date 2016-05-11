// FBSage - Firebelly 2015
/*jshint latedef:false*/

// Good Design for Good Reason for Good Namespace
var FBSage = (function($) {

  var screen_width = 0,
      breakpoint_small = false,
      breakpoint_medium = false,
      breakpoint_large = false,
      breakpoint_array = [480,1000,1200],
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
    // _initSearch();
    // _initLoadMore();
    _initSliders();
    // Set up the show/hide exhibition functionality;
    _initExhibition();

    // Inject all of our svgs so we can grab them throughout the page with <use xlink:href="#..."> commands.
    _injectSvgSprite();

    // Give mouse over behavior to exhibit listings (e.g. duo images and other elements fade away)
    _exhibitListingMouseOver();

    // Esc handlers
    $(document).keyup(function(e) {
      if (e.keyCode === 27) {
        _hideSearch();
        _hideMobileNav();
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

  // function _initSearch() {
  //   $('.search-form:not(.mobile-search) .search-submit').on('click', function (e) {
  //     if ($('.search-form').hasClass('active')) {

  //     } else {
  //       e.preventDefault();
  //       $('.search-form').addClass('active');
  //       $('.search-field:first').focus();
  //     }
  //   });
  //   $('.search-form .close-button').on('click', function() {
  //     _hideSearch();
  //     _hideMobileNav();
  //   });
  // }

  // function _hideSearch() {
  //   $('.search-form').removeClass('active');
  // }

  // Handles main nav
  function _initNav() {
    // SEO-useless nav togglers
    $('<svg class="menu-toggle menu-toggle-close icon-x" role="img"><use xlink:href="#icon-x"></use></svg>')
      .prependTo('.site-nav')
      .on('click', function(e) {
        _hideNav();
      });
    $('<svg class="menu-toggle menu-toggle-open icon-hamburger" role="img"><use xlink:href="#icon-hamburger"></use></svg>')
      .prependTo('.page-header .title')
      .on('click', function(e) {
        _showNav();
      });
  }

  function _showNav() {
    $('.menu-toggle').addClass('menu-open');
    $('.site-nav').addClass('active');
  }

  function _hideNav() {
    $('.menu-toggle').removeClass('menu-open');
    $('.site-nav').removeClass('active');
  }

  function _initExhibition() {
    // SEO-useless nav togglers
    $('<svg class="exhibition-toggle icon-caret" role="img"><use xlink:href="#icon-caret"></use></svg>')
      .appendTo('.exhibition-info .title')
      .on('click', function(e) {
        _toggleExhibition();
      });
  }

  function _toggleExhibition() {
    if( $('.exhibition-info').hasClass('open') ) {
      $('.exhibition-info').removeClass('open');
      $('.accordian-content').velocity("slideUp",{time: 200});
    } else {
      $('.exhibition-info').addClass('open');
      $('.accordian-content').velocity("slideDown",{time: 200});
    }
    console.log('toggle');
  }



  function _initLoadMore() {
    $document.on('click', '.load-more a', function(e) {
      e.preventDefault();
      var $load_more = $(this).closest('.load-more');
      var post_type = $load_more.attr('data-post-type') ? $load_more.attr('data-post-type') : 'news';
      var page = parseInt($load_more.attr('data-page-at'));
      var per_page = parseInt($load_more.attr('data-per-page'));
      var category = $load_more.attr('data-category');
      var more_container = $load_more.parents('section,main').find('.load-more-container');
      loadingTimer = setTimeout(function() { more_container.addClass('loading'); }, 500);

      $.ajax({
          url: wp_ajax_url,
          method: 'post',
          data: {
              action: 'load_more_posts',
              post_type: post_type,
              page: page+1,
              per_page: per_page,
              category: category
          },
          success: function(data) {
            var $data = $(data);
            if (loadingTimer) { clearTimeout(loadingTimer); }
            more_container.append($data).removeClass('loading');
            if (breakpoint_medium) {
              more_container.masonry('appended', $data, true);
            }
            $load_more.attr('data-page-at', page+1);

            // Hide load more if last page
            if ($load_more.attr('data-total-pages') <= page + 1) {
                $load_more.addClass('hide');
            }
          }
      });
    });
  }

  // function _styleActiveSliderInit(slick) {
  //   console.log('INIT');
  //   console.log('init this: '+$(this));
  //   $(this).find('.slide-item.slick-active').addClass('active-style');
  // }

  // function _styleActiveSliderChange(event, slick, currentSlide, nextSlide) {
  //   slick.$slides.removeClass('active-style');
  //   $next = slick.$slides.filter( function() {
  //     return $(this).data('slick-index') === nextSlide;
  //   });
  //   $next.addClass('active-style');


  //   direction = nextSlide > currentSlide;

  //   console.log(slick);
  //   console.log($next);
  //   console.log(currentSlide+' -> '+nextSlide);
  //   console.log(direction);
  // }

  function _initSliders(){
    $('.slider').slick({
      slide: '.slide-item',
      centerMode: true,
      centerPadding: '0',
      slidesToShow: 1,
      speed: 500,
      variableWidth: true,
      draggable: false,
      touchMove: false,
      prevArrow: '',
      nextArrow: '<svg class="slider-nav-right icon-caret" role="img"><use xlink:href="#icon-caret"></use></svg>',   
    });
    // .on('init', _styleActiveSliderInit)
    // .on('beforeChange', _styleActiveSliderChange);
  }

  function _injectSvgSprite() {
    boomsvgloader.load('/app/themes/typeforce/assets/svgs/build/svgs-defs.svg'); 
  }

  function _exhibitListingMouseOver() {
    // For exhibit listings in the slider...
    $('.header-slider .exhibit-listing-info').each(function() {
      //... the desired behavior is to hide ...
      var $this = $(this);
      var $link = $this.find('.info-link');
      //... the duotone image of the slide, and the global headline and update...
      var $duo = $this.find('.duo');
      var $headline = $('.headline');
      var $update = $('.update');
      var $toDisappear = $duo.add($update).add($headline);
      //... on mouseover.
      $link.mouseenter(function() {
        $toDisappear.addClass('disappeared');
        console.log($toDisappear);
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
    breakpoint_small = (screenWidth > breakpoint_array[0]);
    breakpoint_medium = (screenWidth > breakpoint_array[1]);
    breakpoint_large = (screenWidth > breakpoint_array[2]);
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
