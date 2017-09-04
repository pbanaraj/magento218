var config = {
    map: {
        '*': {
            owl_carousel: 'WeltPixel_OwlCarouselSlider/js/owl.carousel',
            owl_config: 'WeltPixel_OwlCarouselSlider/js/owl.config',
            lightbox: 'WeltPixel_OwlCarouselSlider/js/lightbox',
            walkme: 'WeltPixel_OwlCarouselSlider/js/walkme',
            slick: 'WeltPixel_OwlCarouselSlider/js/slick'
        }
    },
    paths: {
        "jquery.bootstrap": "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min",
       
    },
    shim: {
        owl_carousel: {
            deps: ['jquery']
        },
        owl_config: {
            deps: ['jquery','owl_carousel']
        },
        jquery_bootstrap: {
            'deps': ['jquery']
        },
        slick: {
            'deps': ['jquery']
        }
    }
};