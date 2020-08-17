<?php
/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2018 Melis Technology (http://www.melistechnology.com)
 *
 * Extending MelisCmsComments module's dashboard functionality
 * to display the most recent news comments of the MelisCmsNews module
 *
 */

namespace MelisCmsNews\Controller\Plugin\Dashboard;


use MelisCore\Controller\DashboardPlugins\MelisCoreDashboardTemplatingPlugin;
use Laminas\View\Model\ViewModel;

class DashboardLatestComments extends MelisCoreDashboardTemplatingPlugin
{
    public function __construct()
    {
        $this->pluginModule = 'meliscmscomments';
        parent::__construct();
    }

    public function tabContentAction()
    {
        $view = new ViewModel();
        $view->setTemplate('MelisCmsNews/plugins/dashboard/latest-comments/tab-content');

        return $view;
    }

}