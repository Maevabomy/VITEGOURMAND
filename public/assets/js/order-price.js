"use strict";


/* -------------------------------------------------- */
/* récupération des éléments */
/* -------------------------------------------------- */

/* Récupère le champ contenant le nombre de personnes. */
const peopleCountInput = document.querySelector("#people_count");

/* Récupère la zone affichant le prix par personne. */
const pricePerPersonElement = document.querySelector("#price-per-person");

/* Récupère le nombre de personnes affiché dans le récapitulatif. */
const summaryPeopleCount = document.querySelector("#summary-people-count");

/* Récupère la ligne indiquant l'application d'une remise. */
const discountLine = document.querySelector("#discount-line");

/* Récupère le prix total du menu. */
const menuTotalPrice = document.querySelector("#menu-total-price");

/* Récupère le prix total de la commande. */
const orderTotalPrice = document.querySelector("#order-total-price");


/* -------------------------------------------------- */
/* initialisation */
/* -------------------------------------------------- */

/* Exécute le calcul uniquement si tous les éléments nécessaires sont présents. */
if (
    peopleCountInput &&
    pricePerPersonElement &&
    summaryPeopleCount &&
    discountLine &&
    menuTotalPrice &&
    orderTotalPrice
) {

    /* Récupère le nombre minimum de personnes défini pour le menu. */
    const minimumPeople = Number(
        peopleCountInput.dataset.minimumPeople
    );

    /* Récupère le prix de base du menu. */
    const basePrice = Number(
        peopleCountInput.dataset.basePrice
    );

    /* Calcule le prix par personne à partir du prix de base. */
    const pricePerPerson =
        basePrice / minimumPeople;

    /* Initialise le prix courant du menu. */
    let currentMenuPrice =
        basePrice;

    /* Récupère les frais de livraison déjà enregistrés si la commande est modifiée. */
    const savedDeliveryFee = Number(
        peopleCountInput.dataset.currentDeliveryFee
    );

    /* Initialise les frais de livraison courants. */
    let currentDeliveryFee =
        Number.isFinite(savedDeliveryFee)
            ? savedDeliveryFee
            : 0;


    /* -------------------------------------------------- */
    /* validation du stock */
    /* -------------------------------------------------- */

    /* Récupère le stock maximum disponible depuis l'attribut max du champ. */
    const availableStock = Number.parseInt(
        peopleCountInput.max,
        10
    );

    /* Affiche un message personnalisé si la quantité demandée dépasse le stock. */
    peopleCountInput.addEventListener(
        "invalid",
        () => {
            const requestedQuantity = Number.parseInt(
                peopleCountInput.value,
                10
            );

            if (
                Number.isInteger(availableStock) &&
                requestedQuantity > availableStock
            ) {
                peopleCountInput.setCustomValidity(
                    `Il reste seulement ${availableStock} portions disponibles pour ce menu.`
                );

                return;
            }

            peopleCountInput.setCustomValidity("");
        }
    );

    /* Supprime l'ancien message personnalisé dès que l'utilisateur modifie la quantité. */
    peopleCountInput.addEventListener(
        "input",
        () => {
            peopleCountInput.setCustomValidity("");
        }
    );


    /* -------------------------------------------------- */
    /* fonctions utilitaires */
    /* -------------------------------------------------- */

    /* Formate un nombre en prix français. */
    const formatPrice = (price) => {
        return (
            new Intl.NumberFormat("fr-FR", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }).format(price) + " €"
        );
    };


    /* -------------------------------------------------- */
    /* calcul du total de la commande */
    /* -------------------------------------------------- */

    /* Additionne le prix du menu et les frais de livraison. */
    const updateOrderTotal = () => {
        const orderTotal =
            currentMenuPrice + currentDeliveryFee;

        orderTotalPrice.textContent =
            formatPrice(orderTotal);
    };


    /* -------------------------------------------------- */
    /* calcul du prix du menu */
    /* -------------------------------------------------- */

    /* Recalcule le prix du menu selon le nombre de personnes. */
    const updatePrice = () => {
        let peopleCount = Number(
            peopleCountInput.value
        );

        /* Utilise le minimum autorisé si la valeur saisie n'est pas valide. */
        if (
            !Number.isInteger(peopleCount) ||
            peopleCount < minimumPeople
        ) {
            peopleCount = minimumPeople;
        }

        /* Calcule le prix avant remise. */
        let calculatedPrice =
            pricePerPerson * peopleCount;

        /* Applique une remise de 10 % à partir de cinq personnes supplémentaires. */
        const discountApplies =
            peopleCount >= minimumPeople + 5;

        if (discountApplies) {
            calculatedPrice *= 0.9;
        }

        /* Met à jour les informations visibles du récapitulatif. */
        pricePerPersonElement.textContent =
            formatPrice(pricePerPerson);

        summaryPeopleCount.textContent =
            String(peopleCount);

        discountLine.hidden =
            !discountApplies;

        currentMenuPrice =
            calculatedPrice;

        menuTotalPrice.textContent =
            formatPrice(currentMenuPrice);

        updateOrderTotal();
    };


    /* -------------------------------------------------- */
    /* mise à jour des frais de livraison */
    /* -------------------------------------------------- */

    /* Récupère les nouveaux frais envoyés par le script de livraison. */
    document.addEventListener(
        "delivery-fee-updated",
        (event) => {
            currentDeliveryFee =
                Number(event.detail.deliveryFee) || 0;

            updateOrderTotal();
        }
    );


    /* -------------------------------------------------- */
    /* événements utilisateur */
    /* -------------------------------------------------- */

    /* Recalcule immédiatement le prix pendant la saisie. */
    peopleCountInput.addEventListener(
        "input",
        updatePrice
    );

    /* Corrige une valeur inférieure au minimum en quittant le champ. */
    peopleCountInput.addEventListener(
        "change",
        () => {
            const peopleCount = Number(
                peopleCountInput.value
            );

            if (
                !Number.isInteger(peopleCount) ||
                peopleCount < minimumPeople
            ) {
                peopleCountInput.value =
                    String(minimumPeople);
            }

            updatePrice();
        }
    );


    /* -------------------------------------------------- */
    /* premier calcul */
    /* -------------------------------------------------- */

    /* Initialise les prix affichés lors du chargement de la page. */
    updatePrice();
}