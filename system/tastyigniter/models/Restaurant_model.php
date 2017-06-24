<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Languages Model Class
 *
 * @category       Models
 * @package        TastyIgniter\Models\Restaurant_model.php
 * @link           
 */
class Restaurant_model extends TI_Model {

	public function getCount($filter = array()) {
		if ( ! empty($filter['filter_search'])) {
			$this->db->like('name', $filter['filter_search']);			
		}

		$this->db->from('restaurants');

		return $this->db->count_all_results();
	}

	public function getList($filter = array()) {
		if ( ! empty($filter['page']) AND $filter['page'] !== 0) {
			$filter['page'] = ($filter['page'] - 1) * $filter['limit'];
		}

		if ($this->db->limit($filter['limit'], $filter['page'])) {
			$this->db->from('restaurants');

			if ( ! empty($filter['sort_by']) AND ! empty($filter['order_by'])) {
				$this->db->order_by($filter['sort_by'], $filter['order_by']);
			}

			if ( ! empty($filter['filter_search'])) {
				$this->db->like('name', $filter['filter_search']);			
			}

			$query = $this->db->get();
			$result = array();
			if ($query->num_rows() > 0) {
				$result = $query->result_array();
			}

			return $result;
		}
	}

	public function getRestaurants() {

		$this->db->from('restaurants');

		$query = $this->db->get();

		$result = array();

		if ($query->num_rows() > 0) {
			$result = $query->result_array();
		}

		return $result;
	}

	public function getRestaurant($restaurant_id) {
		if ($language_id !== '') {
			$this->db->from('restaurants');
			$this->db->where('restaurant_id', $restaurant_id);
			$query = $this->db->get();
			if ($query->num_rows() > 0) {
				return $query->row_array();
			}
		}
	}

	public function saveRestaurant($restaurant_id, $save = array()) {
		if (empty($save)) return FALSE;

		if (isset($save['name'])) {
			$this->db->set('name', $save['name']);
		}

		if (is_numeric($restaurant_id)) {
			$this->db->where('restaurant_id', $restaurant_id);
			$query = $this->db->update('restaurants');
		} else {
			$query = $this->db->insert('restaurants');
			$restaurant_id = $this->db->insert_id();
		}

		return ($query === TRUE AND is_numeric($restaurant_id)) ? $restaurant_id : FALSE;
	}

	public function deleteRestaurant($restaurant_id) {
		 // TODO
	}
}
