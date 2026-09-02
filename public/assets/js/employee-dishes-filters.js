document.addEventListener('DOMContentLoaded', () => {


    /* -------------------------------------------------- */
    /* récupération des éléments */
    /* -------------------------------------------------- */

    /* Récupère toutes les cartes de plats affichées sur la page. */
    const cards = Array.from(
        document.querySelectorAll('[data-dish-card]')
    );

    /* Récupère le champ de recherche par nom. */
    const searchInput = document.querySelector(
        '[data-dish-search]'
    );

    /* Récupère le filtre par type de plat. */
    const typeFilter = document.querySelector(
        '[data-dish-type-filter]'
    );

    /* Récupère le filtre par statut actif ou inactif. */
    const statusFilter = document.querySelector(
        '[data-dish-status-filter]'
    );

    /* Récupère le filtre indiquant si le plat est utilisé dans un menu. */
    const usageFilter = document.querySelector(
        '[data-dish-usage-filter]'
    );

    /* Récupère le bouton de réinitialisation des filtres. */
    const resetButton = document.querySelector(
        '[data-dish-reset]'
    );

    /* Récupère la zone affichant le nombre de plats visibles. */
    const resultCount = document.querySelector(
        '[data-dish-filter-count]'
    );

    /* Récupère le message affiché lorsqu'aucun plat ne correspond aux filtres. */
    const noResultMessage = document.querySelector(
        '[data-dish-no-result]'
    );


    /* -------------------------------------------------- */
    /* vérification des éléments */
    /* -------------------------------------------------- */

    /* Arrête le script si les éléments nécessaires ne sont pas présents. */
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


    /* -------------------------------------------------- */
    /* fonctions utilitaires */
    /* -------------------------------------------------- */

    /* Normalise un texte pour permettre une recherche sans tenir compte des majuscules ni des accents. */
    const normalizeText = (value) => {
        return value
            .toLocaleLowerCase('fr-FR')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();
    };


    /* Met à jour le compteur selon le nombre de plats visibles. */
    const updateCount = (visibleCount) => {
        if (visibleCount === 0) {
            resultCount.textContent =
                'Aucun plat affiché';

            return;
        }

        if (visibleCount === 1) {
            resultCount.textContent =
                '1 plat affiché';

            return;
        }

        resultCount.textContent =
            `${visibleCount} plats affichés`;
    };


    /* -------------------------------------------------- */
    /* filtrage des plats */
    /* -------------------------------------------------- */

    /* Applique simultanément la recherche et les différents filtres. */
    const applyFilters = () => {
        const searchedName = normalizeText(
            searchInput.value
        );

        const selectedType =
            typeFilter.value;

        const selectedStatus =
            statusFilter.value;

        const selectedUsage =
            usageFilter.value;

        let visibleCount = 0;


        /* Vérifie pour chaque plat s'il correspond aux filtres sélectionnés. */
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


            /* Vérifie la correspondance avec le nom recherché. */
            const matchesName =
                searchedName === ''
                || dishName.includes(searchedName);

            /* Vérifie la correspondance avec le type sélectionné. */
            const matchesType =
                selectedType === ''
                || dishType === selectedType;

            /* Vérifie la correspondance avec le statut sélectionné. */
            const matchesStatus =
                selectedStatus === ''
                || dishStatus === selectedStatus;

            /* Vérifie la correspondance avec l'utilisation sélectionnée. */
            const matchesUsage =
                selectedUsage === ''
                || dishUsage === selectedUsage;


            /* Un plat reste visible uniquement s'il respecte tous les filtres. */
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


        /* Affiche le message uniquement lorsqu'aucun résultat n'est visible. */
        noResultMessage.classList.toggle(
            'd-none',
            visibleCount !== 0
        );

        updateCount(visibleCount);
    };


    /* -------------------------------------------------- */
    /* réinitialisation des filtres */
    /* -------------------------------------------------- */

    /* Réinitialise tous les filtres puis affiche de nouveau tous les plats. */
    const resetFilters = () => {
        searchInput.value = '';
        typeFilter.value = '';
        statusFilter.value = '';
        usageFilter.value = '';

        applyFilters();

        searchInput.focus();
    };


    /* -------------------------------------------------- */
    /* événements utilisateur */
    /* -------------------------------------------------- */

    /* Applique les filtres pendant la saisie du nom du plat. */
    searchInput.addEventListener(
        'input',
        applyFilters
    );

    /* Applique les filtres lorsque le type change. */
    typeFilter.addEventListener(
        'change',
        applyFilters
    );

    /* Applique les filtres lorsque le statut change. */
    statusFilter.addEventListener(
        'change',
        applyFilters
    );

    /* Applique les filtres lorsque l'utilisation du plat change. */
    usageFilter.addEventListener(
        'change',
        applyFilters
    );

    /* Réinitialise tous les filtres au clic sur le bouton. */
    resetButton.addEventListener(
        'click',
        resetFilters
    );


    /* -------------------------------------------------- */
    /* initialisation */
    /* -------------------------------------------------- */

    /* Applique une première fois les filtres au chargement de la page. */
    applyFilters();

});