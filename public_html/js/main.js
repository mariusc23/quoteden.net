/**
 * @author Paul and Marius Craciunoiu
 * @version 1.1
 *
 * quoteden.net
 */

$(document).ready(function() {
    /* show/hide login form */
    $('.add-quotes').click(function() {
        if ($("#login").length > 0) {
            $("#login").slideToggle("fast");
            return false;
        }
    });
});
