<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bundle_Controller extends CI_Controller 
{

	/**
	 * Autoload listed components
	 * @var array
	 */
	public $autoload = array();

	/**
	 * Call the bundle package
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_autoload();

		$this->load->helper('bundle');
		
		if ($bundle_path = config_item('active_bundle_path'))
		{
			add_bundle_package($bundle_path);
		}
	}

	/**
	 * Bundle Autoloader
	 *
	 * Loads component listed in the $autoload attribute.
	 *
	 * @used-by	Bundle_Controller::__construct()
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
						$this->add_package_path($package_path);
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
						$this->driver($item);
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
			}
		}		
	}
}

/* End of file Bundle_Controller.php */
/* Location: ./application/core/Bundle_Controller.php */