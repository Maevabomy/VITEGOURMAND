-- --------------------------------------------------
-- sélection de la base
-- --------------------------------------------------

-- Utilise la base du projet.
USE vite_gourmand;


-- --------------------------------------------------
-- rôles utilisateurs
-- --------------------------------------------------

-- Ajoute les rôles disponibles dans l'application.
INSERT INTO roles (name) VALUES
('user'),
('employee'),
('admin');


-- --------------------------------------------------
-- horaires d'ouverture
-- --------------------------------------------------

-- Ajoute les horaires visibles dans le pied de page.
INSERT INTO opening_hours (
    day_number,
    day_name,
    opening_time,
    closing_time,
    is_closed
) VALUES
(1, 'Lundi', '09:00:00', '18:00:00', FALSE),
(2, 'Mardi', '09:00:00', '18:00:00', FALSE),
(3, 'Mercredi', '09:00:00', '18:00:00', FALSE),
(4, 'Jeudi', '09:00:00', '18:00:00', FALSE),
(5, 'Vendredi', '09:00:00', '18:00:00', FALSE),
(6, 'Samedi', '09:00:00', '18:00:00', FALSE),
(7, 'Dimanche', '09:00:00', '18:00:00', FALSE);


-- --------------------------------------------------
-- thèmes des menus
-- --------------------------------------------------

-- Ajoute les thèmes disponibles pour classer les menus.
INSERT INTO themes (name) VALUES
('Classique'),
('Noël'),
('Printemps'),
('Événement');


-- --------------------------------------------------
-- régimes alimentaires
-- --------------------------------------------------

-- Ajoute les régimes proposés par le traiteur.
INSERT INTO dietary_types (name) VALUES
('Classique'),
('Végétarien'),
('Vegan');


-- --------------------------------------------------
-- statuts des commandes
-- --------------------------------------------------

-- Ajoute les états possibles d'une commande.
INSERT INTO order_statuses (name) VALUES
('En attente'),
('Acceptée'),
('En préparation'),
('En cours de livraison'),
('Livrée'),
('En attente du retour de matériel'),
('Terminée'),
('Annulée');


-- --------------------------------------------------
-- compte administrateur
-- --------------------------------------------------

-- Ajoute le compte administrateur utilisé pour les démonstrations.
INSERT INTO users (
    role_id,
    first_name,
    last_name,
    phone,
    email,
    address,
    postal_code,
    city,
    password_hash,
    is_active
) VALUES (
    3,
    'José',
    'Administrateur',
    '0600000000',
    'admin@vitegourmand.fr',
    '10 rue de Bordeaux',
    '33000',
    'Bordeaux',
    '$2y$10$KaERznd8UtEGaqgwlFFr9eSEoYBe8oMZuQsEP/BE4EVc/M6dwFadO',
    TRUE
);


-- --------------------------------------------------
-- compte employé
-- --------------------------------------------------

-- Ajoute le compte employé de Julie pour les démonstrations.
INSERT INTO users (
    role_id,
    first_name,
    last_name,
    phone,
    email,
    address,
    postal_code,
    city,
    password_hash,
    is_active
) VALUES (
    2,
    'Julie',
    'Employée',
    '0610000000',
    'julie@vitegourmand.fr',
    '10 rue de Bordeaux',
    '33000',
    'Bordeaux',
    '$2y$10$WpVPeDqoFU03afaX26QS8upGqHkL.M90RHXKpMHfI8ivzjOCFSz46',
    TRUE
);


-- --------------------------------------------------
-- menus de démonstration
-- --------------------------------------------------

