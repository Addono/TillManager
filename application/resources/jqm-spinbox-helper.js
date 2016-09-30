/*
 * Implements missing features from jQM Spinbox.
 * Copyright 2016 (c), Adriaan Knapen <a.d.knapen@student.tue.nl> <adriaan.knapen@gmail.com>
 * http://opensource.org/licenses/MIT	MIT License
 */
$(document).ready(function() {
    var counter = $('.spinbox-btn');
    var base = counter.parent().parent().children();

    base.last().click(function() {
        $(this).parent().find(".spinbox-btn").changeInputVal(1);
    });

    base.first().click(function() {
        $(this).parent().changeInputVal(-1).remove();
    });

    $.fn.extend({
       changeInputVal: function (amount) {
            var min = parseFloat($(this).attr("min"));
            var max = parseFloat($(this).attr("max"));
            
            $(this).val(function(i, oldval) {
                var newval = parseFloat(oldval) + amount;

                if((isNaN(min) || min <= newval) 
                        && (isNaN(max) || newval <= max)) {
                    return newval;
                } else {
                    return oldval;
                }
            });
            
            return $(this);
       }
    });
});