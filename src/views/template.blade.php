<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $flatturtle->title }}</title>
    <link href="{{ URL::asset('packages/flatturtle/sitecore/css/common.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('favicon.ico') }}" rel="icon" type="image/x-icon">

    <style>
    .colorful, .btn-special {
        background-color: {{ $flatturtle->color }};
    }

    #jumbo {
        border-top: 5px solid {{ $flatturtle->color }};
    }

    h1, a, a:hover, .carousel-control:hover {
        color: {{ $flatturtle->color }};
    }
    </style>
</head>
<body>


    <section id="jumbo" class="carousel slide">
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
            <span class="icon-prev"></span>
        </a>
        <a class="right carousel-control" href="#jumbo" data-slide="next">
            <span class="icon-next"></span>
        </a>
    </section>



    <nav class="colorful">
        <ul>
        @foreach ($blocks as $block)
            @if ($block->title)
            <li>
                <a href="#{{ $block->id }}">{{ $block->title }}</a>
            </li>
            @endif
        @endforeach
            <li>
                <a href="#newsletter">Newsletter</a>
            </li>
        </ul>
    </nav>



    <section id="content">
    @foreach ($blocks as $block)

    <div class="block">
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



    <section id="map">
        <iframe width="100%" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
            src="https://maps.google.ro/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q={{ $flatturtle->latitude }},{{ $flatturtle->longitude }}&amp;z={{ $flatturtle->zoom }}&amp;iwloc=A&amp;output=embed&amp;iwloc=nea"
        ></iframe>
    </section>



    <section id="social" class="block colorful">
        <div class="container">
            @foreach (Config::get("sitecore::social") as $service => $url)

                <a href="{{ $url }}" target="_blank">
                    <i class="icon-{{ $service }}"></i>
                </a>

            @endforeach

            <div id="copyright">
                &copy; {{ date('Y') }} <a href="http://flatturtle.com" target="_blank">FlatTurtle</a>
            </div>
        </div>
    </section>



    <script src="{{ URL::asset('packages/flatturtle/sitecore/javascript/jquery.js') }}"></script>
    <script src="{{ URL::asset('packages/flatturtle/sitecore/javascript/carousel.js') }}"></script>



    @if (Config::get('sitecore::analytics'))
    <script>
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', '{{ Config::get("sitecore::analytics") }}']);
    _gaq.push(['_trackPageview']);

    (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
    </script>
    @endif


</body>
</html>