-- Ajoute les menus proposés par le traiteur.
INSERT INTO menus (
    theme_id,
    dietary_type_id,
    title,
    description,
    conditions,
    minimum_order_days,
    available_from,
    available_until,
    minimum_people,
    base_price,
    stock_quantity,
    is_active
) VALUES
(
    1,
    1,
    'Terroir',
    'Une escale raffinée au cœur du Sud-Ouest, entre produits généreux et saveurs délicates.',
    'Commande à effectuer au minimum 5 jours avant la prestation.',
    5,
    NULL,
    NULL,
    6,
    210.00,
    120,
    TRUE
),
(
    1,
    2,
    'Primeur',
    'Une composition végétarienne fraîche et élégante, inspirée des produits de saison.',
    'Commande à effectuer au minimum 4 jours avant la prestation.',
    4,
    NULL,
    NULL,
    4,
    140.00,
    110,
    TRUE
),
(
    4,
    1,
    'Millésime',
    'Une proposition haut de gamme pensée pour les réceptions et les événements importants.',
    'Commande à effectuer au minimum 7 jours avant la prestation. Conservation au frais obligatoire avant le service.',
    7,
    NULL,
    NULL,
    10,
    420.00,
    90,
    TRUE
),
(
    4,
    3,
    'Éclosion',
    'Une expérience végétale moderne et colorée qui conjugue gourmandise et légèreté.',
    'Commande à effectuer au minimum 4 jours avant la prestation.',
    4,
    NULL,
    NULL,
    6,
    180.00,
    120,
    TRUE
),
(
    2,
    1,
    'Minuit',
    'Une parenthèse festive et chaleureuse pour célébrer les fêtes autour d’une table généreuse.',
    'Commande à effectuer au minimum 10 jours avant la prestation. Disponible uniquement pendant la période des fêtes.',
    10,
    '2026-12-01',
    '2027-01-05',
    8,
    320.00,
    120,
    TRUE
),
(
    3,
    2,
    'Floraison',
    'Une composition printanière fraîche et légère, imaginée pour un déjeuner du printemps et de l’été.',
    'Commande à effectuer au minimum 7 jours avant la prestation. Disponible uniquement du 1er mai au 30 septembre.',
    7,
    '2026-05-01',
    '2026-09-30',
    6,
    240.00,
    110,
    TRUE
),
(
    1,
    1,
    'Gourmand',
    'Un menu généreux et raffiné, pensé pour les amateurs de saveurs réconfortantes et de belles associations. Une composition équilibrée entre gourmandise, élégance et produits de saison, idéale pour un repas convivial ou une occasion spéciale.',
    'Commande à effectuer au minimum 24 h avant la prestation.',
    1,
    NULL,
    NULL,
    5,
    140.00,
    95,
    TRUE
);


-- --------------------------------------------------
-- plats de démonstration
-- --------------------------------------------------

-- Ajoute les entrées, plats et desserts disponibles.
-- Un même plat peut ensuite être associé à plusieurs menus.
INSERT INTO dishes (
    name,
    description,
    dish_type,
    is_active
) VALUES
(
    'Tartare de saumon aux agrumes',
    'Tartare de saumon frais taillé au couteau, relevé par des zestes d’agrumes, un filet de citron et des herbes fraîches. Une entrée légère aux notes acidulées.',
    'starter',
    TRUE
),
(
    'Magret de canard',
    'Magret de canard d’environ 180 g par personne, rôti et servi rosé. Il est accompagné de pommes grenailles confites aux herbes et d’une sauce aux échalotes doucement caramélisées.',
    'main_course',
    TRUE
),
(
    'Canelé bordelais revisité',
    'Canelé bordelais à la croûte légèrement caramélisée et au cœur moelleux, servi avec une crème légère parfumée à la vanille.',
    'dessert',
    TRUE
),
(
    'Burrata et légumes rôtis',
    'Burrata crémeuse accompagnée de légumes de saison rôtis au four, d’un pesto de roquette aux pignons de pin et d’une touche d’huile d’olive.',
    'starter',
    TRUE
),
(
    'Risotto aux champignons',
    'Risotto crémeux aux champignons poêlés, délicatement relevé par du parmesan affiné et des noisettes torréfiées pour apporter une note croquante.',
    'main_course',
    TRUE
),
(
    'Tarte fine aux pommes',
    'Tarte fine aux pommes fondantes et légèrement caramélisées, accompagnée d’un caramel doux et d’éclats d’amandes.',
    'dessert',
    TRUE
),
(
    'Foie gras mi-cuit',
    'Foie gras mi-cuit d’environ 70 g par personne, délicatement assaisonné et accompagné d’un chutney de figues ainsi que de tranches de pain brioché légèrement toastées.',
    'starter',
    TRUE
),
(
    'Filet de bœuf aux morilles',
    'Filet de bœuf d’environ 180 g par personne, rôti et servi avec une sauce crémeuse aux morilles. Il est accompagné d’un gratin dauphinois fondant, délicatement relevé à la muscade.',
    'main_course',
    TRUE
),
(
    'Entremets chocolat noir',
    'Entremets au chocolat noir composé d’une mousse onctueuse, d’un cœur fondant et d’une note de noisette. Une finition délicate pour les amateurs de chocolat intense.',
    'dessert',
    TRUE
),
(
    'Houmous de betterave',
    'Houmous de betterave à la texture onctueuse, accompagné de légumes frais croquants et de crackers aux graines de sésame.',
    'starter',
    TRUE
),
(
    'Parmentier de patate douce',
    'Parmentier végétal composé d’une purée fondante de patate douce et de lentilles mijotées avec des légumes de saison et des herbes aromatiques.',
    'main_course',
    TRUE
),
(
    'Mousse au chocolat noir',
    'Mousse aérienne au chocolat noir accompagnée d’un praliné végétal à la noisette pour apporter une note délicatement croquante.',
    'dessert',
    TRUE
),
(
    'Velouté de potimarron',
    'Velouté de potimarron à la texture douce et crémeuse, accompagné d’éclats de châtaignes et d’une crème légèrement parfumée à la muscade.',
    'starter',
    TRUE
),
(
    'Bûche chocolat et praliné',
    'Bûche de fête composée d’une mousse au chocolat, d’un insert praliné à la noisette et d’une base croustillante.',
    'dessert',
    TRUE
),
(
    'Asperges rôties',
    'Asperges rôties accompagnées d’un œuf parfait au cœur coulant et d’une crème légère aux herbes fraîches.',
    'starter',
    TRUE
),
(
    'Ravioles printanières',
    'Ravioles garnies de légumes printaniers, accompagnées d’une sauce crémeuse au parmesan et de quelques herbes fraîches.',
    'main_course',
        TRUE
),
(
    'Entremets vanille, sésame noir et framboise',
    'Entremets individuel composé d’une mousse légère à la vanille, d’un cœur fruité à la framboise et d’un croustillant au sésame noir. Il est surmonté d’un glaçage à la framboise, de fruits frais et d’une fine tuile au sésame pour apporter du contraste et du croquant.',
    'dessert',
    TRUE
);


