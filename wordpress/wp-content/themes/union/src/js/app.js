// import Blazy from 'blazy'
import ScrollReveal from 'scrollreveal'
import stickybits from 'stickybits'
import Slick from 'slick-carousel'
import tingle from 'tingle.js'
// import Waypoint from 'waypoints'
// import Vue from 'vue'
// import VeeValidate from 'vee-validate';
window.$ = window.jQuery = require('jquery')

require('../../node_modules/waypoints/lib/jquery.waypoints.js')
require('./vendor/scrolloverflow.js')
require('./vendor/fullpage.fadingEffect.min.js')
require('./vendor/fullpage.scrollOverflowReset.min.js')
import 'fullpage.js/dist/jquery.fullpage.extensions.min.js'
// import 'tilt.js'

// tilt for content

// const tilt = $('.page-id-108 .content-block').tilt({
//   maxTilt: 4
// });

// IE fix for sticky nav on the product page. It's position has bottom:0 so you can't see the content
if ( $('body').hasClass('page-id-102') ) {
	$(document).scroll(function() {
		//console.log($(document).scrollTop());
		if ( $(document).scrollTop() > 0 ) {
			$('.product-page-nav').addClass('ie-fix');
		} else {
			$('.product-page-nav').removeClass('ie-fix');
		}
	});
}

// modals

var modal = new tingle.modal({
    // footer: true,
    // stickyFooter: false,
    closeMethods: ['overlay', 'button', 'escape'],
    closeLabel: "Close",
    // cssClass: ['custom-class-1', 'custom-class-2'],
    // onOpen: function() {
    //     console.log('modal open');
    // },
    // onClose: function() {
    //     console.log('modal closed');
    // },
    beforeClose: function() {
      modal.setContent('')
      // here's goes some logic
      // e.g. save content before closing the modal
      return true; // close the modal
    	return false; // nothing happens
    }
});

$('.content-block--video__embed__play').on('click', function() {
  // console.log($(this).data('modal-content'));
  modal.setContent($(this).data('modal-content'))
  modal.open()
})

$('.show-modal').on('click', function(e) {
  e.preventDefault()
  // console.log($(this).data('modal-content'));
  modal.setContent($(this).data('modal-content'))
  modal.open()
})

// Slick

$('.slick').slick({
  autoplay: true,
  arrows: true,
  fade: true,
  lazyLoad: 'progressive'
})

$('.slick-product').slick({
  autoplay: true,
  arrows: true,
	dots: true,
  fade: true,
  lazyLoad: 'progressive'
})

// scrollreveal

window.sr = ScrollReveal({
  scale: 1,
  duration: 500,
  delay: 200,
});

// sr.reveal('.split-text');
// sr.reveal('.hero-text');


// home full page
var fadingEffectKey = '';
var scrollOverflowResetKey = '';

if ( location.hostname == 'sprinter.theroyals.com.au' ) {
	//staging
	fadingEffectKey = 'dGhlcm95YWxzLmNvbS5hdV8wcFVabUZrYVc1blJXWm1aV04wR3BT'
	scrollOverflowResetKey = 'dGhlcm95YWxzLmNvbS5hdV9FM2pjMk55YjJ4c1QzWmxjbVpzYjNkU1pYTmxkQT09OHRF';
} else {
	//live
	fadingEffectKey = 'eC1jbGFzcy5jb20uYXVfMlM2Wm1Ga2FXNW5SV1ptWldOMDE2UQ==';
	scrollOverflowResetKey = 'eC1jbGFzcy5jb20uYXVfcjgzYzJOeWIyeHNUM1psY21ac2IzZFNaWE5sZEE9PVVFNQ==';
}
/*
*  new_map
*
*  This function will render a Google Map onto the selected jQuery element
*
*  @type  function
*  @date  8/11/2013
*  @since 4.3.0
*
*  @param $el (jQuery element)
*  @return  n/a
*/

function new_map( $el ) {

  // var
  var $markers = $el.find('.marker');


  // vars
  var args = {
    zoom    : 16,
    center    : new google.maps.LatLng(0, 0),
    mapTypeId : google.maps.MapTypeId.ROADMAP
  };


  // create map
  var map = new google.maps.Map( $el[0], args);


  // add a markers reference
  map.markers = [];


  // add markers
  $markers.each(function(){

      add_marker( $(this), map );

  });


  // center map
  center_map( map );


  // return
  return map;

}

