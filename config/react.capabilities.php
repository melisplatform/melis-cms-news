<?php

/**
 * Capacités d'outils — droits avancés du back-office React (déclaration plate, par module).
 *
 * Même convention que melis-core / melis-commerce `config/react.capabilities.php` : ce module
 * déclare ICI les capacités de SES outils, par `melisKey`. Fichier VOLONTAIREMENT séparé de
 * module.config.php ; mergé dans MelisCmsNews\Module::getConfig().
 *
 * Sémantique : ces capacités gouvernent les COMPOSANTS INTERNES d'un outil déjà autorisé
 * (liste / créer / éditer / supprimer / exporter). Default-allow ; pilote l'affichage des cases
 * à cocher dans l'onglet Rights et le gating côté React via `window.__melisUseCaps`.
 *
 * Lu par MelisReactApi\Service\Capabilities via la config mergée (clé `melisReactToolCapabilities`).
 */

return [
    'melisReactToolCapabilities' => [
        // Outil Actualités (CMS News) — melisKey de la garde `canAccess` du contrôleur react-api
        // et du nœud de menu (cf. app.interface.php → meliscmsnews_left_menu).
        'meliscmsnews_left_menu' => ['list', 'create', 'edit', 'delete', 'export'],
    ],
];
