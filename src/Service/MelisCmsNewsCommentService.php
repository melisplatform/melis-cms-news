<?php

namespace MelisCmsNews\Service;

use MelisCore\Service\MelisGeneralService;

class MelisCmsNewsCommentService extends MelisGeneralService
{
	/*
	* @param $workflowType can be page, news or blog
	*/
	public function getNewsComments($newsId, $workflowType = null)
	{
		$months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec');
		$translator = $this->getServiceManager()->get('translator');
		$newsComment = $this->getServiceManager()->get('MelisCmsNewsWorkflowComment');
		$newsCommentType = $this->getServiceManager()->get('MelisCmsNewsWorkflowCommentType');
		$users       = $this->getServiceManager()->get('MelisCoreTableUser');
		$comments = $newsComment->getComments($newsId, $workflowType)->toArray();
		
		$defaultProfile = '/MelisCore/images/profile/default_picture.jpg';
		for ($x = 0; $x < count($comments); $x++) {
			$user = $users->getEntryById($comments[$x]['cnews_com_user_id'])->current();
		
			if ($user) {
				
				$userImg = !empty($user->usr_image) ? 'data:image/jpeg;base64,'. base64_encode($user->usr_image) : $defaultProfile;
				$comments[$x]['name'] = $user->usr_firstname . ' ' . $user->usr_lastname;
				$comments[$x]['email'] = $user->usr_email;
				$comments[$x]['thumbnail'] = $userImg;
			} else {
				$comments[$x]['name'] = $translator->translate('tr_meliscmsnews_page_tab_comments_no_user').' ('.$comments[$x]['cnews_com_user_id'].')';
				$comments[$x]['email'] = '-';
				$comments[$x]['thumbnail'] = $defaultProfile;
			}
			
			$comments[$x]['isToday'] = $this->isToday($comments[$x]['cnews_com_date']);
			$comments[$x]['month'] = $months[date_parse_from_format('Y-m-d H:i:s', $comments[$x]['cnews_com_date'])['month']-1];
			$comments[$x]['day'] = date_parse_from_format('Y-m-d H:i:s', $comments[$x]['cnews_com_date'])['day'];
			$comments[$x]['time'] = date('h:i A', strtotime($comments[$x]['cnews_com_date']));
			
			$type = $newsCommentType->getEntryById($comments[$x]['cnews_com_type_id'])->current();
		
			
			if ($type)
				$comments[$x]['type'] = $type->cnews_comt_translate_key;
			else
				$comments[$x]['type'] = '';
		}
				
		return $comments;
	}
	
	public function setNewsComments(array $data, $commentType = 1)
	{
		$newsComment = $this->getServiceManager()->get('MelisCmsNewsWorkflowComment');
		
		$melisCoreAuth = $this->getServiceManager()->get('MelisCoreAuth');
		$userAuthDatas =  $melisCoreAuth->getStorage()->read();
		$userId = (int) $userAuthDatas->usr_id;
		
		$newsComment->save(array_merge(array(
			'cnews_com_type_id' => (int) $commentType,
			'cnews_com_user_id' => $userId,
			'cnews_com_date' => date('Y-m-d H:i:s'),
		), $data));
		
		return true;
	}
	
	/**
	 * Check if the given date is today
	 * @param String|date $d
	 */
	protected function isToday($d)
	{
		$strDate = date_create(date("Y-m-d", strtotime($d)));
		$today = date_create(date('Y-m-d'));
	
		$diff = date_diff($strDate, $today);
	
		if ( (int) $diff->days == 0)
			return true;
			else
				return false;
	}
}