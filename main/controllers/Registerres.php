<?php if ( ! defined('BASEPATH')) exit('No direct access allowed');

class Registerres extends Main_Controller {

	public function __construct() {
		parent::__construct(); 
        $this->load->library('location');
		$this->load->model('Settings_model');
		$this->load->model('Countries_model');
	
		$this->load->model('NotconfirmedMobile_model');
		$this->load->model('NotconfirmedEmail_model');	

		$this->load->model('Restaurant_model');	
		$this->load->model('Staff_restaurant_model');
		$this->load->model('Staffs_model');
		$this->load->model('Mealtimes_model');
		$this->load->model('Locations_model');

		$this->load->model('Setup_steps_model');
		$this->load->model('Setup_restaurant_complete_model');

		$this->load->library('user');
  	}

	public function index() {
		$this->lang->load('registerres');
 		$this->template->setTitle($this->lang->line('text_heading'));
		$this->template->setStyleTag('tastyigniter-orange/css/registerrestaurant.css');
		$this->template->setScriptTag('js/jquery.validate.min.js');
		$this->template->setScriptTag('js/registerrestaurant.js');
		if ($this->config->item('maps_api_key')) {
		  $data['map_key'] = '&key='. $this->config->item('maps_api_key');
		} else {
		  $data['map_key'] = '';
		}

		$setting = $this->Settings_model->getSetting('country_id');
		$id_country =  $setting['value'];

		$results = $this->Countries_model->getCountry($id_country);
		$data['country'] = $results;
	
		$this->template->setScriptTag('https://maps.googleapis.com/maps/api/js?v=3.20' . $data['map_key'] .'&sensor=false&region=GB&libraries=geometry', 'google-maps-js', '104330');
	 	$this->template->render('registerres', $data);
	}


	public function cheklocation(){
	    
		if(!empty($this->input->post('address_1'))){
		   $address['address_1'] = $this->input->post('address_1');
		}
		if(!empty($this->input->post('address_2'))){
		   $address['address_2'] = $this->input->post('address_2');
		}
		if(!empty($this->input->post('location_city'))){
		   $address['location_city'] = $this->input->post('location_city');
		}
		if(!empty($this->input->post('city'))){
		   $address['city'] = $this->input->post('city');
		}
		if(!empty($this->input->post('state'))){
		   $address['state'] = $this->input->post('state');
		}
		if(!empty($this->input->post('state'))){
		   $address['country'] = $this->input->post('country');
		}

		$output = $this->location->getLatLng($address);
		if($output == "INVALID_SEARCH_QUERY" || $output == "FAILED" ){
          
		   $result['succes'] = false;
		   $result['error']  = $output;
		   echo (json_encode($result));

		}else{
		   echo (json_encode($output));
		}
	}

	public function sendcodes(){
		$info['admin_email']       = $this->input->post('admin_email');
		$info['admin_mobilephone'] = $this->input->post('admin_mobilephone');

		//Validate if user exists 
		$results = $this->Staffs_model->findByEmail($info['admin_email']);

		if(count($results)>0){
			$response = new stdClass();
			$response->success = false;
			$response->error   = 'EMAIL_EXISTS';
			echo (json_encode($response));
			return;
		}
	
		if(!$this->NotconfirmedMobile_model->isActive($info['admin_mobilephone'])){
			// Save or create mobile code
			$arr_mobile['phone']     = $info['admin_mobilephone'];
			$arr_mobile['code']      = rand (1000,9999);
			$arr_mobile['confirmed'] = 0;
			$this->NotconfirmedMobile_model->saveOrCreate($arr_mobile);
			// Send code by SMS
			$this->SendSmsCode($arr_mobile);		
		}

		if(!$this->NotconfirmedEmail_model->isActive($info['admin_email'])){
			//Save or create email code
			$arr_email['email']     = $info['admin_email'];
			$arr_email['code']      = rand (100000,999999);
			$arr_email['confirmed'] = 0;
			//TODO  if ya esta en la base y estan en plazo de la configracion no ejecutar estas dos lineas de codigo
			$this->NotconfirmedEmail_model->saveOrCreate($arr_email);
			$this->SendEmailCode($arr_email);
		}

		$response = new stdClass();
		$response->success = true;
		echo (json_encode($response));
		return;
	}

	public function checkemailcode(){

		$email_code = $this->input->get('email_code');
		$email 		= $this->input->get('email');

		if($this->NotconfirmedEmail_model->validate($email,$email_code)){
			echo 'true';
		}else{
		  	echo 'false';
		}		
		return;

	}

