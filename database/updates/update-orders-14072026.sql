-- --------------------------------------------------
-- mise à jour des contraintes des menus
-- --------------------------------------------------

-- Renomme le thème Pâques en Printemps.
UPDATE themes
SET name = 'Printemps'
WHERE name = 'Pâques';


-- --------------------------------------------------
-- menus disponibles toute l'année
-- --------------------------------------------------

-- Met à jour les contraintes du menu Terroir.
UPDATE menus
SET
    minimum_order_days = 5,
    available_from = NULL,
    available_until = NULL
WHERE title = 'Terroir';

-- Met à jour les contraintes du menu Primeur.
UPDATE menus
SET
    minimum_order_days = 4,
    available_from = NULL,
    available_until = NULL
WHERE title = 'Primeur';

-- Met à jour les contraintes du menu Millésime.
UPDATE menus
SET
    minimum_order_days = 7,
    available_from = NULL,
    available_until = NULL
WHERE title = 'Millésime';

-- Met à jour les contraintes du menu Éclosion.
UPDATE menus
SET
    minimum_order_days = 4,
    available_from = NULL,
    available_until = NULL
WHERE title = 'Éclosion';


-- --------------------------------------------------
-- menu disponible pendant les fêtes
-- --------------------------------------------------

-- Définit la période de disponibilité du menu Minuit.
UPDATE menus
SET
    minimum_order_days = 10,
    available_from = '2026-12-01',
    available_until = '2027-01-05'
WHERE title = 'Minuit';


-- --------------------------------------------------
-- menu disponible au printemps et en été
-- --------------------------------------------------

-- Met à jour la présentation et la disponibilité du menu Floraison.
UPDATE menus
SET
    description = 'Une composition printanière fraîche et légère, imaginée pour un déjeuner du printemps et de l’été.',
    conditions = 'Commande à effectuer au minimum 7 jours avant la prestation. Disponible uniquement du 1er mai au 30 septembre.',
    minimum_order_days = 7,
    available_from = '2026-05-01',
    available_until = '2026-09-30'
WHERE title = 'Floraison';