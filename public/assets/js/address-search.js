"use strict";

const addressInput = document.querySelector("#delivery_address");
const postalCodeInput = document.querySelector("#delivery_postal_code");
const cityInput = document.querySelector("#delivery_city");

const latitudeInput = document.querySelector("#delivery_latitude");
const longitudeInput = document.querySelector("#delivery_longitude");

const searchButton = document.querySelector("#search-address-button");
const searchStatus = document.querySelector("#address-search-status");
const searchResults = document.querySelector("#address-search-results");

const deliveryPriceDetails = document.querySelector("#delivery-price-details");
const deliveryDistance = document.querySelector("#delivery-distance");
const deliveryFixedFee = document.querySelector("#delivery-fixed-fee");
const deliveryDistanceFee = document.querySelector("#delivery-distance-fee");
const deliveryFee = document.querySelector("#delivery-fee");

if (
  addressInput &&
  postalCodeInput &&
  cityInput &&
  latitudeInput &&
  longitudeInput &&
  searchButton &&
  searchStatus &&
  searchResults &&
  deliveryPriceDetails &&
  deliveryDistance &&
  deliveryFixedFee &&
  deliveryDistanceFee &&
  deliveryFee
) {
  const clearSelection = () => {
    latitudeInput.value = "";
    longitudeInput.value = "";

    clearDeliveryDetails();
    updateOrderDeliveryFee(0);
  };

  const clearResults = () => {
    searchResults.innerHTML = "";
  };

  const showStatus = (message) => {
    searchStatus.textContent = message;
  };

  const formatPrice = (price) => {
    return (
      new Intl.NumberFormat("fr-FR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      }).format(price) + " €"
    );
  };

  const clearDeliveryDetails = () => {
    deliveryPriceDetails.hidden = true;

    deliveryDistance.textContent = "—";
    deliveryFixedFee.textContent = "—";
    deliveryDistanceFee.textContent = "—";
    deliveryFee.textContent = "—";
  };

  const displayDeliveryDetails = (data) => {
    deliveryDistance.textContent = `${data.distance} km`;
    deliveryFixedFee.textContent = formatPrice(data.fixed_fee);
    deliveryDistanceFee.textContent = formatPrice(data.distance_fee);
    deliveryFee.textContent = formatPrice(data.delivery_fee);

    deliveryPriceDetails.hidden = false;
  };

  const updateOrderDeliveryFee = (deliveryFee) => {
    document.dispatchEvent(
      new CustomEvent("delivery-fee-updated", {
        detail: {
          deliveryFee: deliveryFee,
        },
      }),
    );
  };

  const calculateDelivery = async (address) => {
    showStatus("Calcul des frais de livraison en cours…");

    const parameters = new URLSearchParams({
      latitude: address.latitude,
      longitude: address.longitude,
      city: address.city,
    });

    try {
      const response = await fetch(
        `${window.BASE_URL}/order/delivery-distance?` + parameters.toString(),
        {
          headers: {
            Accept: "application/json",
          },
        },
      );

      const data = await response.json();

      if (!response.ok || !data.success) {
        showStatus(
          data.message || "Les frais de livraison n’ont pas pu être calculés.",
        );

        return;
      }

      displayDeliveryDetails(data);
      updateOrderDeliveryFee(data.delivery_fee);

      showStatus(
        `Adresse sélectionnée — distance : ${data.distance} km, ` +
          `livraison : ${data.delivery_fee.toFixed(2)} €`,
      );
    } catch (error) {
      showStatus("Les frais de livraison n’ont pas pu être calculés.");
    }
  };

  const selectAddress = (address) => {
    addressInput.value = address.label;
    postalCodeInput.value = address.postal_code;
    cityInput.value = address.city;

    latitudeInput.value = String(address.latitude);
    longitudeInput.value = String(address.longitude);

    clearResults();
    calculateDelivery(address);
  };

  const displayResults = (addresses) => {
    clearResults();

    if (addresses.length === 0) {
      showStatus("Aucune adresse correspondante n’a été trouvée.");

      return;
    }

    showStatus("Sélectionnez l’adresse exacte dans la liste.");

    addresses.forEach((address) => {
      const button = document.createElement("button");

      button.type = "button";
      button.className = "address-search-result";
      button.textContent = address.label;

      button.addEventListener("click", () => {
        selectAddress(address);
      });

      searchResults.appendChild(button);
    });
  };

  const searchAddress = async () => {
    const query = addressInput.value.trim();

    clearSelection();
    clearResults();

    if (query.length < 5) {
      showStatus("Saisissez au moins 5 caractères.");

      return;
    }

    searchButton.disabled = true;
    showStatus("Recherche de l’adresse en cours…");

    try {
      const response = await fetch(
        `${window.BASE_URL}/order/address-search?query=` +
          encodeURIComponent(query),
        {
          headers: {
            Accept: "application/json",
          },
        },
      );

      const data = await response.json();

      if (!response.ok || !data.success) {
        showStatus(
          data.message ||
            "La recherche d’adresse est momentanément indisponible.",
        );

        return;
      }

      displayResults(data.addresses);
    } catch (error) {
      showStatus("La recherche d’adresse est momentanément indisponible.");
    } finally {
      searchButton.disabled = false;
    }
  };

  searchButton.addEventListener("click", searchAddress);

  addressInput.addEventListener("input", () => {
    clearSelection();
    clearResults();
    showStatus("");
  });

  addressInput.addEventListener("keydown", (event) => {
    if (event.key === "Enter") {
      event.preventDefault();
      searchAddress();
    }
  });
}
