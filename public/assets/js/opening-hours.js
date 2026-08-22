document.addEventListener('DOMContentLoaded', () => {
    const rows = document.querySelectorAll(
        '[data-opening-hour-row]'
    );

    rows.forEach((row) => {
        const closedCheckbox = row.querySelector(
            '[data-closed-checkbox]'
        );

        const openingTime = row.querySelector(
            '[data-opening-time]'
        );

        const closingTime = row.querySelector(
            '[data-closing-time]'
        );

        if (
            !closedCheckbox
            || !openingTime
            || !closingTime
        ) {
            return;
        }

        const updateFields = () => {
            const isClosed = closedCheckbox.checked;

            openingTime.disabled = isClosed;
            closingTime.disabled = isClosed;

            openingTime.required = !isClosed;
            closingTime.required = !isClosed;
        };

        closedCheckbox.addEventListener(
            'change',
            updateFields
        );

        updateFields();
    });
});