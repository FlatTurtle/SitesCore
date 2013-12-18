<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $flatturtle->title }}</title>
    <link href="{{ URL::asset('packages/flatturtle/sitecore/css/common.css?v=' . filemtime(public_path() . '/packages/flatturtle/sitecore/css/common.css')) }}" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="https://fast.fonts.com/cssapi/66253153-9c89-413c-814d-60d3ba0d6ac2.css"/>

    @if (File::exists(public_path() . '/favicon.ico'))
    <link href="{{ URL::asset('favicon.ico') }}" rel="icon" type="image/x-icon">
    @else
    <link href="{{ URL::asset('packages/flatturtle/sitecore/favicon.ico') }}" rel="icon" type="image/x-icon">
    @endif

    <style>
    .colorful {
        background-color: {{ $flatturtle->color }} !important;
    }

    .highlight {
        color: {{ $flatturtle->color }} !important;
    }

    #content a {
        color: {{ $flatturtle->color }};
    }
    </style>
</head>
<body>

    <nav>
        <div class="container">
            <a id="brand" href="#">{{ $flatturtle->title }}</a>
            <ul>
            @foreach ($blocks as $block)
                @if ($block->title)
                <li>
                    <a href="#{{ $block->id }}">{{ $block->title }}</a>
                </li>
                @endif
            @endforeach
            @if (Config::get('sitecore::mailchimp'))
                <li>
                    <a href="#newsletter">{{ Lang::get('sitecore::newsletter.title') }}</a>
                </li>
            @endif
            @if ($reservations)
                <li>
                    <a href="#reservations" class="btn colorful">{{ Lang::get('sitecore::reservations.title') }}</a>
                </li>
            @endif
            </ul>
        </div>
    </nav>


    @if ($images)
    <section id="jumbo" class="carousel slide" style="height: {{ Config::get('sitecore::carousel_height', '600px') }}">
        <div id="top-blur"></div>
        <div id="bottom-blur"></div>

        <ol class="carousel-indicators">
        @foreach ($images as $i => $image)
            @if ($i == 0)
                <li data-target="#jumbo" data-slide-to="{{ $i }}" class="active"></li>
            @else
                <li data-target="#jumbo" data-slide-to="{{ $i }}"></li>
            @endif
        @endforeach
        </ol>

        <div class="carousel-inner">
            @foreach ($images as $i => $image)
                @if ($i == 0)
                    <div class="item active" style="background-image: url('{{ $image }}')">
                        <div class="carousel-caption"></div>
                    </div>
                @else
                    <div class="item" style="background-image: url('{{ $image }}')">
                        <div class="carousel-caption"></div>
                    </div>
                @endif
            @endforeach
        </div>

        <a class="left carousel-control" href="#jumbo" data-slide="prev">
            <img src="{{ URL::asset('packages/flatturtle/sitecore/images/paddle_previous.png') }}">
        </a>
        <a class="right carousel-control" href="#jumbo" data-slide="next">
            <img src="{{ URL::asset('packages/flatturtle/sitecore/images/paddle_next.png') }}">
        </a>
    </section>
    @endif



    <section id="content">
    @foreach ($blocks as $block)

    <div class="block">
        <a class="anchor" id="{{ $block->id }}"></a>
        <div id="{{ $block->id }}" class="container">
            {{ $block->html }}
        </div>
    </div>

    @endforeach
    </section>



    @if (Config::get('sitecore::mailchimp'))
    <section id="newsletter" class="block colorful">
        <div class="container">
            <h1>{{ Lang::get('sitecore::newsletter.title') }}</h1>

            <p>{{ Lang::get('sitecore::newsletter.text') }}</p>

            <form class="form-inline" method="POST" action="{{ Config::get('sitecore::mailchimp') }}" role="form">
                <div id="mailbox">
                    <div class="input-group">
                        <input type="email" name="EMAIL" class="form-control">
                        <span class="input-group-addon">
                            <button type="submit" class="btn btn-special">Sign Up</button>
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </section>
    @endif



    @if ($reservations)
    <section id="reservations" data-cluster="{{ $flatturtle->interface->clustername }}" class="block">
        <div class="container">
            <h1>{{ Lang::get('sitecore::reservations.title') }}</h1>
            <h2>{{ Lang::get('sitecore::reservations.subtitle') }}</h2>

            <div id="things"></div>

            <div id="datepicker">
                <h2>{{ Lang::get('sitecore::reservations.date') }}</h2>
                <input type="text" class="date" required placeholder="click me">
            </div>

            <div id="timepicker">

                <h2>{{ Lang::get('sitecore::reservations.time') }}</h2>

                <div id="bar"></div>
                <div id="labels"></div>

                <div id="selection">
                {{ Lang::get('sitecore::reservations.from') }} <input type="text" id="from" placeholder="00:00"> &nbsp; {{ Lang::get('sitecore::reservations.to') }} <input type="text" id="to" placeholder="00:00">
                </div>

            </div>

            <div id="details">
                <h2>{{ Lang::get('sitecore::reservations.details') }}</h2>

                {{ Lang::get('sitecore::reservations.company') }} <input type="text" id="company" required><br>
                {{ Lang::get('sitecore::reservations.email') }} <input type="email" id="email" required><br>
                {{ Lang::get('sitecore::reservations.subject') }} <input type="text" id="subject" required><br>
                {{ Lang::get('sitecore::reservations.announce') }} <input type="text" id="announce" required><br>

                <textarea id="comment" placeholder="{{ Lang::get('sitecore::reservations.comment') }}"></textarea><br>
                <div id="message"></div>

                <button class="btn colorful">{{ Lang::get('sitecore::reservations.button') }}</button>
            </div>

        </div>
    </section>
    @endif



    @if (Config::get("sitecore::map"))
    <section id="map" style="height: {{ Config::get('sitecore::map_height', '450px') }}">
        <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
            src="https://maps.flatturtle.com/{{ Config::get('sitecore::id') }}"
        ></iframe>
    </section>
    @endif



    <section id="social" class="block colorful">
        <div class="container">
            @foreach (Config::get("sitecore::social") as $service => $url)

                <a href="{{ $url }}" target="_blank">
                    <i class="fa fa-{{ $service }}"></i>
                </a>

            @endforeach

            <div id="copyright">
                &copy; {{ date('Y') }} <a href="http://flatturtle.com" target="_blank">FlatTurtle</a>
            </div>
        </div>
    </section>



    @if (App::environment() == 'production')
    <script src="{{ URL::asset('packages/flatturtle/sitecore/javascript/all.js?v=' . filemtime(public_path() . '/packages/flatturtle/sitecore/javascript/all.js')) }}"></script>
    @else
    <script src="{{ URL::asset('packages/flatturtle/sitecore/javascript/jquery.js') }}"></script>
    <script src="{{ URL::asset('packages/flatturtle/sitecore/javascript/jquery.datepicker.js?') }}"></script>
    <script src="{{ URL::asset('packages/flatturtle/sitecore/javascript/carousel.js') }}"></script>
    <script src="{{ URL::asset('packages/flatturtle/sitecore/javascript/script.js') }}"></script>
    @endif



    @if (Config::get('sitecore::analytics'))
    <script>
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', '{{ Config::get("sitecore::analytics") }}']);
    _gaq.push(['_trackPageview']);

    (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
    </script>
    @endif


</body>
</html>
