<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lucky Wheel Kiosk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Lucky Wheel Kiosk (9:16) */
        :root {
            color-scheme: light;
            --bg-top: #210200;
            --bg-mid: #450700;
            --bg-bottom: #080101;
            --accent: #ff7a12;
            --accent-strong: #ff5500;
            --accent-soft: #ffd29a;
            --glass: rgba(255, 255, 255, 0.04);
            --border-light: rgba(255, 255, 255, 0.16);
            --text-primary: #fff8f1;
            --text-muted: rgba(255, 245, 230, 0.68);
            --brand-mi: linear-gradient(145deg, #ff6f00 0%, #ff8f1f 100%);
            --brand-highlight: linear-gradient(180deg, #ffb040 0%, #ff6f00 100%);
            --hub-bg: radial-gradient(circle at 35% 30%, #fff8ed 0%, #ffe0b5 48%, #ffb364 100%);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(18px, 3vh, 40px);
            background: radial-gradient(circle at 20% -10%, rgba(255, 110, 20, 0.45) 0%, transparent 48%),
                radial-gradient(circle at 80% 0%, rgba(255, 80, 0, 0.35) 0%, transparent 52%),
                linear-gradient(180deg, var(--bg-top) 0%, var(--bg-mid) 45%, var(--bg-bottom) 100%);
            font-family: 'Manrope', 'Segoe UI', Roboto, sans-serif;
            color: var(--text-primary);
        }

        .preloader {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, rgba(24, 4, 0, 0.92) 0%, rgba(8, 1, 0, 0.96) 100%);
            z-index: 9999;
            transition: opacity 0.45s ease, visibility 0.45s ease;
        }

        .preloader::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 50% 35%, rgba(255, 130, 20, 0.35) 0%, transparent 62%);
            pointer-events: none;
        }

        .preloader.is-hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .preloader__inner {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: clamp(12px, 3vh, 22px);
            text-align: center;
        }

        .preloader__logo {
            width: clamp(120px, 32vw, 220px);
            height: auto;
            filter: drop-shadow(0 24px 48px rgba(0, 0, 0, 0.45));
        }

        .preloader__message {
            margin: 0;
            font-weight: 600;
            font-size: clamp(16px, 2.6vh, 20px);
            letter-spacing: 0.06em;
            color: rgba(255, 248, 241, 0.86);
        }

        .preloader__track {
            width: clamp(200px, 48vw, 360px);
            height: 8px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.18);
            overflow: hidden;
        }

        .preloader__progress {
            width: 100%;
            height: 100%;
            background: var(--brand-highlight);
            transform-origin: left center;
            transform: scaleX(0);
            transition: transform 0.24s ease-out;
        }

        .preloader__count {
            margin: 0;
            font-size: clamp(14px, 2.4vh, 18px);
            font-weight: 600;
            letter-spacing: 0.12em;
            color: rgba(255, 245, 230, 0.72);
        }

        .screen {
            position: relative;
            width: auto;
            max-width: 96vw;
            height: min(97vh, 1880px);
            aspect-ratio: 9 / 16;
            margin: 0 auto;
            background: linear-gradient(180deg, rgba(24, 4, 0, 0.96) 0%, rgba(8, 1, 0, 0.92) 100%);
            border: 1px solid var(--border-light);
            border-radius: 44px;
            box-shadow:
                0 40px 120px rgba(0, 0, 0, 0.65),
                inset 0 1px 0 rgba(255, 255, 255, 0.12);
            overflow: hidden;
            display: flex;
            padding: clamp(20px, 3vh, 32px);
        }

        .screen::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: radial-gradient(circle at 50% 20%, rgba(255, 150, 40, 0.2) 0%, transparent 50%);
            pointer-events: none;
        }

        .surface {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            width: 100%;
            gap: clamp(18px, 3.6vh, 26px);
        }

        .brand {
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: clamp(10px, 2vh, 16px);
        }

        .brand__row {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: clamp(14px, 3vh, 22px);
        }

        .brand__logo {
            display: grid;
            place-items: center;
            padding: clamp(10px, 2.4vh, 14px) clamp(16px, 3.2vh, 22px);
            border-radius: 16px;
            background: var(--brand-mi);
            color: #ffffff;
            font-weight: 800;
            font-size: clamp(18px, 2.8vh, 26px);
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .brand__divider {
            width: 2px;
            height: clamp(30px, 6vh, 44px);
            background: rgba(255, 255, 255, 0.18);
        }

        .brand__partner {
            font-size: clamp(22px, 3.6vh, 32px);
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .brand h1 {
            margin: 0;
            font-size: clamp(24px, 4vh, 34px);
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--accent-soft);
            text-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
        }

        .stepper {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            gap: clamp(28px, 4vh, 46px);
        }

        .step {
            display: none;
            flex: 1 1 auto;
            align-items: center;
            justify-content: center;
        }

        .step.is-active {
            display: flex;
        }

        .step--intro {
            text-align: center;
            flex-direction: column;
            gap: clamp(20px, 4vh, 36px);
        }

        .step--wheel {
            flex-direction: column;
        }

        .intro {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: clamp(18px, 3.2vh, 28px);
        }

        .intro--step-one {
            width: 100%;
            max-width: clamp(320px, 72vw, 720px);
            margin: 0 auto;
            gap: clamp(32px, 6vh, 52px);
        }

        .intro__logo {
            width: 100%;
            max-width: clamp(260px, 58vw, 540px);
            height: auto;
            display: block;
            filter: drop-shadow(0 28px 48px rgba(0, 0, 0, 0.45));
        }

        .intro__play-button {
            appearance: none;
            border: 0;
            background: none;
            padding: 0;
            cursor: pointer;
            width: 100%;
            max-width: clamp(220px, 48vw, 420px);
            transition: transform 0.18s ease, filter 0.18s ease;
        }

        .intro__play-button img {
            display: block;
            width: 100%;
            height: auto;
            filter: drop-shadow(0 24px 40px rgba(255, 120, 0, 0.45));
        }

        .intro__play-button:hover,
        .intro__play-button:focus-visible {
            transform: translateY(-4px) scale(1.02);
        }

        .intro__play-button:active {
            transform: translateY(0) scale(0.97);
        }

        .intro__title {
            margin: 0;
            font-size: clamp(32px, 6vh, 56px);
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #ffffff;
            text-shadow: 0 18px 46px rgba(0, 0, 0, 0.55);
        }

        .intro__text {
            margin: 0;
            max-width: 48ch;
            font-size: clamp(16px, 2.6vh, 22px);
            line-height: 1.4;
            color: var(--text-muted);
        }

        .cta-button {
            appearance: none;
            border: 0;
            border-radius: 999px;
            padding: clamp(14px, 3vh, 20px) clamp(40px, 8vh, 60px);
            background: var(--brand-highlight);
            color: #ffffff;
            font-size: clamp(18px, 2.8vh, 24px);
            font-weight: 800;
            letter-spacing: 0.32em;
            text-transform: uppercase;
            box-shadow: 0 24px 48px rgba(255, 110, 20, 0.4);
            cursor: pointer;
            transition: transform 0.18s ease, box-shadow 0.18s ease, opacity 0.18s ease;
        }

        .cta-button:hover,
        .cta-button:focus-visible {
            transform: translateY(-2px);
            box-shadow: 0 30px 56px rgba(255, 110, 20, 0.46);
        }

        .cta-button:active {
            transform: translateY(1px);
            box-shadow: 0 14px 28px rgba(255, 110, 20, 0.32);
        }

        .cta-button:disabled {
            opacity: 0.58;
            cursor: not-allowed;
            box-shadow: none;
        }

        .cta-button--ghost {
            background: transparent;
            border: 2px solid var(--accent);
            color: var(--accent);
            box-shadow: none;
            letter-spacing: 0.26em;
        }

        .cta-button--ghost:hover,
        .cta-button--ghost:focus-visible {
            transform: translateY(-2px);
            box-shadow: 0 16px 32px rgba(255, 110, 20, 0.32);
        }

        .wheel-stage {
            flex: 1 1 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: visible;
            padding-top: 0;
        }

        .wheel-wrapper {
            position: relative;
            width: 85vh;
            aspect-ratio: 1 / 1;
            display: grid;
            place-items: center;
            transform: translateY(0);
            overflow: visible;
            height: 116vh;
            margin-top: 14vh;
        }

        .step--result {
            flex-direction: column;
        }

        .result-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: clamp(18px, 3vh, 26px);
        }

        .result-banner {
            width: 100%;
            max-width: clamp(320px, 68vw, 760px);
            height: auto;
            display: block;
            filter: drop-shadow(0 32px 48px rgba(0, 0, 0, 0.45));
        }

        .result-wrapper .status-panel {
            min-width: clamp(320px, 48vw, 560px);
        }

        .result-repeat-button {
            appearance: none;
            border: 0;
            background: none;
            padding: 0;
            cursor: pointer;
            width: clamp(220px, 42vh, 420px);
            transition: transform 0.16s ease, filter 0.16s ease, opacity 0.16s ease;
        }

        .result-repeat-button img {
            display: block;
            width: 100%;
            height: auto;
        }

        .result-repeat-button:hover,
        .result-repeat-button:focus-visible {
            transform: translateY(-4px) scale(1.02);
        }

        .result-repeat-button:active {
            transform: translateY(0) scale(0.97);
        }

        .result-repeat-button:disabled {
            opacity: 0.58;
            cursor: not-allowed;
            filter: none;
        }

        .result-repeat-button:disabled img {
            filter: grayscale(0.35);
        }

        .wheel-wrapper::before {
            content: "";
            position: absolute;
            inset: 0;
            background: url('spin/02/Wheel_1_Steady.png') center/contain no-repeat;
            z-index: 1;
            pointer-events: none;
        }

        .pointer {
            position: absolute;
            top: 17%;
            left: 50%;
            transform: translate(-50%, 0);
            width: clamp(38px, 9vh, 54px);
            height: clamp(46px, 10vh, 64px);
            background: linear-gradient(180deg, #ffffff 0%, #f4f4f4 100%);
            clip-path: polygon(50% 0%, 100% 100%, 0% 100%);
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.45);
            z-index: 4;
        }

        .pointer::after {
            content: "";
            position: absolute;
            top: 58%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 36%;
            height: 36%;
            border-radius: 50%;
            background: var(--accent-strong);
            box-shadow: 0 0 12px rgba(255, 85, 0, 0.6);
        }

        .wheel {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            transform-origin: center center;
            transform: rotate(var(--rotation, 0deg));
            overflow: hidden;
            z-index: 0;
        }

        .wheel::after {
            content: "";
            position: absolute;
            inset: 0;
            background: url('spin/02/Wheel_2_Rotate.png') center/contain no-repeat;
            opacity: 1;
            z-index: 1;
            pointer-events: none;
        }

        .wheel.is-spinning::after {
            opacity: 1;
        }

        .wheel__label {
            display: none;
        }

        .wheel__label span {
            display: none;
        }

        .wheel__hub {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 64%;
            aspect-ratio: 1 / 1;
            border-radius: 50%;
            background: var(--hub-bg);
            box-shadow:
                0 18px 36px rgba(0, 0, 0, 0.55),
                inset 0 -12px 18px rgba(255, 120, 30, 0.45);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: clamp(10px, 2vh, 14px);
            padding: clamp(20px, 4vh, 26px) clamp(14px, 2.6vh, 20px) clamp(28px, 4.8vh, 34px);
            z-index: 3;
        }

        .wheel__badge {
            font-size: clamp(18px, 2.6vh, 24px);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.22em;
            color: var(--accent-strong);
        }

        .spin-button {
            appearance: none;
            border: 0;
            background: none;
            padding: 0;
            cursor: pointer;
            position: absolute;
            top: 57vh;
            z-index: 1000;
            width: clamp(220px, 36vh, 380px);
            transition: transform 0.16s ease, filter 0.16s ease, opacity 0.16s ease;
        }

        .spin-button img {
            display: block;
            width: 100%;
            height: auto;
            pointer-events: none;
            filter: drop-shadow(0 24px 42px rgba(0, 0, 0, 0.55));
        }

        .spin-button:hover,
        .spin-button:focus-visible {
            transform: translateY(-4px) scale(1.02);
        }

        .spin-button:active {
            transform: translateY(0) scale(0.97);
        }

        .spin-button:disabled {
            opacity: 0.58;
            cursor: not-allowed;
            filter: none;
        }

        .spin-button:disabled img {
            filter: drop-shadow(0 16px 28px rgba(0, 0, 0, 0.28));
        }

        .status-panel {
            background: var(--glass);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 28px;
            padding: clamp(16px, 3vh, 22px);
            display: flex;
            flex-direction: column;
            gap: clamp(14px, 3vh, 18px);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.05);
        }

        .status-label {
            font-size: clamp(11px, 1.8vh, 13px);
            text-transform: uppercase;
            letter-spacing: 0.32em;
            color: var(--text-muted);
            display: block;
            margin-bottom: clamp(6px, 1.2vh, 8px);
        }

        .status-current {
            background: rgba(255, 255, 255, 0.04);
            border-radius: 20px;
            padding: clamp(14px, 2.6vh, 18px) clamp(16px, 3vh, 22px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            flex-direction: column;
            gap: clamp(6px, 1.2vh, 8px);
        }

        .status-value {
            font-size: clamp(24px, 4vh, 30px);
            font-weight: 800;
            letter-spacing: 0.22em;
            text-transform: uppercase;
        }

        .status-history {
            display: flex;
            flex-direction: column;
            gap: clamp(10px, 2vh, 14px);
        }

        .history-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: clamp(8px, 1.8vh, 12px);
        }

        .history-item {
            background: rgba(255, 255, 255, 0.07);
            border-radius: 16px;
            padding: clamp(10px, 2vh, 14px);
            text-align: center;
            font-weight: 600;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(255, 216, 170, 0.92);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.05);
        }

        .status-current.celebrate {
            animation: glow 0.9s ease-in-out 3;
        }

        @keyframes glow {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 rgba(255, 130, 0, 0.0);
            }
            50% {
                transform: scale(1.02);
                box-shadow: 0 0 40px rgba(255, 120, 0, 0.45);
            }
        }

        @media (max-width: 720px) {
            .screen {
                height: auto;
                width: 92vw;
                aspect-ratio: 9 / 16;
            }

            .history-list {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 420px) {
            body {
                padding: 8px;
            }

            .screen {
                border-radius: 32px;
            }
        }
    </style>
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
                                data-segments='["Water bottle","Ice cream","Try again","T shirt","Mug","Umbrella","Try again","Cap"]'
                            >
                                @foreach ([
                                    'Water bottle',
                                    'Ice cream',
                                    'Try again',
                                    'T shirt',
                                    'Mug',
                                    'Umbrella',
                                    'Try again',
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const wheelElement = document.querySelector('[data-wheel]');
            const spinButton = document.querySelector('[data-spin]');
            const resultLabel = document.querySelector('[data-result]');
            const historyList = document.querySelector('[data-history]');
            const highlightCard = document.querySelector('[data-highlight]');
            const startButton = document.querySelector('[data-start]');
            const repeatButton = document.querySelector('[data-repeat]');
            const steps = {
                intro: document.querySelector('[data-step="intro"]'),
                wheel: document.querySelector('[data-step="wheel"]'),
                result: document.querySelector('[data-step="result"]'),
            };

            const preloaderElement = document.querySelector('[data-preloader]');
            const preloaderProgress = preloaderElement?.querySelector('[data-preloader-progress]');
            const preloaderCount = preloaderElement?.querySelector('[data-preloader-count]');
            const preloaderTotal = preloaderElement?.querySelector('[data-preloader-total]');
            const preloaderNote = preloaderElement?.querySelector('[data-preloader-note]');

            const assetSources = [
                'spin/01/01_Logo.png',
                'spin/01/01_Button.png',
                'spin/01/BG.jpg',
                'spin/02/02_Button.png',
                'spin/02/Wheel_1_Steady.png',
                'spin/02/Wheel_2_Rotate.png',
                'spin/02/BG.jpg',
                'spin/03/Congratulations.png',
                'spin/03/03_Button.png',
                'spin/03/better luck next time.png',
                'spin/03/BG.jpg',
            ];

            const hidePreloader = () => {
                if (!preloaderElement) {
                    return;
                }

                preloaderElement.classList.add('is-hidden');

                const cleanup = () => {
                    preloaderElement.removeEventListener('transitionend', cleanup);
                    if (preloaderElement.parentNode) {
                        preloaderElement.parentNode.removeChild(preloaderElement);
                    }
                };

                preloaderElement.addEventListener('transitionend', cleanup);
                window.setTimeout(cleanup, 1000);
            };

            const preloadAssets = () => {
                if (!preloaderElement) {
                    return Promise.resolve();
                }

                const uniqueSources = Array.from(new Set(assetSources));
                const total = uniqueSources.length;

                if (preloaderTotal) {
                    preloaderTotal.textContent = String(total);
                }

                if (total === 0) {
                    hidePreloader();
                    return Promise.resolve();
                }

                if (preloaderCount) {
                    preloaderCount.textContent = '0';
                }

                if (preloaderProgress) {
                    preloaderProgress.style.transform = 'scaleX(0)';
                }

                return new Promise((resolve) => {
                    let loaded = 0;

                    const advance = () => {
                        loaded += 1;

                        if (preloaderCount) {
                            preloaderCount.textContent = String(loaded);
                        }

                        if (preloaderProgress) {
                            preloaderProgress.style.transform = `scaleX(${Math.min(1, loaded / total)})`;
                        }

                        if (loaded >= total) {
                            if (preloaderNote) {
                                preloaderNote.textContent = 'Ready!';
                            }

                            window.setTimeout(() => {
                                hidePreloader();
                                resolve();
                            }, 320);
                        }
                    };

                    uniqueSources.forEach((src) => {
                        const image = new Image();
                        image.addEventListener('load', advance, { once: true });
                        image.addEventListener('error', advance, { once: true });
                        image.src = new URL(src, window.location.origin).toString();
                    });
                });
            };

            preloadAssets().catch(() => {
                hidePreloader();
            });

            if (!wheelElement || !spinButton || !resultLabel) {
                return;
            }

            const showStep = (key) => {
                Object.entries(steps).forEach(([name, element]) => {
                    if (!element) {
                        return;
                    }

                    if (name === key) {
                        element.classList.add('is-active');
                        element.setAttribute('aria-hidden', 'false');
                    } else {
                        element.classList.remove('is-active');
                        element.setAttribute('aria-hidden', 'true');
                    }
                });
            };

            showStep('intro');

            const segments = (wheelElement.dataset.segments && JSON.parse(wheelElement.dataset.segments)) || [];
            const segmentCount = segments.length || 8;
            const segmentAngle = 360 / segmentCount;
            const spinDuration = 4200;

            let rotation = 0;
            let spinning = false;

            const renderHistory = (selection) => {
                if (!historyList) {
                    return;
                }

                const item = document.createElement('li');
                item.className = 'history-item';
                item.textContent = selection;
                historyList.prepend(item);

                const maxItems = 6;
                while (historyList.children.length > maxItems) {
                    historyList.removeChild(historyList.lastElementChild);
                }
            };

            const activateCelebration = () => {
                if (!highlightCard) {
                    return;
                }

                highlightCard.classList.add('celebrate');
                highlightCard.addEventListener('animationend', () => {
                    highlightCard.classList.remove('celebrate');
                }, { once: true });
            };

            const calculateIndex = (currentRotation) => {
                const normalized = ((currentRotation % 360) + 360) % 360;
                const relative = (360 - normalized + segmentAngle / 2) % 360;
                return Math.floor(relative / segmentAngle) % segmentCount;
            };

            startButton?.addEventListener('click', () => {
                if (spinning) {
                    return;
                }

                showStep('wheel');
            });

            repeatButton?.addEventListener('click', () => {
                if (spinning) {
                    return;
                }

                resultLabel.textContent = '—';
                showStep('wheel');
            });

            spinButton.addEventListener('click', () => {
                if (spinning) {
                    return;
                }

                spinning = true;
                spinButton.disabled = true;
                wheelElement.classList.add('is-spinning');

                const currentNormalized = ((rotation % 360) + 360) % 360;
                rotation = currentNormalized;

                wheelElement.style.transition = 'none';
                wheelElement.style.setProperty('--rotation', `${rotation}deg`);

                window.requestAnimationFrame(() => {
                    wheelElement.style.transition = `transform ${spinDuration}ms cubic-bezier(0.22, 0.9, 0.15, 1)`;

                    const extraSpins = 4 + Math.random() * 3;
                    const randomOffset = Math.random() * 360;
                    rotation += extraSpins * 360 + randomOffset;

                    wheelElement.style.setProperty('--rotation', `${rotation}deg`);
                });

                window.setTimeout(() => {
                    spinning = false;
                    spinButton.disabled = false;
                    wheelElement.classList.remove('is-spinning');
                    wheelElement.style.transition = 'none';

                    const index = calculateIndex(rotation);
                    const selection = segments[index] || `Reward ${index + 1}`;
                    resultLabel.textContent = selection;
                    renderHistory(selection);
                    showStep('result');
                    activateCelebration();
                }, spinDuration);
            });
        });
    </script>
</body>
</html>
