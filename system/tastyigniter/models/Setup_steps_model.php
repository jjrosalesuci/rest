<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Setup_steps_model Model Class
 *
 * @category       Models
 * @package        TastyIgniter\Models\Setup_steps_model.php
 * @link           
 */
class Setup_steps_model extends TI_Model { 
	function getAll(){
	   $this->db->from('setup_steps');	   
	   $query = $this->db->get();
	   $result = array();
	   if ($query->num_rows() > 0) {
		 $result = $query->result_array();
	   }
       return $result;
	}
}