<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bundle_Router extends CI_Router 
{
	/**
	 * Set of CI-Bundles
	 * @var array
	 */
	protected $_bundles = array();

	/**
	 * [__construct description]
	 * @param [type] $routing [description]
	 */
	public function __construct($routing = NULL)
	{
		$this->config =& load_class('Config', 'core');
		$this->uri =& load_class('URI', 'core');					

		$this->enable_query_strings = ( ! is_cli() && $this->config->item('enable_query_strings') === TRUE);

		// If a directory override is configured, it has to be set before any dynamic routing logic
		is_array($routing) && isset($routing['directory']) && $this->set_directory($routing['directory']);

		$this->_set_bundles();
		$this->_set_routing();

		// Set any routing overrides that may exist in the main index file
		if (is_array($routing))
		{
			empty($routing['controller']) OR $this->set_class($routing['controller']);
			empty($routing['function'])   OR $this->set_method($routing['function']);
		}

		log_message('info', 'Router Class Initialized');
	}

	/**
	 * Set the CI-Bundles locations and routes.
	 */
	protected function _set_bundles()
	{
		$this->config->load('bundles', FALSE, TRUE);

		$this->_bundles['active'] = FALSE;

		if (! empty($bundles = $this->config->item('bundles'))) 
		{
			foreach ($bundles as $key => $value) 
			{
				$location = (isset($value['location']))
					? rtrim(BUNDLEPATH.$value['location'],'/').'/controllers'
					: rtrim(BUNDLEPATH.$key,'/').'/controllers';

				if (! is_dir($location)) 
				{
					show_error('The path "'.$location.'" currently not exist.', 404, 'Bundle Error');
				}
				
				if (isset($value['route'])) 
				{
					$this->_bundles['router'][$value['route'].'/(.+)'] = '$1';
					$this->_bundles['controllers'][$value['route']] = $location;
				}
				else
				{
					$p_route = str_replace('bundle', '', strtolower(basename(dirname($location))));
					$this->_bundles['router']["{$p_route}/(.+)"] = '$1';
					$this->_bundles['controllers'][$p_route] = $location;
				}
				if (isset($value['default']) && $value['default'] !== FALSE) 
				{
					$this->_bundles['active'] = $value['route'];
				}
			}
		}
		
		if ($this->_bundles['active'] !== FALSE) 
		{
			$bundle_name = $this->_bundles['active'];
		}
		else 
		{
			$bundle_name = isset($this->uri->segments[1])
				? $this->uri->segments[1]
				: NULL;			
		}

		$this->_bundles['path_request'] = (isset($this->_bundles['controllers'][$bundle_name]))
			? $this->_sanitize_path($this->_bundles['controllers'][$bundle_name]).'/'
			: APPPATH.'controllers/';
	}

	protected function _set_routing()
	{					
		// Load the routes.php file. It would be great if we could
		// skip this for enable_query_strings = TRUE, but then
		// default_controller would be empty ...	
		
		if (file_exists(APPPATH.'config/routes.php'))
		{
			include(APPPATH.'config/routes.php');
		}

		if ($this->_bundles['active'] !== FALSE) 
		{
			if (file_exists($bundle_router = realpath($this->_bundles['path_request'].'../config/routes.php'))) 
			{
				include($bundle_router);	

				$this->_bundles['router'] = array_merge($route, $this->_bundles['router']);
			}
		}	

		if (file_exists(APPPATH.'config/'.ENVIRONMENT.'/routes.php'))
		{
			include(APPPATH.'config/'.ENVIRONMENT.'/routes.php');
		}			

		if (isset($route) && is_array($route))
		{
			// Merge bundle router with default routes.
			$route = array_merge($route, $this->_bundles['router']);

			// Validate & get reserved routes	
			isset($route['default_controller']) && $this->default_controller = $route['default_controller'];
			isset($route['translate_uri_dashes']) && $this->translate_uri_dashes = $route['translate_uri_dashes'];
			unset($route['default_controller'], $route['translate_uri_dashes']);

			$this->routes = $route;
		}

		// Are query strings enabled in the config file? Normally CI doesn't utilize query strings
		// since URI segments are more search-engine friendly, but they can optionally be used.
		// If this feature is enabled, we will gather the directory/class/method a little differently
		if ($this->enable_query_strings)
		{
			// If the directory is set at this time, it means an override exists, so skip the checks
			if ( ! isset($this->directory))
			{
				$_d = $this->config->item('directory_trigger');
				$_d = isset($_GET[$_d]) ? trim($_GET[$_d], " \t\n\r\0\x0B/") : '';

				if ($_d !== '')
				{
					$this->uri->filter_uri($_d);
					$this->set_directory($_d);
				}
			}

			$_c = trim($this->config->item('controller_trigger'));
			if ( ! empty($_GET[$_c]))
			{
				$this->uri->filter_uri($_GET[$_c]);
				$this->set_class($_GET[$_c]);

				$_f = trim($this->config->item('function_trigger'));
				if ( ! empty($_GET[$_f]))
				{
					$this->uri->filter_uri($_GET[$_f]);
					$this->set_method($_GET[$_f]);
				}

				$this->uri->rsegments = array(
					1 => $this->class,
					2 => $this->method
				);
			}
			else
			{
				$this->_set_default_controller();
			}

			// Routing rules don't apply to query strings and we don't need to detect
			// directories, so we're done here
			return;
		}

		// Is there anything to parse?
		if ($this->uri->uri_string !== '')
		{
			$this->_parse_routes();
		}
		else
		{
			$this->_set_default_controller();
		}
	}

	protected function _set_default_controller()
	{
		$response = FALSE;

		if (empty($this->default_controller))
		{
			show_error('Unable to determine what should be displayed. A default route has not been specified in the routing file.');
		}

		// Is the method being specified?
		if (sscanf($this->default_controller, '%[^/]/%s', $class, $method) !== 2)
		{
			$method = 'index';
		}
		$path_request = $this->_bundles['path_request'];
		if (file_exists($path_request.'/'.$this->directory.ucfirst($class).'.php')) 
		{
			$response = TRUE;
		}	
		if ($response !== TRUE)
		{
			// This will trigger 404 later
			return;
		}

		$this->set_class($class);
		$this->set_method($method);

		// Assign routed segments, index starting from 1
		$this->uri->rsegments = array(
			1 => $class,
			2 => $method
		);

		log_message('debug', 'No URI present. Default controller set.');
	}

	/**
	 * [_validate_request description]
	 * @param  [type] $segments [description]
	 * @return [type]           [description]
	 */
	protected function _validate_request($segments)
	{
		$c = count($segments);
		$directory_override = isset($this->directory);

		// Loop through our segments and return as soon as a controller
		// is found or when such a directory doesn't exist
		while ($c-- > 0)
		{
			$test = $this->directory
				.ucfirst($this->translate_uri_dashes === TRUE ? str_replace('-', '_', $segments[0]) : $segments[0]);

			if (! file_exists($this->_bundles['path_request'].$test.'.php') 
					&& $directory_override === FALSE 
					&& is_dir($this->_bundles['path_request'].$this->directory.$segments[0])
				)
			{
					$this->set_directory(array_shift($segments), TRUE);
					continue;	
			}
			return $segments;
		}

		// This means that all segments were actually directories
		return $segments;
	}	

	/**
	 * [check_bundles_controller description]
	 * @param  [type] $route [description]
	 * @return [type]        [description]
	 */
	public function check_bundle_controller($route)
	{
		$test_file = $this->_bundles['path_request'].$route.'.php';
		return (file_exists($test_file)) ? $test_file : FALSE;
	}

	/**
	 * [_sanitize_path description]
	 * @param  string $path [description]
	 * @return [type]       [description]
	 */
	private function _sanitize_path($path)
	{
		return rtrim(preg_replace("/[^a-zA-Z0-9\/_|+ -]/", "", $path),'/');		
	}
	
}

/* End of file MY_Router.php */
/* Location: ./application/core/MY_Router.php */