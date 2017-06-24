<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Languages Model Class
 *
 * @category       Models
 * @package        TastyIgniter\Models\Staff_restaurant_model.php
 * @link           
 */
class Staff_restaurant_model extends TI_Model {

    function isStaffOnRestaurant($staff_id, $restaurant_id){
	   $this->db->from('staff_restaurant');
	   $this->db->where('restaurant_id', $restaurant_id);
       $this->db->where('staff_id', $staff_id);
	   $query = $this->db->get();
	   if ($query->num_rows() > 0) {
		return true;
	   }
       return false;
    }

	function getRestaurantListByStaff($staff_id){
	   $this->db->from('staff_restaurant');
	   $this->db->where('staff_id', $staff_id);	  
	   $query = $this->db->get();
	   $result = array();
	   if ($query->num_rows() > 0) {
		 $result = $query->result_array();
	   }
       return $result;
	}

	function setRestaurantListForStaff($restaurant_for_acces,$staff_id){

		// Obtener la lista que es account Manager para setearlo luego.
		$this->db->from('staff_restaurant');
		$this->db->where('staff_id', $staff_id);	  
	    $this->db->where('is_account_manager', true);
		$query = $this->db->get();
		$result = array();
		if ($query->num_rows() > 0) {
		    $result = $query->result_array();
		}
	
		$this->db->where_in('staff_id', $staff_id);		
		$this->db->delete('staff_restaurant');
		foreach ($restaurant_for_acces as $key => $value) {
			$this->db->set('restaurant_id' , $value);
			$this->db->set('staff_id'      , $staff_id);			
		 	$query = $this->db->insert('staff_restaurant');
		}

		foreach ($result as $key => $value) {
			$this->setAsAcountManager($value['staff_id'],$value['restaurant_id']);		
		}
		
	}

	function setAsAcountManager($staff_id,$restaurant_id){
		$query = FALSE;
		$this->db->set('is_account_manager', true);
		$this->db->where('staff_id', $staff_id);
		$this->db->where('restaurant_id', $restaurant_id);
		$query = $this->db->update('staff_restaurant');
		return $query;
	}


	function isStaffAccountManager($staff_id){
	   $this->db->from('staff_restaurant');	  
       $this->db->where('staff_id', $staff_id);
	   $this->db->where('is_account_manager', true);
	   $query = $this->db->get();
	   if ($query->num_rows() > 0) {
		return true;
	   }
       return false;
    }

}