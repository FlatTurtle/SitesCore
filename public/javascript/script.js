// Exists method
jQuery.fn.exists = function(){return this.length>0;}

$(document).ready(function(){

    // Carousel
    $('.carousel').carousel({
        interval: 5000
    });

    // Fancybox
    $(".fancybox").fancybox();

    // Open external links in new window
    $("a[href^='http']").not("[href*='" + location.hostname + "']").attr('target', '_blank');

    /*
    |--------------------------------------------------------------------------
    | Collapsed
    |--------------------------------------------------------------------------
    */
    $('.expand').css('cursor', 'pointer');
    $('.collapsed').hide();
    $('.expand').click(function()
    {
        $(this).next('.collapsed').slideDown('slow');
        $(this).remove();
    });


    /*
    |--------------------------------------------------------------------------
    | Reservations
    |--------------------------------------------------------------------------
    */
    if ($('#reservations').exists())
    {
        // Some "global" data
        var cluster = $('#reservations').data('cluster');
        var things; // all things
        var thing; // current thing
        var timeline_start = 0; // timepicker start
        var timeline_end = 86400; // timepickser end

        // Datepicker
        $('.date').datepicker({
            dateFormat: 'yy-mm-dd',
            firstDay: 1,
            minDate: 0
        });

        // Timepicker
        $('.time').timepicker({
            timeFormat: ' HH:mm',
            stepMinute: 15,
            showButtonPanel: false,
            timeOnly: true,
            hourMin: 1,
            hourMax: 23
        });

        // Re-highlight "done" parts
        var wasDoneOnHover = false;
        $('#reservations > div > div').hover(function()
        {
            if ($(this).hasClass('done'))
            {
                wasDoneOnHover = true;
                $(this).removeClass('done');
            }
        },
        function()
        {
            if (wasDoneOnHover)
            {
                wasDoneOnHover = false;
                $(this).addClass('done');
            }
        });


        // Get reservable things
        $.getJSON("https://reservations.flatturtle.com/" + cluster + "/things", function(data)
        {
            var things = data;

            // Add thing labels
            for (var i in data)
            {
                var thing = data[i];

                // Create label
                var label = $('<span class="label">' + thing.name + '</span>');

                // Set data
                label.attr('data-name', thing.name);
                label.attr('data-type', thing.type);

                // Add label
                $('#reservations #things').append(label);
            }


            // Thing click event
            $('#reservations #things .label').click(function()
            {
                // Set thing
                for (var i in things)
                {
                    if (things[i].name == $(this).data('name'))
                    {
                        thing = things[i];
                    }
                }

                // Mark thing
                $('#reservations #things .label').removeClass('colorful');
                $(this).addClass('colorful');

                showAvailability();

                // Show date picker
                $('#reservations #datepicker').slideDown('slow', function()
                {
                    // Mark as done
                    $('#reservations #things').addClass('done');
                });

            });


            // Date change event
            $('#reservations #datepicker input').change(function()
            {
                showAvailability();

                // Add some color
                $('#reservations #datepicker input').addClass('colorful');

                // Show datepicker
                $('#reservations #timepicker').slideDown('slow', function()
                {
                    // Mark as done
                    $('#reservations #datepicker').addClass('done');
                });
            });


            // Time change event
            $('#reservations #timepicker input').change(function()
            {
                var from = $('#reservations #timepicker #from').val();
                if (!from) return;

                var to = $('#reservations #timepicker #to').val();
                if (!to) return;

                // Draw selection
                showSelection();

                // Show details
                $('#reservations #details').slideDown('slow', function()
                {
                    // Mark as done
                    $('#reservations #timepicker').addClass('done');
                });
            });


            // Load companies
            $.getJSON("https://reservations.flatturtle.com/" + cluster + "/companies", function(data)
            {
                for (var i in data)
                {
                    var company = data[i];
                    $('#reservations #details #company').append('<option value="' + company.name + '">' + company.name + '</option>');
                }
            });


            // Submit reservation
            $('#reservations #details button').click(function()
            {
                var from = $('#reservations #timepicker #from').val();
                var to = $('#reservations #timepicker #to').val();
                var date = $('#reservations #datepicker input').val();

                // Form data
                data = {
                    name:       thing.name,
                    type:       thing.type,
                    cluster:    cluster,
                    company:    $('#reservations select#company').val(),
                    email:      $('#reservations input#email').val(),
                    subject:    $('#reservations input#subject').val(),
                    announce:   $('#reservations input#announce').val(),
                    from:       date + " " + from,
                    to:         date + " " + to,
                    comment:    $('#reservations textarea#comment').val(),
                }

                // Clear error message
                $('#reservations #details #message').removeClass('error').removeClass('success').html('');

                // Send data to "proxy"
                $.ajax
                ({
                    type: "POST",
                    url: "reserve",
                    data: data,
                    dataType: "json",
                    success: function(response)
                    {
                        // Set success message
                        $('#reservations #details #message').addClass('success').html(response.message);
                    },
                    error: function(response)
                    {
                        // Get JSON exception
                        exception = response.responseJSON;

                        if (exception && exception.error && exception.error.message)
                        {
                            $('#reservations #details #message').addClass('error').html(exception.error.message);
                        }
                        else
                        {
                            // Fallback
                            $('#reservations #details #message').addClass('error').html("Something went wrong");
                        }
                    }
                });
            });


            /**
             * Display the availability on the timepicker
             */
            function showAvailability()
            {
                var date = $('#reservations #datepicker input').val();
                if (!date) return;

                // Remove existing blocks
                $('#reservations #timepicker #bar span').remove();

                // Remove existing labels
                $('#reservations #timepicker #labels span').remove();

                // Monday is first day of the week
                var dotw  = new Date(date).getDay();
                dotw = dotw > 0 ? dotw - 1 : 6;

                // Found opening hours for this day of the week
                if (thing.opening_hours.length && thing.opening_hours[dotw] != undefined)
                {
                    var hours = thing.opening_hours[dotw];
                }
                // No opening hours for this day
                else
                {
                    var hours = {
                        opens: ["00:00", "24:00"],
                        closes: ["00:00", "24:00"]
                    }
                }

                // Convert start and end to seconds
                opens = convertToSeconds(hours.opens[0]);
                closes = convertToSeconds(hours.closes[hours.closes.length - 1]);

                // Round opening hours
                timeline_start = Math.floor(opens / 3600) * 3600;
                timeline_end = Math.ceil(closes / 3600) * 3600;

                // Draw disabled regions
                for (var i in hours.opens)
                {
                    if (i == 0)
                    {
                        // first block
                        drawBlock(timeline_start, hours.opens[i], 'disabled');
                    }
                    else if (i == hours.opens.length - 1)
                    {
                        // last block
                        drawBlock(hours.closes[i], timeline_end, 'disabled');
                    }
                    if (i > 0)
                    {
                        drawBlock(hours.closes[i-1], hours.opens[i], 'disabled');
                    }
                }

                // Add time labels
                for (var i=timeline_start; i<= timeline_end; i+=3600)
                {
                    hour = Math.floor(i / 3600);
                    perc = (i-timeline_start) / (timeline_end-timeline_start) * 100;

                    var label = $('<span>' + hour + ':00</span>');
                    label.css('left', perc + '%');

                    $('#reservations #timepicker #labels').append(label);
                }

                // Get existing reservations
                $.getJSON("https://reservations.flatturtle.com/" + cluster + "/things/" + escape(thing.name) + "/reservations?day=" + date, function(data)
                {
                    // Loop existing reservations
                    for (var i in data)
                    {
                        var reservation = data[i];
                        var from = new Date(reservation.from).toTimeString();
                        var to   = new Date(reservation.to).toTimeString();

                        // Draw reservation
                        drawBlock(from, to);
                    }
                });

                // Draw user selection
                showSelection();
            }


            /**
             * Check the user time input
             */
            function checkSelection()
            {
                var from = $('#reservations #timepicker #from').val();
                if (from)
                {
                    var seconds = convertToSeconds(from);
                    var minutes = Math.floor((seconds % 3600) / 60);
                    var hours = Math.floor(seconds / 3600);

                    if (minutes % 15 != 0)
                    {
                        // Round minutes
                        hours = minutes > 52 ? (hours === 23 ? 0 : hours + 1) : hours;
                        minutes = (Math.round(minutes/15) * 15) % 60;

                        // Add padding
                        minutes = ("0" + minutes).slice(-2);
                        hours = ("0" + hours).slice(-2);

                        $('#reservations #timepicker #from').val(hours + ":" + minutes).trigger('change');
                        return;
                    }
                }

                var to = $('#reservations #timepicker #to').val();
                if (to)
                {
                    var seconds = convertToSeconds(to);
                    var minutes = Math.floor((seconds % 3600) / 60);
                    var hours = Math.floor(seconds / 3600);

                    // Done
                    if (minutes % 15 != 0)
                    {
                        // Round minutes
                        hours = minutes > 52 ? (hours === 23 ? 0 : hours + 1) : hours;
                        minutes = (Math.round(minutes/15) * 15) % 60;

                        // Add padding
                        minutes = ("0" + minutes).slice(-2);
                        hours = ("0" + hours).slice(-2);

                        $('#reservations #timepicker #to').val(hours + ":" + minutes).trigger('change');
                        return;
                    }
                }

                from = $('#reservations #timepicker #from').val();
                to = $('#reservations #timepicker #to').val();

                // Only continue with both values
                if (!from || !to) return;

                // Sawp times
                if (from > to)
                {
                    $('#reservations #timepicker #from').val(to);
                    $('#reservations #timepicker #to').val(from);
                    from = $('#reservations #timepicker #from').val();
                    to = $('#reservations #timepicker #to').val();
                    $('#reservations #timepicker #from').trigger('change');
                    $('#reservations #timepicker #to').trigger('change');
                    return;
                }

                // Convert time to seconds
                from = convertToSeconds(from);
                to = convertToSeconds(to);

                // Get duration in seconds
                var duration = to - from;

                if (duration % 3600 != 0)
                {
                    // Round duration to nearest hour
                    duration = Math.round(duration / 3600) * 3600;

                    var seconds = from + duration;
                    var minutes = Math.floor((seconds % 3600) / 60);
                    var hours = Math.floor(seconds / 3600);

                    // Add padding
                    minutes = ("0" + minutes).slice(-2);
                    hours = ("0" + hours).slice(-2);

                    $('#reservations #timepicker #to').val(hours + ":" + minutes).trigger('change');
                }
            }


            /**
             * Show the user selection on the timepicker
             */
            function showSelection()
            {
                // Validate user input
                checkSelection();

                var from = $('#reservations #timepicker #from').val();
                if (!from) return;

                var to = $('#reservations #timepicker #to').val();
                if (!to) return;

                // Remove existing selection
                $('#reservations #timepicker #bar .selection').remove();

                // Draw selection
                drawBlock(from, to, 'selection colorful');
            }


            /**
             * Draw a time block on the timepicker
             */
            function drawBlock(from, to, style)
            {
                // Convert time to seconds
                from = convertToSeconds(from);
                to = convertToSeconds(to);

                // No out of bounds
                if (from < timeline_start)
                {
                    from = timeline_start;
                }
                if (to > timeline_end)
                {
                    to = timeline_end;
                }

                if (from == to)
                {
                    // Don't draw blocks that you can't see
                    return;
                }

                // Convert to percentages
                from = (from-timeline_start) / (timeline_end-timeline_start) * 100;
                to = (to-timeline_start) / (timeline_end-timeline_start) * 100;

                var block = $('<span></span>');
                block.css('left', from + '%');
                block.css('width', (to - from) +'%');

                // Add optional class
                if (style)
                {
                    block.addClass(style);
                }

                // Add block to timepicker
                $('#reservations #timepicker #bar').append(block);
            }


            /**
             * Convert a time string or object to seconds since the beginning of the day
             */
            function convertToSeconds(time)
            {
                if (typeof time == 'string')
                {
                    if (time.indexOf(':') != -1)
                    {
                        parts = time.split(':');
                        parts[0] = parseInt(parts[0]);
                        parts[1] = parseInt(parts[1]);

                        if (isNaN(parts[0])) parts[0] = 0;
                        if (isNaN(parts[1])) parts[1] = 0;

                        return parts[0] * 60*60 + parts[1] * 60;
                    }

                    return 0;
                }
                else if (time instanceof Date)
                {
                    return time.getTime() % (86400 * 1000) / 1000;
                }
            }


        });
    }

});