/*
*  add_marker
*
*  This function will add a marker to the selected Google Map
*
*  @type  function
*  @date  8/11/2013
*  @since 4.3.0
*
*  @param $marker (jQuery element)
*  @param map (Google Map object)
*  @return  n/a
*/

function add_marker( $marker, map ) {

  // var
  var latlng = new google.maps.LatLng( $marker.attr('data-lat'), $marker.attr('data-lng') );

  // create marker
  var marker = new google.maps.Marker({
    position  : latlng,
    map     : map
  });

  // add to array
  map.markers.push( marker );

  // if marker contains HTML, add it to an infoWindow
  if( $marker.html() )
  {
    // create info window
    var infowindow = new google.maps.InfoWindow({
      content   : $marker.html()
    });

    // show info window when marker is clicked
    google.maps.event.addListener(marker, 'click', function() {

      infowindow.open( map, marker );

    });
  }

}

/*
*  center_map
*
*  This function will center the map, showing all markers attached to this map
*
*  @type  function
*  @date  8/11/2013
*  @since 4.3.0
*
*  @param map (Google Map object)
*  @return  n/a
*/

function center_map( map ) {

  // vars
  var bounds = new google.maps.LatLngBounds();

  // loop through all markers and create bounds
  $.each( map.markers, function( i, marker ){

    var latlng = new google.maps.LatLng( marker.position.lat(), marker.position.lng() );

    bounds.extend( latlng );

  });

  // only 1 marker?
  if( map.markers.length == 1 )
  {
    // set center of map
      map.setCenter( bounds.getCenter() );
      map.setZoom( 16 );
  }
  else
  {
    // fit to bounds
    map.fitBounds( bounds );
  }

}

