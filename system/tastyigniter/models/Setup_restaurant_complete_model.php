<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Setup_steps_model Model Class
 *
 * @category       Models
 * @package        TastyIgniter\Models\Setup_steps_model.php
 * @link           
 */
class Setup_restaurant_complete_model extends TI_Model {

    function getStepsForRestaurant($restaurant_id){
        $this->db->from('setup_restaurant_complete');
        $this->db->join('setup_steps', 'setup_steps.id = setup_restaurant_complete.id_step');
        $this->db->where('id_restaurant', $restaurant_id);	   
	    $query = $this->db->get();
	    $result = array();
	    if ($query->num_rows() > 0) {
		 $result = $query->result_array();
	    }
        return $result;
	}

    function initStepsForRestaurant($arr_steps,$restaurant_id){
      foreach($arr_steps as $index => $value ){         
        $this->db->set('id_restaurant',$restaurant_id);
        $this->db->set('id_step', $value['id']);
        $this->db->set('completed', 0);
        $this->db->insert('setup_restaurant_complete');
      }
    }

}