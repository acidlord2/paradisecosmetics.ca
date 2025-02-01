jQuery(document).ready(function ($) {
  const catsSwiper = new Swiper('.cats-swiper', {
    slidesPerView: 3,
    spaceBetween: 20,

    navigation: {
      nextEl: '.cats-button-next',
      prevEl: '.cats-button-prev',
    },

    breakpoints: {
      0: {
        slidesPerView: 2,
        spaceBetween: 10,
      },
      650: {
        slidesPerView: 3,
        spaceBetween: 10,
      },
      1024: {
        slidesPerView: 3,
        spaceBetween: 20,
      },
    },

  });

});