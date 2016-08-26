jQuery(document).ready(function($) {                      
    var container = $('#splashing_images');
    var settings = wp_splashing_settings;
    $.LoadingOverlaySetup({
        color           : "rgba(241,241,241,0.7)",
        maxSize         : "80px",
        minSize         : "20px",
        resizeInterval  : 0,
        size            : "30%"
    });

    $('a.upload').click(function(e){
         var element = $(this);
         var image = element.find('img');
            // If not saving, then proceed
        if(!element.hasClass('saving')){
            element.addClass('saving');
            e.preventDefault();
            var data = $(this).data('source')
            $.ajax({
                type: 'POST',
                url: settings.ajax_admin_url,
                dataType: 'JSON',
                data: {
                    action: 'wp_splashing_save_image',
                    image: data, 
                    nonce: settings.wp_splashing_admin_nonce,
                },
                beforeSend: function() {
                    console.log('Submitting image for download');
                    image.LoadingOverlay("show");
                },
                success: function(response) {                                               
                    console.log(response); 
                    image.LoadingOverlay("hide");
                    var checkmark = '<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>';
                    element.append(checkmark);
                    setTimeout(function() {
                        element.children('svg.checkmark').fadeOut(1400);
                    }, 1400);
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        };
    });
    $('#splashing-search').submit(function(e){
        console.log(settings.ajax_admin_url);
        var element = $(this);
        if(!element.hasClass('disabled')) {
            element.addClass('disabled');
            e.preventDefault();
            var data = $('#splashing-search input').val();
            $.ajax({
                type: 'POST',
                url: settings.ajax_admin_url,
                dataType: 'JSON',
                data: {
                    action: 'wp_splashing_search',
                    data: data, 
                    nonce: settings.wp_splashing_admin_nonce,
                }
            });
        };
    });
});