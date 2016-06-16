<?php
/**************************************************************************
This file is part of Magma.
Magma is free software: you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
Magma is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU Lesser General Public License for more details.
You should have received a copy of the GNU Lesser General Public License
along with Magma. If not, see <http://www.gnu.org/licenses/>.
**************************************************************************/
<<<<<<< HEAD

=======
>>>>>>> origin/master
namespace Magma;

define("DS", DIRECTORY_SEPARATOR);

class Magma{

	private $db 	  = null;
	private $table 	  = null;
	public  $autouse  = false;
	public  $debug    = false;
	private $fatal    = false;
	private $types    = ["INT","DATE","TIMESTAMP","TEXT","FLOAT"];


	/**
	 * Make HTML Format errors
	 * @param array $e
	 * @return string 
	 */

	private function html_errors($e){
		if($e["TYPE"]=="FATAL"){
			$css = "padding:4px;font-family: Arial;background-color: #F00;";
		}elseif($e["TYPE"]=="WARNING"){
			$css = "padding:4px;font-family: Arial;background-color: #FF6600;";
		}elseif($e["TYPE"]=="NOTICE"){
			$css = "padding:4px;font-family: Arial;background-color: #008AFF;";
		}

		$backtrace = debug_backtrace();

		$file = $backtrace[count($backtrace)-1]['file'];
		$line = $backtrace[count($backtrace)-1]['line'];

		return "<div><span style=\"{$css}\">[{$e['TYPE']}] : {$e['MSG']} in {$file} on line {$line}</span></div>";
	}

	/**
	 * Manage exceptions errors
	 * @param array $e
	 * @return boolean true if an exception occured 
	 */

	private function exception($e){
		if($this->debug==true){

			if($e["TYPE"]=="FATAL"){
				$this->fatal = true;
			}

			echo $this->html_errors($e);
			return true;
		}
		return false;
	}

	/**
	 * Append
	 */

	private function append($data, $vars=[]){

		if($this->read()){
			$content = $this->read();
		}

		if(!empty($vars)){
			$struct = $this->getStruct();

			foreach($vars as $k=>$v){
				$content->VARS->$k = $v;
			}	
		}


		if(isset($content->DATA)){
			$content->DATA = array_merge($content->DATA, $data);
		}else{
			$content->DATA = $data;
		}

		$this->write($content);

	}

	/**
	 * Write data into table file
	 * @param text $data
	 * @return true
	 */

	private function write($data){

		$data = gzcompress(json_encode($data));

		if(file_put_contents($this->db.DS.$this->table, $data, LOCK_EX)){
			return true;
		}
		return false;
	}

	private function read(){

		$filename = $this->db.DS.$this->table;
		return json_decode(gzuncompress(file_get_contents($filename)));
	}


	/**
	 * Get structure of a table
	 * @return object
	 */

	private function getStruct(){

		$content = $this->read();

		$OBJ = new \stdClass();

		foreach($content as $k=>$v){
			$OBJ->$k = $v;
		}

		if(isset($content->DATA)){
			$OBJ->TOTAL = count($content->DATA);
		}else{
			$OBJ->TOTAL = 0;
		}

		return $OBJ;
	}

	/**
	 * Sort $data by $sort (column=>order)
	 * @param $data(array)		Data
	 * @param $sort(array)		Order (ASC or DESC) by the column ("id"=>"DESC") will be (5,4,3,2,1)
	 * @return $data(array)		Return $data ordered by $sort, if $sort = NULL, $data is equaled to the return
	 */

	private function sortBy($data, $sort=NULL){

		$total = count($data);

		if($sort==NULL || $total===1){
			return $data;
		}else{

			$tmp = NULL;

			$keys = array_keys($sort);
			$key = $keys[0];
			$value = array_values($sort);

			if($value[0]=="DESC"){
				for($i = 0; $i < $total; $i++){
					for($j = $total-1; $j >= $i; $j--){ 
						if(isset($data[$j+1]) &&  ($data[$j+1]->$key > $data[$j]->$key)){ 
							$tmp = $data[$j+1]; 
							$data[$j+1] = $data[$j]; 
							$data[$j] = $tmp; 
						}
					} 
				}
			}elseif($value[0]=="ASC"){
				for($i = 0; $i < $total; $i++){
					for($j = $total-1; $j >= $i; $j--){ 
						if(isset($data[$j+1]) &&  ($data[$j+1]->$key < $data[$j]->$key)){ 
							$tmp = $data[$j+1]; 
							$data[$j+1] = $data[$j]; 
							$data[$j] = $tmp; 
						}
					} 
				}					
			}				
		}

		return $data;

	}

	/**
	 * Constructor init config vars
	 * @param array $vars
	 * @return true
	 */

