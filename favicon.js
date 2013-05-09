jQuery(document).ready(function($){


    var custom_uploader;


    $('#favicon_upload').click(function(e) {

        e.preventDefault();

        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }

        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Favicon',
            button: {
                text: 'Choose Favicon'
            },
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#favicon_url').val(attachment.url);
        });

        //Open the uploader dialog
        custom_uploader.open();

    });

    var custom_network_uploader;

    $('#network_favicon_upload').click(function(e) {

        e.preventDefault();

        if (custom_network_uploader) {
            custom_network_uploader.open();
            return;
        }

        custom_network_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Favicon',
            button: {
                text: 'Choose Favicon'
            },
            multiple: false
        });

        custom_network_uploader.on('select', function() {
            attachment = custom_network_uploader.state().get('selection').first().toJSON();
            $('#network_favicon_url').val(attachment.url);
        });

        //Open the uploader dialog
        custom_network_uploader.open();

    });
});