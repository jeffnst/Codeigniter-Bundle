<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bundle_Controller extends CI_Controller 
{
	/**
	 * Autoload listed components
	 * @var array
	 */
	public $autoload = array();

	/**
	 * Constructor
	 * 
	 * Call the bundle package
	 * @return	void
	 */
	public function __construct()
	{
		$this->load =& load_class('Loader', 'core');

		$this->_autoload();
		$this->load->bundle();

		log_message('info', 'Bundle Controller Class Initialized');		

		parent::__construct();
	}

	/**
	 * Bundle Autoloader
	 * 
	 * Loads component listed in the $autoload attribute.
	 * @return	void
	 */
	protected function _autoload()
	{
		foreach ($this->autoload as $loader => $packages) 
		{
			switch ($loader) 
			{
				case 'packages':
					foreach ($packages as $package_path) 
					{
						$this->load->add_package_path($package_path);
					}
					break;

				case 'libraries':
					if (in_array('database', $packages)) 
					{
						$this->load->database();
						$packages = array_diff($packages, array('database'));				
					}
					$this->load->library($packages);
					break;

				case 'drivers':
					foreach ($packages as $item)
					{
						$this->load->driver($item);
					}
					break;

				case 'helper':
					(! empty($packages)) && $this->load->helper($packages);
					break;

				case 'config':
					foreach ($packages as $val)
					{
						$this->load->config($val);
					}
					break;

				case 'language':
					(! empty($packages)) && $this->load->language($packages);
					break;

				case 'model':
					$this->load->model($packages);
					break;
				case 'bundles':
					foreach ($packages as $package => $params) 
					{
						if (is_numeric($package)) 
						{
							$this->load->bundle($params);
						}
						else
						{
							$this->load->bundle($package, TRUE, $params);
						}
					}
					break;
			}
		}		
	}
}

/* End of file Bundle_Controller.php */
/* Location: ./application/core/Bundle_Controller.php */