	public function __construct($vars=[]){

		if(!empty($vars) && is_array($vars)){
			foreach($vars as $k=>$v){
				$this->$k = $v;
			}
			return true;
		}
		return false;
	}

	/**
	 * Create new database (folder)
	 * @param   string $dbname
	 * @return true
	 */	

	public function new($dbname){

		if($this->fatal){
			return false;
		}

		if(!is_dir('_'.$dbname)){
			mkdir('_'.$dbname, 0600);
			if($this->autouse==true){
				$this->use($dbname);
			}
			return true;
		}
		return false;
	}


	/**
	 * Load the database folder
	 * @param   string $dbname
	 * @return true
	 */

	public function use($dbname){

		if($this->fatal){
			return false;
		}

		if(is_dir('_'.$dbname)){
			$this->db = '_'.$dbname;
			return true;
		}
		return false;
	}

	/**
	 * Load the table file
	 * @param   string $table
	 * @return true
	 */

	public function load($table){

		if($this->fatal){
			return false;
		}

		if(is_file($this->db.DS.$table)){
			$this->table = $table;
			return true;
		}
		$this->exception(["TYPE"=>"FATAL", "MSG"=>"The table {$table} don't exists"]);
		return false;
	}

	/**
	 * Create table with structure
	 * @param  string $name
	 * @param  array $structure
	 * @return true
	 */

	public function create($name, $structure){

		if($this->fatal){
			return false;
		}

		if(isset($this->db)){

			$filename = $this->db.DS.$name;

			if(!file_exists($filename)){

				if(!preg_match('/([A-z0-9_\-\.]+)/', $name)){
					$this->exception(["TYPE"=>"FATAL", "MSG"=>"Invalid table name : {$name}"]);
					return false;			
				}

				$OBJ = new \stdClass();

				foreach($structure as $column=>$options){

					$options = strtoupper(trim($options, ','));

					if(!preg_match('/([A-z0-9_\-\.]+)/', $column)){
						$this->exception(["TYPE"=>"FATAL", "MSG"=>"Invalid column name : {$column}"]);
						return false;			
					}

					$option = explode(',', $options);

					if(in_array($option[0], $this->types)){

						$OBJ->STRUCT[$column]["TYPE"] = $option[0];
						$OBJ->STRUCT[$column]["NAME"] = $column;

						array_shift($option);

						foreach($option as $v){
							$v = strtoupper($v);
							if($v=="AUTO_INCREMENT"){
								$OBJ->VARS[$v] = 1;
							}
							$OBJ->STRUCT[$column][$v] = true;
						}
					}else{
						$this->exception(["TYPE"=>"FATAL", "MSG"=>"Unknown type : {$option[0]}"]);
						return false;
					}
				}

				touch($filename);
				$this->table = $name;
				$this->write($OBJ);
				$this->table = null;

				return true;

			}else{
				$this->exception(["TYPE"=>"FATAL", "MSG"=>"The table {$name} already exists"]);
			}
		}else{
			$this->exception(["TYPE"=>"FATAL", "MSG"=>"No database selected"]);
		}
		return false;
	}

	/**
	 * Fetch data with matching $conditions
	 * @param  array $conditions
	 * @param  array $options
	 * @return object
	 */

	public function fetch($conditions=[], $options=[]){

		if($this->fatal){
			return false;
		}

		if(!isset($this->db)){
			$this->exception(["TYPE"=>"FATAL", "MSG"=>"No database selected"]);
			return false;
		}

		if(!isset($this->table)){
			$this->exception(["TYPE"=>"FATAL", "MSG"=>"No table selected"]);
			return false;
		}

		$content = $this->read();

		if(!isset($content->DATA)){
			$this->exception(["TYPE"=>"FATAL", "MSG"=>"The table {$this->table} is empty"]);
			return false;
		}

		if(isset($options["LIMIT"])){
			$LIMIT = explode(',', $options["LIMIT"]);
			if(isset($LIMIT[1])){
				$limit = $LIMIT[1];
				$offset = $LIMIT[0];
			}else{
				$limit = $options["LIMIT"];
				$offset = 0;
			}
		}

		if(!empty($conditions)){
			foreach($content->DATA as $i=>$v){
				if(isset($offset) && isset($limit)){
					if($i>$offset+$limit-1){
						break;
					}
				}

				foreach($v as $k=>$w){
					if(isset($conditions[$k])){
						foreach($conditions as $col=>$val){
							if($val==$w){
								$alternate[] = $v;
							}
						}
						break;
					}else{
						continue;
					}
				}
			}
		}

		if(isset($options["LIMIT"])){
			foreach($content->DATA as $k=>$v){

				if($k<=$offset-1){
					continue;
				}

				if($k>$offset+$limit-1){
					break;
				}else{
					$alternate[$k] = $v;
				}
			}

		}

		if(isset($options["ORDER"]) && is_array($options["ORDER"])){
			if(isset($alternate)){
				$alternate = $this->sortBy($alternate, $options["ORDER"]);
			}else{
				$content->DATA = $this->sortBy($content->DATA, $options["ORDER"]);
			}
		}

		if(!isset($alternate)){
			return $content->DATA;
		}else{
			return $alternate;
		}

	}

