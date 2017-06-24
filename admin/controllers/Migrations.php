
<?php if (!defined('BASEPATH')) exit('No direct access allowed');
class Migrations extends Base_Controller {

    public function __construct() {
        parent::__construct(); 
        $this->load->library('migration');    
    }

    public function migrate(){
        $this->migration->current();
        echo 'ok'; return;
    }


    public function sendsms(){
      
        $client = \Clx\Xms\Client(
            'aseresoft32',
            '946a48b4b5224d5393329d7fa969362e',
            'https://api.clxcommunications.com/xms/v1/aseresoft32/'
        );

        $batchParams = new \Clx\Xms\Api\MtBatchTextSmsCreate();
        $batchParams->setSender('12345');
        $batchParams->setRecipients(['+59893615202']);
        $batchParams->setBody('Hello, World!');
        $result = $client->createTextBatch($batchParams);

      
    }

}