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
 * Bundle Class
 *
 * Loads Bundle components.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Bundle
 * @author		David Sosa Valdes
 * @link		https://github.com/davidsosavaldes/Codeigniter-Bundle
 */
class CI_Bundle
{
	/**
	 * Set Bundle ID active.
	 * @var string
	 */
	protected $_active = NULL;

	/**
	 * Set of all Bundle paths
	 * @var array
	 */
	protected $_paths = array();

	/**
	 * Set of routes appended to CI_Router
	 * @var array
	 */
	protected $_routes = array();

	/**
	 * Class constructor
	 *
	 * You can also define inside the constants config file.
	 */
	public function __construct()
	{
		if (! defined('BUNDLEPATH')) 
		{
			define('BUNDLEPATH', APPPATH.'bundles/');
		}
		log_message('info', 'Bundle Class Initialized');
	}

	/**
	 * Initializer
	 * 
	 * @param  CI_Router $router Codeigniter Router class
	 * @return void
	 */
	public function initialize(CI_Router $router)
	{
		$CFG =& load_class('Config', 'core');
		$URI =& load_class('URI', 'core');
		$EXT =& load_class('Hooks', 'core');		
		$RTR =& $router;

		$CFG->load('bundles', FALSE, TRUE);

		if (! empty($bundles = $CFG->item('bundles')))
		{
			foreach ($bundles as $bundle => $config) 
			{
				$location = isset($config['location'])
					? BUNDLEPATH.$config['location']
					: BUNDLEPATH.$bundle;
				
				if (! is_dir($location = rtrim($location,'/').'/')) 
				{
					log_message('error', 'Could not find the specified $bundle[\'location\'] path: '.$location);
					continue;
				}

				$route = strtolower(
					isset($config['route'])
						? rtrim($config['route'],'/')
						: basename(dirname($location))
				);				
				$this->_paths[$route] = $location;
				$this->_routes[$route.'/(.+)'] = '$1';
			}
		}	

		$bundle = isset($URI->segments[1])
			? $URI->segments[1]
			: NULL;	

		if (isset($this->_paths[$bundle])) 
		{
			$bundle_path = $this->_paths[$bundle];

			$this->_active = $bundle;

			$RTR->set_directory($bundle_path, FALSE, TRUE);
			$EXT->add($bundle_path);
			$CFG->_config_paths[] = $bundle_path;

			// Update system config if config bundle file exist
			if (file_exists($config_path = $bundle_path.'config/config.php')) 
			{
				require($config_path);

				if (isset($config) && is_array($config))
				{
					get_config($config);
				}
			}			

			// Register Bundle Core classes
			spl_autoload_register(function($class) use ($bundle_path) {
				if (file_exists($bundle_path.'core/'.$class.'.php')) 
				{
					require_once($bundle_path.'core/'.$class.'.php');
				}
			});			
		}
	}

	/**
	 * Get absolute path from the active bundle
	 * 
	 * @return mix Returns false when does not exist
	 */
	public function get_active_path()
	{
		return isset($this->_paths[$this->_active])
			? $this->_paths[$this->_active]
			: FALSE;
	}	

	/**
	 * Get aditional bundle routes used by CI-Router
	 *
	 * @return array
	 */
	public function get_routes()
	{
		return $this->_routes;
	}
}

/* End of file CI_Bundle.php */
/* Location: ./application/third_party/CI-Bundle/CI_Bundle.php */
