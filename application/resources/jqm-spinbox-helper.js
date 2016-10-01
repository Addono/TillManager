/*
 * Implements missing features from jQM Spinbox.
 * Copyright 2016 (c), Adriaan Knapen <a.d.knapen@student.tue.nl> <adriaan.knapen@gmail.com>
 * http://opensource.org/licenses/MIT	MIT License
 */
$(document).ready(function() {
    $.fn.extend({
       changeInputVal: function (amount) {
            $(this).val(function(i, oldval) {
                // Get the minimum and maximum values of the input.
                var min = parseFloat($(this).attr('min'));
                var max = parseFloat($(this).attr('max'));

                // Calculate the new value.
                var newval = parseFloat(oldval) + amount;

                // Check if the new value is within the minimum and maximum bounds.
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

    var counter = $('.spinbox-btn');
    var base = counter.parent().parent().children();

    /*
     * @TODO: Fix selector to be more strict.
     */
    $('.ui-icon-plus').click(function() {
        $(this).parent().find(counter).changeInputVal(1);
    });

    $('.ui-icon-minus').click(function() {
        $(this).parent().find(counter).changeInputVal(-1);
    });

});
