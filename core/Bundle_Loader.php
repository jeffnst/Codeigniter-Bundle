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
}
