/**
 * @author Paul and Marius Craciunoiu
 * @version 1.1
 *
 * quoteden.net
 */

$(document).ready(function() {
    /* show/hide login form */
    if ($('.log-in').length > 0) {
        $('.log-in').click(function() {
            $("#login").slideToggle("fast");
            return false;
        });
    }

});