-- --------------------------------------------------
-- allergènes
-- --------------------------------------------------

-- Ajoute les allergènes utilisés dans les plats.
INSERT INTO allergens (name) VALUES
('Gluten'),
('Lait'),
('Œufs'),
('Fruits à coque'),
('Poisson'),
('Sésame');


-- --------------------------------------------------
-- association des plats et allergènes
-- --------------------------------------------------

-- Associe chaque plat à ses allergènes connus.
INSERT INTO dish_allergen (
    dish_id,
    allergen_id
) VALUES

-- Tartare de saumon aux agrumes.
(1, 5),

-- Canelé bordelais revisité.
(3, 1),
(3, 2),
(3, 3),

-- Burrata et légumes rôtis.
(4, 2),

-- Risotto aux champignons.
(5, 2),
(5, 4),

-- Tarte fine aux pommes.
(6, 1),
(6, 2),
(6, 4),

-- Foie gras mi-cuit et pain brioché.
(7, 1),
(7, 2),
(7, 3),

-- Filet de bœuf aux morilles et gratin dauphinois.
(8, 2),

-- Entremets chocolat noir.
(9, 1),
(9, 2),
(9, 3),
(9, 4),

-- Houmous de betterave et crackers.
(10, 1),
(10, 6),

-- Mousse au chocolat noir et praliné végétal.
(12, 4),

-- Velouté de potimarron et crème.
(13, 2),

-- Bûche chocolat et praliné.
(14, 1),
(14, 2),
(14, 3),
(14, 4),

-- Asperges rôties et œuf parfait.
(15, 2),
(15, 3),

-- Ravioles printanières.
(16, 1),
(16, 2),
(16, 3),

-- Entremets vanille, sésame noir et framboise.
(17, 1),
(17, 2),
(17, 3),
(17, 6);


-- --------------------------------------------------
-- association des menus et plats
-- --------------------------------------------------

-- Associe une entrée, un plat et un dessert à chaque menu.
-- Certains plats sont volontairement présents dans plusieurs menus.
INSERT INTO menu_dish (
    menu_id,
    dish_id
) VALUES

-- Menu Terroir.
(1, 1),
(1, 2),
(1, 3),

