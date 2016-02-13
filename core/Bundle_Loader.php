<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bundle_Loader extends CI_Loader 
{
	/**
	 * CI-Hooks class
	 * @var object
	 */
	protected $_hooks;

	/**
	 * Class Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_hooks =& load_class('Hooks', 'core');
	}

	/**
	 * CI Autoloader
	 *
	 * Loads component listed in the config/autoload.php file.
	 * Loads component listed in the <active bundle>/config/autoload.php file
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

		if (file_exists(BUNDLEPATH.config_item('active_bundle').'/config/autoload.php')) 
		{
			include(BUNDLEPATH.config_item('active_bundle').'/config/autoload.php');
		}

		if (file_exists(BUNDLEPATH.config_item('active_bundle').'/config/'.ENVIRONMENT.'/autoload.php')) 
		{
			include(BUNDLEPATH.config_item('active_bundle').'/config/'.ENVIRONMENT.'/autoload.php');
		}

		if ( ! isset($autoload))
		{
			return;
		}

		// Autoload packages
		if (isset($autoload['packages']))
		{
			foreach ($autoload['packages'] as $package_path)
			{
				$this->add_package_path($package_path);
			}
		}

		// Load any custom config file
		if (count($autoload['config']) > 0)
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

		// Autoload Bundles
		if (isset($autoload['bundles'])) 
		{
			foreach ($autoload['bundles'] as $package => $params) 
			{
				if (is_numeric($package)) 
				{
					$this->bundle($params);
				}
				else
				{
					$this->bundle($package, TRUE, $params);
				}
			}
		}
	}

	/**
	 * Load a Bundle
	 * 
	 * @param  string  $path         Bundle name
	 * @param  boolean $view_cascade View cascade method active
	 * @param  array   $params 		 Bundle params passed with autoload.php file
	 * @return object
	 */
	public function bundle($path = '', $view_cascade = TRUE, array $params = array())
	{
		if ((! empty($path) && is_dir($bundle_path = BUNDLEPATH.$path))) 
		{
			$this->add_package_path($bundle_path, $view_cascade);
			spl_autoload_register(function($class) use ($bundle_path) 
			{
				if (file_exists($bundle_core_class = $bundle_path.'/core/'.$class.'.php')) 
				{
					require_once($bundle_core_class);
				}
			});
		}
		else 
		{
			// We don't autoload the core classes here 
			$bundle_path = BUNDLEPATH.config_item('active_bundle');
			$this->add_package_path($bundle_path, $view_cascade);
			$params['enable_hooks'] = TRUE;
		}
		if (isset($params['enable_hooks']) && $params['enable_hooks'] !== FALSE) 
		{
			$this->_hooks->add_hooks($bundle_path);	
		}
		return $this;
	}

	/**
	 * Internal CI Library Instantiator
	 *
	 * @used-by	CI_Loader::_ci_load_stock_library()
	 * @used-by	CI_Loader::_ci_load_library()
	 *
	 * @param	string		$class		Class name
	 * @param	string		$prefix		Class name prefix
	 * @param	array|null|bool	$config		Optional configuration to pass to the class constructor:
	 *						FALSE to skip;
	 *						NULL to search in config paths;
	 *						array containing configuration data
	 * @param	string		$object_name	Optional object name to assign to
	 * @return	void
	 */
	protected function _ci_init_library($class, $prefix, $config = FALSE, $object_name = NULL)
	{
		// Is there an associated config file for this class? Note: these should always be lowercase
		if ($config === NULL)
		{
			// Fetch the config paths containing any package paths
			$config_component = $this->_ci_get_component('config');

			if (is_array($config_component->_config_paths))
			{
				foreach ($config_component->_config_paths as $path)
				{
					// We test for both uppercase and lowercase, for servers that
					// are case-sensitive with regard to file names. Load global first,
					// override with environment next
					if (file_exists($path.'config/'.strtolower($class).'.php'))
					{
						include($path.'config/'.strtolower($class).'.php');
					}
					elseif (file_exists($path.'config/'.ucfirst(strtolower($class)).'.php'))
					{
						include($path.'config/'.ucfirst(strtolower($class)).'.php');
					}

					if (file_exists($path.'config/'.ENVIRONMENT.'/'.strtolower($class).'.php'))
					{
						include($path.'config/'.ENVIRONMENT.'/'.strtolower($class).'.php');
					}
					elseif (file_exists($path.'config/'.ENVIRONMENT.'/'.ucfirst(strtolower($class)).'.php'))
					{
						include($path.'config/'.ENVIRONMENT.'/'.ucfirst(strtolower($class)).'.php');
					}
				}
			}
		}

		$class_name = $prefix.$class;

		// Is the class name valid?
		if ( ! class_exists($class_name, FALSE))
		{
			log_message('error', 'Non-existent class: '.$class_name);
			show_error('Non-existent class: '.$class_name);
		}

		// Set the variable name we will assign the class to
		// Was a custom class name supplied? If so we'll use it
		if (empty($object_name))
		{
			$object_name = strtolower($class);
			if (isset($this->_ci_varmap[$object_name]))
			{
				$object_name = $this->_ci_varmap[$object_name];
			}
		}

		// Don't overwrite existing properties
		$CI =& get_instance();
		if (isset($CI->$object_name))
		{
			if ($CI->$object_name instanceof $class_name)
			{
				log_message('debug', $class_name." has already been instantiated as '".$object_name."'. Second attempt aborted.");
				return;
			}

			show_error("Resource '".$object_name."' already exists and is not a ".$class_name." instance.");
		}

		// Save the class name and object name
		$this->_ci_classes[$object_name] = $class;

		// Instantiate the class
		$CI->$object_name = isset($config)
			? new $class_name($config)
			: new $class_name();
	}	
}
