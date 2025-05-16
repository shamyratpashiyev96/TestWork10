jQuery(function($) {
    $('#ajax-search').on('input', function() {
        let keyword = $(this).val();

        if (keyword.length < 3) {
            $('#ajax-search-results').empty();
            return;
        }

        $.ajax({
            url: ajax_search_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'ajax_search_posts',
                nonce: ajax_search_obj.nonce,
                keyword: keyword
            },
            success: function(response) {
                $('#ajax-search-results').html(response);
            }
        });
    });
});