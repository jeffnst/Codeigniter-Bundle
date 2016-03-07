<?php
/*
|--------------------------------------------------------------------------
| Bundle Configuration
|--------------------------------------------------------------------------
|
| Bundles allow you to conveniently extend and organize your application 
| like self-contained applications. 
|
| They can have routes, controllers, models, views, configuration, etc. 
|
| Example: if you have an "admin" bundle located in "path/to/bundles/AdminBundle" 
| that you want to handle requests with URIs that begin with "admin",
| simply add it to the array like this:
|
|		'admin' => array(
|			'location' => 'AdminBundle',
|			'route'    => 'admin',
|		),
|
| Note that the "location" is relative to the "bundles" directory.
|
| You can also define the "bundles" inside the constants config file.
| 	Default: APPPATH.'bundles/'
|
| Now the bundle will be recognized by Codeigniter and will be able
| to respond to requests beginning with "admin".
*/

$config['bundles'] = array();
?>