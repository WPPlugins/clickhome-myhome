jQuery(function ($) {
    mh.houseType = {

        init: function () { //console.log('houseType.init()');
        },

        slideshows: {
            main: $('.mh-slideshow-main').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: true,
                //fade: true,
                dots: true,
                asNavFor: '.mh-slideshow-carousel'
            }),

            carousel: $('.mh-slideshow-carousel').slick({
                // slidesToShow: 7,
                slidesToScroll: 1,
                asNavFor: '.mh-slideshow-main',
                arrows: false,
                infinite: false,
                //dots: true,
                centerMode: false,
                // focusOnSelect: true,
                variableWidth: true
            })
        }

    };
    var self = mh.houseType;
});
