document.addEventListener('DOMContentLoaded', () => {
    const wheelElement = document.querySelector('[data-wheel]');
    const spinButton = document.querySelector('[data-spin]');
    const resultLabel = document.querySelector('[data-result]');
    const resultImage = document.querySelector('[data-result-image]');
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

    const prizeImages = Object.freeze({
        'water bottle': 'spin/gifts/Water Bottle.png',
        'ice cream': 'spin/gifts/ice_cream.png',
        'try again': 'spin/03/better luck next time.png',
        't shirt': 'spin/gifts/T-SHIRT.png',
        'mug': 'spin/gifts/MUG.png',
        'umbrella': 'spin/umbrella.png',
        'cap': 'spin/gifts/CAP.png',
    });

    const updateResultImage = (selection) => {
        if (!resultImage) {
            return;
        }

        const normalized = selection.trim().toLowerCase();
        const relativeSrc = prizeImages[normalized] || null;

        if (relativeSrc) {
            const resolved = new URL(relativeSrc, window.location.origin).toString();
            resultImage.src = resolved;
            resultImage.alt = selection;
            resultImage.style.display = 'block';
        } else {
            resultImage.removeAttribute('src');
            resultImage.alt = '';
            resultImage.style.display = 'none';
        }
    };

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
        updateResultImage('—');
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
            updateResultImage(selection);
            renderHistory(selection);
            showStep('result');
            activateCelebration();
        }, spinDuration);
    });
});
