<?php if ( ! defined('BASEPATH')) exit('No direct access allowed');

class Monitor extends Admin_Controller {

	public function __construct() {
		parent::__construct();
        $this->lang->load('monitor_online');
        $this->load->model('Orders_model');
        $this->load->model('Orders_versions_model');
        $this->load->library('currency'); // load the currency library
  	}

	public function index() {
        $this->template->setTitle($this->lang->line('text_title'));
        $this->template->setHeading($this->lang->line('text_heading'));

        $this->template->setStyleTag('tastyigniter-blue/jqgrid/css/ui.jqgrid-bootstrap.css');       
        $this->template->setStyleTag('tastyigniter-blue/css/online-monitor.css');

		$this->template->setScriptTag('tastyigniter-blue/js/moment.min.js');
        $this->template->setScriptTag('tastyigniter-blue/jqgrid/js/i18n/grid.locale-en.js');
    	$this->template->setScriptTag('tastyigniter-blue/jqgrid/js/jquery.jqGrid.min.js');

		$this->template->render('monitor_online');
	}


	public function RecivedOrdersCuantity(){
		$response    = new stdClass();
		$filter['restaurant_id'] = $this->user->restaurant_id;
		$filter['filter_status']  = 11;

		$recived = $this->Orders_model->getCount($filter);

		$response->recived    = $recived;
		$response->success = true; 
		echo json_encode($response);
		return;	
	}

	function getTotalMinutes(DateInterval $int){
		return ($int->d * 24 * 60) + ($int->h * 60) + $int->i;
	}


    public function currentorders(){       
        $callback    = $this->input->get('callback');

		$isFromTimer = $this->input->post('isfromtimer');
		$version     = $this->input->post('version'); 
		$response    = new stdClass();
	
		
		/* First load monitor */
		if($isFromTimer == NULL){
		  	$response->version = $this->Orders_versions_model->getMaxVersionForRestaurat($this->user->restaurant_id);
			$filter['restaurant_id'] = $this->user->restaurant_id;
			$filter['only_actives']  = true;
			$orders = $this->Orders_model->getList($filter);
			$data['orders'] = array();
			foreach ($orders as $result) {
				$payment_title = '--';
				if ($payment = $this->extension->getPayment($result['payment'])) {
					$payment_title = !empty($payment['ext_data']['title']) ? $payment['ext_data']['title']: $payment['title'];
				}

				$data['orders'][] = array(
					'order_id'			=> $result['order_id'],
					'location_name'		=> $result['location_name'],
					'first_name'		=> $result['first_name'],
					'last_name'			=> $result['last_name'],
					'order_type' 		=> ($result['order_type'] === '1') ? $this->lang->line('text_delivery') : $this->lang->line('text_collection'),
					'payment'			=> $payment_title,
					'order_time'		=> mdate('%H:%i', strtotime($result['order_time'])),
					'deadline'			=> mdate('%H:%i', strtotime($result['order_time'])),
				//	'order_date'		=> day_elapsed($result['order_date']),
					'order_date'		=> $result['order_date'],
					'order_status'		=> $result['status_name'],
					'order_status_read'	=> $result['status_name'],
					'status_color'		=> $result['status_color'],
					'order_total'		=> $this->currency->format($result['order_total']),
					'date_added'		=> day_elapsed($result['date_added']),
					'edit' 				=> site_url('orders/edit?id=' . $result['order_id']),
					'status_priority'   => ($result['status_id'] == 11) ? 1 : 2
				);
			}

			foreach ($data['orders'] as $key => $row) {				
				if($row['status_priority']==1){
					$data['orders'][$key]['status_priority'] = $row['order_id'] * -1;
				}else{	
					$current   = new DateTime("now");
				    $order_t   = new DateTime($row['order_date'].' '.mdate('%H:%i', strtotime($row['order_time'])));	
					$interval  = $current - $order_t;
					$interval  = $current->diff($order_t);
					$minutes   =  ($this->getTotalMinutes($interval)) + 1000000;					
					$data['orders'][$key]['status_priority'] = $minutes;
				}
			}	

			foreach ($data['orders'] as $key => $row) {
				$aux[$key] = $row['status_priority'];
			}

			array_multisort($aux, SORT_ASC, $data['orders']);
			$response->rows    = $data['orders'];
		}

		/*Is timer*/
		if($isFromTimer == true){
			$order_with_changes =  $this->Orders_versions_model->getOrdersWithChanges($version,$this->user->restaurant_id);
			if(count($order_with_changes)==0){
				$response->version = $version;
				$response->rows    = [];
			}else{
				//version_id

				$temp_version =  (int)$version;

				$data['orders'] = array();

				foreach ($order_with_changes as $result) {
					$payment_title = '--';
					if ($payment = $this->extension->getPayment($result['payment'])) {
						$payment_title = !empty($payment['ext_data']['title']) ? $payment['ext_data']['title']: $payment['title'];
					}

					if((int)$result['version_id'] > (int)$temp_version ){
						$temp_version = $result['version_id'];
					}
					

					$data['orders'][] = array(
						'actionv' 			=> $result['actionv'],
						'status_code'       => $result['status_id'],						
						'order_id'			=> $result['order_id'],
						'location_name'		=> $result['location_name'],
						'first_name'		=> $result['first_name'],
						'last_name'			=> $result['last_name'],
						'order_type' 		=> ($result['order_type'] === '1') ? $this->lang->line('text_delivery') : $this->lang->line('text_collection'),
						'payment'			=> $payment_title,
						'order_time'		=> mdate('%H:%i', strtotime($result['order_time'])),
						'deadline'			=> mdate('%H:%i', strtotime($result['order_time'])),
						//'order_date'		=> day_elapsed($result['order_date']),
						'order_date'		=> $result['order_date'],
						'order_status'		=> $result['status_name'],
						'order_status_read'	=> $result['status_name'],
						'status_color'		=> $result['status_color'],
						'order_total'		=> $this->currency->format($result['order_total']),
						'date_added'		=> day_elapsed($result['date_added']),
						'edit' 				=> site_url('orders/edit?id=' . $result['order_id']),
						'status_priority'   => ($result['status_id'] == 11) ? 1 : 2
					);
				}

				$response->version = $temp_version;

				foreach ($data['orders'] as $key => $row) {				
					if($row['status_priority']==1){
						$data['orders'][$key]['status_priority'] = $row['order_id'] * -1;
					}else{	
						$current   = new DateTime("now");
						$order_t   = new DateTime($row['order_date'].' '.mdate('%H:%i', strtotime($row['order_time'])));	
						$interval  = $current - $order_t;
						$interval = $current->diff($order_t);
						$minutes =  ($this->getTotalMinutes($interval)) +1000000;					
						$data['orders'][$key]['status_priority'] = $minutes;
					}
				}		


				$response->rows    = $data['orders'];

			}
		}
       



		$response->success = true; 
		if($isFromTimer == true){
			echo json_encode($response);
			return;
		}
        echo ($callback.'('.json_encode($response).')');
        return;
    }

	

