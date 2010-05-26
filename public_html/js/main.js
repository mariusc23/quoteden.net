/**
 * @author Paul and Marius Craciunoiu
 * @version 1.1
 *
 * quoteden.net
 */
$('.msg').html('');
$(document).ready(function() {
    var template_result = $('.quote-add #content .qresult:last').clone(),
        RATING_MULTIPLIER = 20,
        RATING_URL = $('#logo a')[0].href + 'vote/add/',
        SEARCH_URL = '/search?format=json&short=1&q=',
        search_input = $('#search input[type="text"]'),
        searchTimeout = false,
        SEARCH_MESSAGE_DEFAULT = $('.search-status').html();

    function check_search_input() {
        if (!search_input.parents('li').hasClass('active')
            && search_input.val().length > 0) {
            search_input.parents('li').addClass('active');
        }
    }
    if (!search_input.parents('li').hasClass('active')) {
        search_input.keyup(function() { check_search_input(); })
                    .focus(function() { search_input.parents('li').addClass('active'); })
                    .blur (function() { search_input.parents('li').removeClass('active'); })
        ;
    }
    check_search_input();

    /* show/hide login form */
    if ($('.log-in').length > 0) {
        $('.log-in').click(function() {
            $("#login").slideToggle("fast");
            return false;
        });
    }

    $('.rating a').live('click', function() {
        var rating = parseInt(this.href.substr(-1, 1))
          , quote_id = parseInt($(this).parents('.quote').find('.id a').text())
          , msg_div = $(this).parents('.rating-wrap').children('.msg')
          , current_rating = $(this).parents('.rating').children('.current')
        ;
        if (!isNaN(rating) && !isNaN(quote_id)) {
            rating = rating * RATING_MULTIPLIER;

            $.ajax({
                url: RATING_URL + quote_id,
                type: 'POST',
                async: true,
                data: {'rating' : rating},
                cache: false,
                dataType: 'text',
                timeout: 3000,
                global: false,
                error: function(request, textStatus, errorThrown) {
                    msg_div.addClass('error');
                    if (request.status == 403) {
                        msg_div.html('Already rated');
                    } else if (request.status == 500) {
                        msg_div.html('Server error');
                    } else if (request.status == 400) {
                        msg_div.html('Invalid rating');
                    }
                    setTimeout(function() {
                        msgHide(msg_div);
                    }, 4000);
                },
                success: function(data, textStatus, request) {
                    var new_rating = parseInt(data);
                    if (!isNaN(new_rating)) {
                        current_rating.css('width', new_rating + '%');
                        var message = 'Thanks for rating!';
                        if (request.status == 200) {
                            message = 'Rating updated';
                        }
                        msg_div.html(message);
                        setTimeout(function() {
                            msgHide(msg_div);
                        }, 4000);
                    }
                }
            });
        }
        return false;
    });

    function msgHide(jquery_object) {
        jquery_object.html('');
        jquery_object.removeClass('error');
    }


    /**
     * Display search results at the bottom of add quotes form
     */
    $('.quote-add textarea[name="text"]').keyup(function() {
        var text = $(this).val();
        if (text.length < 10) {
            $('.search-status').html(SEARCH_MESSAGE_DEFAULT);
            $('#content .qresults').children().remove();
            return;
        }

        if (searchTimeout) clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function () {
        $.ajax({
            url: SEARCH_URL + text,
            type: 'POST',
            async: true,
            cache: false,
            dataType: 'json',
            timeout: 3000,
            global: false,
            error: function(request, textStatus, errorThrown) {
                alert('Error searching for quote.');
            },
            success: function(data, textStatus, request) {
                if ($('.quote-add textarea[name="text"]').val() != text) return;
                $('#content .qresults').children().remove();
                if (data.message) {
                    $('.search-status').html('No results');
                    return;
                } else {
                    $('.search-status').html(data.total + ' results');
                }
                var quote, qresult;
                for (var i in data.results) {
                    quote = data.results[i];
                    qresult = template_result.clone();

                    qresult.children('.id').html(
                        '<a href="/quote/id/' + quote.id + '">'
                        + quote.id + '</a>'
                    );
                    qresult.children('.text').html(
                        '<a href="/quote/id/' + quote.id + '">'
                        + quote.text + '</a>'
                    );
                    qresult.children('.author').html(
                        '<a href="/author/id/' + quote.author_id + '">'
                        + quote.author_name + '</a>'
                    );

                    qresult.show();
                    qresult.appendTo($('#content .qresults'));
                }
            }
        })
        }, 100); // setTimeout
    });


    $('textarea[name="categories"]')
        .autocomplete(QUOTEDEN.categories, {multiple: true, autoFill: true});
    $('input[name="author"]')
        .autocomplete(QUOTEDEN.authors, {autoFill: true});

});