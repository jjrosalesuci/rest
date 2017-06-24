<?php if (!defined('BASEPATH')) exit('No direct access allowed');

class Migration_create_setup_steps_for_restaurants_tables extends CI_Migration {    
  	public function up() {
	    $fields = array(
            'id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
			'link VARCHAR(500) NOT NULL',
            'step VARCHAR(500) NOT NULL',
			'error VARCHAR(2000) NOT NULL'
        );
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table('setup_steps');

		$this->db->insert('setup_steps', array(
			'id'        => 1,
			'link'      => 'locations',
			'step'      => 'step_1',
			'error'      => '{}'
		));
		$this->db->insert('setup_steps', array(
			'id'        => 2,
			'link'      => 'locations#data',
			'step'      => 'step_2',
			'error'     => '{}'		
		));
		$this->db->insert('setup_steps', array(
			'id'        => 3,
			'link'      => 'menus',
			'step'      => 'step_3',
			'error'     => '{}'		
		));
		$this->db->insert('setup_steps', array(
			'id'        => 4,
			'link'      => 'step_1',
			'step'      => 'step_4',
			'error'     => '{}'	
		));

        $field = array(
            'id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'id_restaurant INT(11) NOT NULL',
            'id_step INT(11) NOT NULL',
            'completed INT(11)'
        );
        $this->dbforge->add_field($field);
        $this->dbforge->create_table('setup_restaurant_complete');
	}
	public function down() {
		$this->dbforge->drop_table('setup_steps');
        $this->dbforge->drop_table('setup_restaurant_complete');  	    
	}
}


