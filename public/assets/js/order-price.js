"use strict";

const peopleCountInput = document.querySelector("#people_count");
const pricePerPersonElement = document.querySelector("#price-per-person");
const summaryPeopleCount = document.querySelector("#summary-people-count");
const discountLine = document.querySelector("#discount-line");
const menuTotalPrice = document.querySelector("#menu-total-price");

// Mise à jour du prix total en fonction du nombre de personnes
if (
  peopleCountInput &&
  pricePerPersonElement &&
  summaryPeopleCount &&
  discountLine &&
  menuTotalPrice
) {
  const minimumPeople = Number(peopleCountInput.dataset.minimumPeople);

  const basePrice = Number(peopleCountInput.dataset.basePrice);

  const pricePerPerson = basePrice / minimumPeople;

  const formatPrice = (price) => {
    return (
      new Intl.NumberFormat("fr-FR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      }).format(price) + " €"
    );
  };

  const updatePrice = () => {
    let peopleCount = Number(peopleCountInput.value);

    if (!Number.isInteger(peopleCount) || peopleCount < minimumPeople) {
      peopleCount = minimumPeople;
    }

    let calculatedPrice = pricePerPerson * peopleCount;

    // Vérifie si le nombre de personnes dépasse le minimum requis pour la réduction
    const discountApplies = peopleCount >= minimumPeople + 5;
    // Vérifie si la réduction s'applique
    if (discountApplies) {
      calculatedPrice *= 0.9;
    }

    pricePerPersonElement.textContent = formatPrice(pricePerPerson);

    summaryPeopleCount.textContent = String(peopleCount);

    discountLine.hidden = !discountApplies;

    menuTotalPrice.textContent = formatPrice(calculatedPrice);
  };

  peopleCountInput.addEventListener("input", updatePrice);

/* Corrige une valeur inférieure au minimum en quittant le champ. */
peopleCountInput.addEventListener("change", () => {
  const peopleCount = Number(peopleCountInput.value);

  if (!Number.isInteger(peopleCount) || peopleCount < minimumPeople) {
    peopleCountInput.value = String(minimumPeople);
  }

  updatePrice();
});

updatePrice();
}
