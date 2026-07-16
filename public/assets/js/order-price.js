"use strict";

const peopleCountInput = document.querySelector("#people_count");
const pricePerPersonElement = document.querySelector("#price-per-person");
const summaryPeopleCount = document.querySelector("#summary-people-count");
const discountLine = document.querySelector("#discount-line");
const menuTotalPrice = document.querySelector("#menu-total-price");
const orderTotalPrice = document.querySelector("#order-total-price");

// Mise à jour du prix total en fonction du nombre de personnes
if (
  peopleCountInput &&
  pricePerPersonElement &&
  summaryPeopleCount &&
  discountLine &&
  menuTotalPrice &&
  orderTotalPrice
) {
  const minimumPeople = Number(peopleCountInput.dataset.minimumPeople);

  /* -------------------------------------------------- */
  /* validation du stock */
  /* -------------------------------------------------- */

  const availableStock = Number.parseInt(peopleCountInput.max, 10);

  peopleCountInput.addEventListener("invalid", () => {
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
  });

  peopleCountInput.addEventListener("input", () => {
    peopleCountInput.setCustomValidity("");
  });

  const basePrice = Number(peopleCountInput.dataset.basePrice);

  const pricePerPerson = basePrice / minimumPeople;

  let currentMenuPrice = basePrice;
  let currentDeliveryFee = 0;

  const formatPrice = (price) => {
    return (
      new Intl.NumberFormat("fr-FR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      }).format(price) + " €"
    );
  };

  const updateOrderTotal = () => {
    const orderTotal = currentMenuPrice + currentDeliveryFee;

    orderTotalPrice.textContent = formatPrice(orderTotal);
  };

  const updatePrice = () => {
    let peopleCount = Number(peopleCountInput.value);

    if (!Number.isInteger(peopleCount) || peopleCount < minimumPeople) {
      peopleCount = minimumPeople;
    }

    let calculatedPrice = pricePerPerson * peopleCount;

    const discountApplies = peopleCount >= minimumPeople + 5;

    if (discountApplies) {
      calculatedPrice *= 0.9;
    }

    pricePerPersonElement.textContent = formatPrice(pricePerPerson);
    summaryPeopleCount.textContent = String(peopleCount);
    discountLine.hidden = !discountApplies;

    currentMenuPrice = calculatedPrice;

    menuTotalPrice.textContent = formatPrice(currentMenuPrice);

    updateOrderTotal();
  };

  document.addEventListener("delivery-fee-updated", (event) => {
    currentDeliveryFee = Number(event.detail.deliveryFee) || 0;

    updateOrderTotal();
  });

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