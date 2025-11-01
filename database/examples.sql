-- Exemples de données pour ResiLink
-- Exécuter ce fichier après avoir créé la base de données

USE resilink;

-- Insertion de quelques utilisateurs de test
INSERT INTO users (username, email, password, full_name, apartment_number, phone, role) VALUES
('marie_dupont', 'marie.dupont@example.com', '$2y$10$BzjAwO98PEN8nt7Zvgg0K.xY/d/pEuZzkWjwNz4BnGThABy7XK5Uq', 'Marie Dupont', 'A12', '0612345678', 'user'),
('pierre_martin', 'pierre.martin@example.com', '$2y$10$BzjAwO98PEN8nt7Zvgg0K.xY/d/pEuZzkWjwNz4BnGThABy7XK5Uq', 'Pierre Martin', 'B05', '0623456789', 'user'),
('sophie_bernard', 'sophie.bernard@example.com', '$2y$10$BzjAwO98PEN8nt7Zvgg0K.xY/d/pEuZzkWjwNz4BnGThABy7XK5Uq', 'Sophie Bernard', 'C20', '0634567890', 'user'),
('lucas_roux', 'lucas.roux@example.com', '$2y$10$BzjAwO98PEN8nt7Zvgg0K.xY/d/pEuZzkWjwNz4BnGThABy7XK5Uq', 'Lucas Roux', 'D08', '0645678901', 'user'),
('emma_leclerc', 'emma.leclerc@example.com', '$2y$10$BzjAwO98PEN8nt7Zvgg0K.xY/d/pEuZzkWjwNz4BnGThABy7XK5Uq', 'Emma Leclerc', 'E15', '0656789012', 'user');

-- Note: Tous les utilisateurs ont le mot de passe "admin123" pour la démo

-- Exemples de posts TYPE GENERAL
INSERT INTO posts (user_id, title, content, type, created_at) VALUES
(2, 'Bienvenue dans la résidence !', 'Bonjour à tous ! Je suis nouveau dans la résidence et je suis ravi de vous rejoindre. N''hésitez pas à venir me dire bonjour !', 'general', '2024-11-01 10:00:00'),
(3, 'Réunion du conseil de copropriété', 'Bonjour chers voisins, une réunion du conseil est prévue le 15 novembre à 19h dans la salle commune. À bientôt !', 'general', '2024-11-02 08:30:00');

-- Exemples de posts TYPE VENTE
INSERT INTO posts (user_id, title, content, type, price, created_at) VALUES
(2, 'Machine à laver en excellent état', 'Je vends ma machine à laver Whirlpool de 8kg, utilisée pendant 2 ans, excellent état. Fonctionne parfaitement, je déménage. Livraison possible.', 'sale', 250.00, '2024-11-03 14:00:00'),
(4, 'Canapé convertible', 'Canapé convertible 3 places, gris clair, très confortable. Quelques traces d''usure mais très fonctionnel. Vendu avec des housses lavables.', 'sale', 350.00, '2024-11-04 09:00:00'),
(5, 'Vélo électrique', 'Vélo électrique Mia electric, autonomie 50km, utilisé pendant 1 an seulement. Batterie excellente état. Vendu avec chargeur et accessoires.', 'sale', 800.00, '2024-11-05 11:00:00');

-- Exemples de posts TYPE ÉVÉNEMENT
INSERT INTO posts (user_id, title, content, type, event_date, location, created_at) VALUES
(3, 'Fête de Noël dans la résidence', 'Bonjour à tous ! Je propose d''organiser une fête de Noël pour tous les résidents le samedi 20 décembre à partir de 19h. Si vous souhaitez participer, faites-moi signe !', 'event', '2024-12-20 19:00:00', 'Salle commune', '2024-11-06 15:00:00'),
(5, 'Petit déjeuner de quartier', 'Salut ! Je propose un petit déjeuner partagé dimanche prochain à 9h dans le jardin commun si le temps le permet. Chacun amène quelque chose à partager !', 'event', '2024-11-10 09:00:00', 'Jardin commun', '2024-11-07 08:00:00'),
(2, 'Nettoyage de printemps collectif', 'Bonjour ! Je propose une matinée de nettoyage collectif des espaces communs le samedi 25 novembre. Tous les volontaires sont les bienvenus !', 'event', '2024-11-25 09:00:00', 'Espaces communs', '2024-11-08 10:00:00');

-- Exemples de posts TYPE PROBLÈME
INSERT INTO posts (user_id, title, content, type, status, created_at) VALUES
(4, 'Lampe cassée dans l''ascenseur', 'Bonjour, la lampe dans l''ascenseur est cassée depuis 3 jours. C''est assez sombre, pourriez-vous la réparer ? Merci !', 'issue', 'active', '2024-11-05 18:00:00'),
(2, 'Chaudière qui fait du bruit', 'La chaudière de l''appartement B05 fait énormément de bruit, surtout la nuit. C''est très gênant. Pourriez-vous faire venir quelqu''un ?', 'issue', 'active', '2024-11-06 20:00:00'),
(3, 'Porte d''entrée qui grince', 'La porte d''entrée principale grince beaucoup à chaque ouverture/fermeture. Serait-il possible de la graisser ?', 'issue', 'resolved', '2024-11-03 12:00:00'),
(5, 'Ascenseur en panne', 'L''ascenseur ne répond plus aux commandes, il reste bloqué au 3ème étage. C''est urgent car Mme Dubois habite au 4ème...', 'issue', 'active', '2024-11-08 08:00:00');

