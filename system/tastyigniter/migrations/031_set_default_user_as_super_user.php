<?php if (!defined('BASEPATH')) exit('No direct access allowed');

/**
 * Create field issuperadmin for user
 * update de defalut user to superadmin
 */
class Migration_set_default_user_as_super_user extends CI_Migration {
    public function up() {     
        $this->dbforge->add_column('users', array('is_super_admin INT NOT NULL DEFAULT 0')); 
    }

    public function down() {
        $this->dbforge->drop_column('users', 'is_super_admin');
    }
}