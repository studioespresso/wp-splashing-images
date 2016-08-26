jQuery(document).ready(function($) {                      
    var container = $('#splashing_images');
    var settings = wp_splashing_settings;

    $('a.upload').click(function(e){
         var el = $(this);                           ;
            // If not saving, then proceed
            if(!el.hasClass('saving')){
                el.addClass('saving');
                e.preventDefault();
                var image = $(this).data('source')
                $.ajax({
                    type: 'POST',
                    url: settings.ajax_admin_url,
                    dataType: 'JSON',
                    data: {
                        action: 'wp_splashing_save_image',
                        image: image, 
                        nonce: settings.wp_splashing_admin_nonce,
                    },

                    success: function(response) {                                               
                        console.log(response); 
                    },
                    error: function(xhr, status, error) {
                        console.log(error);
                    }
                });
            };
        });
        console.log(settings.ajax_admin_url);
        var el = $(this);
        if(!el.hasClass('disabled')) {
            el.addClass('disabled');
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
