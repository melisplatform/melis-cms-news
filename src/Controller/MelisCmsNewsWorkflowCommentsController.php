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

	const WF_TYPE = "NEWS";

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
		$commentForm = $this->getCommentNewsForm();
		
		/**
		 * Send back the view and add the form config inside
		*/
		$view = new ViewModel();
		$view->newsId = $newsId;
		$view->melisKey = $melisKey;
		$view->setVariable('commentForm', $commentForm);
	
		return $view;
	}
	
	/*
	* retrieves news workflow comments
	*/
	public function renderNewsWorkflowCommentsTimelineAction()
	{
		$newsId = $this->params()->fromRoute('newsId', $this->params()->fromQuery('newsId', ''));
		$melisKey = $this->params()->fromRoute('melisKey', '');					
		$newsComments = $this->getServiceManager()->get('MelisCmsNewsCommentService')->getNewsComments((int) $newsId, self::WF_TYPE);

		/**
		 * Send back the view and add the form config inside
		*/
		$view = new ViewModel();
		$view->newsId = $newsId;
		$view->comments = $newsComments;
		$view->melisKey = $melisKey;
	
		return $view;
	}
	
	/*
	* Saves new comment
	*/
	public function addCommentAction()
	{
		$translator = $this->getServiceManager()->get('translator');
		$success = 0;
		$errors  = array();
		$textTitle = $translator->translate('tr_meliscmsnews_comments_content');
		$textMessage = $translator->translate('tr_meliscmsnews_comments_modal_content');
		
		if ($this->getRequest()->isPost()) {						
			$commentForm = $this->getCommentNewsForm();			
			$postValues = $this->getRequest()->getPost()->toArray();
			$commentForm->setData($postValues);
			
			if ($commentForm->isValid()) {
				
				$newsComment = $this->getServiceManager()->get('MelisCmsNewsCommentService');
				$success      = (int) $newsComment->setNewsComments($postValues);
				$textMessage  = $translator->translate('tr_meliscmsnews_comments_content_ok');
			} else {
				$errors = $commentForm->getMessages();
			}
			
			foreach ($errors as $keyError => $valueError) {
				foreach ($appConfigForm['elements'] as $keyForm => $valueForm) {
					if ($valueForm['spec']['name'] == $keyError &&
						!empty($valueForm['spec']['options']['label']))
						$errors[$keyError]['label'] = $valueForm['spec']['options']['label'];
				}
			}
		}
		
		return new JsonModel(array(
			'success' => $success,
			'errors' => $errors,
			'textTitle' => $textTitle,
			'textMessage' => $textMessage
			
		));
	}

	/*
    * retrieves the news comment form
    */
    private function getCommentNewsForm() 
    {
        // Get the form properly loaded               
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');  
        $commentFormConfig = $melisCoreConfig->getFormMergedAndOrdered('MelisCmsNews/forms/meliscmsnews_comment_form', 'meliscmsnews_comment_form');
        $commentNewsForm = $factory->createForm($commentFormConfig);

        return $commentNewsForm;
    }
}
