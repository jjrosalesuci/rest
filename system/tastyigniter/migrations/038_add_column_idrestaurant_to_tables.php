<?php if (!defined('BASEPATH')) exit('No direct access allowed');

class Migration_add_column_idrestaurant_to_tables extends CI_Migration {
    
  	public function up() {
		// Column
        $this->dbforge->add_column('tables', array('restaurant_id INT(11) NULL'));
        // Index
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('tables')."   ADD INDEX `fk_restaurant_id` (`restaurant_id` ASC);");
        // FOREIGN
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('tables')."  
         ADD CONSTRAINT `fk_tables_restaurant`
         FOREIGN KEY (`restaurant_id`)
         REFERENCES  ".$this->db->dbprefix('restaurants')."(`restaurant_id`)
         ON DELETE NO ACTION
         ON UPDATE NO ACTION;");
	}

	public function down() {
       	$this->db->query("ALTER TABLE ".$this->db->dbprefix('tables')."  DROP FOREIGN KEY `fk_tables_restaurant`;");
		$this->dbforge->drop_column('tables', 'restaurant_id');
	}

}