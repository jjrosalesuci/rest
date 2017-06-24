<?php if (!defined('BASEPATH')) exit('No direct access allowed');

class Migration_add_column_id_restaurant_to_staft extends CI_Migration {
    
  	public function up() {
		// Column
        $this->dbforge->add_column('staffs', array('restaurant_id INT(11) NULL'));
        // Index
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('staffs')."   ADD INDEX `fk_restaurant_idx` (`restaurant_id` ASC);");
        // FOREIGN
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('staffs')."  
         ADD CONSTRAINT `fk_restaurant`
         FOREIGN KEY (`restaurant_id`)
         REFERENCES  ".$this->db->dbprefix('restaurants')."(`restaurant_id`)
         ON DELETE NO ACTION
         ON UPDATE NO ACTION;");
	}

	public function down() {
       	$this->db->query("ALTER TABLE ".$this->db->dbprefix('staffs')."  DROP FOREIGN KEY `fk_restaurant`;");
		$this->dbforge->drop_column('staffs', 'restaurant_id');
	}
}