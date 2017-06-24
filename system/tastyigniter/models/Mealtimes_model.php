<?php
/**
 * TastyIgniter
 *
 * An open source online ordering, reservation and management system for restaurants.
 *
 * @package   TastyIgniter
 * @author    SamPoyigi
 * @copyright TastyIgniter
 * @link      http://tastyigniter.com
 * @license   http://opensource.org/licenses/GPL-3.0 The GNU GENERAL PUBLIC LICENSE
 * @since     File available since Release 1.0
 */
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Mealtimes Model Class
 *
 * @category       Models
 * @package        TastyIgniter\Models\Mealtimes_model.php
 * @link           http://docs.tastyigniter.com
 */
class Mealtimes_model extends TI_Model {

	public function getMealtimes($restaurant_id = null) {
		$this->db->from('mealtimes');

		if (!empty($restaurant_id)) {
		   $this->db->where($this->db->dbprefix('mealtimes').'.restaurant_id', $restaurant_id);
		}

		$query = $this->db->get();
		$result = array();

		if ($query->num_rows() > 0) {
			$result = $query->result_array();
		}

		return $result;
	}

	public function getMealtime($mealtime_id) {
		$this->db->from('mealtimes');

		$this->db->where('mealtime_id', $mealtime_id);
		$query = $this->db->get();

		return $query->row_array();
	}

	public function updateMealtimes($mealtimes = array(),$restaurant_id = null) {
		$query = FALSE;

		if ( ! empty($mealtimes)) {
			foreach ($mealtimes as $mealtime) {

				$this->db->set('mealtime_name', $mealtime['mealtime_name']);
				$this->db->set('start_time', mdate('%H:%i', strtotime($mealtime['start_time'])));
				$this->db->set('end_time', mdate('%H:%i', strtotime($mealtime['end_time'])));
				$this->db->set('mealtime_status', $mealtime['mealtime_status']);
				
				if(!empty($restaurant_id)){
				  $this->db->set('restaurant_id', $restaurant_id);
				}

				if ( ! empty($mealtime['mealtime_id']) AND $mealtime['mealtime_id'] > 0) {
					$this->db->where('mealtime_id', $mealtime['mealtime_id']);
					$this->db->update('mealtimes');
				} else {
					$this->db->insert('mealtimes');
				}
			}

			$query = TRUE;
		}

		return $query;
	}

	public function initForRestaurant($id_restaurant){
	  
		$this->db->insert('mealtimes', array('mealtime_name' => 'Breakfast', 'start_time' => '07:00:00', 'end_time' => '10:00:00', 'mealtime_status' => '1','restaurant_id'=>$id_restaurant));
        $this->db->insert('mealtimes', array('mealtime_name' => 'Lunch', 'start_time' => '12:00:00', 'end_time' => '14:30:00', 'mealtime_status' => '1','restaurant_id'=>$id_restaurant));
        $this->db->insert('mealtimes', array('mealtime_name' => 'Dinner', 'start_time' => '18:00:00', 'end_time' => '20:00:00', 'mealtime_status' => '1','restaurant_id'=>$id_restaurant));

	}
}

/* End of file Mealtimes_model.php */
/* Location: ./system/tastyigniter/models/Mealtimes_model.php */