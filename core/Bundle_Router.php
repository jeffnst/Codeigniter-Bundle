<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bundle_Router extends CI_Router 
{
	/**
	 * Set of CI-Bundles
	 * @var array
	 */
	protected $_bundles = array();

	/**
	 * Class constructor
	 * 
	 * @param array $routing
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
		$this->config->load('bundles', FALSE);

		$this->_bundles['active'] = FALSE;

		if (! empty($bundles = $this->config->item('bundles'))) 
		{
			foreach ($bundles as $key => $value) 
			{
				$location = (isset($value['location']))
					? rtrim(BUNDLEPATH.$value['location'],'/').'/'
					: rtrim(BUNDLEPATH.$key,'/').'/';

				if (! is_dir($location)) 
				{
					show_error('The path "'.$location.'" currently not exist.', 404, 'Bundle Error');
				}
				
				if (isset($value['route'])) 
				{
					$this->_bundles['router'][$value['route'].'/(.+)'] = '$1';
					$this->_bundles['paths'][$value['route']] = $location;
				}
				else
				{
					$p_route = str_replace('bundle', '', strtolower(basename(dirname($location))));
					$this->_bundles['router']["{$p_route}/(.+)"] = '$1';
					$this->_bundles['paths'][$p_route] = $location;
				}
				if (isset($value['default']) && $value['default'] !== FALSE) 
				{
					$this->_bundles['active'] = $value['route'];
				}
			}
		}

		$bundle_name = isset($this->uri->segments[1])
			? $this->uri->segments[1]
			: NULL;
		
		if ($this->_bundles['active'] !== FALSE) 
		{
			$bundle_name = isset($this->_bundles['paths'][$bundle_name])
				? $bundle_name
				: $this->_bundles['active'];
		}

		$this->_bundles['request'] = (isset($this->_bundles['paths'][$bundle_name]))
			? $this->_sanitize_path($this->_bundles['paths'][$bundle_name]).'/'
			: APPPATH;
		
		if (is_dir($this->_bundles['request'].'config/')) 
		{
			if (! in_array($this->_bundles['request'], $this->config->_config_paths)) 
			{
				$this->config->_config_paths[] = $this->_bundles['request'];
			}
		}
	}

	/**
	 * Set routes
	 * Set Bundle routes
	 */
	protected function _set_routing()
	{					
		// Load the routes.php file. It would be great if we could
		// skip this for enable_query_strings = TRUE, but then
		// default_controller would be empty ...	
		
		if (file_exists(APPPATH.'config/routes.php'))
		{
			include(APPPATH.'config/routes.php');
		}

		if (file_exists($this->_bundles['request'].'config/routes.php')) 
		{
			include($this->_bundles['request'].'config/routes.php');

			if (isset($route)) 
			{
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

	/**
	 * Set default controller
	 * Set default bundle controller
	 */
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
		if (file_exists($this->_bundles['request'].'controllers/'.$this->directory.ucfirst($class).'.php')) 
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
	 * Validate controller request
	 * 
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

			if (! file_exists($this->_bundles['request'].'controllers/'.$test.'.php') 
					&& $directory_override === FALSE 
					&& is_dir($this->_bundles['request'].'controllers/'.$this->directory.$segments[0])
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
	 * [set_controller description]
	 * 
	 * @param  [type] $route [description]
	 * @return [type]        [description]
	 */
	public function set_controller($route)
	{
		$test_file = $this->_bundles['request'].'controllers/'.$route.'.php';
		return (file_exists($test_file)) ? $test_file : FALSE;
	}

	/**
	 * [get_bundle_path description]
	 * @return [type] [description]
	 */
	public function get_bundle_path()
	{
		return rtrim(realpath($this->_bundles['request']),'/').'/';
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