-- Menu Primeur.
(2, 4),
(2, 5),
(2, 6),

-- Menu Millésime.
(3, 7),
(3, 8),
(3, 9),

-- Menu Éclosion.
(4, 10),
(4, 11),
(4, 12),

-- Menu Minuit.
(5, 13),
(5, 8),
(5, 14),

-- Menu Floraison.
(6, 15),
(6, 16),
(6, 9),

-- Menu Gourmand.
(7, 4),
(7, 2),
(7, 17);

-- --------------------------------------------------
-- clients de démonstration
-- --------------------------------------------------

-- Ajoute les comptes clients utilisés pour les démonstrations.
INSERT INTO users (
    role_id,
    first_name,
    last_name,
    phone,
    email,
    address,
    postal_code,
    city,
    password_hash,
    is_active
) VALUES
(
    1,
    'Célia',
    'Dezalles',
    '0611111111',
    'celia.dezalles@example.com',
    '12 rue Sainte-Catherine',
    '33000',
    'Bordeaux',
    '$2y$12$z5nwj7pKHm3CL0lYr2.QB.qZfjpWA1bk15uwWPD2pmA97dcyUrUJi',
    TRUE
),
(
    1,
    'Rémy',
    'Piau',
    '0650545857',
    'remypiau@test.fr',
    '8 rue du Béarnais',
    '31830',
    'Plaisance-du-Touch',
    '$2y$12$z5nwj7pKHm3CL0lYr2.QB.qZfjpWA1bk15uwWPD2pmA97dcyUrUJi',
    TRUE
);

-- --------------------------------------------------
-- commandes de démonstration
-- --------------------------------------------------

-- Ajoute quelques commandes permettant de tester
-- les différents espaces de l'application.
INSERT INTO orders (
    order_number,
    user_id,
    menu_id,
    current_status_id,
    customer_first_name,
    customer_last_name,
    customer_email,
    customer_phone,
    delivery_address,
    delivery_postal_code,
    delivery_city,
    event_date,
    delivery_time,
    people_count,
    menu_price,
    delivery_price,
    total_price,
    equipment_loaned
) VALUES
(
    'CMD-2026-001',
    3,
    1,
    7,
    'Célia',
    'Dezalles',
    'celia.dezalles@example.com',
    '0611111111',
    '12 rue Sainte-Catherine',
    '33000',
    'Bordeaux',
    '2026-09-05',
    '12:30:00',
    8,
    280.00,
    0.00,
    280.00,
    FALSE
),
(
    'CMD-2026-002',
    4,
    1,
    7,
    'Rémy',
    'Piau',
    'remypiau@test.fr',
    '0650545857',
    '8 rue du Béarnais',
    '31830',
    'Plaisance-du-Touch',
    '2026-09-10',
    '12:00:00',
    6,
    210.00,
    0.00,
    210.00,
    FALSE
),
(
    'CMD-2026-003',
    4,
    3,
    3,
    'Rémy',
    'Piau',
    'remypiau@test.fr',
    '0650545857',
    '8 rue du Béarnais',
    '31830',
    'Plaisance-du-Touch',
    '2026-10-10',
    '11:30:00',
    10,
    420.00,
    0.00,
    420.00,
    FALSE
);


-- --------------------------------------------------
-- historique des commandes
-- --------------------------------------------------

-- Ajoute un historique cohérent avec le statut actuel
-- de chaque commande de démonstration.
INSERT INTO order_status_history (
    order_id,
    status_id,
    changed_by_user_id,
    note
) VALUES
(1, 7, 2, 'Commande terminée avec succès.'),
(2, 7, 2, 'Commande terminée avec succès.'),
(3, 1, NULL, 'Commande créée par le client.'),
(3, 2, 2, 'Commande acceptée.'),
(3, 3, 2, 'Commande en préparation.');


-- --------------------------------------------------
-- avis clients validés
-- --------------------------------------------------

-- Ajoute des avis validés visibles sur la page d'accueil.
INSERT INTO reviews (
    order_id,
    user_id,
    rating,
    comment,
    moderation_status
) VALUES
(
    1,
    3,
    5,
    'Une prestation très soignée et des plats appréciés par tous nos invités.',
    'approved'
),
(
    2,
    4,
    5,
    'Une équipe disponible, ponctuelle et très professionnelle. Je recommande vivement.',
    'approved'
);