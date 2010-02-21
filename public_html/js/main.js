/**
 * @author Paul and Marius Craciunoiu
 * @version 1.1
 *
 * quoteden.net
 */
$(document).ready(function() {
    var template_add_form = $('.quote-add #content .form:last').clone()
      , num_forms = $('.quote-add #content .form').length;
    template_add_form.find('input[type="text"]').val('');
    template_add_form.find('textarea').html('');

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
});
