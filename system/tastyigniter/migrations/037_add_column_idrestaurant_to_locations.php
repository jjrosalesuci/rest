<?php if (!defined('BASEPATH')) exit('No direct access allowed');

class Migration_add_column_idrestaurant_to_locations extends CI_Migration {
    
  	public function up() {
		// Column
        $this->dbforge->add_column('locations', array('restaurant_id INT(11) NULL'));
        // Index
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('locations')."   ADD INDEX `fk_restaurant_id` (`restaurant_id` ASC);");
        // FOREIGN
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('locations')."  
         ADD CONSTRAINT `fk_locations_restaurant`
         FOREIGN KEY (`restaurant_id`)
         REFERENCES  ".$this->db->dbprefix('restaurants')."(`restaurant_id`)
         ON DELETE NO ACTION
         ON UPDATE NO ACTION;");
	}

	public function down() {
       	$this->db->query("ALTER TABLE ".$this->db->dbprefix('locations')."  DROP FOREIGN KEY `fk_locations_restaurant`;");
		$this->dbforge->drop_column('locations', 'restaurant_id');
	}

}