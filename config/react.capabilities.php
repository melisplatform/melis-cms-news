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
        // News tool (CMS News) — melisKey of the RIGHTS-BEARING menu node, i.e. the one with
        // rights_checkbox_disable=false (app.interface.php → meliscmsnews_tools_section). That is
        // the `nodeKey = melisKey||key` RightsTreeView hangs capabilities on, and the key the
        // legacy rights modal now stores too. NOT `meliscmsnews_left_menu`: that is the type-link
        // TARGET, which stays the renderable ZONE key (iframe react-tool-page?key=, ToolTabBar).
        // Same 3-key split as MelisCmsSlider.
        'meliscmsnews_tools_section' => ['list', 'create', 'edit', 'delete', 'export'],
    ],
];
