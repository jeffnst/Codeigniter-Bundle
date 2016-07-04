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
 * Bundle Hooks Class
 *
 * Extends CI_Hooks class for implement a Modular Environment.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Hooks
 * @author		David Sosa Valdes
 * @link		https://github.com/davidsosavaldes/Codeigniter-Bundle
 */
class Bundle_Hooks extends CI_Hooks
{
	/**
	 * Add new hooks anytime from anywhere
	 * 
	 * @param string $path Absolute path (according to CI standards)
	 */
	public function add($path = '')
	{
		if (config_item('enable_hooks') !== FALSE) 
		{
			$path = rtrim($path, '/');

			if (file_exists($path.'/config/hooks.php')) 
			{
				include($path.'/config/hooks.php');
			}	
			if (file_exists($path.'/config/'.ENVIRONMENT.'/hooks.php'))
			{
				include($path.'/config/'.ENVIRONMENT.'/hooks.php');
			}
			// If there are no hooks, we're done.
			if ( ! isset($hook) OR ! is_array($hook))
			{
				return;
			}
			// Name collisions
			$this->hooks = array_merge_recursive($hook, $this->hooks);
			return $this->enabled = TRUE;
		}
		return FALSE;
	}

	/**
	 * Run Hook
	 *
	 * Runs a particular hook
	 *
	 * @param	array	$data	Hook details
	 * @return	bool	TRUE on success or FALSE on failure
	 */
	protected function _run_hook($data)
	{
		if (is_array($data)) 
		{
			$a_path = APPPATH.trim($data['filepath'],'/').'/'.$data['filename'];
			
			if (! file_exists($a_path)) 
			{
				// Let's try to load a hook outside
				$repeater = rtrim(str_repeat('../', substr_count(APPPATH, '/')),'/');
				$b_path = $repeater.'/'.trim($data['filepath'],'/');

				if (realpath(APPPATH.$b_path)) 
				{
					$data['filepath'] = rtrim($b_path,'/').'/';
				}
			}
		}
		return parent::_run_hook($data);
	}
}

/* End of file Bundle_Hooks.php */
/* Location: ./application/core/Bundle_Hooks.php */
