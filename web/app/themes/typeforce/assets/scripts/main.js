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
    _initSearch();
    _initLoadMore();
    // Set up the show/hide exhibition functionality;
    // _initExhibition();

    // Inject all of our svgs so we can grab them throughout the page with <use xlink:href="#..."> commands.
    _injectSvgSprite();

    // Give mouse over behavior to exhibit listings (e.g. duo images and other elements fade away)
    _exhibitListingMouseOver();

    // Scroll to the header immediately on every page but home
    _startScrolledToHeader();

    _initSliders();


    // Esc handlers
    $(document).keyup(function(e) {
      if (e.keyCode === 27) {
        _hideNav();
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
    $('.search').addClass('active');
    $('.search-field:first').focus();
    $('.nav-links').removeClass('active');
  }
  function _closeSearch() {
    $('.search').removeClass('active');
    $('.nav-links').addClass('active');

  }

 function _initSearch() {
  $('<svg class="icon-search" role="img"><use xlink:href="#icon-search"></use></svg>')
    .prependTo('.search-submit');
  $('.nav-links').addClass('active');
  $('.open-search').on('click', function (e) {
    e.preventDefault();
    _openSearch();
  });
  $('.search-form:not(.mobile-search) .search-submit').on('click', function (e) {
    if ($('.search-form').hasClass('active')) {

    } else {
      e.preventDefault();
      $('.search').addClass('active');
      $('.search-field:first').focus();
    }
  });
  $('.search-form .close-button').on('click', function() {
    _hideSearch();
    _hideMobileNav();
  });
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
    _closeSearch();
  }

  // function _initExhibition() {
  //   SEO-useless nav togglers
  //   $('<svg class="exhibition-toggle icon-caret" role="img"><use xlink:href="#icon-caret"></use></svg>')
  //     .appendTo('.exhibition-info .title')
  //     .on('click', function(e) {
  //       _toggleExhibition();
  //     });
  // }

  // function _toggleExhibition() {
  //   if( $('.exhibition-info').hasClass('open') ) {
  //     $('.exhibition-info').removeClass('open');
  //     $('.accordian-content').velocity("slideUp",{time: 200});
  //   } else {
  //     $('.exhibition-info').addClass('open');
  //     $('.accordian-content').velocity("slideDown",{time: 200});
  //   }
  //   console.log('toggle');
  // }


  function _initLoadMore() {
    $('<svg class="icon-load-more" role="img"><use xlink:href="#icon-load-more"></use></svg>')
      .prependTo('.load-more a');

    // Hide load more if starting on last page
    $('.load-more').each(function() {
      $load_more = $(this);
      var page = parseInt($load_more.attr('data-page-at'));
      var tot_pages = parseInt($load_more.attr('data-total-pages'));
      console.log('tot pages '+tot_pages);
      console.log('page '+page);
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
      var page = parseInt($load_more.attr('data-page-at'));
      var per_page = parseInt($load_more.attr('data-per-page'));
      var $more_container = $load_more.parents('main').find('.load-more-container');
      loadingTimer = setTimeout(function() { $more_container.addClass('loading'); }, 0);
      // var wp_ajax_url = $load_more.attr('data-wp-ajax-url'); 

      $.ajax({
        url: wp_ajax_url,
        method: 'post',
        data: {
          action: 'load_more_posts',
          post_type: post_type,
          exhibition_id: exhibition_id,
          search_query: search_query,
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
        }
      });
    });
  }

  // Scroll to the header immediately on every page but home
  function _startScrolledToHeader() {
    $body = $('body');
    $header = $('.page-header');
    if(!$body.hasClass('home')) {
      window.scroll(0,$header.offset().top-20);
    }
    // $(window).on('beforeunload', function() {
    //   window.scroll(0,$header.offset().top-20);
    // });
  }

  function _resizeSliders() {
    var headlineHeight = $('.headline').outerHeight(true);
    var updateHeight = $('.update').outerHeight(true);
    var totalHeight = headlineHeight + updateHeight; 

    $('.header-slider .slide-item').css('min-height',totalHeight);
    $('.header-content').css('min-height',totalHeight);
    console.log(headlineHeight);
    console.log(updateHeight);
    console.log(totalHeight);
  }

  function _initSliders(){

    $('.slide-item').addClass('site-just-loaded');

    $('.slider').slick({
      slide: '.slide-item',
      centerMode: true,
      centerPadding: '0',
      slidesToShow: 1,
      autoplay: true,
      autoplaySpeed: 5000,
      speed: 500,
      variableWidth: true,
      // draggable: false,
      // touchMove: false,
      prevArrow: '',
      nextArrow: '<div class="slider-nav-right"><svg class="icon-caret" role="img"><use xlink:href="#icon-caret"></use></svg></div>',   
    });

    window.setTimeout(function() {
      $('.site-just-loaded').removeClass('site-just-loaded');
    },1);


    $(window).load(_resizeSliders); 


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

    _resizeSliders(); 

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
