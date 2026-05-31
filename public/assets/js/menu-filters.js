/* -------------------------------------------------- */
/* filtres du catalogue */
/* -------------------------------------------------- */

/* Récupère les champs utilisés pour filtrer les menus. */
const minimumPriceInput = document.querySelector("#minimum-price");
const maximumPriceInput = document.querySelector("#maximum-price");
const themeSelect = document.querySelector("#theme-filter");
const dietaryTypeSelect = document.querySelector("#dietary-type-filter");
const peopleInput = document.querySelector("#people-filter");

/* Récupère les éléments utiles pour actualiser l'affichage. */
const menuItems = document.querySelectorAll(".menu-item");
const menusCount = document.querySelector("#menus-count");
const noMenuMessage = document.querySelector("#no-menu-message");
const resetFiltersButton = document.querySelector("#reset-filters");


/* -------------------------------------------------- */
/* actualisation des résultats */
/* -------------------------------------------------- */

/* Affiche uniquement les menus correspondant aux critères. */
function filterMenus() {
    const minimumPrice = Number(minimumPriceInput.value);
    const maximumPrice = Number(maximumPriceInput.value);
    const selectedTheme = themeSelect.value;
    const selectedDietaryType = dietaryTypeSelect.value;
    const selectedPeople = Number(peopleInput.value);

    let visibleMenusCount = 0;

    menuItems.forEach((menuItem) => {
        const menuPrice = Number(menuItem.dataset.price);
        const menuTheme = menuItem.dataset.theme;
        const menuDietaryType = menuItem.dataset.dietaryType;
        const menuMinimumPeople = Number(menuItem.dataset.minimumPeople);

        /* Vérifie chaque critère renseigné par le visiteur. */
        const matchesMinimumPrice =
            !minimumPrice || menuPrice >= minimumPrice;

        const matchesMaximumPrice =
            !maximumPrice || menuPrice <= maximumPrice;

        const matchesTheme =
            !selectedTheme || menuTheme === selectedTheme;

        const matchesDietaryType =
            !selectedDietaryType
            || menuDietaryType === selectedDietaryType;

        const matchesPeople =
            !selectedPeople || menuMinimumPeople <= selectedPeople;

        const isVisible =
            matchesMinimumPrice
            && matchesMaximumPrice
            && matchesTheme
            && matchesDietaryType
            && matchesPeople;

        /* Affiche ou masque la carte. */
        menuItem.classList.toggle("d-none", !isVisible);

        if (isVisible) {
            visibleMenusCount++;
        }
    });

    /* Met à jour le nombre de menus affichés. */
    menusCount.textContent =
        visibleMenusCount === 1
            ? "1 menu disponible"
            : `${visibleMenusCount} menus disponibles`;

    /* Affiche un message lorsqu'aucun menu ne correspond. */
    noMenuMessage.classList.toggle(
        "d-none",
        visibleMenusCount !== 0
    );
}


/* -------------------------------------------------- */
/* écoute des champs */
/* -------------------------------------------------- */

/* Actualise les menus pendant la saisie des nombres. */
minimumPriceInput.addEventListener("input", filterMenus);
maximumPriceInput.addEventListener("input", filterMenus);
peopleInput.addEventListener("input", filterMenus);

/* Actualise les menus lorsque les listes changent. */
themeSelect.addEventListener("change", filterMenus);
dietaryTypeSelect.addEventListener("change", filterMenus);


/* -------------------------------------------------- */
/* réinitialisation des filtres */
/* -------------------------------------------------- */

/* Vide les champs et affiche tous les menus. */
resetFiltersButton.addEventListener("click", () => {
    minimumPriceInput.value = "";
    maximumPriceInput.value = "";
    themeSelect.value = "";
    dietaryTypeSelect.value = "";
    peopleInput.value = "";

    filterMenus();
});