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
 * Bundle Loader Class
 *
 * Extends CI_Loader class for implement a Modular Environment.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Loader
 * @author		David Sosa Valdes
 * @link		https://github.com/davidsosavaldes/Codeigniter-Bundle
 */
class Bundle_Loader extends CI_Loader
{
	/**
	 * CI Autoloader
	 *
	 * Loads component listed in the config/autoload.php file.
	 *
	 * @used-by	CI_Loader::initialize()
	 * @return	void
	 */
	protected function _ci_autoloader()
	{
		if (file_exists(APPPATH.'config/autoload.php'))
		{
			include(APPPATH.'config/autoload.php');
		}

		if (file_exists(APPPATH.'config/'.ENVIRONMENT.'/autoload.php'))
		{
			include(APPPATH.'config/'.ENVIRONMENT.'/autoload.php');
		}

		isset($autoload) && $this->_set_autoloader($autoload);
	}

	public function autoloader($path = '')
	{
		if (file_exists($path.'config/autoload.php'))
		{
			include($path.'config/autoload.php');
		}

		if (file_exists($path.'config/'.ENVIRONMENT.'/autoload.php'))
		{
			include($path.'config/'.ENVIRONMENT.'/autoload.php');
		}

		isset($autoload) && $this->_set_autoloader($autoload);		
	}

	/**
	 * [_set_autoloader description]
	 * @param array $autoload [description]
	 */
	protected function _set_autoloader(array $autoload)
	{
		// Autoload packages
		if (isset($autoload['packages']))
		{
			foreach ($autoload['packages'] as $package_path)
			{
				$this->add_package_path($package_path);
			}
		}

		// Load any custom config file
		if (isset($autoload['config']) && count($autoload['config']) > 0)
		{
			foreach ($autoload['config'] as $val)
			{
				$this->config($val);
			}
		}

		// Autoload helpers and languages
		foreach (array('helper', 'language') as $type)
		{
			if (isset($autoload[$type]) && count($autoload[$type]) > 0)
			{
				$this->$type($autoload[$type]);
			}
		}

		// Autoload drivers
		if (isset($autoload['drivers']))
		{
			foreach ($autoload['drivers'] as $item)
			{
				$this->driver($item);
			}
		}

		// Load libraries
		if (isset($autoload['libraries']) && count($autoload['libraries']) > 0)
		{
			// Load the database driver.
			if (in_array('database', $autoload['libraries']))
			{
				$this->database();
				$autoload['libraries'] = array_diff($autoload['libraries'], array('database'));
			}

			// Load all other libraries
			$this->library($autoload['libraries']);
		}

		// Autoload models
		if (isset($autoload['model']))
		{
			$this->model($autoload['model']);
		}		
	}

	/**
	 * Load a Bundle
	 * 
	 * @param  string  $path         Bundle name
	 * @param  boolean $view_cascade View cascade method active
	 * @return object
	 */
	public function bundle($path, $view_cascade = TRUE)
	{
		if (is_dir($path = rtrim(BUNDLEPATH . str_replace(BUNDLEPATH, '', $path),'/').'/')) 
		{
			$this->add_package_path($path, $view_cascade);

			spl_autoload_register(function($class) use ($path) 
			{
				if (file_exists($path.'core/'.$class.'.php')) 
				{
					require_once($path.'core/'.$class.'.php');
				}
			});
		}
		return $this;
	}

}

/* End of file Bundle_Loader.php */
/* Location: ./application/core/Bundle_Loader.php */
