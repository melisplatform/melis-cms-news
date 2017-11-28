# melis-cms-news

MelisCmsNews provides a full News system for Melis Platform, including templating plugins.

## Getting Started

These instructions will get you a copy of the project up and running on your machine.  
This Melis Platform module is made to work with the MelisCore.

### Prerequisites

You will need to install melisplatform/melis-cms in order to have this module running.  
This will automatically be done when using composer.

### Installing

Run the composer command:
```
composer require melisplatform/melis-cms-news
```

### Database    

Database model is accessible on the MySQL Workbench file:  
/melis-cms-news/install/sql/model  
Database will be installed through composer and its hooks.  
In case of problems, SQL files are located here:  
/melis-cms-news/install/sql  

## Tools & Elements provided

* News Tool
* Melis Templating News Plugins (ListNews, LatestNews, NewsShow)
* News with Sliders when MelisCmsSlider Module is installed

## Running the code

### MelisCmsNews Services  

MelisCmsNews provides many services to be used in other modules:  

* MelisCmsNewsService  
Services to retrieve lists of news, news details and save news  
File: /melis-cms-news/src/Service/MelisCmsNewsService.php  
```
// Get the service
$newsService = $this->getServiceLocator()->get("MelisCmsNewsService");  
// Get news by site id
$siteNews = $newsService->getNews($siteId);  
```

### MelisCms Forms  

#### Forms factories
All Melis CMS News forms are built using Form Factories.  
All form configuration are available in the file: /melis-cms-news/config/app.forms.php  
Any module can override or add items in this form by building the keys in an array and marge it in the Module.php config creation part.  
``` 
return array(
	'plugins' => array(

		// MelisCmsNews array
		'MelisCmsNews' => array(

			// Form key
			'forms' => array(

				// MelisCmsNews Properties form
				'meliscmsnews_properties_form' => array(
					'attributes' => array(
						'name' => 'newsLetterForm',
						'id' => 'newsLetterForm',
						'method' => 'POST',
						'action' => '',
					),
					'hydrator'  => 'Zend\Stdlib\Hydrator\ArraySerializable',
					'elements' => array(  
						array(
							'spec' => array(
									...
							),
						),
					),
					'input_filter' => array(      
						'cnews_id' => array(
								...
						),   
					),
				),
			), 
		),
	),
),
``` 

#### Forms elements
MelisCmsNews provides form elements to be used in forms:  
* MelisCmsNewsSelect: a dropdown to select a news  
* MelisCmsSiteNewsSelect: a dropdown to select a site  


### Listening to services and update behavior with custom code  
Most services trigger events so that the behavior can be modified.  
```  
public function attach(EventManagerInterface $events)
{
    $sharedEvents      = $events->getSharedManager();
    
    $callBackHandler = $sharedEvents->attach(
        'MelisCmsSlider',
        array(
            'meliscmsslider_delete_slider_end'
        ),
    	function($e){
    	    
    		$sm = $e->getTarget()->getServiceLocator();   	
    		$params = $e->getParams();
    		
    		// Custom code    		
    		    
    	},
    100);
    
    $this->listeners[] = $callBackHandler;
}
```  


## Authors

* **Melis Technology** - [www.melistechnology.com](https://www.melistechnology.com/)

See also the list of [contributors](https://github.com/melisplatform/melis-cms-news/contributors) who participated in this project.


## License

This project is licensed under the OSL-3.0 License - see the [LICENSE.md](LICENSE.md) file for details