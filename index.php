<?php 
error_reporting(E_ERROR | E_PARSE);
require __DIR__.'/vendor/autoload.php';
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class fifazo{
    
    private $par = null;
    private $jug = null;
    private $fif = null;
    public $data = null;
    
    public function __construct() {
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/fifazo-6f4bc-firebase-adminsdk-nvzly-563d004083.json');
        $firebase = (new Factory)
           ->withServiceAccount($serviceAccount)
           ->withDatabaseUri('https://fifazo-6f4bc.firebaseio.com')
           ->create();
        $database = $firebase->getDatabase();

        $this->par = $database->getReference('partidos')->getValue();
        $this->jug = $database->getReference('usuarios')->getValue();
        $this->fif = $database->getReference('fifazos')->getValue();
        $this->generateData();
    }
    
    private function generateData(){
        if(isset($this->par)){
            foreach($this->par as $pcode => $pdata){
                $fif = $this->getNameBy('fifazo', $pdata['fifazo']);
                var_dump($pdata);
                die();
            }
        }
    }
    
    private function getNameBy($type, $id){
        if($type == 'fifazo'){
            var_dump($this->fif[$id]);die();
        }else{
            
        }
    }
    
}

$fif = new fifazo();
echo '<pre>';
var_dump($fif->data);