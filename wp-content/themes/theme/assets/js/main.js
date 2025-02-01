jQuery(document).ready(function ($) {

    $('input[type=tel]').inputmask({
        mask: "+9 999 999-99-99",
        definitions: {
            '9': {
                validator: "[0-9]",
                cardinality: 1,
                placeholder: "_"
            }
        },
        prefix: '',
        showMaskOnHover: false,
        showMaskOnFocus: true,
        placeholder: '_'
    });

    window.formPhoneValidator = function (input) {
        let tempInput = input.toString().replaceAll(/[^0-9]+/g, '');
        return tempInput.length > 10;
    }
    $(document.body).on('checkout_error', function (event, data) {
        let content = $('.woocommerce-NoticeGroup-checkout').html()
        let wrapper = $('.custom-notice-wrapper')
        
        wrapper.html(content)
        
        
    });
    $('form.comment-form').on('submit', function (event) {
        var isValid = true;
        event.preventDefault();

        $(this).find('[required]').each(function () {
            if ($(this).val().trim() === '') {
                $(this).addClass('error');
                isValid = false;

                var $field = $(this);
                setTimeout(function () {
                    $field.removeClass('error');
                }, 3000);
            }
        });

        if (!isValid) {
            event.preventDefault();
            exit()
        }

        var formData = new FormData(this);

        const action = $(this).prop('action');

        $.ajax({
            url: action,
            type: 'POST',
            data: formData,
            contentType: false, // Отключаем обработку contentType, чтобы не устанавливался multipart/form-data
            processData: false, // Отключаем преобразование данных в строку (FormData должна отправляться как файл)
            success: function (response) {
                console.log('Успешно');
                triggerStatusModal('#modal-success');
            },
            error: function (error) {
                console.log('Ошибка при отправке формы');
                triggerStatusModal('#modal-error');
            }
        });
    });

    $('select.orderby').on('focus', function () {
        $('.woocommerce-ordering').addClass('active');
    });

    $('select.orderby').on('blur', function () {
        $('.woocommerce-ordering').removeClass('active');
    });


    $('#show-reviews').on('click', function () {
        $('.comments .comment').removeClass('disabled')
        $(this).remove()
    })

    $('input[name="images[]"]').change(function () {
        let length = $(this).get(0).files.length

        $(".file-field-container .files-info").text(`added ${length} files`);
    });


    $('#order_review_heading').on('click', function () {
        if ($(this).hasClass('show')) {
            $('#order_review .cart-products-wrapper').removeClass('disabled')
            $('#hidden_detals').text('hidden detals')
            $(this).removeClass('show')
        } else {
            $('#order_review .cart-products-wrapper').addClass('disabled')
            $('#hidden_detals').text('show detals')
            $(this).addClass('show')
        }
    })

    function toggleShippingFields() {
        let input = $('#ship-to-different-address-checkbox');
        let wrapper = $('.shipping-fields-wrapper')


        if (input.is(':checked')) {
            wrapper.addClass('active');
        } else {
            wrapper.removeClass('active');
        }
    }

    $('#ship-to-different-address-checkbox').on('change', toggleShippingFields)

    toggleShippingFields();


    $('.form-row input').on('focus', function () {
        let label = $(this).closest('.form-row').find('label')
        label.addClass('active')
    })

    $('.form-row input').on('blur', function () {
        let label = $(this).closest('.form-row').find('label');
        if ($(this).val() === '') {
            label.removeClass('active');
        }
    });
    $('.comment-form-comment textarea').on('focus', function () {
        let label = $(this).closest('.comment-form-comment').find('label')
        label.addClass('active')
    });
    $('.comment-form-comment textarea').on('blur', function () {
        let label = $(this).closest('.comment-form-comment').find('label');
        if ($(this).val() === '') {
            label.removeClass('active');
        }
    });
    $('.form-row input').each(function () {
        let label = $(this).closest('.form-row').find('label');
        if ($(this).val() !== '') {
            label.addClass('active');
        }
    });


    $('.search-toggler').on('click', function () {
        if ($(this).hasClass('hide')) {
            $('.header').removeClass('active');
            $(this).removeClass('hide')
        } else {
            $('.header').addClass('active');
            $(this).addClass('hide')
        }
    })

    $(document).scroll(function () {
        if ($(this).scrollTop() >= 10) {
            $('#header').addClass('top');
        } else {
            $('#header').removeClass('top');
        }
    });

    $(document).scroll(function () {
        if ($(this).scrollTop() >= $('#mainbanner-block').height() - 100) {
            $('.header').removeClass('transparent');
        } else {
            $('.header').addClass('transparent');
        }
    });

    $('#open-filter').on('click', function () {
        $('.filters-form').addClass('active')
    })

    $('.filters-form #close-filter').on('click', function () {
        $('.filters-form').removeClass('active')
    })

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.filters-form').length && !$(e.target).closest('#open-filter').length) {
            $('.filters-form').removeClass('active');
        }
    });

    $('.item').on('click', function () {
        $('.item').not(this).removeClass('active').find('.item__bottom').slideUp();

        if (!$(this).hasClass('active')) {
            $(this).find('.item__bottom').slideDown();
            $(this).addClass('active');
        } else {
            $(this).find('.item__bottom').slideUp();
            $(this).removeClass('active');
        }
    })


    if (document.querySelector(".single-product__gallery-swiper.swiped")) {
        const swiperProductGalleryThumbnails = new Swiper(
            ".gallery-thumbnails",
            {
                speed: 400,
                freeMode: true,
                direction: "vertical",
                breakpoints: {
                    1: {
                        slidesPerView: 2,
                        spaceBetween: 10,
                    },
                    320: {
                        slidesPerView: 2,
                        spaceBetween: 10,
                    },
                    500: {
                        slidesPerView: 3,
                        spaceBetween: 10,
                    },
                    700: {
                        slidesPerView: 4,
                        spaceBetween: 10,
                    },
                    1200: {
                        slidesPerView: 4,
                        spaceBetween: 10,
                    },
                },
            }
        );

        var swiperProductGallery = new Swiper(
            ".single-product__gallery-swiper.swiped",
            {
                spaceBetween: 10,
                slidesPerView: 1,
                effect: "fade",
                fadeEffect: {
                    crossFade: true
                },

                thumbs: {
                    swiper: swiperProductGalleryThumbnails,
                },
                navigation: {
                    nextEl: ".product-navigation__button--next",
                    prevEl: ".product-navigation__button--prev",
                },
            }
        );
    }

    const productsPageSwiper = new Swiper('.products-page-swiper', {
        slidesPerView: 2,
        spaceBetween: 20,

        navigation: {
            nextEl: '.page-products-button-next',
            prevEl: '.page-products-button-prev',
        },

        breakpoints: {
            0: {
                slidesPerView: 1,
                spaceBetween: 10
            },
            600: {
                slidesPerView: 2,
                spaceBetween: 10
            },
            1100: {
                slidesPerView: 2,
                spaceBetween: 20
            },
            1200: {
                slidesPerView: 1,
                spaceBetween: 20
            },
            1400: {
                slidesPerView: 2,
                spaceBetween: 20
            },
            1700: {
                slidesPerView: 2,
                spaceBetween: 20
            }
        }
    });

    const relatedSwiper = new Swiper('.related-products-swiper', {
        slidesPerView: 4,
        spaceBetween: 30,

        navigation: {
            nextEl: '.related-products-button-next',
            prevEl: '.related-products-button-prev',
        },

        breakpoints: {
            0: {
                slidesPerView: 1,
                spaceBetween: 10
            },
            600: {
                slidesPerView: 2,
                spaceBetween: 10
            },
            1270: {
                slidesPerView: 3,
                spaceBetween: 15
            },
            1700: {
                slidesPerView: 4,
                spaceBetween: 20
            }
        }
    });


    $("a.review").click(function () { // ID откуда кливаем
        let hash = $(this).attr('href');
        if (hash.length > 1) {
            $(this).parent().addClass('active');
            $(this).parent().siblings().removeClass('active');
            $('html, body').animate({
                scrollTop: $(hash).offset().top - 120 // класс объекта к которому приезжаем
            }, 1000); // Скорость прокрутки
        }
    });


    /*============ FUNCTIONS ===========*/

    let mobileMenu = new MobileMenu(); // Вызов объекта класса мобильного меню
    mobileMenu.init(); // Инициализация мобильного меню

    function triggerStatusModal(id) {
        Fancybox.close();

        Fancybox.show([{
            src: id,
            type: "inline"
        }], {
            dragToClose: false,
            on: {
                close: () => $(document).trigger('statusModalClosed'),
            }
        });

        let timerId = setTimeout(function () {
            Fancybox.close();
        }, 2500);

        $(document).on('statusModalClosed', () => {
            clearTimeout(timerId);
            $(document).off('statusModalClosed');
        })
    }

    $("body").on("click", ".structure-text-all", function () {
        if (!$(this).hasClass("opened")) {
            $(this).closest(".desc").find(".full__text").show();
            $(this).closest(".desc").find(".short__text").hide();
            $(this).html("hide details");
            $(this).addClass("opened");
        } else {
            $(this).closest(".desc").find(".full__text").hide();
            $(this).closest(".desc").find(".short__text").show();
            $(this).html("show more");
            $(this).removeClass("opened");
        }
    });



});
