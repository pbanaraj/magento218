

//********** Home Slider ***********//

$(document).ready(function(){
    
    $('.slider').slick({
      slidesToShow: 4,
      slidesToScroll: 1,
      autoplay: true,
      autoplaySpeed: 5000,
       responsive: [
              {
                breakpoint: 2921,
                settings: {
                  slidesToShow: 8,
                  slidesToScroll: 1,
                  infinite: true
                }
              },
               {
                breakpoint: 2561,
                settings: {
                  slidesToShow: 7,
                  slidesToScroll: 1,
                  infinite: true
                }
              },
              {
                breakpoint: 2201,
                settings: {
                  slidesToShow: 6,
                  slidesToScroll: 1,
                  infinite: true
                }
              },
              {
                breakpoint: 1841,
                settings: {
                  slidesToShow: 5,
                  slidesToScroll: 1,
                  infinite: true
                }
              },
              {
                breakpoint: 1481,
                settings: {
                  slidesToShow: 4,
                  slidesToScroll: 1,
                  infinite: true
                }
              },
              {
                breakpoint: 1111,
                settings: {
                  slidesToShow: 3,
                  slidesToScroll: 1
                }
              },
              {
                breakpoint: 741,
                settings: {
                  slidesToShow: 2,
                  slidesToScroll: 1
                }
              },
              {
                breakpoint: 371,
                settings: {
                  slidesToShow: 1,
                  slidesToScroll: 1
                }
              }
              ]
    });
  });


//********** Interior Slider ***********//

  $(document).ready(function(){
    
    $('.carousel').slick({
      slidesToShow: 3,
      slidesToScroll: 1,
      autoplay: false,
      centerMode: true,
  variableWidth: true,
  centerPadding: '60px',
      
    });
  });


//********** Scroll Magic ***********//

  // init controller
  var controller = new ScrollMagic.Controller();

  $(function () { // wait for document ready
    // build scene
    var scene = new ScrollMagic.Scene({triggerElement: '#trigger1', triggerHook: 'onLeave'})
            .setPin("#pin2")
            .addTo(controller);

  });


//********** Login Toggle ***********//


$(document).ready(function() {
    $( "button.login" ).click(function() {
      $( ".login-info" ).slideToggle( );
    });
  });

