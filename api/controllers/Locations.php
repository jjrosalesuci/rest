<?php if ( ! defined('BASEPATH')) exit('No direct access allowed');

// Including Phil Sturgeon's Rest Server Library in our Server file.
require BASEPATH . '/libraries/REST_Controller.php';

class Locations extends REST_Controller{
    // Load model in constructor
    public function __construct() {
        parent::__construct();
        //$this->load->model('API_model');
        
        $this->load->model('Locations_model');
		
        $this->load->model('Pages_model');
		$this->load->model('Reviews_model');

		$this->load->model('Addresses_model');

        $this->load->library('location'); 														// load the location library
		$this->load->library('currency');  	

    }
    // Server's Get Method
  
    public function search_get(){

        $this->load->library('country');
		$this->load->library('pagination');
		$this->load->library('cart'); 															// load the cart library
		$this->load->model('Image_tool_model');

		$url = '?';
		$filter = array();
		if ($this->input->get('page')) {
			$filter['page'] = (int) $this->input->get('page');
		} else {
			$filter['page'] = '';
		}

		if ($this->config->item('menus_page_limit')) {
			$filter['limit'] = $this->config->item('menus_page_limit');
		}

		$filter['filter_status'] = '1';
		$filter['order_by'] = 'ASC';

		if ($this->input->get('search')) {
			$filter['filter_search'] = $this->input->get('search');
			$url .= 'search='.$filter['filter_search'].'&';
		}

		if ($this->input->get('sort_by')) {
			$sort_by = $this->input->get('sort_by');

			if ($sort_by === 'newest') {
				$filter['sort_by'] = 'location_id';
				$filter['order_by'] = 'DESC';
			} else if ($sort_by === 'name') {
				$filter['sort_by'] = 'location_name';
			}

			$url .= 'sort_by=' . $sort_by . '&';
		}
		
		$review_totals = $this->Reviews_model->getTotalsbyId();                                    // retrieve all customer reviews from getMainList method in Reviews model

		$data['locations'] = array();
		$locations = $this->Locations_model->getList($filter);
		if ($locations) {
			foreach ($locations as $location) {
                $this->location->setLocation($location['location_id'], FALSE);
                

				$opening_status = $this->location->workingStatus('opening');
				$delivery_status = $this->location->workingStatus('delivery');
				$collection_status = $this->location->workingStatus('collection');

				$delivery_time = $this->location->deliveryTime();
				if ($delivery_status === 'closed') {
					$delivery_time = 'closed';
				} else if ($delivery_status === 'opening') {
					$delivery_time = $this->location->workingTime('delivery', 'open');
				}

				$collection_time = $this->location->collectionTime();
				if ($collection_status === 'closed') {
					$collection_time = 'closed';
				} else if ($collection_status === 'opening') {
					$collection_time = $this->location->workingTime('collection', 'open');
				}

				$review_totals = isset($review_totals[$location['location_id']]) ? $review_totals[$location['location_id']] : 0;

				$data['locations'][] = array(                                                            // create array of menu data to be sent to view
					'location_id'       => $location['location_id'],
					'location_name'     => $location['location_name'],
					'description'       => (strlen($location['description']) > 120) ? substr($location['description'], 0, 120) . '...' : $location['description'],
					'address'           => $this->location->getAddress(TRUE),
					'total_reviews'     => $review_totals,
					'location_image'    => $this->location->getImage(),
					'is_opened'         => $this->location->isOpened(),
					'is_closed'         => $this->location->isClosed(),
					'opening_status'    => $opening_status,
					'delivery_status'   => $delivery_status,
					'collection_status' => $collection_status,
					'delivery_time'     => $delivery_time,
					'collection_time'   => $collection_time,
					'opening_time'      => $this->location->openingTime(),
					'closing_time'      => $this->location->closingTime(),
					'min_total'         => $this->location->minimumOrder($this->cart->total()),
					'delivery_charge'   => $this->location->deliveryCharge($this->cart->total()),
					'has_delivery'      => $this->location->hasDelivery(),
					'has_collection'    => $this->location->hasCollection(),
					'last_order_time'   => $this->location->lastOrderTime(),
					'distance'   		=> $this->input->get('lat') && $this->input->get('lon') ? round($this->location->checkDistance($this->input->get('lat'),$this->input->get('lon'))) : round($this->location->checkDistance()) ,
					'distance_unit'   	=> $this->config->item('distance_unit') === 'km' ? $this->lang->line('text_kilometers') : $this->lang->line('text_miles'),
					'href'              => site_url('local?location_id=' . $location['location_id']),
				);
			}
		}

		if (!empty($sort_by) AND $sort_by === 'distance') {
			$data['locations'] = sort_array($data['locations'], 'distance');
		} else if (!empty($sort_by) AND $sort_by === 'rating') {
			$data['locations'] = sort_array($data['locations'], 'total_reviews');
		}

		$config['base_url'] 		= site_url('local/all'.$url);
		$config['total_rows'] 		= $this->Locations_model->getCount($filter);
		$config['per_page'] 		= $filter['limit'];

		$this->pagination->initialize($config);

		$data['pagination'] = array(
			'info'		=> $this->pagination->create_infos(),
			'links'		=> $this->pagination->create_links()
		);

		$this->location->initialize();

		$data['locations_filter'] = $this->filter($url);

		//$this->template->renderb('local_all', $data);
  

            $this->set_response([
            'status' => TRUE,
            'result' => $data
            ], REST_Controller::HTTP_OK);

    }

	public function addresses_get() {
		$filter = array();
		if ($this->input->get('customer_id')) {
			$filter['customer_id'] = (int) $this->input->get('customer_id');
		} else {
			$filter['customer_id'] = '1';
		}

		$data['addresses'] = $this->Addresses_model->getList($filter);

		$this->set_response([
            'status' => TRUE,
            'result' => $data
            ], REST_Controller::HTTP_OK);
	}

public function filter() {
		$url = '';

		$data['search'] = '';
		if ($this->input->get('search')) {
			$data['search'] = $this->input->get('search');
			$url .= 'search='.$this->input->get('search').'&';
		}

		$filters['distance']['name'] = lang('text_filter_distance');
		$filters['distance']['href'] = site_url('local/all?'.$url.'sort_by=distance');

		$filters['newest']['name'] = lang('text_filter_newest');
		$filters['newest']['href'] = site_url('local/all?'.$url.'sort_by=newest');

		$filters['rating']['name'] = lang('text_filter_rating');
		$filters['rating']['href'] = site_url('local/all?'.$url.'sort_by=rating');

		$filters['name']['name'] = lang('text_filter_name');
		$filters['name']['href'] = site_url('local/all?'.$url.'sort_by=name');

		$data['sort_by'] = '';
		if ($this->input->get('sort_by')) {
			$data['sort_by'] = $this->input->get('sort_by');
			$url .= 'sort_by=' . $data['sort_by'];
		}

		$data['filters'] = $filters;

		$url = (!empty($url)) ? '?'.$url : '';
		$data['search_action'] = site_url('local/all'.$url);

		return $data;
	}
  
}

/* End of file API.php */
/* Location: ./api/API.php */