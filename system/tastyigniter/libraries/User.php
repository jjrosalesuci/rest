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
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User Class
 *
 * @category       Libraries
 * @package        TastyIgniter\Libraries\User.php
 * @link           http://docs.tastyigniter.com
 */
class User {

	private $is_logged = FALSE;
	public  $is_super_admin = FALSE;
	public  $restaurant_id  = null;
	public  $restaurant_name = "";
	public  $restaurant_list = array();
    private $user_id;
    private $username;
    private $staff_id;
    private $permissions = array();
    private $permitted_actions = array();
    private $available_actions = array();
    private $staff_name;
    private $staff_email;
    private $staff_group_name;
    private $staff_group_id;
    private $location_id;
    private $location_name;
    private $customer_account_access;
    private $location_access;
	private $unread;

    public function __construct() {
		$this->CI =& get_instance();
		$this->CI->load->database();

		$this->initialize();
    }

	public function initialize() {
        $user_info = $this->CI->session->userdata('user_info');

        if ( !empty($user_info['user_id']) AND !empty($user_info['username']) AND !empty($user_info['email'])) {
            $this->CI->db->from('users');
            $this->CI->db->join('staffs', 'staffs.staff_id = users.staff_id', 'left');
            $this->CI->db->join('staff_groups', 'staff_groups.staff_group_id = staffs.staff_group_id', 'left');
            $this->CI->db->join('locations', 'locations.location_id = staffs.staff_location_id', 'left');

            $this->CI->db->where('user_id', $user_info['user_id']);
            $this->CI->db->where('username', $user_info['username']);
            $this->CI->db->where('staff_email', $user_info['email']);
            $this->CI->db->where('staff_status', '1');
            $query = $this->CI->db->get();

            if ($query->num_rows() === 1) {
                foreach ($query->row_array() as $key => $value) {
                    if (property_exists($this, $key)) {
                        $this->$key = $value;
                    }
                }

                $this->setPermissions();

                $this->is_logged = TRUE;

				if($user_info['is_super_admin']==TRUE){
					$this->is_super_admin = TRUE;
				}else{
					$this->restaurant_id    = $user_info['restaurant_id'];
					$this->restaurant_name  = $user_info['restaurant_name'];				
					$this->restaurant_list  = $user_info['restaurant_list'];
				}
            }
        }

        if (!$this->is_logged) $this->logout();
    }

	public function login($user, $password) {
		$this->CI->db->from('users');
		$this->CI->db->join('staffs', 'staffs.staff_id = users.staff_id', 'left');

		$this->CI->db->where('username', strtolower($user));
		$this->CI->db->where('password', 'SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1("' . $password . '")))))', FALSE);
		$this->CI->db->where('staff_status', '1');

		$query = $this->CI->db->get();

		//Login Successful
		if ($query->num_rows() === 1) {
			$row = $query->row_array();

			$user_data = array(
				'user_id'  			=> $row['user_id'],
				'username'     		=> $row['username'],
				'email'     		=> $row['staff_email']				
			);

			// Nuew validations
			if($row['is_super_admin']=='1'){
				$user_data['is_super_admin'] = true;
				$this->is_super_admin  = true;
			}else{
				$user_data['is_super_admin'] = false;
				$this->is_super_admin  = false;
				// if not have restaurant return false
				if($row['restaurant_id']==null){
					return false;
				}
				// if have a restaurant put this id on session.
				$user_data['restaurant_id'] = $row['restaurant_id'];
				$this->restaurant_id = $row['restaurant_id'];

				// load restaurant name

				$this->CI->db->from('restaurants');			
				$this->CI->db->where('restaurant_id', $this->restaurant_id);			
				$query_rest = $this->CI->db->get();

				if ($query_rest ->num_rows() === 1) {
					$restaurant = $query_rest->row_array();
					$user_data['restaurant_name'] = $restaurant['name'];
					$this->restaurant_name        = $restaurant['name'];

					// Load List restaurant user have acces
					$this->CI->db->from('staff_restaurant');
					$this->CI->db->join('restaurants', 'staff_restaurant.restaurant_id = restaurants.restaurant_id', 'left');
					$this->CI->db->where('staff_id', $row['staff_id']);			
					$query_staff_restaurant = $this->CI->db->get();
					if ($query_staff_restaurant ->num_rows() >= 1) {
						$list_restaurants = $query_staff_restaurant->result_array();
					    $user_data['restaurant_list'] = $list_restaurants;					
						$this->restaurant_list        =  $list_restaurants;
					}
				}
			}	

			$this->CI->session->set_userdata('user_info', $user_data);

			$this->user_id 		   = $row['user_id'];
			$this->username 	   = $row['username'];
			$this->staff_id 	   = $row['staff_id'];
			$this->staff_name 	   = $row['staff_name'];
			$this->staff_email 	   = $row['staff_email'];	

	  		return TRUE;
		} else {
      		return FALSE;
		}
	}

	public function setCurrentRestaurant($restaurant_id,$restaurant_name){
		 $user_info = $this->CI->session->userdata('user_info');
		 $user_info['restaurant_id'] = $restaurant_id;
		 $user_info['restaurant_name'] = $restaurant_name;
		 $this->CI->session->set_userdata('user_info', $user_info);
	}

