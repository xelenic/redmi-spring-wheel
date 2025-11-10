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
