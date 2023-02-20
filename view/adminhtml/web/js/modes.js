var require = window.top.require;
require(['jquery'], function($) {
    $('.cs_stores_availability_mode').on('click', function(){
        $('.cs_stores_availability_mode').removeClass('active');
        $(this).find('input').prop('checked', true);
        $(this).addClass('active');
    });
});
