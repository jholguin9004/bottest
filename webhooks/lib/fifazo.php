<?php
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class fifazo{
    private $break = "\n";
    private $par = null;
    private $jug = null;
    private $fif = null;
    private $months = array('Enero' => '01','Febrero' => '02','Marzo' => '03','Abril' => '04','Mayo' => '05','Junio' => '06','Julio' => '07','Agosto' => '08','Septiembre' => '09','Octubre' => '10','Noviembre' => '11','Diciembre' => '12');
    private $rounds = array(0 => 'Grupos', 1 => 'Octavos', 2 => 'Cuartos', 3 => 'Semis', 5 => 'Final');
    
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
        $this->par = array_reverse($this->par);
    }
    
    /*
     * Crea la información
     */
    private function generateData(){
        if(isset($this->par)){
            foreach($this->par as $pcode => $pdata){
                //información del fifazo
                $fif = $this->getNameBy('fifazo', $pdata['fifazo']);
                $pdata = array_merge($pdata, $fif);
                //información de jugador 1
                $p1 = $this->getNameBy('user', $pdata['p1']);
                $pdata['p1_name'] = $p1['nick'];
                //información de jugador 2
                $p2 = $this->getNameBy('user', $pdata['p2']);
                $pdata['p2_name'] = $p2['nick'];
                //ajusta la fecha
                $date = $this->gettimestamp($pdata['date']);
                $pdata = array_merge($pdata, $date);
                unset($pdata['date']);
                $this->par[$pcode] = $pdata;
            }
        }
    }
    
    /*
     * Obtiene la fecha
     */
    private function gettimestamp($str){
        //partes de la fecha
        $dparts = explode('/', $str);
        $month = $this->months[$dparts[1]];
        $year = $dparts[0];
        $day = $dparts[2];
        //crea la fecha
        $timestamp = strtotime("{$year}-{$month}-{$day}");
        return array(
            'stamp' => $timestamp,
            'date_string' => date("Y-m-d", $timestamp)
        );
    }
    
    /*
     * Ontiene foraneas por id
     */
    private function getNameBy($type, $id){
        if($type == 'fifazo'){
            return $this->fif[$id];
        }else{
            return $this->jug[$id];
        }
    }
    
    
    public function getVsInfo($names, $last = false){
        $str = '';
        $fifazo = false;
        if($last){
            $revF = array_reverse($this->fif);
            reset($revF);
            $fifazo = key($revF);
        }
        $parts = $this->getVsData($names, $fifazo);
        //si no hay resultados
        if(empty($parts)){
            $str = ($fifazo) ? implode(' y ', $names) . " no jugaron partidos en el último fifazo." :  implode(' y ', $names) . " no han jugado partidos aún.";
        }else{
            $table = $this->getTableByMatches($parts);
            $table = $this->sortTable($table);
            $strTable = $this->getTableStr($table);
            $strMatches = $this->getMatchesStr($parts);
            $str = "Partidos{$this->break}{$strMatches}Tabla{$this->break}{$strTable}";
        }
        return $str;
    }
    
    private function getMatchesStr($table){
        $str = '';
        foreach($table as $cell){
            $ronda = $this->rounds[$cell['ronda']];
            $str.= "{$cell['p1_name']} {$cell['p1r']} - {$cell['p2r']} {$cell['p2_name']} | {$cell['name']}, {$ronda}" . $this->break;
        }
        return $str;
    }
    
    private function getTableStr($table){
        $str = '';
        $head = array('name','j','g','e','p','gf','gc','dg','ps');
        $str = implode(' | ', $head) . $this->break;
        foreach($table as $cell){
            $str.= implode(' | ', $cell) . $this->break;
        }
        return $str;
    }
    
    private function sortTable($table){
        $sort = array();
        foreach($table as $k=>$v){
            $sort['ps'][$k] = $v['ps'];
            $sort['dg'][$k] = $v['dg'];
        }
        array_multisort($sort['ps'], SORT_DESC, $sort['dg'], SORT_DESC,$table);
        return $table;
    }
    
    private function getTableByMatches($parts){
        $tableInfo = array();
        foreach($parts as $k => $m){
            //si no existe el jugador 1 en la tabla
            if(!isset($tableInfo[$m['p1_name']])) $tableInfo[$m['p1_name']] = array(
                'name' => $m['p1_name'],'j' => 0,'g' => 0,'e' => 0,'p' => 0,'gf' => 0,'gc' => 0,'dg' => 0,'ps' => 0,
            );
            //si no existe el jugador 2 en la tabla
            if(!isset($tableInfo[$m['p2_name']])) $tableInfo[$m['p2_name']] = array(
                'name' => $m['p2_name'],'j' => 0,'g' => 0,'e' => 0,'p' => 0,'gf' => 0,'gc' => 0,'dg' => 0,'ps' => 0,
            );
            //suma jugador 1
            $tableInfo[$m['p1_name']] = $this->matchToTable($tableInfo[$m['p1_name']], $m, 'p1');
            $tableInfo[$m['p2_name']] = $this->matchToTable($tableInfo[$m['p2_name']], $m, 'p2');
        }
        return $tableInfo;
    }
    
    private function matchToTable($table, $match, $p){
        $other = ($p == 'p1') ? 'p2' : 'p1';
        $table['j']++;
        $table['gf']+= $match["{$p}r"];
        $table['gc']+= $match["{$other}r"];
        $table['dg'] = $table['gf'] - $table['gc'];
        if($match["{$p}r"] > $match["{$other}r"]){
            //victoria
            $table['g']++;
            $table['ps']+= 3;
        }elseif($match["{$other}r"] > $match["{$p}r"]){
            //derrota
            $table['p']++;
        }else{
            //empate
            $table['e']++;
            $table['ps']+= 1;
        }
        return $table;
    }
    
    private function getVsData($names, $fifazo){
        $parts = array();
        foreach($this->par as $id => $par){
            //si consultan para el último fifazo, verifica que sea solo ese
            if($fifazo && $par['fifazo'] != $fifazo) continue;
            //verifia que se para los jugadores consultados únicamente
            if(in_array($par['p1_name'], $names) && in_array($par['p2_name'], $names)){
                $parts[] = $par;
            }
        }
        return $parts;
    }
}