    public function restrict($permission, $uri = '') {
	    // If user isn't logged in, redirect to the login page.
	    if ( ! $this->is_logged AND $this->uri->rsegment(1) !== 'login') redirect(admin_url('login'));

	    // Check whether the user has the proper permissions action.
	    if (($has_permission = $this->checkPermittedActions($permission, TRUE)) === TRUE) return TRUE;

	    if ($uri === '') { // get the previous page from the session.
		    $uri = referrer_url();

		    // If previous page and current page are the same, but the user no longer
		    // has permission, redirect to site URL to prevent an infinite loop.
		    if (empty($uri) OR $uri === current_url() AND !$this->CI->input->post()) {
			    $uri = site_url();
		    }
	    }

	    if (!$this->CI->input->is_ajax_request()) {  // remove later
		    redirect($uri);
	    }
    }

  	public function isLogged() {
        return $this->is_logged;
	}

  	public function getId() {
		return $this->user_id;
  	}

  	public function getUserName() {
    	return $this->username;
  	}

  	public function getStaffId() {
    	return $this->staff_id;
  	}

  	public function getStaffName() {
    	return $this->staff_name;
  	}

  	public function getStaffEmail() {
    	return $this->staff_email;
  	}
	
	public function getCurrentRestaurantName(){
		return $this->restaurant_name;
	}

	public function getRestauratList(){
		return $this->restaurant_list;
	}

  	public function getLocationId() {
    	return $this->location_id;
  	}

  	public function getLocationName() {
    	return $this->location_name;
  	}

  	public function staffGroup() {
    	return $this->staff_group_name;
  	}

  	public function getStaffGroupId() {
    	return $this->staff_group_id;
  	}

	public function isStrictLocation() {
		return ($this->location_access == '1') ? TRUE : FALSE;
	}

	public function canAccessCustomerAccount() {
		return ($this->customer_account_access == '1') ? TRUE : FALSE;
	}

    public function unreadMessageTotal() {
        if (empty($this->unread)) {
            $this->CI->load->model('Messages_model');
            $this->unread = $this->CI->Messages_model->getUnreadCount($this->staff_id);
        }

        return $this->unread;
    }

    public function hasPermission($permission) {
	    if ( ! $this->is_logged) return FALSE;

	    return $this->checkPermittedActions($permission);
    }

    private function setPermissions() {
        $group_permissions = (!empty($this->permissions)) ? @unserialize($this->permissions) : NULL;

        if (is_array($group_permissions)) {
            $this->CI->load->model('Permissions_model');
            $permissions = $this->CI->Permissions_model->getPermissionsByIds();

            foreach ($permissions as $permission_id => $permission) {
                $this->available_actions[$permission['name']] = $permissions[$permission_id]['action'];
            }

            foreach ($group_permissions as $permission_id => $permitted_actions) {
                if (!empty($permissions[$permission_id]['name'])  AND $permission_name = $permissions[$permission_id]['name']) {
                    $intersect = array_intersect($permitted_actions, $this->available_actions[$permission_name]);
                    if (!empty($intersect)) $this->permitted_actions[$permission_name] = $permitted_actions;
                }
            }
        }
    }

    private function checkPermittedActions($perm, $display_error = FALSE) {
	    $action = $this->getPermissionAction($perm);

	    // Ensure the permission string is matches pattern Domain.Context
	    $perm = (substr_count($perm, '.') === 2) ? substr($perm, 0, strrpos($perm, '.')) : $perm;

	    $available_actions = (isset($this->available_actions[$perm]) AND is_array($this->available_actions[$perm])) ? $this->available_actions[$perm] : array();
	    $permitted_actions = (isset($this->permitted_actions[$perm]) AND is_array($this->permitted_actions[$perm])) ? $this->permitted_actions[$perm] : array();

	    // Success if the staff_group_id is the default one
	    if ($this->staff_group_id === '11') return TRUE;

	    foreach ($action as $value) {

		    // Fail if action is not available or permitted.
		    if (in_array($value, $available_actions) AND !in_array($value, $permitted_actions)) {
			    if ($display_error) {
				    $context = substr($perm, strpos($perm, '.')+1);

				    $this->CI->alert->set('warning', sprintf($this->CI->lang->line('alert_user_restricted'), $value, $context));
			    }

			    return FALSE;
		    }
	    }

	    return TRUE;
    }

	private function getPermissionAction($permission) {
		$action = array();

		if (substr_count($permission, '.') === 2) {
			$action[] = strtolower(substr($permission, strrpos($permission, '.')+1));
		} else {
			// Specify the requested action if not present, based on the $_SERVER REQUEST_METHOD
			if ($this->CI->input->server('REQUEST_METHOD') === 'POST') {
				if ($this->CI->input->post('delete')) {
					$action = array('access', 'delete');
				} else if (is_numeric($this->CI->input->get('id'))) {
					$action = array('access', 'manage');
				} else {
					$action = array('access', 'add');
				}
			} else if ($this->CI->input->server('REQUEST_METHOD') === 'GET') {
				$action = array('access');
			}
		}

		return $action;
	}

    public function logout() {
		$this->CI->session->unset_userdata('user_info');

		$this->is_logged = FALSE;
		$this->user_id = '';
		$this->username = '';
		$this->staff_id = '';
		$this->staff_name = '';
		$this->staff_group_name = '';
		$this->staff_group_id = '';
		$this->location_id = '';
		$this->location_name = '';
		$this->customer_account_access = '';
	}
}

// END User Class

/* End of file User.php */
/* Location: ./system/tastyigniter/libraries/User.php */