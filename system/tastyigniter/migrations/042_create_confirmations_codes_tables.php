<?php if (!defined('BASEPATH')) exit('No direct access allowed');

class Migration_create_confirmations_codes_tables extends CI_Migration {
    
    public function up() {
        $fields = array(
            'id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'email VARCHAR(500) NOT NULL',
            'code VARCHAR(10) NOT NULL',
            'datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'confirmed INT(11)'
        );
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table('notconfirmed_emails'); 

        $field = array(
            'id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'phone VARCHAR(500) NOT NULL',
            'code VARCHAR(10) NOT NULL',
            'datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'confirmed INT(11)'
        );
        $this->dbforge->add_field($field);
        $this->dbforge->create_table('notconfirmed_mobile'); 
    }

    public function down() {
        $this->dbforge->drop_table('notconfirmed_emails');
        $this->dbforge->drop_table('notconfirmed_mobile');
    }
}