-- Exemples de commentaires
INSERT INTO comments (post_id, user_id, content, created_at) VALUES
(1, 3, 'Bienvenue ! Ne tarde pas à te présenter 😊', '2024-11-01 10:15:00'),
(1, 4, 'Ravi de te rencontrer !', '2024-11-01 11:00:00'),
(1, 5, 'Super de t''avoir parmi nous !', '2024-11-01 14:30:00'),
(3, 3, 'Très intéressé ! Est-ce que tu pourrais envoyer des photos ?', '2024-11-03 15:00:00'),
(4, 2, 'Ça m''intéresse aussi, je vais t''envoyer un message privé', '2024-11-04 10:00:00'),
(6, 4, 'Super idée ! Je serai présent avec ma tarte 🥧', '2024-11-06 16:00:00'),
(6, 5, 'Je viens avec mon meilleur café !', '2024-11-06 17:00:00'),
(8, 3, 'Merci de signaler, je vais contacter le gérant', '2024-11-05 19:00:00'),
(8, 2, 'Oui c''est dangereux, surtout le soir !', '2024-11-05 19:30:00'),
(10, 4, 'Merci Pierre pour le signalement !', '2024-11-03 13:00:00');

-- Exemples de likes
INSERT INTO likes (post_id, user_id, created_at) VALUES
(1, 3, '2024-11-01 10:20:00'),
(1, 4, '2024-11-01 11:15:00'),
(1, 5, '2024-11-01 14:35:00'),
(2, 2, '2024-11-02 09:00:00'),
(2, 4, '2024-11-02 10:00:00'),
(3, 3, '2024-11-03 15:05:00'),
(3, 5, '2024-11-04 08:00:00'),
(4, 2, '2024-11-04 09:30:00'),
(5, 3, '2024-11-05 11:30:00'),
(6, 2, '2024-11-06 16:30:00'),
(6, 3, '2024-11-06 17:00:00'),
(6, 4, '2024-11-06 17:30:00'),
(7, 3, '2024-11-07 09:00:00'),
(7, 4, '2024-11-07 10:00:00'),
(8, 3, '2024-11-05 18:30:00'),
(9, 2, '2024-11-06 21:00:00'),
(11, 2, '2024-11-08 09:00:00'),
(11, 3, '2024-11-08 10:00:00'),
(11, 4, '2024-11-08 11:00:00');

-- Exemples de notifications
INSERT INTO notifications (user_id, type, message, link, is_read, created_at) VALUES
(2, 'comment', 'Sophie Bernard a commenté votre post "Bienvenue dans la résidence !"', 'post_detail.php?id=1', FALSE, '2024-11-01 10:15:00'),
(2, 'comment', 'Lucas Roux a commenté votre post "Bienvenue dans la résidence !"', 'post_detail.php?id=1', FALSE, '2024-11-01 11:00:00'),
(2, 'comment', 'Emma Leclerc a commenté votre post "Bienvenue dans la résidence !"', 'post_detail.php?id=1', TRUE, '2024-11-01 14:30:00'),
(2, 'like', 'Sophie Bernard a aimé votre post "Bienvenue dans la résidence !"', 'post_detail.php?id=1', TRUE, '2024-11-01 10:20:00'),
(2, 'like', 'Lucas Roux a aimé votre post "Bienvenue dans la résidence !"', 'post_detail.php?id=1', TRUE, '2024-11-01 11:15:00'),
(3, 'comment', 'Pierre Martin a commenté votre post "Vente Machine à laver"', 'post_detail.php?id=3', TRUE, '2024-11-03 15:00:00'),
(4, 'comment', 'Pierre Martin a commenté votre post "Canapé convertible"', 'post_detail.php?id=4', TRUE, '2024-11-04 10:00:00'),
(6, 'comment', 'Lucas Roux a commenté votre événement "Fête de Noël"', 'post_detail.php?id=6', TRUE, '2024-11-06 16:00:00'),
(6, 'comment', 'Emma Leclerc a commenté votre événement "Fête de Noël"', 'post_detail.php?id=6', TRUE, '2024-11-06 17:00:00'),
(4, 'comment', 'Sophie Bernard a commenté votre post "Lampe cassée"', 'post_detail.php?id=8', TRUE, '2024-11-05 19:00:00'),
(9, 'comment', 'Marie Dupont a commenté votre post "Porte d''entrée"', 'post_detail.php?id=10', TRUE, '2024-11-03 13:00:00');

