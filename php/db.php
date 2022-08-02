<?php

class Db {
	private $_pdo=null;
	private $_workdir='';
	private $_filename='';
	public $error=false;
	public $error_message='';

	/*
		$place = base (This is a basic set of databases: local, nodes, etc)
		$place = file (specify a specific database for the full pathth)
	*/
	function __construct($place='base', $database='', $connect = null){

		if (is_null($connect)){
			list($file_name, $connect) = $this->prep_connect($place, $database);

			if(is_null($file_name) || !file_exists($file_name)){
				$this->error=true;
				$this->error_message='DB file name not set or not found!';
				return;
			} else {
				$this->_filename=$file_name;
			}

			if(is_null($connect)) {
				$this->error=true;
				$this->error_message='DB file name not set or invalid';
				return;
			}
		}

		try {
			$this->_pdo = new PDO($connect);
			$this->_pdo->setAttribute(PDO::ATTR_TIMEOUT,5000);
		}catch (PDOException $e){
			$this->error=true;
			$this->error_message=$e->getMessage();	//'DB Error';
		}

	}

	private function prep_connect($place, $database){

		$this->_workdir=getenv('WORKDIR');	// /usr/jails/
		$connect = null;
		$file_name = null;

		switch($place){
			case 'base':
				$file_name=$this->_workdir.'/var/db/'.$database.'.sqlite';
				$connect='sqlite:'.$file_name;
				break;
			case 'file':
				$file_name=$database;
				$connect='sqlite:'.$file_name;
				break;
			case 'helper':
				if(is_array($database)){
					///usr/jails/jails-system/cbsdpuppet1/helpers/redis.sqlite
					$file_name=$this->_workdir.'/jails-system/'.$database['jname'].'/helpers/'.$database['helper'].".sqlite";
					$connect='sqlite:'.$file_name;
				} else {
					$file_name=$this->_workdir.'/formfile/'.$database.".sqlite";
					$connect='sqlite:'.$file_name;
				}
				break;
			case 'cbsd-settings':
				$file_name=$this->_workdir.'/jails-system/CBSDSYS/helpers/cbsd.sqlite';
				$connect='sqlite:'.$file_name;
				break;
			case 'clonos':
				$file_name='/var/db/clonos/clonos.sqlite';
				$connect='sqlite:'.$file_name;
				break;
			case 'racct':
				$file_name=$this->_workdir.'/jails-system/'.$database['jname'].'/racct.sqlite';
				$connect='sqlite:'.$file_name;
				break;
			case 'bhyve':
				$file_name=$this->_workdir.'/jails-system/'.$database['jname'].'/local.sqlite';
				$connect='sqlite:'.$file_name;
				break;
		}

		/*
		$databases=array(
			'tasks'=>'cbsdtaskd',
			'jails'=>'local',
		);
		
		switch($driver){
			case 'sqlite_webdev':
				$connect='sqlite:/var/db/webdev/webdev.sqlite';
				break;
			case 'forms':
				$connect='sqlite:/var/db/webdev/forms.sqlite';
				break;
			case 'helpers':
				if(is_array($database)){
					$connect='sqlite:'.$this->_workdir.'/jails-system/'.
						$database['jname'].'/helpers/'.$database['helper'].".sqlite";
				}else $connect='';
				break;
			case 'sqlite_cbsd':
				if($database!=''){
					if(!isset($databases[$database])) break;
					$db=$databases[$database];
					$connect='sqlite:'.$this->_workdir.'/var/db/'.$db.'.sqlite';
				}
				break;
			case 'pkg':
				$connect='sqlite:'.$this->_workdir.'/jails-data/'.$database.'-data/var/db/pkg/local.sqlite';
				break;
/-*
			case 'from_file':
				echo $this->_workdir.$database;
				$connect='sqlite:'.$this->_workdir.$database;
				//"/jails-system/jail".$this->jailId."/helpers/".$this->helper.".sqlite"
				break;
*-/
			default:
				throw new Exception('Unknown database driver!');
				break;
		}
		*/

		return [$file_name, $connect];
	}

	# TODO once tested $values can have a default value of an empty array
	function select($sql, $values, $single = false){
		try {
			$query = $this->_pdo->prepare($sql);
			$i = 1;
			foreach($values as $v){
				if (count($v) == 1){ # TODO: Make default type string
					$query->bindParam($i, $v[0]);
				} elseif (count($v) == 2){ # if type defined
					$query->bindParam($i, $v[0], $v[1]);
				}
				$i++;
			}
			$query->execute();
			if ($single){
				$res = $query->fetch(PDO::FETCH_ASSOC);
			} else {
				$res = $query->fetchAll(PDO::FETCH_ASSOC);
			}
			return $res;
		} catch(PDOException $e) {
			# TODO: Handling ?
			return array();
		}
	}

	function selectOne($sql, $values){
		return $this->select($sql, $values, true);
	}

	function insert($sql, $values){
		try {
			$this->_pdo->beginTransaction();
			$query = $this->_pdo->prepare($sql);
			$i = 1;
			foreach($values as $v){
				if (count($v) == 1){ # TODO: Make default type string
					$query->bindParam($i, $v[0]);
				} elseif (count($v) == 2){ # if type defined
					$query->bindParam($i, $v[0], $v[1]);
				}
				$i++;
			}
			$query->execute();
			$lastId = $this->_pdo->lastInsertId();
			$this->_pdo->commit();
		} catch(PDOException $e) {
			$this->_pdo->rollBack();
			#throw new Exception($e->getMessage());
			return array('error'=>true,'info'=>$e->getMessage());
		}
		return array('error'=>false,'lastID'=>$lastId);
	}

	function update($sql, $values){
		try {
			$this->_pdo->beginTransaction();
			$query = $this->_pdo->prepare($sql);
			$i = 1;
			foreach($values as $v){
				if (count($v) == 1){ # TODO: Make default type string
					$query->bindParam($i, $v[0]);
				} elseif (count($v) == 2){ # if type defined
					$query->bindParam($i, $v[0], $v[1]);
				}
				$i++;
			}
			$query->execute();
			$rowCount=$query->rowCount();
			$this->_pdo->commit();
		} catch(PDOException $e) {
			$this->_pdo->rollBack();
			#return false;
			throw new Exception($e->getMessage());
		}
		return array('rowCount'=>$rowCount);
	}

	function isConnected(){ return !is_null($this->_pdo); }
	function getWorkdir(){  return $this->_workdir;    }
	function getFileName(){ return $this->_filename;   }
}
