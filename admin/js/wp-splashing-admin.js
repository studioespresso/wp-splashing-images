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
        if(!image.hasClass('saving')){
            image.addClass('saving');
            e.preventDefault();
            var data = $(this).data('source');
            var author = $(this).data('author');
            var credit = $(this).data('credit');
            $.ajax({
                type: 'POST',
                url: settings.ajax_admin_url,
                dataType: 'JSON',
                data: {
                    action: 'wp_splashing_save_image',
                    image: data,
                    author: author,
                    credit: credit,
                    nonce: settings.wp_splashing_admin_nonce,
                },
                beforeSend: function() {
                    console.log('Submitting image for download');
                    image.LoadingOverlay("show");
                },
                success: function(response) {                                               
                    console.log(response); 
                    image.LoadingOverlay("hide");
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        };
    });
});