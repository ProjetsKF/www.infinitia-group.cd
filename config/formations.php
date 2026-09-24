<?php

function infinitia_training_course_fallbacks()
{
    return array(
        array('code' => 'initiation-informatique', 'name' => 'Initiation en informatique et bureautique professionnelle', 'category' => 'Informatique & Bureautique', 'short_description' => 'Un parcours d’initiation aux outils informatiques et aux usages essentiels de la bureautique professionnelle.', 'detailed_content' => 'Cette formation est destinée à développer les bases nécessaires à l’utilisation professionnelle de l’outil informatique et des applications bureautiques.', 'duration' => '2 mois', 'price' => '100.00', 'price_unit' => '/ mois', 'is_active' => 1, 'display_order' => 1),
        array('code' => 'it-essentials', 'name' => 'IT Essentials', 'category' => 'Maintenance informatique', 'short_description' => 'Une formation consacrée aux connaissances essentielles du matériel, des logiciels et de l’environnement informatique.', 'detailed_content' => 'Le parcours aborde les notions fondamentales nécessaires pour comprendre et prendre en charge un environnement informatique.', 'duration' => '2 mois', 'price' => '210.00', 'price_unit' => '', 'is_active' => 1, 'display_order' => 2),
        array('code' => 'networking-essentials', 'name' => 'Networking Essentials', 'category' => 'Réseaux', 'short_description' => 'Une introduction pratique aux concepts essentiels des réseaux informatiques et à leur fonctionnement.', 'detailed_content' => 'Cette formation permet d’aborder les principes de base utiles à la compréhension et à l’utilisation des réseaux informatiques.', 'duration' => '4 semaines', 'price' => '150.00', 'price_unit' => '', 'is_active' => 1, 'display_order' => 3),
        array('code' => 'ccna-1', 'name' => 'CCNA 1 – Introduction to Networks', 'category' => 'Réseaux', 'short_description' => 'Un parcours d’introduction aux réseaux, à leurs composants et aux principes fondamentaux de communication.', 'detailed_content' => 'Le programme porte sur les bases des réseaux et les notions nécessaires pour comprendre leur architecture et leur fonctionnement.', 'duration' => '2 mois', 'price' => '210.00', 'price_unit' => '', 'is_active' => 1, 'display_order' => 4),
        array('code' => 'ccna-2', 'name' => 'CCNA 2 – Switching, Routing and Wireless Essentials', 'category' => 'Réseaux', 'short_description' => 'Une formation axée sur les notions essentielles de commutation, de routage et de réseaux sans fil.', 'detailed_content' => 'Le parcours approfondit les principes de commutation, de routage et de connectivité sans fil dans un environnement réseau.', 'duration' => '2 mois', 'price' => '210.00', 'price_unit' => '', 'is_active' => 1, 'display_order' => 5),
        array('code' => 'ccna-3', 'name' => 'CCNA 3 – Enterprise Networking, Security and Automation', 'category' => 'Réseaux', 'short_description' => 'Un parcours consacré aux réseaux d’entreprise, à la sécurité et aux notions d’automatisation.', 'detailed_content' => 'La formation aborde les environnements réseau d’entreprise ainsi que les principes associés à leur sécurité et à leur automatisation.', 'duration' => '2 mois', 'price' => '210.00', 'price_unit' => '', 'is_active' => 1, 'display_order' => 6),
        array('code' => 'cyberops', 'name' => 'CyberOps', 'category' => 'Cybersécurité', 'short_description' => 'Une formation orientée vers les opérations de cybersécurité et la compréhension des menaces numériques.', 'detailed_content' => 'Le parcours introduit les notions utiles aux opérations de cybersécurité et à l’analyse des risques liés aux systèmes numériques.', 'duration' => '2 mois', 'price' => '300.00', 'price_unit' => '', 'is_active' => 1, 'display_order' => 7),
        array('code' => 'administration-base-donnees', 'name' => 'Administration de base de données', 'category' => 'Bases de données', 'short_description' => 'Un parcours dédié aux principes fondamentaux de gestion et d’administration des bases de données.', 'detailed_content' => 'La formation présente les notions essentielles pour comprendre l’organisation, la gestion et l’administration d’une base de données.', 'duration' => '1 mois', 'price' => '210.00', 'price_unit' => '', 'is_active' => 1, 'display_order' => 8),
        array('code' => 'windows-server', 'name' => 'Administration systèmes – Windows Server', 'category' => 'Administration systèmes', 'short_description' => 'Une formation pratique aux bases de l’administration de systèmes dans un environnement Windows Server.', 'detailed_content' => 'Le parcours porte sur les notions fondamentales nécessaires à la prise en main et à l’administration d’un environnement Windows Server.', 'duration' => '1 mois', 'price' => '150.00', 'price_unit' => '', 'is_active' => 1, 'display_order' => 9),
        array('code' => 'videosurveillance', 'name' => 'Vidéosurveillance', 'category' => 'Vidéosurveillance', 'short_description' => 'Une introduction pratique aux équipements et aux principes essentiels d’un système de vidéosurveillance.', 'detailed_content' => 'Cette formation aborde les notions de base permettant de comprendre les composants et le fonctionnement d’un dispositif de vidéosurveillance.', 'duration' => '2 semaines', 'price' => '100.00', 'price_unit' => '', 'is_active' => 1, 'display_order' => 10)
    );
}

function infinitia_training_catalog()
{
    if (isset($GLOBALS['infinitia_training_courses_by_code']) && is_array($GLOBALS['infinitia_training_courses_by_code'])) {
        $catalog = array();
        foreach ($GLOBALS['infinitia_training_courses_by_code'] as $code => $course) {
            $catalog[$code] = $course['name'];
        }
        return $catalog;
    }
    $catalog = array();
    foreach (infinitia_training_course_fallbacks() as $course) {
        $catalog[$course['code']] = $course['name'];
    }
    return $catalog;
}

function infinitia_training_name($code)
{
    $catalog = infinitia_training_catalog();
    return isset($catalog[$code]) ? $catalog[$code] : '';
}

function infinitia_session_statuses()
{
    return array('draft' => 'Brouillon', 'open' => 'Ouverte', 'full' => 'Complète', 'ended' => 'Terminée', 'cancelled' => 'Annulée');
}

function infinitia_registration_statuses()
{
    return array('new' => 'Nouvelle', 'processing' => 'En traitement', 'accepted' => 'Acceptée', 'refused' => 'Refusée', 'cancelled' => 'Annulée');
}