	/**
	 * Insert data into table
	 * @param  array $data
	 * @return true
	 */

	public function insert(){

		if($this->fatal){
			return false;
		}

		$datas = func_get_args();

		if(empty($datas)){
			$this->exception(["TYPE"=>"NOTICE", "MSG"=>"Nothing to insert, empty values"]);
			return false;			
		}
		
		$struct = $this->getStruct();

		$i=0;

		$columns = [];

		foreach($struct->STRUCT as $k=>$v){
			$columns[$i] = $v;
			$i++;
		}

		$content = "";


		$key = $struct->TOTAL;
		$data2 = [];

		foreach($datas as $data){
			foreach($data as $k=>$v){
				/**
				 * DATA PROCESS HERE
				 */
				if($columns[$k]->TYPE=="INT" || $columns[$k]->TYPE=="TIMESTAMP"){
					if(!is_int($v) && !empty($v) && !intval($v)){
						$this->exception(["TYPE"=>"FATAL", "MSG"=>"Unexpected value : {$v} is not an {$columns[$k]->TYPE}"]);
						return false;
					}else{
						if($columns[$k]->TYPE=="INT" && empty($v) && isset($columns[$k]->AUTO_INCREMENT)){
							$data2[$key][$columns[$k]->NAME] = $struct->VARS->AUTO_INCREMENT;
						}else{
							$this->exception(["TYPE"=>"FATAL", "MSG"=>"Unexpected null value"]);
							return false;								
						}
					}
				}elseif($columns[$k]->TYPE=="FLOAT"){
					if(!is_float($v) && !empty($v) && !floatval($v)){
						$this->exception(["TYPE"=>"FATAL", "MSG"=>"Unexpected value : {$v} is not an {$columns[$k]->TYPE}"]);
						return false;
					}						
				}elseif($columns[$k]->TYPE=="DATE" && !empty($v)){
					$data2[$key][$columns[$k]->NAME] = date($v);
				}elseif($columns[$k]->TYPE=="DATE" && empty($v)){
					$data2[$key][$columns[$k]->NAME] = date("Y-m-d");
				}else{
					$data2[$key][$columns[$k]->NAME] = $v;
				}
			}

			if(isset($struct->VARS->AUTO_INCREMENT)){
				$struct->VARS->AUTO_INCREMENT++;
			}

			$key++;
		}

		if(isset($struct->VARS->AUTO_INCREMENT)){
			$this->append($data2, ["AUTO_INCREMENT"=>$struct->VARS->AUTO_INCREMENT]);
		}else{
			$this->append($data2);
		}

		return true;
	}

	/**
	 * Update data into table
	 * @param  array $data
	 * @param  array $conditions
	 * @return true
	 */

	public function update($data = [], $conditions = []){

		if($this->fatal){
			return false;
		}

		if(empty($data)){
			$this->exception(["TYPE"=>"NOTICE", "MSG"=>"Nothing to update, missing argument 1"]);
			return false;			
		}
		
		$struct = $this->getStruct();

		if(empty($conditions)){
			foreach($struct->DATA as $k=>$v){
				foreach($v as $col=>$val){
					if(!isset($data[$col])){
						continue;
					}else{
						$struct->DATA[$k]->$col = $data[$col];
						continue;	
					}
				}
			}
		}else{
			foreach($struct->DATA as $k=>$v){
				foreach($v as $col=>$val){
					if(isset($conditions[$col])){
						if($conditions[$col]==$struct->DATA[$k]->$col){
							foreach($data as $key2=>$val2){
								$struct->DATA[$k]->$key2 = $val2;
								continue;
							}
						}
					}else{
						continue;
					}
				}
			}			
		}

		if($this->write($struct)){
			return true;
		}
		return false;
	}

	/**
	 * Update data into table
	 * @param  array $data
	 * @param  array $conditions
	 * @return true
	 */

	public function delete($conditions = []){

		if($this->fatal){
			return false;
		}

		if(empty($conditions)){
			$this->exception(["TYPE"=>"NOTICE", "MSG"=>"Nothing to delete, missing argument 1"]);
			return false;			
		}

		$struct = $this->getStruct();

		foreach($struct->DATA as $k=>$v){
			foreach($v as $col=>$val){
				if(isset($conditions[$col])){
					if($conditions[$col]==$struct->DATA[$k]->$col){
						unset($struct->DATA[$k]);
						break;
					}
				}else{
					continue;
				}
			}
		}
		if($this->write($struct)){
			return true;
		}
		return false;

	}
}
?>
