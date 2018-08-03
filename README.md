# Melis CMS News 

Provides a venue for creating and maintaining news posts inside Melis Platform

## Getting started

These instructions will get you a copy of the project up and running on your machine.

### Prerequisites

The following modules need to be installed to have Melis CMS News module run:
* Melis core
* Melis engine
* Melis front
* Melis CMS

### Installing

Run the composer command:
```
composer require melisplatform/melis-cms-news
```

### Database    

Database model is accessible via the MySQL Workbench file:  
```
/melis-cms-news/install/sql/model
```  
Database will be installed through composer and its hooks.  
In case of problems, SQL files are located here:  
```
/melis-cms-news/install/sql  
```

## Tools and elements provided

* News tool
* Latest news plugin
* News list plugin
* News details plugin
 
### News tool
Provides the user with the basic actions in managing news posts such as:
* Creation
    - user can create a news post in the available languages.
* Edition
    - user can manage the news post's publish date, titles, contents, or attach post-specific media(images, files, etc.). 
* Deletion
    - user can delete a news post.
     
### News service  

```
File: /melis-cms-news/src/Service/MelisCmsNewsService.php
```

* This service can be used inside other modules like so:  

```
// Get the service
$newsService = $this->getServiceLocator()->get('MelisCmsNewsService');
```
* Common methods the service offers are as follows:
    - Get news post details: getNewsById(...)
    - Get a list of posts: getNewsList(...)
    - Delete a post: deleteNewsById(...)
- For a more detailed information on the methods, please visit the file.

## Authors

* **Melis Technology** - [www.melistechnology.com](https://www.melistechnology.com/)

See also the list of [contributors](https://github.com/melisplatform/melis-cms-news/contributors) who participated in this project.

## License

This project is licensed under the OSL-3.0 License - see the [LICENSE.md](LICENSE.md) file for details
