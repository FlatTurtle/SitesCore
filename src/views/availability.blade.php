<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>{{ $flatturtle->title }}</title>
    <link href="{{ URL::asset('packages/flatturtle/sitecore/css/common.css?v=' . filemtime(public_path() . '/packages/flatturtle/sitecore/css/common.css')) }}" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="https://fast.fonts.com/cssapi/66253153-9c89-413c-814d-60d3ba0d6ac2.css"/> 
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.6.1/fullcalendar.min.css" rel="stylesheet"/>


    @if (File::exists(public_path() . '/favicon.ico'))
    <link href="{{ URL::asset('favicon.ico') }}" rel="icon" type="image/x-icon">
    @else
    <link href="{{ URL::asset('packages/flatturtle/sitecore/favicon.ico') }}" rel="icon" type="image/x-icon">
    @endif

</head>
<body data-cluster="{{ $flatturtle->interface->clustername }}">
    <div style="float:left; margin-right: 50px;"><ul id="legend" style="list-style:none"></ul></div>
    <div id="calendar" style="float:left; width:75%;"></div>

    <script
        src="https://code.jquery.com/jquery-3.2.1.min.js"
        integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
        crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.6.1/fullcalendar.min.js"></script>
    
    <script>
        $(document).ready(function() {
            var colors = ["#f44242","#41f483","#415bf4","#158ec1","#eded3d","#036b0b"]; //cheap color list
            var cluster = $('body').data('cluster');
            var roomList = [];

            $.getJSON("https://reservations.flatturtle.com/" + cluster + "/things", function(data)
            {
                for (var i in data)
                {
                    var thing = data[i];

                    roomList.push(
                        {
                            name: thing.name, 
                            url: "https://reservations.flatturtle.com/"+ cluster + "/things/" + thing.name + "/reservationsInRange",
                            color: colors[i]
                        });                    
                }

                $.each(roomList, function( index, item) {
                    $("#legend").append("<li style='height: 30px'><div style='width:20px; height: 20px;float:left; margin-right:10px;background-color:" + item.color + ";'></div>" + item.name + "</li>")
                });

                $('#calendar').fullCalendar({
                    header: {
                        left: "prev,next today",
                        center: "title",
                        right: "month,agendaWeek,agendaDay"
                    },
                    height: "auto",
                    minTime: "06:00:00",
                    // put your options and callbacks here
                    events: function(start, end, timezone, callback) {  
                        var events =[ ];              
                        $.each(roomList, function(index, item) {
                            $.ajax({
                                url: item.url,
                                crossDomain: true,
                                async:false,
                                data: {
                                    start: start.format('YYYY-MM-DD'),
                                    end: end.format('YYYY-MM-DD')
                                },
                                success: function(data, textStatus) {    
                                    
                                    $.each(data, function (i, reservation) {
                                        events.push({
                                            title: reservation.subject,
                                            start: reservation.from,
                                            end: reservation.to,
                                            color: item.color
                                        });
                                    });
                                          
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    console.debug("Request failed: " + textStatus);
                                }
                            });
                        });
                        callback(events); 
                    }
                })
            });
        });
    </script>


</body>
</html>
