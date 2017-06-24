<?php if (!defined('BASEPATH')) exit('No direct access allowed');

class Migration_create_staff_restaurant_table extends CI_Migration {
    
    public function up() {
        $fields = array(
            'sr_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'restaurant_id INT(11) NOT NULL',
            'staff_id INT(11) NOT NULL',
        );
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table('staff_restaurant');

        // Index and foreign key  for restaurant     
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('staff_restaurant')."   ADD INDEX `fk_restaurant_id` (`restaurant_id` ASC);");
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('staff_restaurant')."  
         ADD CONSTRAINT `fk_staff_restaurant_restaurant`
         FOREIGN KEY (`restaurant_id`)
         REFERENCES  ".$this->db->dbprefix('restaurants')."(`restaurant_id`)
         ON DELETE NO ACTION
         ON UPDATE NO ACTION;");
       
        // Index and foreign key  for restaurant     
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('staff_restaurant')."   ADD INDEX `fk_staff_id` (`staff_id` ASC);");
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('staff_restaurant')."  
         ADD CONSTRAINT `fk_staff_restaurant_staff`
         FOREIGN KEY (`staff_id`)
         REFERENCES  ".$this->db->dbprefix('staffs')."(`staff_id`)
         ON DELETE NO ACTION
         ON UPDATE NO ACTION;");
    }

    public function down() {
    	$this->db->query("ALTER TABLE ".$this->db->dbprefix('staff_restaurant')."  DROP FOREIGN KEY `fk_staff_restaurant_restaurant`;");
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('staff_restaurant')."  DROP FOREIGN KEY `fk_staff_restaurant_staff`;");
        $this->dbforge->drop_table('staff_restaurant');
    }
}