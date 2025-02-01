jQuery(document).ready(function ($) {
    let progressbar = $('.mainbanner-swiper .progressbar')
    
    
    const mainbannerSwiper = new Swiper('.mainbanner-swiper', {
        slidesPerView: 1,
        effect: "fade",
        fadeEffect: {
            crossFade: true
        },

        loop: true,

        autoplay: {
            delay: 21000,
            disableOnInteraction: false
        },


        navigation: {
            nextEl: '.mainbanner-button-next',
            prevEl: '.mainbanner-button-prev',
        },

        on: {
            autoplayTimeLeft(s, time, progress) {                
                progressbar.width(`${(1 - progress) * 100}%`);
            }
        }
    });

});