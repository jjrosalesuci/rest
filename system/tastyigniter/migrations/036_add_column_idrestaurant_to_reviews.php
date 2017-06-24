<?php if (!defined('BASEPATH')) exit('No direct access allowed');

class Migration_add_column_idrestaurant_to_reviews extends CI_Migration {
    
  	public function up() {
		// Column
        $this->dbforge->add_column('reviews', array('restaurant_id INT(11) NULL'));
        // Index
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('reviews')."   ADD INDEX `fk_restaurant_id` (`restaurant_id` ASC);");
        // FOREIGN
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('reviews')."  
         ADD CONSTRAINT `fk_reviews_restaurant`
         FOREIGN KEY (`restaurant_id`)
         REFERENCES  ".$this->db->dbprefix('restaurants')."(`restaurant_id`)
         ON DELETE NO ACTION
         ON UPDATE NO ACTION;");
	}

	public function down() {
       	$this->db->query("ALTER TABLE ".$this->db->dbprefix('reviews')."  DROP FOREIGN KEY `fk_reviews_restaurant`;");
		$this->dbforge->drop_column('reviews', 'restaurant_id');
	}

}