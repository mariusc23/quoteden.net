(function() {
    // Search
    var C = $('#content-inner'),
        SEARCH_URL = '/search?format=json&short=1&d=1&q=',
        APPROVE_URL = '/quote/add',
        Q_TEMPLATE = $('<div class="dupe"><div></div><div class="author"></div></div>');


    /**
     * Check for duplicates on quote submission.
     */
    $('form.quote-form', C).submit(function check_duplicates() {
        var text = $(this).find('textarea').val();
            that = $(this);

        $.ajax({
            url: SEARCH_URL + text,
            type: 'POST',
            async: true,
            cache: false,
            dataType: 'json',
            timeout: 3000,
            global: false,
            error: function(request, textStatus, errorThrown) {
                alert('Error while checking for duplicates. Please try again.');
            },
            success: function(data, textStatus, request) {
                // clean up after previous attempt
                clear_duplicates(that);

                // populate results
                var forced = force_approve(that);

                // approve it if no dupes found or if forced
                if (forced || !populate_duplicates(that, data)) {
                    approve_quote(that);
                }
            }
        });
        return false;
    });


    /**
     * Clears all the previously populated duplicates.
     */
    function clear_duplicates(target_form) {
        $('.dupe', target_form.prev()).remove();
    };


    /**
     * Returns true if the submit button forces (confirms), false otherwise
     */
    function force_approve(target_form) {
        return target_form.children().children('.meta')
            .find('input[type="submit"]').hasClass('confirm');
    }


    /**
     * Populates with duplicates above the quote form.
     * @returns true if any duplicates found, false otherwise
     */
    function populate_duplicates(target_form, json) {
        // ORIG is the container for original/dupes
        var ORIG = target_form.prev(), qc = null, i, first = true;
        ORIG.attr('rel', json.total);
        if (json.message) {
            ORIG.children('strong').html('Original:');
            target_form.children().children('.meta')
                .find('input[type="submit"]').val('Approve')
                .removeClass('confirm');
            return false;
        }

        ORIG.children('strong').html('Duplicates (1/' + json.total + '):');
        ORIG.children('span').hide();
        for (i in json.results) {
            quote = json.results[i];
            qresult = Q_TEMPLATE.clone();
            if (first) {
                qresult.show();
            }

            qc = qresult.children(); // 0 - content, 1 - author

            qc.first().html(quote.text);
            qc.last().html(quote.author_name);
            qresult.appendTo(ORIG);
            first = false;
        }
        ORIG.children('em').show();
        target_form.children().children('.meta').find('input[type="submit"]')
            .val('Confirm').addClass('confirm');
        return true;
    };


    /**
     * Approves a quote using an AJAX request. Called by the check_duplicates
     * function.
     */
    function approve_quote(target_form) {
        $.ajax({
            url: APPROVE_URL,
            type: 'POST',
            async: true,
            cache: false,
            dataType: 'json',
            data: target_form.serialize(),
            timeout: 3000,
            global: false,
            error: function(request, textStatus, errorThrown) {
                alert('Error approving your quote. Please try again.');
            },
            success: function(data, textStatus, request) {
                vanish_quote(target_form);
            }
        });
    };


    // cycle duplicates
    $('div.original div.dupe', C).live('click', function () {
        var d_next = $(this).next(), d_index,
            ORIG = $(this).parent(),
            dupes = ORIG.children('div.dupe');
        if (d_next.length === 0) {
            d_next = dupes.first();
        }
        $(this).hide();
        d_next.show();
        d_index = dupes.index(d_next) + 1;
        ORIG.children('strong').html('Duplicates (' + d_index + '/' +
            ORIG.attr('rel') + '):');
    });


    // delete quotes from the queue
    $('div.meta div.submit a.delete', C).click(function () {
        var the_form = $(this).parents('form');
        $.ajax({
            url: $(this).attr('href'),
            type: 'POST',
            async: true,
            cache: false,
            dataType: 'json',
            timeout: 3000,
            global: false,
            error: function(request, textStatus, errorThrown) {
                alert('Error deleting this quote. Please try again.');
            },
            success: function(data, textStatus, request) {
                vanish_quote(the_form);
            }
        });
        return false;
    });


    /**
     * Removes a quote from the DOM after it's been approved or deleted.
     */
    function vanish_quote(target_form) {
        target_form.prev().fadeOut('slow', function() {
            target_form.prev().remove();
        });
        target_form.fadeOut('slow', function() {
            target_form.remove();
        });
    }

})();
