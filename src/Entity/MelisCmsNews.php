<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Entity;

class MelisCmsNews
{
	protected $id;
	protected $news;
 
	public function getId()
	{
	    return $this->id;
	}
	
	public function setId($id)
	{
	    $this->id = $id;
	}
	
	public function getNews()
	{
	    return $this->news;
	}
	
	public function setNews($news)
	{
	    $this->news = $news;
	}
	
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}