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
    <div class="screen">
        <div class="surface">
            <header class="brand" aria-label="Brand">
                <div class="brand__row">
                    <span class="brand__logo">mi</span>
                    <span class="brand__divider" aria-hidden="true"></span>
                    <span class="brand__partner">Abans</span>
                </div>
                <h1>Lucky Wheel</h1>
            </header>

            <div class="wheel-stage">
                <div class="wheel-wrapper">
                    <div class="pointer" aria-hidden="true"></div>
                    <div
                        class="wheel"
                        data-wheel
                        data-segments='["Balloon","Bonus Spin","Ice Cream","Try Again","Mystery Gift","Coffee Voucher","VIP Pass","Lucky Draw"]'
                    >
                        @foreach ([
                            'Balloon',
                            'Bonus Spin',
                            'Ice Cream',
                            'Try Again',
                            'Mystery Gift',
                            'Coffee Voucher',
                            'VIP Pass',
                            'Lucky Draw',
                        ] as $index => $label)
                            <div class="wheel__label" style="--index: {{ $index }}">
                                <span>{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="spin-button" data-spin>Spin</button>

                </div>
            </div>

            <section class="status-panel" aria-live="polite">
                <div class="status-current" data-highlight>
                    <span class="status-label">Current Reward</span>
                    <span class="status-value" data-result>—</span>
                </div>
                <div class="status-history">
                    <span class="status-label">Recent Winners</span>
                    <ul class="history-list" data-history></ul>
                </div>
            </section>
        </div>
    </div>

    <script src="{{ asset('spring-wheel.js') }}"></script>
</body>
</html>
