<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */
namespace MelisCmsNews\Controller; 

use MelisCore\Controller\MelisAbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use Laminas\Session\Container;

/**
 * This class renders Melis CMS Page tab properties
 */
class MelisCmsNewsWorkflowCommentsController extends MelisAbstractActionController
{	


	/**
	 * Makes the rendering of the Workflow Comments Tab
	 * @return \Laminas\View\Model\ViewModel
	 */
	public function renderNewsWorkflowCommentsAction()
	{
		$newsId = $this->params()->fromRoute('newsId', $this->params()->fromQuery('newsId', ''));
		$melisKey = $this->params()->fromRoute('melisKey', '');
		
		/**
		 * Send back the view and add the form config inside
		*/
		$view = new ViewModel();
		$view->newsId = $newsId;
		$view->melisKey = $melisKey;
	
		return $view;
	}
	
	public function renderNewsWorkflowCommentsAddAction()
	{
		$newsId = $this->params()->fromRoute('newsId', $this->params()->fromQuery('newsId', ''));
		$melisKey = $this->params()->fromRoute('melisKey', '');
		$commentForm = $this->getServiceManager()->get('MelisSBPageCommentService')->getPageCommentForm();
		
		/**
		 * Send back the view and add the form config inside
		*/
		$view = new ViewModel();
		$view->newsId = $newsId;
		$view->melisKey = $melisKey;
		$view->commentForm = $commentForm;
	
		return $view;
	}
	
	/*
	* retrieves news workflow comments
	*/
	public function renderNewsWorkflowCommentsTimelineAction()
	{
		$newsId = $this->params()->fromRoute('newsId', $this->params()->fromQuery('newsId', ''));
		$melisKey = $this->params()->fromRoute('melisKey', '');					
		$newsComments = $this->getServiceManager()->get('MelisSBPageCommentService')->getPageComments(null, (int) $newsId);

		/**
		 * Send back the view and add the form config inside
		*/
		$view = new ViewModel();
		$view->setTemplate('melis-small-business/workflow-comments-timeline');
		$view->idPage = $newsId;
		$view->comments = $newsComments;
		$view->melisKey = $melisKey;
	
		return $view;
	}
	
		
			
				
			
		
			


}
