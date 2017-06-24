<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Languages Model Class
 *
 * @category       Models
 * @package        TastyIgniter\Models\Staff_restaurant_model.php
 * @link           
 */
class Orders_versions_model extends TI_Model {
	
	function getMaxVersionForRestaurat($restaurant_id){
	   $this->db->select_max('version_id');
	   $this->db->where('restaurant_id', $restaurant_id);
	   $res = $this->db->get('orders_versions');
	   if ($res->num_rows() > 0) {
		 $result = $res->result_array();
		 return $result[0]['version_id'];
	   }
	   return 0;
	}

	function getOrdersWithChanges($version,$restaurant_id){
	
	  $this->db->select('*, orders.status_id, status_name, status_color, orders.date_added, orders.date_modified');
	  $this->db->from('orders_versions');
      $this->db->join('orders','orders_versions.order_id = orders.order_id', 'left');
      $this->db->join('statuses', 'statuses.status_id = orders.status_id', 'left');
      $this->db->join('locations', 'locations.location_id = orders.location_id', 'left');
  	
	  $this->db->where('version_id >',(int)$version);
      $this->db->where($this->db->dbprefix('orders_versions').'.restaurant_id', $restaurant_id);
	  $this->db->order_by('version_id');
	  
	  $query = $this->db->get();
	  $result = array();
	  if ($query->num_rows() > 0) {
		 $result = $query->result_array();
	  }
      return $result;
	}
	
	

    
}