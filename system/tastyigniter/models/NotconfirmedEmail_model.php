<?php

defined('BASEPATH') or exit('No direct script access allowed');

class NotconfirmedEmail_model extends TI_Model {

    public function saveOrCreate($save = array()) {

            $id = null;
            $this->db->from('notconfirmed_emails');
			$this->db->where('email', $save['email']);
			$query = $this->db->get();
			$result = array();
			if ($query->num_rows() > 0) {
				$result = $query->result_array();
                $id = $result[0]['id'];
			}
        
		    $this->db->set('email',$save['email']);
            $this->db->set('code', $save['code']);
            $this->db->set('confirmed', $save['confirmed']);	  

            if (is_numeric($id)) {
                $this->db->where('id', (int) $id);
                $query = $this->db->update('notconfirmed_emails');
            } else {
                $query = $this->db->insert('notconfirmed_emails');               
            }
    }


    public function confirm($email){
      	 $this->db->where('email', $email);
         $this->db->set('confirmed', 1);
         $this->db->update('notconfirmed_emails');
    }

    public function isActive($email){
        $this->db->from('notconfirmed_emails');
		$this->db->where('email', $email);
        $this->db->where('datetime > NOW() - INTERVAL 40 MINUTE');
		$query = $this->db->get();	
		if ($query->num_rows() > 0) {
		  return true;
		} 
        return false;  
    }

    public function validate($email,$code){        
        $this->db->from('notconfirmed_emails');
		$this->db->where('email', $email);
        $this->db->where('code' , $code);
		$query = $this->db->get();		
		if ($query->num_rows() > 0) {
		  return true;
		} 
        return false;
    }

}    
?>