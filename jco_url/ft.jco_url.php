<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Jco_url_ft Class
 *
 * @author    J&eacute;r&ocirc;me Coup&eacute; and Pierre-Vincent Ledoux
 * @copyright Copyright (c) 2011 La casa productions
 * @license   http://creativecommons.org/licenses/by-sa/3.0/
 *
 */

class Jco_url_ft extends EE_Fieldtype {

	public $info = array(
		'name'		=> 'JCO URL',
		'version'	=> '1.0'
	);
	
	
	
	/*
	* --------------------------------------------------------------
	* PUBLIC METHODS
	* --------------------------------------------------------------
	*/
	
	//Install: uses default uninstall method
	
	//Uninstall: uses default uninstall method
		
	/**
	* Display field
	* @access public
	* @param string
	* @return string
	*/
	public function display_field($data)
	{
		$data = ($data == '') ? 'http://' : $data;
		
		echo "<p> SETTINGS".var_dump($this->settings)."</p>";
		
		return form_input(array(
			'name'	=> $this->field_name,
			'id'	=> $this->field_id,
			'value'	=> $data
		));
	}
	
	/**
	* Regexp validation of URL + check http headers
	* Will validate if file mandatory OR if value is different than "http://"
	*
	* @access public
	* @param string
	* @return mixed
	*/
	public function validate($data)
	{
		//check if field is mandatory OR if value != "http://"
		if ($this->settings['field_required'] == "y" OR $data != "http://")
		{
			//Load Helpers and Language files
			$this->EE->load->helper('url');
			$this->EE->lang->loadfile('jco_url');
			
			//sanitise URL with URL helper (add http:// if not there)
			$data = prep_url($data);
			
			//syntax check using regexp
			if (!$this->_url_check_syntax($data))
			{
				return lang('jco_url_badsyntax');
			}
			
			//check if page exists by checking returned http headers
			if (!$this->_url_check_headers($data))
			{
				return lang('jco_url_doesnotexist');
			}
			
			return TRUE;
		}
		else
		{
			return TRUE;
		}
	}
	
	/**
	* Save data: prep url if people forgot the http:// bit
	* Will save blank value if file is not mandatory and if value == "http://"
	*
	* @access public
	* @param string
	* @return string
	*/
	
	public function save($data)
	{
		//check if field is mandatory OR if value != "http://"
		if ($this->settings['field_required'] == "y" OR $data != "http://")
		{
			//sanitise URL with URL helper (add http:// if not there)
			$data = prep_url($data);
			return $data;
		}
		else
		{
			$data = NULL;
			return $data;
		}
	}
	
	/*
	* --------------------------------------------------------------
	* PRIVATE METHODS
	* --------------------------------------------------------------
	*/
	
	/**
	* Check if URL exists by checking http headers
	*
	* @access private
	* @param string
	* @return bool
	* @author: Pierre-Vincent Ledoux (thanks, mate! These recursive wierd arrays of headers were driving me up the wall)
	*/
	private function _url_check_headers($url)
	{
		@ $headers = get_headers($url, 1);
	
		if (empty($headers))
		{
			return FALSE; 
		}
		
		foreach($headers as $key => $header)
		{
			if (is_numeric($key))
			{
				$http_code = preg_match('/^HTTP\/\d\.\d\s+(200|301|302)/', $header);
			}
		}
		
		return ($http_code != 0) ? TRUE : FALSE;
	}
	
	/**
	* Check URL syntax using regexp
	*
	* @access private
	* @param string
	* @return bool
	*/
	private function _url_check_syntax($url)
	{
		$regexp = "/^"; // BEGIN
		$regexp .= "((https?|ftp)\:\/\/)?"; // SCHEME
		$regexp .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
		$regexp .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP
		$regexp .= "(\:[0-9]{2,5})?"; // Port
		$regexp .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
		$regexp .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
		$regexp .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor 
		$regexp .= "/"; // END
		
		return (preg_match($regexp, $url)) ? TRUE : FALSE;
	}
}
// END Jco_url_ft class

/* End of file ft.jco_url.php */
/* Location: ./system/expressionengine/third_party/jco_url/ft.jco_url.php */