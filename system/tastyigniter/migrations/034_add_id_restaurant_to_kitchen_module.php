<?php if (!defined('BASEPATH')) exit('No direct access allowed');


class Migration_add_id_restaurant_to_kitchen_module extends CI_Migration {
	
    public function up() {
		/* Menu */		
		// 		Column
		$this->dbforge->add_column('menus', array('restaurant_id INT(11) NULL'));
		
		// 		Index
		$this->db->query("ALTER TABLE ".$this->db->dbprefix('menus')."   ADD INDEX `fk_menu_restaurant_idx` (`restaurant_id` ASC);");
		
		// 		FOREIGN
		$this->db->query("ALTER TABLE ".$this->db->dbprefix('menus')."  
         ADD CONSTRAINT `fk_menu_restaurant`
         FOREIGN KEY (`restaurant_id`)
         REFERENCES  ".$this->db->dbprefix('restaurants')."(`restaurant_id`)
         ON DELETE NO ACTION
         ON UPDATE NO ACTION;");
		
		/* Menus -options */
		
		// 		Column
    	$this->dbforge->add_column('options', array('restaurant_id INT(11) NULL'));
		
		// 		Index
		$this->db->query("ALTER TABLE ".$this->db->dbprefix('options')."   ADD INDEX `fk_op_restaurant_id` (`restaurant_id` ASC);");
		
		// 		FOREIGN
		$this->db->query("ALTER TABLE ".$this->db->dbprefix('options')."  
         ADD CONSTRAINT `fk_options_restaurant`
         FOREIGN KEY (`restaurant_id`)
         REFERENCES  ".$this->db->dbprefix('restaurants')."(`restaurant_id`)
         ON DELETE NO ACTION
         ON UPDATE NO ACTION;");		
	}
	
	
	public function down() {
		/* Menu */		
		$this->db->query("ALTER TABLE ".$this->db->dbprefix('menus')."  DROP FOREIGN KEY `fk_menu_restaurant`;");
		$this->dbforge->drop_column('menus', 'restaurant_id');	
		
		/* Menus options */		
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('options')."  DROP FOREIGN KEY `fk_options_restaurant`;");
		$this->dbforge->drop_column('options', 'restaurant_id');		
	}
	
}
