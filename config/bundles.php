<?php
/*
|---------------------------------------------------------------------------------
| Bundle Configuration
|---------------------------------------------------------------------------------
|
| Bundles allow you to conveniently extend and organize your application 
| like self-contained applications. 
|
| They can have routes, controllers, models, views, configuration, etc. 
|
| Example: if you have an "admin" bundle located in "application/bundles/AdminBundle" 
| that you want to handle requests with URIs that begin with "admin",
| simply add it to the array like this:
|
|		'admin' => array(
|			'location' => 'AdminBundle',
|			'route'    => 'admin'
|		),
|
| Note that the "location" is relative to the "bundles" directory.
|
| Now the bundle will be recognized by Codeigniter and will be able
| to respond to requests beginning with "admin".
|
| Notes:
|
| - You can change the 'BUNDLEPATH' constant inside the constants config file
| 	of your application.
| 	
| 		BUNDLEPATH = APPPATH.'bundles/' #Default;
|
| - If you want to override the default CI Controller path with a Bundle,
| 	set TRUE the "default" key:
|
|		'admin' => array(
|			'location' => 'AdminBundle',
|			'route'    => 'admin',
| 			'default'  => TRUE
|		),
*/

$config['bundles'] = array();
?>