var require = window.top.require;
require(['jquery'], function($) {
    jQuery('.cs_stores_availability_mode').on('click', function(i,el){
        jQuery('input.mode').removeAttr('checked');
        jQuery(this).find('input').attr('checked', 'checked');
        jQuery('.cs_stores_availability_mode').removeClass('active');
        jQuery(this).addClass('active');
    });
});
