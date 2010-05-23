/**
 * @author Paul and Marius Craciunoiu
 * @version 1.1
 *
 * quoteden.net
 */
$('.msg').html('');
$(document).ready(function() {
    var template_result = $('.quote-add #content .qresult:last').clone()
      , RATING_MULTIPLIER = 20
      , RATING_URL = $('#logo a')[0].href + 'vote/add/'
      , SEARCH_URL = '/search?format=json&short=1&q='
      , search_input = $('#search input[type="text"]')
      , searchTimeout = false
      , SEARCH_MESSAGE_DEFAULT = $('.search-status').html()
      , categoriesAuthorTimeout = false
      , CATEGORIES_URL = '/category/jsonlist/'
      , AUTHOR_URL = '/author/jsonlist/'
        preventKeyUp = false
    ;

    $.each($('.meta'), function() {
        $('label:first', $(this)).after('<ul class="categories_auto autocomplete hide"></ul>');
        $('label:last',  $(this)).after('<ul class="author_auto autocomplete hide"></ul>');
    });

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

    /**
     * Handles moving up and down on the suggestion list
     * and tab + enter.
     */
    function autocomplete_keydown(e, obj, class, comma_separated) {
        var text = obj.val()
          , suggest_box = $('.' + class, obj.parent().parent())
          , results = false, active = -1
          , new_text
        ;

        if (suggest_box.is(':visible')) {
            results = suggest_box.children();

            // move to the next one
            active = results.index(results.filter('.active'));
            if (e.keyCode == 40) {
                // down arrow
                results.removeClass('active');
                results.eq(active+1).addClass('active');
                return false;
            } else if (e.keyCode == 38) {
                // up arrow
                results.removeClass('active');
                results.eq(active-1).addClass('active');
                return false;
            } else if (e.keyCode == 13 || e.keyCode == 9) {
                // enter or tab

                if (comma_separated) {
                    var caret_pos = getCaretPosition(obj[0])
                    , last_comma = text.lastIndexOf(',', caret_pos-1)
                    , next_comma = text.indexOf(',', caret_pos)
                    ;

                    new_text = '';
                    if (last_comma > 0) {
                        new_text += text.substr(0, last_comma+1) + ' ';
                    }
                    new_text += results.eq(active).text();
                    if (next_comma > 0) {
                        new_text += text.substr(next_comma);
                    }
                } else {
                    new_text = results.eq(active).text();
                }

                obj.val(new_text);
                suggest_box.hide();
                return false;
            }
        }

        return true;
    }

    /**
     * Handles doing the lookup and filling in suggestions.
     */
    function autocomplete_keyup(e, obj, class, the_url, comma_separated) {
        if (preventKeyUp) return false;

        var text = obj.val()
          , suggest_box = $('.' + class, obj.parent().parent());
        if (e.keyCode != 8
            && (
                (e.keyCode >= 16 && e.keyCode <= 18) || e.keyCode == 32
                || (e.ctrlKey && e.keyCode != 86) || e.altKey || e.shiftKey
                || e.keyCode < 65 || e.keyCode > 122
                || (e.keyCode > 90 && e.keyCode < 97)
            )
            ) {
            return false;
        }

        if (text.length <= 0) {
            suggest_box.hide();
            return false;
        }

        if (comma_separated) {
            var caret_pos = getCaretPosition(obj[0])
            , last_comma = text.lastIndexOf(',', caret_pos-1)
            , next_comma = text.indexOf(',', caret_pos)
            , lookup_start = last_comma+1
            ;
            if (last_comma < 0) {
                lookup_start = 0;
            }
            lookup_length = next_comma-lookup_start;
            if (lookup_length < 0) {
                lookup_length  = text.length;
            }
            lookup = (text.substr(lookup_start, lookup_length)).trim();
            if (lookup.length < 0) {
                return false;
            }
        } else {
            lookup = text;
        }


        if (categoriesAuthorTimeout) clearTimeout(categoriesAuthorTimeout);
        categoriesAuthorTimeout = setTimeout(function () {
        $.ajax({
            url: the_url + lookup,
            type: 'POST',
            async: true,
            cache: false,
            dataType: 'json',
            timeout: 3000,
            global: false,
            error: function(request, textStatus, errorThrown) {
                console.log('Error trying to autocomplete.');
            },
            success: function(data, textStatus, request) {
                suggest_box.children().remove();
                if (data.message) {
                    return;
                }
                var result, qresult;
                for (var i in data.results) {
                    result = data.results[i];
                    qresult = $('<li></li>');
                    qresult.html(result.name);
                    qresult.appendTo(suggest_box);
                }
                suggest_box.show();
            }
        })
        }, 200); // setTimeout
    }

    $('.form textarea[name="categories"]')

        .keydown(function(e) {
        preventKeyUp = !autocomplete_keydown(e, $(this), 'categories_auto', true);
        return !preventKeyUp;
    })

        .keyup(function(e) {
        return autocomplete_keyup(e, $(this), 'categories_auto', CATEGORIES_URL, true);
    })

    $('.form input[name="author"]')
        .keydown(function(e) {
        preventKeyUp = !autocomplete_keydown(e, $(this), 'author_auto', false);
        return !preventKeyUp;
    })
        .keyup(function(e) {
        return autocomplete_keyup(e, $(this), 'author_auto', AUTHOR_URL, false);
    })

    $('.quote-add #content-inner').click(function() {
        if ($('.quote-add .categories_auto').is(':visible')) {
            $('.quote-add .categories_auto').hide();
            $('.quote-add .categories_auto').children().remove();
        }
        if ($('.quote-add .author_auto').is(':visible')) {
            $('.quote-add .author_auto').hide();
            $('.quote-add .author_auto').children().remove();
        }
    });

    $('.autocomplete li').live('mousedown', function() {
        var e = {'keyCode': 13}
          , obj = $('.form input[name="author"]')
          , class = 'author_auto'
          , comma_separated = false;
        ;
        if ($(this).parent().hasClass('categories_auto')) {
            obj = $('.form textarea[name="categories"]')
            class = 'categories_auto';
            comma_separated = true;
        }
        $(this).siblings().removeClass('active');
        $(this).addClass('active');
        autocomplete_keydown(e, obj, class, comma_separated);
        obj.focus();
    });
});

/**
 * From http://blog.vishalon.net/index.php/javascript-getting-and-setting-caret-position-in-textarea/
 */
function getCaretPosition(ctrl) {
    var CaretPos = 0;   // IE Support
    if (document.selection) {
    ctrl.focus ();
        var Sel = document.selection.createRange ();
        Sel.moveStart ('character', -ctrl.value.length);
        CaretPos = Sel.text.length;
    }
    // Firefox support
    else if (ctrl.selectionStart || ctrl.selectionStart == '0')
        CaretPos = ctrl.selectionStart;
    return (CaretPos);
}
function setCaretPosition(ctrl, pos){
    if(ctrl.setSelectionRange)
    {
        ctrl.focus();
        ctrl.setSelectionRange(pos,pos);
    }
    else if (ctrl.createTextRange) {
        var range = ctrl.createTextRange();
        range.collapse(true);
        range.moveEnd('character', pos);
        range.moveStart('character', pos);
        range.select();
    }
}
String.prototype.trim = function() {
    return this.replace(/^\s+|\s+$/g,"");
}