    public function options(){
       
		$order_info = $this->Orders_model->getOrder($this->input->get('id_order'),null,$this->user->restaurant_id);

        $data['cart_items'] = array();
		$cart_items = $this->Orders_model->getOrderMenus($order_info['order_id']);
        $menu_options = $this->Orders_model->getOrderMenuOptions($order_info['order_id']);
		foreach ($cart_items as $cart_item) {
			$option_data = array();

			if (!empty($menu_options)) {
				foreach ($menu_options as $menu_option) {
					if ($cart_item['order_menu_id'] === $menu_option['order_menu_id']) {
						$option_data[] = $menu_option['order_option_name'] . $this->lang->line('text_equals') . $this->currency->format($menu_option['order_option_price']);
					}
				}
			}

			$data['cart_items'][] = array(
				'id' 			=> $cart_item['menu_id'],
				'name' 			=> $cart_item['name'],
				'qty' 			=> $cart_item['quantity'],
				'price' 		=> $this->currency->format($cart_item['price']),
				'subtotal' 		=> $this->currency->format($cart_item['subtotal']),
				'comment' 		=> $cart_item['comment'],
				'options'		=> implode('<br /> ', $option_data)
			);
		}

		$data['totals'] = array();
		$order_totals = $this->Orders_model->getOrderTotals($order_info['order_id']);

		
		foreach ($order_totals as $total) {
			if ($order_info['order_type'] === '2' AND $total['code'] == 'delivery') {
				continue;
			}

			$data['totals'][] = array(
				'code'  => $total['code'],
				'title' => htmlspecialchars_decode($total['title']),
				'value' => $this->currency->format($total['value']),
				'priority' => $total['priority'],
			);
		}

	 

		$data['order_total'] 		= $this->currency->format($order_info['order_total']);
		$data['total_items']		= $order_info['total_items'];

		$this->load->view('monitor_online_order_detail', $data, false);
    
    }


	public function changeOrderStatus(){
		$orders     = explode( ',',$this->input->post('orders'));
		$status     = $this->input->post('status');
		
	/*	'11', 'Received', 'Your order has been received.', '1', 'order', '#686663'
		'12', 'Pending', 'Your order is pending', '1', 'order', '#f0ad4e'
		'13', 'Preparation', 'Your order is in the kitchen', '1', 'order', '#00c0ef'
		'14', 'Delivery', 'Your order will be with you shortly.', '0', 'order', '#00a65a'
		'15', 'Completed', '', '0', 'order', '#00a65a'
		'16', 'Confirmed', 'Your table reservation has been confirmed.', '0', 'reserve', '#00a65a'
		'17', 'Canceled', 'Your table reservation has been canceled.', '0', 'reserve', '#dd4b39'
		'18', 'Pending', 'Your table reservation is pending.', '0', 'reserve', ''
		'19', 'Canceled', '', '0', 'order', '#ea0b29' */

		$update = array();
		$update['order_status'] = $status;
		foreach($orders as $order_id){		  
		   $this->Orders_model->updateOrder($order_id,$update);
		}
		 
		$response    = new stdClass();
		$response->success = true; 
	    echo (json_encode($response));
        return;
	}

	public function orderEdit(){
			$order_time     = $this->input->post('order_time');
			$order_id       = $this->input->post('order_id');			
			$response    = new stdClass();
			if(preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/',$order_time)) {
				// Update something here
				$current   = new DateTime("now");
				$order_t   = new DateTime($order_time);								
				if($current > $order_t){
					$response->error = $this->lang->line('error_time_invalid'); 					 
					echo (json_encode($response));
					return;
				}
			    $update = array();
		   		$update['order_time'] = $order_time;
				$this->Orders_model->updateOrder($order_id,$update);				
				echo (json_encode($response));
			    return;
			}else{				
				$response->error = $this->lang->line('error_format_invalid'); 
				echo (json_encode($response));
				return;
			}
	}
}