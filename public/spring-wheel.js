document.addEventListener('DOMContentLoaded', () => {
    const wheel = document.querySelector('[data-wheel]');
    const spinButton = document.querySelector('[data-spin]');
    const resultLabel = document.querySelector('[data-result]');
    const historyList = document.querySelector('[data-history]');
    const highlightCard = document.querySelector('[data-highlight]');

    if (!wheel || !spinButton || !resultLabel) {
        return;
    }

    const segments = (wheel.dataset.segments && JSON.parse(wheel.dataset.segments)) || [];
    const segmentCount = segments.length || 8;
    const segmentAngle = 360 / segmentCount;

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
        const relative = (360 - normalized) % 360;
        return Math.floor(relative / segmentAngle) % segmentCount;
    };

    spinButton.addEventListener('click', () => {
        if (spinning) {
            return;
        }

        spinning = true;
        spinButton.disabled = true;
        wheel.classList.add('is-spinning');

        const extraSpins = 4 + Math.random() * 3;
        const randomOffset = Math.random() * 360;
        rotation += extraSpins * 360 + randomOffset;

        wheel.style.setProperty('--rotation', `${rotation}deg`);

        const transitionDuration = 4200;

        window.setTimeout(() => {
            spinning = false;
            spinButton.disabled = false;
            wheel.classList.remove('is-spinning');

            const index = calculateIndex(rotation);
            const selection = segments[index] || `Reward ${index + 1}`;
            resultLabel.textContent = selection;
            renderHistory(selection);
            activateCelebration();
        }, transitionDuration);
    });
});
