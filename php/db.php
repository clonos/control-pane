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
	function __construct($place='base',$database=''){
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

		if(is_null($file_name) || !file_exists($file_name)){
			$this->error=true;
			$this->error_message='DB file name not set or not found!';
			return;
		}

		if(is_null($connect)) {
			$this->error=true;
			$this->error_message='DB file name not set or invalid';
			return;
		}

		try {
			$this->_pdo = new PDO($connect);
			$this->_pdo->setAttribute(PDO::ATTR_TIMEOUT,5000);
		}catch (PDOException $e){
			$this->error=true;
			$this->error_message=$e->getMessage();	//'DB Error';
			return;
		}

		$this->_filename=$file_name;
		//echo $file_name,PHP_EOL,PHP_EOL;
	}

	function select($query){
		if($quer=$this->_pdo->query($query)){
			$res=$quer->fetchAll(PDO::FETCH_ASSOC);
			return $res;
		}
		return array();
	}
	
	function selectAssoc($query){
		if($quer=$this->_pdo->query($query)){
			$res=$quer->fetch(PDO::FETCH_ASSOC);
			return $res;
		}
		return array();
	}

	function insert($query){
		if($quer=$this->_pdo->query($query)){
			$lastID=$this->_pdo->lastInsertId();
			return array('error'=>false,'lastID'=>$lastID);
		}
		$error=array('error'=>true,'info'=>$this->_pdo->errorInfo());
		return $error;
	}

	function update($query) {
		if($quer=$this->_pdo->query($query)){
			$rowCount=$quer->rowCount();
			return array('rowCount'=>$rowCount);
		}
		$error=$this->_pdo->errorInfo();
		return $error;
	}

	function isConnected(){ return( !is_null($this->_pdo); }
	function getWorkdir(){  return $this->_workdir;    }
	function getFileName(){ return $this->_filename;   }
	function escape($str){  return SQLite3::escapeString($str); } // For now sqlite only!
}
