<?php if (!defined('BASEPATH')) exit('No direct access allowed');

class Migration_add_column_idrestaurant_to_orders extends CI_Migration {
    
  	public function up() {
		// Column
        $this->dbforge->add_column('orders', array('restaurant_id INT(11) NULL'));
        // Index
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('orders')."   ADD INDEX `fk_restaurant_idx` (`restaurant_id` ASC);");
        // FOREIGN
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('orders')."  
         ADD CONSTRAINT `fk_orders_restaurant`
         FOREIGN KEY (`restaurant_id`)
         REFERENCES  ".$this->db->dbprefix('restaurants')."(`restaurant_id`)
         ON DELETE NO ACTION
         ON UPDATE NO ACTION;");
	}

	public function down() {
       	$this->db->query("ALTER TABLE ".$this->db->dbprefix('orders')."  DROP FOREIGN KEY `fk_orders_restaurant`;");
		$this->dbforge->drop_column('orders', 'restaurant_id');
	}
}