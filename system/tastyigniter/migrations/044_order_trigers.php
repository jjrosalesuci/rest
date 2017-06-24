044_order_trigers

<?php if (!defined('BASEPATH')) exit('No direct access allowed');

class Migration_order_trigers extends CI_Migration {
    
    public function up() {
         
          $this->db->query("CREATE TRIGGER `orders_AFTER_INSERT` AFTER INSERT ON `".$this->db->dbprefix('orders')."` FOR EACH ROW
          BEGIN
            INSERT `".$this->db->dbprefix('orders_versions')."`(order_id,restaurant_id,actionv) values (NEW.order_id, NEW.restaurant_id,'insert');
          END");

          $this->db->query("CREATE TRIGGER `orders_AFTER_UPDATE` AFTER UPDATE ON `".$this->db->dbprefix('orders')."` FOR EACH ROW
          BEGIN
            INSERT `".$this->db->dbprefix('orders_versions')."`(order_id,restaurant_id,actionv) values (NEW.order_id, NEW.restaurant_id,'update');
          END");
    }

    public function down() {
           $this->db->query("DROP TRIGGER `orders_AFTER_INSERT`");
           $this->db->query("DROP TRIGGER `orders_AFTER_UPDATE`");
    }
}