	public function checkphonecode(){

		$mobile_code  = $this->input->get('mobile_code');
		$mobile 	  = $this->input->get('mobile');
				
		if($this->NotconfirmedMobile_model->validate($mobile,$mobile_code)){
		  echo 'true';		
		}else{
		  echo 'false';		
		}	
		return;
	}

	public function register(){	

		$restaurant_lat  	        = $this->input->post('restaurant_lat');
		$restaurant_long 	        = $this->input->post('restaurant_long');
		$restaurant_location_name   = $this->input->post('restaurant_location_name');
		$restaurant_email 		    = $this->input->post('restaurant_email');
		$restaurant_telephone 	    = $this->input->post('restaurant_telephone');
		$restaurant_address_1 	    = $this->input->post('restaurant_address_1');
        $restaurant_address_2 	    = $this->input->post('restaurant_address_2');

		$restaurant_city 	        = $this->input->post('restaurant_city');
        $restaurant_state 	        = $this->input->post('restaurant_state');
	    $restaurant_postcode 	    = $this->input->post('restaurant_postcode');
        $restaurant_country 	    = $this->input->post('restaurant_country');
	
	    $admin_first_name 	        = $this->input->post('admin_first_name');
		$admin_last_name 	        = $this->input->post('admin_last_name');

		$admin_email 	            = $this->input->post('admin_email');
		$admin_mobilephone 	        = $this->input->post('admin_mobilephone');
        $admin_password 	        = $this->input->post('admin_password');
			
		$mobile_code 	            = $this->input->post('mobile_code');
		$email_code 	            = $this->input->post('email_code');
		

		$response = new stdClass();

		// Mark as confirmed email validation

		if($this->NotconfirmedEmail_model->validate($admin_email,$email_code)){			
			$this->NotconfirmedEmail_model->confirm($admin_email);
		}else{
			//Fire Error 
			$response->success = false;
			$response->error   = 'EMAIL_CODE_INVALID';
			echo (json_encode($response));
			return;
		}

		// Mark as confirmed sms validation

		if($this->NotconfirmedMobile_model->validate($admin_mobilephone,$mobile_code)){			
			$this->NotconfirmedMobile_model->confirm($admin_mobilephone);
		}else{
			//Fire Error 
			$response->success = false;
			$response->error   = 'SMS_CODE_INVALID';
			echo (json_encode($response));
			return;
		}

		// Insert Restaurant
		$restaurat = array();
		$restaurat['name'] = $restaurant_location_name;
        $id_restaurant = $this->Restaurant_model->saveRestaurant(null,$restaurat);

		// Insert Location
	    $location['location_name']           = $restaurant_location_name;
	    $location['address']['address_1']    = $restaurant_address_1;
        $location['address']['address_2']    = $restaurant_address_2;
		$location['address']['city']         = $restaurant_city;
		$location['address']['state']        = $restaurant_state;
	    $location['address']['postcode']     = $restaurant_postcode;
		$location['address']['country']      = $restaurant_country;
		$location['address']['location_lat'] = $restaurant_lat;
		$location['address']['location_lng'] = $restaurant_long;
		$location['email'] 				     = $restaurant_email;
		$location['telephone']               = $restaurant_telephone;
		$location['location_status']         = '1';

		$location_id = $this->Locations_model->saveLocation(null,$location,$id_restaurant);
		
		// Insert Staft  and User
		$staff    = array();
		$staff['staff_name']     = $admin_first_name. ' ' . $admin_last_name ;
		$staff['staff_email']    = $admin_email;
		$staff['staff_group_id'] = 12;
		$staff['rest_default']   = $id_restaurant;
		$staff['staff_status']   = '1'; // Active
		$staff['password'] 		= $admin_password;
		$staff['username']      = $admin_email;
		
		$id_staff = $this->Staffs_model->saveStaff(null,$staff);

		// Insert Staf Restaurnt 
		$arr = array();
		$arr[]= $id_restaurant;
		$this->Staff_restaurant_model->setRestaurantListForStaff($arr,$id_staff);

		//Set as Account Manager
		$this->Staff_restaurant_model->setAsAcountManager($id_staff,$id_restaurant);

		// Insert Mealtimes.
		$this->Mealtimes_model->initForRestaurant($id_restaurant);
	
		// Login user
		$this->user->login($admin_email, $admin_password);

		// Add Check list to restaurant
	    $steps = $this->Setup_steps_model->getAll();
		$this->Setup_restaurant_complete_model->initStepsForRestaurant($steps,$id_restaurant);

		// TODO $this->load->model('Setup_restaurant_complete_model');

		$response->success = true;		
		echo (json_encode($response));
		return;
	}

    public function SendSmsCode($arr_mobile){
		// TODO
	}

	public function SendEmailCode($arr_email){
        //TODO
	}


	
}