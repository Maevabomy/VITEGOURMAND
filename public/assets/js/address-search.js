"use strict";

/* -------------------------------------------------- */
/* éléments du formulaire */
/* -------------------------------------------------- */

/* Récupère les champs utilisés pour rechercher et enregistrer l'adresse de livraison.*/

const addressInput = document.querySelector("#delivery_address");
const postalCodeInput = document.querySelector("#delivery_postal_code");
const cityInput = document.querySelector("#delivery_city");

const latitudeInput = document.querySelector("#delivery_latitude");
const longitudeInput = document.querySelector("#delivery_longitude");

const searchButton = document.querySelector("#search-address-button");
const searchStatus = document.querySelector("#address-search-status");
const searchResults = document.querySelector("#address-search-results");

/* -------------------------------------------------- */
/* éléments liés au prix de livraison */
/* -------------------------------------------------- */

const deliveryPriceDetails = document.querySelector("#delivery-price-details");

const deliveryDistance = document.querySelector("#delivery-distance");

const deliveryFixedFee = document.querySelector("#delivery-fixed-fee");

const deliveryDistanceFee = document.querySelector("#delivery-distance-fee");

const deliveryFee = document.querySelector("#delivery-fee");

/* -------------------------------------------------- */
/* initialisation */
/* -------------------------------------------------- */

/* Le script s'exécute uniquement si tous les éléments nécessaires existent sur la page. */

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
  /* -------------------------------------------------- */
  /* fonctions utilitaires */
  /* -------------------------------------------------- */

  /* Supprime les résultats de recherche affichés. */
  const clearResults = () => {
    searchResults.innerHTML = "";
  };

  /* Affiche un message d'information sous la recherche. */
  const showStatus = (message) => {
    searchStatus.textContent = message;
  };

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
  /* affichage des frais de livraison */
  /* -------------------------------------------------- */

  /* Réinitialise les informations de livraison affichées. */
  const clearDeliveryDetails = () => {
    deliveryPriceDetails.hidden = true;

    deliveryDistance.textContent = "—";
    deliveryFixedFee.textContent = "—";
    deliveryDistanceFee.textContent = "—";
    deliveryFee.textContent = "—";
  };

  /* Affiche le détail du calcul de la livraison. */
  const displayDeliveryDetails = (data) => {
    deliveryDistance.textContent = `${data.distance} km`;

    deliveryFixedFee.textContent = formatPrice(data.fixed_fee);

    deliveryDistanceFee.textContent = formatPrice(data.distance_fee);

    deliveryFee.textContent = formatPrice(data.delivery_fee);

    deliveryPriceDetails.hidden = false;
  };

  /* Informe le reste du formulaire que les frais de livraison ont changé. L'événement personnalisé est utilisé notamment pour recalculer le prix total de la commande. */
  const updateOrderDeliveryFee = (deliveryFee) => {
    document.dispatchEvent(
      new CustomEvent("delivery-fee-updated", {
        detail: {
          deliveryFee: deliveryFee,
        },
      }),
    );
  };

  /* -------------------------------------------------- */
  /* réinitialisation de l'adresse sélectionnée */
  /* -------------------------------------------------- */

  /* Efface les coordonnées GPS et remet les frais de livraison à zéro. */
  const clearSelection = () => {
    latitudeInput.value = "";
    longitudeInput.value = "";

    clearDeliveryDetails();
    updateOrderDeliveryFee(0);
  };

  /* -------------------------------------------------- */
  /* calcul de la livraison */
  /* -------------------------------------------------- */

  /* Envoie les coordonnées de l'adresse au serveur afin de calculer la distance et les frais. */
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
          data.message ||
            "Les frais de livraison " + "n’ont pas pu être calculés.",
        );

        return;
      }

      displayDeliveryDetails(data);

      updateOrderDeliveryFee(data.delivery_fee);

      showStatus(
        `Adresse sélectionnée — distance : ` +
          `${data.distance} km, ` +
          `livraison : ` +
          `${data.delivery_fee.toFixed(2)} €`,
      );
    } catch (error) {
      showStatus("Les frais de livraison " + "n’ont pas pu être calculés.");
    }
  };

  /* -------------------------------------------------- */
  /* sélection d'une adresse */
  /* -------------------------------------------------- */

  /* Enregistre dans le formulaire l'adresse choisie l'utilisateur puis calcule sa livraison.*/
  const selectAddress = (address) => {
    addressInput.value = address.label;

    postalCodeInput.value = address.postal_code;

    cityInput.value = address.city;

    latitudeInput.value = String(address.latitude);

    longitudeInput.value = String(address.longitude);

    clearResults();

    calculateDelivery(address);
  };

  /* -------------------------------------------------- */
  /* affichage des résultats de recherche */
  /* -------------------------------------------------- */

  /* Crée un bouton pour chaque adresse retournée par la recherche. */
  const displayResults = (addresses) => {
    clearResults();

    if (addresses.length === 0) {
      showStatus("Aucune adresse correspondante " + "n’a été trouvée.");

      return;
    }

    showStatus("Sélectionnez l’adresse exacte " + "dans la liste.");

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

  /* -------------------------------------------------- */
  /* recherche d'adresse */
  /* -------------------------------------------------- */

  /* Envoie la saisie au serveur. Le serveur recherche les adresses correspondantes puis retourne les résultats au format JSON. */

  const searchAddress = async () => {
    const query = addressInput.value.trim();

    clearSelection();
    clearResults();

    /* Évite d'envoyer une recherche avec une saisie trop courte. */

    if (query.length < 5) {
      showStatus("Saisissez au moins 5 caractères.");

      return;
    }

    searchButton.disabled = true;

    showStatus("Recherche de l’adresse en cours…");

    try {
      const response = await fetch(
        `${window.BASE_URL}` +
          `/order/address-search?query=` +
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
            "La recherche d’adresse est " + "momentanément indisponible.",
        );

        return;
      }

      displayResults(data.addresses);
    } catch (error) {
      showStatus("La recherche d’adresse est " + "momentanément indisponible.");
    } finally {
      /* Réactive toujours le bouton,que la requête réussisse ou échoue.*/
      searchButton.disabled = false;
    }
  };

  /* -------------------------------------------------- */
  /* événements utilisateur */
  /* -------------------------------------------------- */

  /* Lance la recherche avec le bouton. */
  searchButton.addEventListener("click", searchAddress);

  /* Une modification manuelle de l'adresse annule l'adresse précédemment sélectionnée.*/
  addressInput.addEventListener("input", () => {
    clearSelection();
    clearResults();
    showStatus("");
  });

  /* Permet également de lancer la recherche avec la touche Entrée.*/
  addressInput.addEventListener("keydown", (event) => {
    if (event.key === "Enter") {
      event.preventDefault();

      searchAddress();
    }
  });
}
