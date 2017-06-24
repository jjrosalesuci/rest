<?php

defined('BASEPATH') or exit('No direct script access allowed');

class NotconfirmedMobile_model extends TI_Model {

    public function saveOrCreate($save = array()) {
            $id = null;
            $this->db->from('notconfirmed_mobile');
			$this->db->where('phone', $save['phone']);
			$query = $this->db->get();
			$result = array();
			if ($query->num_rows() > 0) {
				$result = $query->result_array();
                $id = $result[0]['id'];
			}        
		    $this->db->set('phone',$save['phone']);
            $this->db->set('code', $save['code']);
            $this->db->set('confirmed', $save['confirmed']);	   	   

            if (is_numeric($id)) {
                $this->db->where('id', (int) $id);
                $query = $this->db->update('notconfirmed_mobile');
            } else {
                $query = $this->db->insert('notconfirmed_mobile');               
            }
    }

     public function confirm($phone){       
		 $this->db->where('phone', $phone);
         $this->db->set('confirmed', 1);
         $this->db->update('notconfirmed_mobile');
    }

    public function isActive($phone){
        $this->db->from('notconfirmed_mobile');
		$this->db->where('phone', $phone);
        $this->db->where('datetime > NOW() - INTERVAL 40 MINUTE');
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
		  return true;
		} 
        return false;  
    }

    public function validate($phone,$code){        
        $this->db->from('notconfirmed_mobile');
		$this->db->where('phone', $phone);
        $this->db->where('code', $code);
		$query = $this->db->get();	
		if ($query->num_rows() > 0) {
		  return true;
		} 
        return false;        
    }
}    
?>