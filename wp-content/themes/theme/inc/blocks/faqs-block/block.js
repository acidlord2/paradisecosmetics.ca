jQuery(document).ready(function($){
	$('.question').on('click', function(){
        $('.question').not(this).removeClass('active').find('.question__bottom').slideUp();
        
        if (!$(this).hasClass('active')) {
            $(this).find('.question__bottom').slideDown();
            $(this).addClass('active');
        } else {
            $(this).find('.question__bottom').slideUp();
            $(this).removeClass('active');
        }
    })

});