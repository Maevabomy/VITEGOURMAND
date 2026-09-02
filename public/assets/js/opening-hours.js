document.addEventListener('DOMContentLoaded', () => {


    /* -------------------------------------------------- */
    /* récupération des lignes d'horaires */
    /* -------------------------------------------------- */

    /* Récupère toutes les lignes correspondant aux horaires d'ouverture. */
    const rows = document.querySelectorAll(
        '[data-opening-hour-row]'
    );


    /* -------------------------------------------------- */
    /* gestion de chaque jour */
    /* -------------------------------------------------- */

    /* Parcourt chaque ligne d'horaires pour gérer son état ouvert ou fermé. */
    rows.forEach((row) => {

        /* Récupère la case indiquant si l'établissement est fermé. */
        const closedCheckbox = row.querySelector(
            '[data-closed-checkbox]'
        );

        /* Récupère le champ de l'heure d'ouverture. */
        const openingTime = row.querySelector(
            '[data-opening-time]'
        );

        /* Récupère le champ de l'heure de fermeture. */
        const closingTime = row.querySelector(
            '[data-closing-time]'
        );


        /* Ignore la ligne si un élément nécessaire est absent. */
        if (
            !closedCheckbox
            || !openingTime
            || !closingTime
        ) {
            return;
        }


        /* -------------------------------------------------- */
        /* mise à jour des champs */
        /* -------------------------------------------------- */

        /* Active ou désactive les horaires selon l'état de la case "fermé". */
        const updateFields = () => {
            const isClosed =
                closedCheckbox.checked;

            openingTime.disabled =
                isClosed;

            closingTime.disabled =
                isClosed;

            openingTime.required =
                !isClosed;

            closingTime.required =
                !isClosed;
        };


        /* -------------------------------------------------- */
        /* événement utilisateur */
        /* -------------------------------------------------- */

        /* Met à jour les champs lorsque l'état "fermé" change. */
        closedCheckbox.addEventListener(
            'change',
            updateFields
        );


        /* -------------------------------------------------- */
        /* initialisation */
        /* -------------------------------------------------- */

        /* Applique immédiatement le bon état lors du chargement de la page. */
        updateFields();

    });

});