// global var
var map = null;
$(document).ready(() => {
  // $('#fullpage').fullpage({
  //   fadingEffect: true,
  //   fadingEffectKey: fadingEffectKey,
  //   scrollOverflow: true,
  //   scrollOverflowReset: true,
  //   scrollOverflowResetKey: scrollOverflowResetKey,
  //   // loopBottom: true,
  //   // loopTop: true
  // });
	// $('#fullpage').fullpage();
  // var waypoint = new Waypoint({
  //   element: $('.img_txt_overlay')[0],
  //   handler: function(direction) {
  //     console.log(this.id + ' hit')
  //   }
  // })

  $('.acf-map').each(function(){

    // create map
    map = new_map( $(this) );

  });

  var firstContentSection = $('.body-content > div:first-child');
  var mainNav = $('.nav-main');
  if(firstContentSection.hasClass('dark-left-menu')) {
    mainNavClasses('dark-left-menu');
  } else if (firstContentSection.hasClass('dark-right-menu')) {
    mainNavClasses('dark-right-menu');
  } else if(firstContentSection.hasClass('dark-menu')) {
    mainNavClasses('dark-menu');
  }

  function mainNavClasses(addClass) {
    var newClass = addClass || '';
    mainNav.removeClass('dark-left-menu').removeClass('dark-right-menu').removeClass('dark-menu').addClass(newClass);
  }

  $('.dark-menu').each(function(){
    var darkwaypointsTop = $(this).waypoint({
      handler: function(direction) {
        if (direction === 'down') {
          console.log('darkwaypointsTop: ', direction);
          mainNavClasses('dark-menu');
        }
      },
      offset: 20
    })
  })

  $('.dark-menu').each(function(){
    var darkwaypointsBot = $(this).waypoint({
      handler: function(direction) {
        if (direction === 'up') {
        console.log('darkwaypointsBot: ', direction);
          mainNavClasses('dark-menu');
        }
      },
      offset: function() {
        // console.log($(this));
        return -this.element.clientHeight
      }
    })
  })

  $('.dark-left-menu').each(function(){
    var darkwaypointsTop = $(this).waypoint({
      handler: function(direction) {
        if (direction === 'down') {
          console.log('darkwaypointsTop: ', direction);
          mainNavClasses('dark-left-menu');
        }
      },
      offset: 20
    })
  })

  $('.dark-left-menu').each(function(){
    var darkwaypointsBot = $(this).waypoint({
      handler: function(direction) {
        if (direction === 'up') {
        console.log('darkwaypointsBot: ', direction);
          mainNavClasses('dark-left-menu');
        }
      },
      offset: function() {
        // console.log($(this));
        return -this.element.clientHeight
      }
    })
  })

  $('.dark-right-menu').each(function(){
    var darkwaypointsTop = $(this).waypoint({
      handler: function(direction) {
        if (direction === 'down') {
          console.log('darkwaypointsTop: ', direction);
          mainNavClasses('dark-right-menu');
        }
      },
      offset: 20
    })
  })

  $('.dark-right-menu').each(function(){
    var darkwaypointsBot = $(this).waypoint({
      handler: function(direction) {
        if (direction === 'up') {
        console.log('darkwaypointsBot: ', direction);
          mainNavClasses('dark-right-menu');
        }
      },
      offset: function() {
        // console.log($(this));
        return -this.element.clientHeight
      }
    })
  })


  $('.light-menu').each(function(){
    var lightwaypointsTop = $(this).waypoint({
      handler: function(direction) {
        if (direction === 'down') {
        // console.log(this.element.clientHeight);
        console.log('lightwaypointsTop: ', direction);
          mainNavClasses();
        }
      },
      offset: 20
    })
  })

  $('.light-menu').each(function(){
    var lightwaypointsBot = $(this).waypoint({
      handler: function(direction) {
        if (direction === 'up') {
        console.log('lightwaypointsBot: ', direction);
          mainNavClasses();
        }
      },
      offset: function() {
        // console.log(this);
        return -this.element.clientHeight
      }
    })
  })

  $('a[href*="#"]').on('click touchend', function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {

      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
      $('.mobile_menu').prop('checked', false);
      if (target.length) {
        setTimeout(function() {window.scroll(0,target.offset().top - 60);},1) //offset height of header here too.
        return false;
      }
    }
  });

  // Causing Sizzle Error
  // Executed on page load with URL containing an anchor tag.
  // if($(location.href.split("#")[1])) {
  //   var target = $('#'+location.href.split("#")[1]);
  //   if (target.length) {
  //     setTimeout(function() {window.scroll(0,target.offset().top - 60);},1) //offset height of header here too.
  //     return false;
  //   }
  // }

  // Slider table code >>

  $(".progressbar li, .progress_text li").on('click', function(){
    var currentIndex = $(this).index() + 1;
    $('.progressbar li, .progress_text li, .mobile_sub_text h2, img, table').removeClass('on').removeClass("active");
    $(".progressbar li:nth-child(" + currentIndex + ")").addClass('on');
    $(".progress_text li:nth-child(" + currentIndex + ")").addClass('on');
    $(".image" + currentIndex).addClass('active');
    $(".stats" + currentIndex).addClass('active');
    $(".mobile_sub_text h2:nth-child(" + currentIndex + ")").addClass('active');
  })

  // End slider table code

  $('p').each(function(item) {
    $(this).html($(this).html().replace('+', '<span class="font-normalize">+</span>'));
    $(this).html($(this).html().replace('©', '<span class="font-normalize">©</span>'));
  })

});



// $('.home-scroll-down').on('click', function(){
//   $.fn.fullpage.moveSectionDown()
// });



// product page sticky menu
let stuck = false;
let stickyNav;
if (window.innerWidth >= 901) {
  stuck = true;
  stickyNav = stickybits('#product-page-nav');
}
$(window).on('resize', function(){
  if(window.innerWidth <= 900 && stuck) {
    stuck = false;
    stickyNav.cleanup();
  }
  if (window.innerWidth >= 901 && !stuck) {
    stuck = true;
    stickyNav = stickybits('#product-page-nav');
  }
})

// read more buttons
$('.read-more, .read-less').on('click', function() {
  $(this).parent().toggleClass('read_more_text--active')
})

// product features component
$('.features-table .column').on('mouseenter', function(){
  // console.log("hello");
  $(this).css({
    "border-top-color": "#209cee",
    "cursor": "pointer"
  })
})
$('.features-table-item').on('mouseout', function(){
  $(this).css()
})


$.fn.scrollAcrossTable = function(scrollParent) {

  $(this).on('click', function() {

    let offset = 0

    if (scrollParent.scrollLeft() === 0) {
      offset = scrollParent.find('thead tr th').first().innerWidth() - 20
    }

    scrollParent.animate({ scrollLeft: scrollParent.find('thead tr th').eq(2).innerWidth() + scrollParent.scrollLeft() + offset
    }, 'medium')
  });

  return this

}

$(".comparison-table__scroller").scrollAcrossTable($('#comparison-table-scroller'))

