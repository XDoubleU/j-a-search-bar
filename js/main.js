function productSearch(form,query,currentQuery,timeout){

    var search   = form.find('.search-input'),
        category = form.find('.search-category');

    form.next('.search-results').html('').removeClass('active');

    query = query.trim();

    if (query.length >= 3) {

        if (timeout) {
            clearTimeout(timeout);
        }

        form.next('.search-results').removeClass('empty');

        search.parent().addClass('loading');
        if (query != currentQuery) {
            timeout = setTimeout(function() {

                $.ajax({
                    url:opt.ajaxUrl,
                    type: 'post',
                    data: { action: 'search_product', keyword: query, category: category.val() },
                    success: function(data) {
                        currentQuery = query;
                        search.parent().removeClass('loading');

                        if (!form.next('.search-results').hasClass('empty')) {

                            if (data.length) {
                                form.next('.search-results').html('<ul>'+data+'</ul>').addClass('active');
                            } else {
                                form.next('.search-results').html(opt.noResults).addClass('active');
                            }

                        }

                        clearTimeout(timeout);
                        timeout = false;


                    }
                });

            }, 500);
        }
    } else {

        search.parent().removeClass('loading');
        form.next('.search-results').empty().removeClass('active').addClass('empty');

        clearTimeout(timeout);
        timeout = false;

    }
}

(function($){

    "use strict";
$('form[name="product-search"]').each(function(){

        var form          = $(this),
            search        = form.find('.search'),
            category      = form.find('.category'),
            currentQuery  = '',
            timeout       = false;

        category.on('change',function(){
            currentQuery  = '';
            var query = search.val();
            productSearch(form,query,currentQuery,timeout);
        });

        search.keyup(function(){
            var query = $(this).val();
            productSearch(form,query,currentQuery,timeout);
        });

    });

})(jQuery);
