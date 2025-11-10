<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lucky Wheel Kiosk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('spring-wheel.css') }}">
</head>
<body>
    <div class="preloader" data-preloader>
        <div class="preloader__inner" role="status" aria-live="polite">
            <img
                src="{{ asset('spin/01/01_Logo.png') }}"
                alt="Loading Redmi Spring Wheel"
                class="preloader__logo"
            >
            <p class="preloader__message" data-preloader-note>Preparing your experience…</p>
            <div class="preloader__track" aria-hidden="true">
                <div class="preloader__progress" data-preloader-progress></div>
            </div>
            <p class="preloader__count">
                <span data-preloader-count>0</span>
                /
                <span data-preloader-total>0</span>
            </p>
        </div>
    </div>
    <div class="screen">
        <div class="surface">

            <div class="stepper">
                <section class="step step--intro is-active" data-step="intro">
                    <div class="intro intro--step-one">
                        <img
                            src="{{ asset('spin/01/01_Logo.png') }}"
                            alt="Redmi Spring Wheel"
                            class="intro__logo" style="height: 14vh;"
                        >


                        <img src="{{ asset('spin/01/01_Button.png') }}" alt="Play" style="height: 15vh;margin-top: 30vh;" data-start>


                    </div>
                </section>

                <section class="step step--wheel" data-step="wheel">
                    <img
                    src="{{ asset('spin/01/01_Logo.png') }}"
                    alt="Redmi Spring Wheel"
                    class="intro__logo" style="height: 14vh;"
                >
                    <div class="wheel-stage">
                        <div class="wheel-wrapper">
                            <div class="pointer" aria-hidden="true"></div>
                            <div
                                class="wheel"
                                data-wheel
                                data-segments='["Heart","Try Again","T-Shirt","Try Again","Ice Cream","Balloon","Try Again","Cap"]'
                            >
                                @foreach ([
                                    'Heart',
                                    'Try Again',
                                    'T-Shirt',
                                    'Try Again',
                                    'Ice Cream',
                                    'Balloon',
                                    'Try Again',
                                    'Cap',
                                ] as $index => $label)
                                    <div class="wheel__label" style="--index: {{ $index }}">
                                        <span>{{ $label }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <img src="{{ asset('spin/02/02_Button.png') }}"  data-spin aria-label="Spin the wheel" alt="" style="width: 40vh;z-index: 1;margin-top: 7vh;">
                        </div>
                    </div>
                </section>

                <section class="step step--result" data-step="result">
                    <div class="result-wrapper">
                        <img
                            src="{{ asset('spin/03/Congratulations.png') }}"
                            alt="Congratulations"
                            class="result-banner" style="width: 40vh;margin-bottom: 3vh;"
                        >
                        <div class="status-current" data-highlight >
                            <span style="font-size: 5vh;" class="status-value" data-result>—</span>
                        </div>
                        <div class="status-history" style="display: none">
                            <span class="status-label">Recent Winners</span>
                            <ul class="history-list" data-history></ul>
                        </div>

                        <img src="{{ asset('spin/03/03_Button.png') }}" alt="Play Again" data-repeat style="width: 27vh;margin-top: 14vh;">
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script src="{{ asset('spring-wheel.js') }}"></script>
</body>
</html>
