<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2015, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	http://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Bundle Router Class
 *
 * Extends CI_Router class for implement a Modular Environment.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Router
 * @author		David Sosa Valdes
 * @link		https://github.com/davidsosavaldes/Codeigniter-Bundle
 */
class Bundle_Router extends CI_Router
{
	/**
	 * Bundle class
	 * @var object
	 */
	protected $bundle;

	/**
	 * Class constructor
	 * 
	 * @param array $routing
	 */
	public function __construct($routing = NULL)
	{
		$this->config =& load_class('Config', 'core');
		$this->uri    =& load_class('URI', 'core');
		$this->bundle =& load_class('Bundle', 'third_party/CI-Bundle');

		$this->enable_query_strings = ( ! is_cli() && $this->config->item('enable_query_strings') === TRUE);

		// If a directory override is configured, it has to be set before any dynamic routing logic
		is_array($routing) && isset($routing['directory']) && $this->set_directory($routing['directory']);
		
		$this->bundle->initialize($this);
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
	 * Parse routes, appending aditional bundle routes
	 * 
	 * @return void
	 */
	protected function _parse_routes()
	{
		if ($bundle_path = $this->bundle->get_active_path()) 
		{
			if (file_exists($bundle_path.'config/routes.php')) 
			{
				include($bundle_path.'config/routes.php');
			}	
			// Validate & get reserved routes
			if (isset($route) && is_array($route))
			{
				$this->routes = array_merge($route, $this->bundle->get_routes() ,$this->routes);
			}						
		}
		parent::_parse_routes();
	}

	/**
	 * Set directory name
	 *
	 * Codeigniter ignore the changes of directory paths by default
	 *
	 * @param	string	$dir		  Directory name
	 * @param	bool	$append		  Whether we're appending rather than setting the full value
	 * @param   string 	$default_path Change the default controller path
	 * @return	void
	 */
	public function set_directory($dir, $append = FALSE, $default_path = FALSE)
	{
		if ($default_path !== FALSE) 
		{
			$repeater = rtrim(str_repeat('../', substr_count(APPPATH.'controllers/', '/')),'/');
			
			if (realpath(APPPATH.'controllers/'.$repeater.$dir)) 
			{
				$dir = trim($repeater.$dir,'/').'/controllers';
			}
		}
		
		if ($append !== TRUE || empty($this->directory)) 
		{
			$this->directory = rtrim($dir,'/').'/';
		}
		else
		{
			$this->directory .= rtrim($dir,'/').'/';
		}		
	}


	/**
	 * Validate request
	 *
	 * Attempts validate the URI request and determine the controller path.
	 *
	 * Here we only revert the $directory_override validation.
	 *
	 * @used-by	CI_Router::_set_request()
	 * @param	array	$segments	URI segments
	 * @return	mixed	URI segments
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

			if ( ! file_exists(APPPATH.'controllers/'.$test.'.php')
				&& $directory_override !== FALSE # Bundle Mode
				&& is_dir(APPPATH.'controllers/'.$this->directory.$segments[0])
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
}

/* End of file Bundle_Router.php */
/* Location: ./application/core/Bundle_Router.php */
