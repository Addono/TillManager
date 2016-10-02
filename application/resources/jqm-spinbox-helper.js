/*
 * Implements missing features from jQM Spinbox.
 * Copyright 2016 (c), Adriaan Knapen <a.d.knapen@student.tue.nl> <adriaan.knapen@gmail.com>
 * http://opensource.org/licenses/MIT	MIT License
 */
$(document).ready(function() {
    var counter = $('.spinbox-btn');
    var base = counter.parent().parent().children();

    base.last().click(function() {
        $(this).parent().changeInputVal(1);
    });

    base.first().click(function(event) {
        $(this).parent().changeInputVal(-1);
    });

    $.fn.extend({
       changeInputVal: function (amount) {
            $(this).val(function(i, oldval) {
                return parseFloat(oldval) + amount;
            });
            
            return $(this);
       }
    });
});