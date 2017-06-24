<?php if (!defined('BASEPATH')) exit('No direct access allowed');

class Migration_create_restaurant_table extends CI_Migration {
    
    public function up() {
        $fields = array(
            'restaurant_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'name VARCHAR(500) NOT NULL'      
        );
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table('restaurants');
    }

    public function down() {
        $this->dbforge->drop_table('restaurants');
    }
}