document.addEventListener('DOMContentLoaded', () => {
    const wheelElement = document.querySelector('[data-wheel]');
    const spinButton = document.querySelector('[data-spin]');
    const resultLabel = document.querySelector('[data-result]');
    const resultImage = document.querySelector('[data-result-image]');
    const resultBanner = document.querySelector('[data-result-banner]');
    const summaryList = document.querySelector('[data-summary-list]');
    const historyList = document.querySelector('[data-history]');
    const highlightCard = document.querySelector('[data-highlight]');
    const startButton = document.querySelector('[data-start]');
    const repeatButton = document.querySelector('[data-repeat]');
    const steps = {
        intro: document.querySelector('[data-step="intro"]'),
        wheel: document.querySelector('[data-step="wheel"]'),
        result: document.querySelector('[data-step="result"]'),
    };

    const bannerSources = {
        default: resultBanner?.dataset.defaultSrc ?? '',
        alt: resultBanner?.dataset.altSrc ?? '',
    };
    const altBannerKeys = new Set(['ice-cream', 'umbrella']);
    const hiddenBannerKeys = new Set(['try-again']);

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
        'spin/03/congrauations_an.png',
        'spin/03/03_Button.png',
        'spin/03/better luck next time.png',
        'spin/03/BG.jpg',
    ];

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

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

    const parseJsonAttribute = (value, fallback) => {
        if (!value) {
            return fallback;
        }

        try {
            return JSON.parse(value);
        } catch (error) {
            console.warn('Failed to parse JSON attribute', value, error);
            return fallback;
        }
    };

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

    const renderWheelLabels = (data) => {
        if (!wheelElement) {
            return;
        }

        wheelElement.querySelectorAll('.wheel__label').forEach((node) => node.remove());

        const fragment = document.createDocumentFragment();

        data.forEach((segment, index) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'wheel__label';
            wrapper.style.setProperty('--index', String(index));

            const span = document.createElement('span');
            span.textContent = segment.label;
            wrapper.appendChild(span);

            fragment.appendChild(wrapper);
        });

        wheelElement.appendChild(fragment);
    };

    const updateSummary = (summary) => {
        if (!summaryList) {
            return;
        }

        summaryList.innerHTML = '';

        if (!Array.isArray(summary) || summary.length === 0) {
            return;
        }

        const fragment = document.createDocumentFragment();

        summary.forEach((item) => {
            const issuedCount = item.total != null && item.remaining != null
                ? Math.max(0, item.total - item.remaining)
                : null;

            const li = document.createElement('li');
            li.className = 'summary-item';

            const strong = document.createElement('strong');
            strong.textContent = item.label;

            const span = document.createElement('span');
            if (issuedCount !== null) {
                span.textContent = `${issuedCount} issued · ${item.remaining} left`;
            } else {
                span.textContent = 'Unlimited';
            }

            li.append(strong, span);
            fragment.appendChild(li);
        });

        summaryList.appendChild(fragment);
    };

    const setResultState = ({ label = '—', image = null } = {}) => {
        if (resultLabel) {
            resultLabel.textContent = label;
        }

        if (resultImage) {
            if (image) {
                resultImage.src = image;
                resultImage.alt = label;
                resultImage.style.display = 'block';
            } else {
                resultImage.removeAttribute('src');
                resultImage.alt = '';
                resultImage.style.display = 'none';
            }
        }
    };

    const updateResultBanner = (key) => {
        if (!resultBanner) {
            return;
        }

        const normalizedKey = typeof key === 'string' ? key.toLowerCase() : '';

        if (normalizedKey && hiddenBannerKeys.has(normalizedKey)) {
            if (resultBanner.dataset.currentSrc !== 'hidden') {
                resultBanner.removeAttribute('src');
                resultBanner.dataset.currentSrc = 'hidden';
            }
            resultBanner.style.display = 'none';
            return;
        }

        const shouldUseAlt = normalizedKey && altBannerKeys.has(normalizedKey);
        const targetSrc = shouldUseAlt
            ? (bannerSources.alt || bannerSources.default)
            : bannerSources.default;

        if (!targetSrc) {
            resultBanner.removeAttribute('src');
            resultBanner.dataset.currentSrc = '';
            resultBanner.style.display = 'none';
            return;
        }

        if (resultBanner.dataset.currentSrc === targetSrc) {
            resultBanner.style.display = 'block';
            return;
        }

        resultBanner.src = targetSrc;
        resultBanner.dataset.currentSrc = targetSrc;
        resultBanner.style.display = 'block';
    };

    const mergeSummaryIntoSegments = (segmentsSource, summarySource) => {
        const summaryMap = new Map(
            Array.isArray(summarySource)
                ? summarySource.map((item) => [item.key, item])
                : [],
        );

        return (Array.isArray(segmentsSource) ? segmentsSource : []).map((segment, index) => {
            const record = summaryMap.get(segment.key);

            return {
                ...segment,
                index,
                label: record?.label ?? segment.label,
                image: record?.image ?? segment.image,
                remaining: record?.remaining ?? segment.remaining,
                total: record?.total ?? segment.total,
            };
        });
    };

    let latestSummary = parseJsonAttribute(document.body?.dataset.initialSummary, []);
    if (!Array.isArray(latestSummary)) {
        latestSummary = [];
    }

    let segmentData = [];
    let segments = [];
    let segmentCount = 8;
    let segmentAngle = 360 / segmentCount;
    const spinDuration = 4200;
    let rotation = 0;
    let spinning = false;

    const setSegmentData = (data) => {
        segmentData = Array.isArray(data)
            ? data.map((segment, index) => ({ ...segment, index }))
            : [];

        segments = segmentData.map((segment) => segment.label);
        segmentCount = segments.length || 8;
        segmentAngle = 360 / Math.max(segmentCount, 1);

        if (wheelElement) {
            wheelElement.dataset.segments = JSON.stringify(segments);
            wheelElement.dataset.segmentConfig = JSON.stringify(segmentData);
        }

        renderWheelLabels(segmentData);
    };

    const applyData = (segmentsSource, summarySource) => {
        if (Array.isArray(summarySource)) {
            latestSummary = summarySource;
        }

        const merged = mergeSummaryIntoSegments(
            segmentsSource ?? segmentData,
            latestSummary,
        );

        setSegmentData(merged);
        updateSummary(latestSummary);
    };

    const calculateIndex = (currentRotation) => {
        const normalized = ((currentRotation % 360) + 360) % 360;
        const relative = (360 - normalized + segmentAngle / 2) % 360;
        return Math.floor(relative / segmentAngle) % Math.max(segmentCount, 1);
    };

    const renderHistory = (label) => {
        if (!historyList || !label) {
            return;
        }

        const item = document.createElement('li');
        item.className = 'history-item';
        item.textContent = label;
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

    const submitSpin = async (segmentKey) => {
        if (!segmentKey) {
            throw new Error('Missing prize key');
        }

        const response = await fetch('/api/spins', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            credentials: 'same-origin',
            body: JSON.stringify({ key: segmentKey }),
        });

        if (response.ok) {
            return response.json();
        }

        const errorPayload = await response.json().catch(() => null);
        const errorMessage = errorPayload?.message ?? 'Unable to record spin. Please try again.';
        throw new Error(errorMessage);
    };

    const refreshFromApi = async () => {
        try {
            const response = await fetch('/api/prizes', { cache: 'no-store' });
            if (!response.ok) {
                throw new Error(`Request failed with status ${response.status}`);
            }

            const data = await response.json();
            const segmentsSource = Array.isArray(data.segments) ? data.segments : segmentData;
            const summarySource = Array.isArray(data.summary) ? data.summary : latestSummary;

            applyData(segmentsSource, summarySource);
        } catch (error) {
            console.warn('Unable to refresh prize data', error);
        }
    };

    const finalizeSpin = async (segment, fallbackLabel) => {
        const initialLabel = segment?.label ?? fallbackLabel;
        const initialImage = segment?.image ?? null;

        setResultState({ label: initialLabel, image: initialImage });
        updateResultBanner(segment?.key ?? null);
        showStep('result');
        activateCelebration();

        if (!segment?.key) {
            setResultState({ label: initialLabel, image: initialImage });
            updateResultBanner(null);
            renderHistory(initialLabel);
            return;
        }

        try {
            const payload = await submitSpin(segment.key);
            const awarded = payload?.result ?? null;
            const summary = payload?.summary ?? null;

            const displayLabel = awarded?.label ?? initialLabel;
            const image = awarded?.image ?? initialImage;

            setResultState({ label: displayLabel, image });
            updateResultBanner(awarded?.key ?? segment?.key ?? null);

            renderHistory(displayLabel);

            if (Array.isArray(summary)) {
                applyData(undefined, summary);
            } else {
                refreshFromApi();
            }
        } catch (error) {
            console.error('Unable to record spin', error);
            setResultState({ label: initialLabel, image: initialImage });
            updateResultBanner(segment?.key ?? null);
            renderHistory(initialLabel);
            refreshFromApi();
            if (error?.message) {
                console.warn(error.message);
            }
        }
    };

    const resetResult = () => {
        setResultState({ label: '—', image: null });
        updateResultBanner(null);
    };

    const initialSegmentSource = parseJsonAttribute(wheelElement.dataset.segmentConfig, []);
    applyData(initialSegmentSource, latestSummary);

    showStep('intro');
    resetResult();
    refreshFromApi();

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

        resetResult();
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
            const fallbackLabel = segments[index] || `Reward ${index + 1}`;
            const selectedSegment = segmentData[index];

            finalizeSpin(selectedSegment, fallbackLabel);
        }, spinDuration);
    });
});
