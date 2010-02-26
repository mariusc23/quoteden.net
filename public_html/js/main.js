/**
 * @author Paul and Marius Craciunoiu
 * @version 1.1
 *
 * quoteden.net
 */
$('.msg').html('');
$(document).ready(function() {
    var template_add_form = $('.quote-add #content .form:last').clone()
      , num_forms = $('.quote-add #content .form').length
      , RATING_MULTIPLIER = 20
      , RATING_URL = $('#logo a')[0].href + 'vote/add/'
      , search_input = $('#search input[type="text"]');
    ;
    template_add_form.find('input[type="text"]').val('');
    template_add_form.find('textarea').html('');

    function check_search_input() {
        if (!search_input.parents('li').hasClass('active')
            && search_input.val().length > 0) {
            search_input.parents('li').addClass('active');
        }
    }
    if (!search_input.parents('li').hasClass('active')) {
        search_input.keyup(function () { check_search_input(); });
    }
    check_search_input();

    /* show/hide login form */
    if ($('.log-in').length > 0) {
        $('.log-in').click(function() {
            $("#login").slideToggle("fast");
            return false;
        });
    }

    $('#quote-add-more').click(function () {
        var new_form = template_add_form.clone();
        new_form.find('label:first span').html('Quote ' + (++num_forms));
        new_form.hide();
        new_form.appendTo($('#content form:last')).fadeIn('slow');
        document.getElementById('footer').scrollIntoView(true);
        return false;
    });

    $('.form .delete').live('click', function() {
        $(this).parents('.form').fadeOut('slow');
        return false;
    });

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
});
