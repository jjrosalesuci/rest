<?php if (!defined('BASEPATH')) exit('No direct access allowed');

class Migration_create_orders_versions_table extends CI_Migration {
    
    public function up() {
        
        // Create table
        $fields = array(
            'version_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'order_id INT(11)',
            'restaurant_id INT(11) NULL',
            'actionv VARCHAR(45)'
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->create_table('orders_versions'); 

        // restaurant_id , version _orden
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('orders_versions')."  ADD INDEX `index_rest_ver_orfer` (`restaurant_id`,`version_id`,`order_id`);");
    
        // Foreign key to restaurant    
        // Index
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('orders_versions')."  ADD INDEX `fk_restaurant_id` (`restaurant_id` ASC);");
        // FOREIGN
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('orders_versions')."  
         ADD CONSTRAINT `fk_restaurant_version_orders`
         FOREIGN KEY (`restaurant_id`)
         REFERENCES  ".$this->db->dbprefix('restaurants')."(`restaurant_id`)
         ON DELETE NO ACTION
         ON UPDATE NO ACTION;");

        // Foreign key to orders
        // Index
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('orders_versions')."  ADD INDEX `fk_orders_id` (`order_id` ASC);");
        // FOREIGN
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('orders_versions')."  
         ADD CONSTRAINT `fk_orders_version_orders`
         FOREIGN KEY (`order_id`)
         REFERENCES  ".$this->db->dbprefix('orders')."(`order_id`)
         ON DELETE NO ACTION
         ON UPDATE NO ACTION;");

    }

    public function down() {
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('orders_versions')."  DROP FOREIGN KEY `fk_restaurant_version_orders`;");
        $this->db->query("ALTER TABLE ".$this->db->dbprefix('orders_versions')."  DROP FOREIGN KEY `fk_orders_version_orders`;");

        $this->dbforge->drop_table('orders_versions');      
    }
}