<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (! function_exists('add_bundle_package')) 
{
	function add_bundle_package($name)
	{
		$ci =& get_instance();
		if (is_dir(BUNDLEPATH.$name)) 
		{
			$ci->load->add_package_path(BUNDLEPATH.$name);
		}
	}
}

/* End of file bundle_helper.php */
/* Location: ./application/helpers/bundle_helper.php */
