// import Blazy from 'blazy'
import stickybits from 'stickybits'
import Rellax from 'rellax'
window.$ = window.jQuery = require('jquery')

require('../../node_modules/waypoints/lib/jquery.waypoints.min.js')
require('../../node_modules/waypoints/lib/shortcuts/sticky.min.js')
require('./vendor/scrolloverflow.js')

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
      zoom: 15,
      center: new google.maps.LatLng(0, 0),
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      disableDefaultUI: true,
      styles: [{
              elementType: 'geometry',
              stylers: [{
                  color: '#f5f5f5'
              }]
          },
          {
              elementType: 'labels.icon',
              stylers: [{
                  visibility: 'off'
              }]
          },
          {
              elementType: 'labels.text.fill',
              stylers: [{
                  color: '#616161'
              }]
          },
          {
              elementType: 'labels.text.stroke',
              stylers: [{
                  color: '#f5f5f5'
              }]
          },
          {
              featureType: 'administrative.land_parcel',
              elementType: 'labels.text.fill',
              stylers: [{
                  color: '#bdbdbd'
              }]
          },
          {
              featureType: 'poi',
              elementType: 'geometry',
              stylers: [{
                  color: '#eeeeee'
              }]
          },
          {
              featureType: 'poi',
              elementType: 'labels.text.fill',
              stylers: [{
                  color: '#757575'
              }]
          },
          {
              featureType: 'poi.park',
              elementType: 'geometry',
              stylers: [{
                  color: '#e5e5e5'
              }]
          },
          {
              featureType: 'poi.park',
              elementType: 'labels.text.fill',
              stylers: [{
                  color: '#9e9e9e'
              }]
          },
          {
              featureType: 'road',
              elementType: 'geometry',
              stylers: [{
                  color: '#ffffff'
              }]
          },
          {
              featureType: 'road.arterial',
              elementType: 'labels.text.fill',
              stylers: [{
                  color: '#757575'
              }]
          },
          {
              featureType: 'road.highway',
              elementType: 'geometry',
              stylers: [{
                  color: '#dadada'
              }]
          },
          {
              featureType: 'road.highway',
              elementType: 'labels.text.fill',
              stylers: [{
                  color: '#616161'
              }]
          },
          {
              featureType: 'road.local',
              elementType: 'labels.text.fill',
              stylers: [{
                  color: '#9e9e9e'
              }]
          },
          {
              featureType: 'transit.line',
              elementType: 'geometry',
              stylers: [{
                  color: '#e5e5e5'
              }]
          },
          {
              featureType: 'transit.station',
              elementType: 'geometry',
              stylers: [{
                  color: '#eeeeee'
              }]
          },
          {
              featureType: 'water',
              elementType: 'geometry',
              stylers: [{
                  color: '#c9c9c9'
              }]
          },
          {
              featureType: 'water',
              elementType: 'labels.text.fill',
              stylers: [{
                  color: '#9e9e9e'
              }]
          }
      ]
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
      map.setZoom( 15 );
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
  if($('body').hasClass('home')) {
    window.scrollTo(0,0);
    $('body').css({'height': '100vh', 'overflow': 'hidden'});
    setTimeout(function(){
      $('body').css({'height': 'auto', 'overflow': 'auto'});
      var scrollToPosition = $('.hero1').next().offset().top;
      $('html, body').animate({scrollTop:scrollToPosition}, 1000, 'swing')
    }, 4000)
  }

  if ($('.rellax').length) {
    var rellax = new Rellax('.rellax');
  }

  // Quotes page
  $('.quotes a').on('click', function(e){
    e.preventDefault();
    console.log($(this).text());
    if($(this).find('span').text() === 'Read More') {
      $(this).closest('.quote').find('.readmore-text').css({'max-height': '600px'});
      $(this).find('span').text('Close')
    } else {
      $(this).closest('.quote').find('.readmore-text').css({'max-height': '0'});
      $(this).find('span').text('Read More')
    }
  })

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

  // Menu treatment
  function mainNavClasses(addClass) {
    var newClass = addClass || '';
    mainNav.removeClass('dark-left-menu').removeClass('dark-right-menu').removeClass('dark-menu').addClass(newClass);
  }

  // Waypoints
  $('.dark-menu').each(function(){
    var darkwaypointsTop = $(this).waypoint({
      handler: function(direction) {
        if (direction === 'down') {
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
          mainNavClasses('dark-menu');
        }
      },
      offset: function() {
        return -this.element.clientHeight
      }
    })
  })

  $('.dark-left-menu').each(function(){
    var darkwaypointsTop = $(this).waypoint({
      handler: function(direction) {
        if (direction === 'down') {
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
          mainNavClasses('dark-left-menu');
        }
      },
      offset: function() {
        return -this.element.clientHeight
      }
    })
  })

  $('.dark-right-menu').each(function(){
    var darkwaypointsTop = $(this).waypoint({
      handler: function(direction) {
        if (direction === 'down') {
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
          mainNavClasses('dark-right-menu');
        }
      },
      offset: function() {
        return -this.element.clientHeight
      }
    })
  })


  $('.light-menu').each(function(){
    var lightwaypointsTop = $(this).waypoint({
      handler: function(direction) {
        if (direction === 'down') {
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
          mainNavClasses();
        }
      },
      offset: function() {
        return -this.element.clientHeight
      }
    })
  })

  if($('.nav-main').index() !== 0 && window.innerWidth > 768) {
    var sticky = new Waypoint.Sticky({
      element: $('.nav-main')[0]
    })
  }

  // $('a[href*="#"]').on('click touchend', function() {
  //   if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {

  //     var target = $(this.hash);
  //     target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
  //     $('.mobile_menu').prop('checked', false);
  //     if (target.length) {
  //       setTimeout(function() {window.scroll(0,target.offset().top - 60);},1) //offset height of header here too.
  //       return false;
  //     }
  //   }
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
    $(this).css({
      "border-top-color": "#209cee",
      "cursor": "pointer"
    })
  })
  $('.features-table-item').on('mouseout', function(){
    $(this).css()
  })

  const $posts = $('.post-col');
  const $morePosts = $('.columns.load a');
  if($morePosts.length) {
    $morePosts.on('click', function(e) {
      e.preventDefault();
      const firstHiddenPost = $('.post-col:hidden').index();
      let revealNo = 9;
      if (window.innerWidth < 768) {
        revealNo = 3;
      }
      for(let i = firstHiddenPost; i < firstHiddenPost + revealNo || i < $posts.length; i++) {
        $posts.eq(i).show();
      }
      if(!$('.post-col:hidden').length) {
        $('.columns.load').hide();
      }
    })
  }

});


// $.fn.scrollAcrossTable = function(scrollParent) {

//   $(this).on('click', function() {

//     let offset = 0

//     if (scrollParent.scrollLeft() === 0) {
//       offset = scrollParent.find('thead tr th').first().innerWidth() - 20
//     }

//     scrollParent.animate({ scrollLeft: scrollParent.find('thead tr th').eq(2).innerWidth() + scrollParent.scrollLeft() + offset
//     }, 'medium')
//   });

//   return this

// }

// $(".comparison-table__scroller").scrollAcrossTable($('#comparison-table-scroller'))

