document.addEventListener('DOMContentLoaded', () => {
    const cards = Array.from(
        document.querySelectorAll('[data-dish-card]')
    );

    const searchInput = document.querySelector(
        '[data-dish-search]'
    );

    const typeFilter = document.querySelector(
        '[data-dish-type-filter]'
    );

    const statusFilter = document.querySelector(
        '[data-dish-status-filter]'
    );

    const usageFilter = document.querySelector(
        '[data-dish-usage-filter]'
    );

    const resetButton = document.querySelector(
        '[data-dish-reset]'
    );

    const resultCount = document.querySelector(
        '[data-dish-filter-count]'
    );

    const noResultMessage = document.querySelector(
        '[data-dish-no-result]'
    );

    if (
        cards.length === 0
        || !searchInput
        || !typeFilter
        || !statusFilter
        || !usageFilter
        || !resetButton
        || !resultCount
        || !noResultMessage
    ) {
        return;
    }

    const normalizeText = (value) => {
        return value
            .toLocaleLowerCase('fr-FR')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();
    };

    const updateCount = (visibleCount) => {
        if (visibleCount === 0) {
            resultCount.textContent = 'Aucun plat affiché';
            return;
        }

        if (visibleCount === 1) {
            resultCount.textContent = '1 plat affiché';
            return;
        }

        resultCount.textContent =
            `${visibleCount} plats affichés`;
    };

    const applyFilters = () => {
        const searchedName = normalizeText(
            searchInput.value
        );

        const selectedType = typeFilter.value;
        const selectedStatus = statusFilter.value;
        const selectedUsage = usageFilter.value;

        let visibleCount = 0;

        cards.forEach((card) => {
            const dishName = normalizeText(
                card.dataset.dishName ?? ''
            );

            const dishType =
                card.dataset.dishType ?? '';

            const dishStatus =
                card.dataset.dishStatus ?? '';

            const dishUsage =
                card.dataset.dishUsage ?? '';

            const matchesName =
                searchedName === ''
                || dishName.includes(searchedName);

            const matchesType =
                selectedType === ''
                || dishType === selectedType;

            const matchesStatus =
                selectedStatus === ''
                || dishStatus === selectedStatus;

            const matchesUsage =
                selectedUsage === ''
                || dishUsage === selectedUsage;

            const isVisible =
                matchesName
                && matchesType
                && matchesStatus
                && matchesUsage;

            card.classList.toggle(
                'd-none',
                !isVisible
            );

            if (isVisible) {
                visibleCount += 1;
            }
        });

        noResultMessage.classList.toggle(
            'd-none',
            visibleCount !== 0
        );

        updateCount(visibleCount);
    };

    const resetFilters = () => {
        searchInput.value = '';
        typeFilter.value = '';
        statusFilter.value = '';
        usageFilter.value = '';

        applyFilters();

        searchInput.focus();
    };

    searchInput.addEventListener(
        'input',
        applyFilters
    );

    typeFilter.addEventListener(
        'change',
        applyFilters
    );

    statusFilter.addEventListener(
        'change',
        applyFilters
    );

    usageFilter.addEventListener(
        'change',
        applyFilters
    );

    resetButton.addEventListener(
        'click',
        resetFilters
    );

    applyFilters();
});