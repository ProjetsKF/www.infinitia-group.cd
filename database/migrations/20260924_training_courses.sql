CREATE TABLE IF NOT EXISTS training_courses (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    code VARCHAR(80) NOT NULL,
    name VARCHAR(190) NOT NULL,
    category VARCHAR(120) NOT NULL,
    short_description VARCHAR(500) NOT NULL,
    detailed_content TEXT NULL,
    duration VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NULL,
    price_unit VARCHAR(50) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    display_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_training_courses_code (code),
    KEY idx_training_courses_active_order (is_active, display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO training_courses (code, name, category, short_description, detailed_content, duration, price, price_unit, is_active, display_order, created_at, updated_at) VALUES
('initiation-informatique', 'Initiation en informatique et bureautique professionnelle', 'Informatique & Bureautique', 'Un parcours d’initiation aux outils informatiques et aux usages essentiels de la bureautique professionnelle.', 'Cette formation est destinée à développer les bases nécessaires à l’utilisation professionnelle de l’outil informatique et des applications bureautiques.', '2 mois', 100.00, '/ mois', 1, 1, NOW(), NOW()),
('it-essentials', 'IT Essentials', 'Maintenance informatique', 'Une formation consacrée aux connaissances essentielles du matériel, des logiciels et de l’environnement informatique.', 'Le parcours aborde les notions fondamentales nécessaires pour comprendre et prendre en charge un environnement informatique.', '2 mois', 210.00, NULL, 1, 2, NOW(), NOW()),
('networking-essentials', 'Networking Essentials', 'Réseaux', 'Une introduction pratique aux concepts essentiels des réseaux informatiques et à leur fonctionnement.', 'Cette formation permet d’aborder les principes de base utiles à la compréhension et à l’utilisation des réseaux informatiques.', '4 semaines', 150.00, NULL, 1, 3, NOW(), NOW()),
('ccna-1', 'CCNA 1 – Introduction to Networks', 'Réseaux', 'Un parcours d’introduction aux réseaux, à leurs composants et aux principes fondamentaux de communication.', 'Le programme porte sur les bases des réseaux et les notions nécessaires pour comprendre leur architecture et leur fonctionnement.', '2 mois', 210.00, NULL, 1, 4, NOW(), NOW()),
('ccna-2', 'CCNA 2 – Switching, Routing and Wireless Essentials', 'Réseaux', 'Une formation axée sur les notions essentielles de commutation, de routage et de réseaux sans fil.', 'Le parcours approfondit les principes de commutation, de routage et de connectivité sans fil dans un environnement réseau.', '2 mois', 210.00, NULL, 1, 5, NOW(), NOW()),
('ccna-3', 'CCNA 3 – Enterprise Networking, Security and Automation', 'Réseaux', 'Un parcours consacré aux réseaux d’entreprise, à la sécurité et aux notions d’automatisation.', 'La formation aborde les environnements réseau d’entreprise ainsi que les principes associés à leur sécurité et à leur automatisation.', '2 mois', 210.00, NULL, 1, 6, NOW(), NOW()),
('cyberops', 'CyberOps', 'Cybersécurité', 'Une formation orientée vers les opérations de cybersécurité et la compréhension des menaces numériques.', 'Le parcours introduit les notions utiles aux opérations de cybersécurité et à l’analyse des risques liés aux systèmes numériques.', '2 mois', 300.00, NULL, 1, 7, NOW(), NOW()),
('administration-base-donnees', 'Administration de base de données', 'Bases de données', 'Un parcours dédié aux principes fondamentaux de gestion et d’administration des bases de données.', 'La formation présente les notions essentielles pour comprendre l’organisation, la gestion et l’administration d’une base de données.', '1 mois', 210.00, NULL, 1, 8, NOW(), NOW()),
('windows-server', 'Administration systèmes – Windows Server', 'Administration systèmes', 'Une formation pratique aux bases de l’administration de systèmes dans un environnement Windows Server.', 'Le parcours porte sur les notions fondamentales nécessaires à la prise en main et à l’administration d’un environnement Windows Server.', '1 mois', 150.00, NULL, 1, 9, NOW(), NOW()),
('videosurveillance', 'Vidéosurveillance', 'Vidéosurveillance', 'Une introduction pratique aux équipements et aux principes essentiels d’un système de vidéosurveillance.', 'Cette formation aborde les notions de base permettant de comprendre les composants et le fonctionnement d’un dispositif de vidéosurveillance.', '2 semaines', 100.00, NULL, 1, 10, NOW(), NOW());
