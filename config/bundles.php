<?php
/*
|--------------------------------------------------------------------------
| Bundle Configuration
|--------------------------------------------------------------------------
|
| Bundles allow you to conveniently extend and organize your application.
| Think of bundles as self-contained applications. They can have routes,
| controllers, models, views, configuration, etc. 
|
| Example: if you have an "admin" bundle located in "bundles/adminBundle" 
| that you want to handle requests with URIs that begin with "admin",
| simply add it to the array like this:
|
|		'admin' => array(
|			'location' => 'adminBundle',
|			'route'  => 'admin',
|		),
|
| Note that the "location" is relative to the "bundles" directory.
| Now the bundle will be recognized by Codeigniter and will be able
| to respond to requests beginning with "admin".
|
*/

$config['bundles'] = array(
	'Backend' => array(
		'location' => 'BackendBundle',
		'route' => 'backend',
		'default' => TRUE
	)
);

?>
