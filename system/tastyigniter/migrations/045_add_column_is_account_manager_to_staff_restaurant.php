<?php if (!defined('BASEPATH')) exit('No direct access allowed');

class Migration_add_column_is_account_manager_to_staff_restaurant extends CI_Migration {
    
  	public function up() {
		// Column
        $this->dbforge->add_column('staff_restaurant', array('is_account_manager INT(1) NULL'));       
	}

	public function down() {      
		$this->dbforge->drop_column('staff_restaurant', 'is_account_manager');
	}

}