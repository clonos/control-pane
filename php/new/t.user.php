<?php
trait tUser {
	
	private $_user_info=array(
		'id'=>0,
		'username'=>'guest',
		'unregistered'=>true,
		'error'=>false,
	);

	function userAutologin()
	{
		if(isset($_COOKIE['mhash'])){
			$memory_hash=$_COOKIE['mhash'];
			$secure_memory_hash=md5($memory_hash.$this->_client_ip);
			$db=new Db('clonos');
			if($db->isConnected()){
				$query="SELECT au.id,au.username FROM auth_user au, auth_list al WHERE al.secure_sess_id=? AND au.id=al.user_id AND au.is_active=1";
				//echo $query;exit;
				$res=$db->selectOne($query, array([$secure_memory_hash]));
				//echo $this->_client_ip;
				//print_r($res);exit;
				if(!empty($res)){
					$res['error']=false;
					return $res;
				}
			}
		}
		return array('error'=>true);
	}
	
	function userRegister($user_info=array()){
		if(empty($user_info)) return false;
		if(isset($user_info['username']) && isset($user_info['password'])){
			$db=new Db('clonos');
			if($db->isConnected()) {
				$res=$db->selectOne("SELECT username FROM auth_user WHERE username=?", array([$user_info['username']]));
				if(!empty($res)){
					$res['user_exists']=true;	// было user_exsts, похоже была опечатка
					return $res;
				}

				$password=$this->getPasswordHash($user_info['password']);
				$is_active=0;
				if(isset($user_info['actuser']) && $user_info['actuser']=='on') $is_active=1;
				$query=$db->query_protect("INSERT INTO auth_user
					(username,password,first_name,last_name,is_active,date_joined) VALUES
					(?,?,?,?,?,datetime('now','localtime'))");
				$res=$db->insert($query, array(
					[$user_info['username']],
					[$password],
					[$user_info['first_name']],
					[$user_info['last_name']],
					[$is_active]
				));
				return array('error'=>false,'res'=>$res);
			}
		}
	}

	function userRegisterCheck($user_info=array()){
		/*
		[0] => Array
		(
			[id] => 1
			[username] => admin
			[password] => 01...87a
			[first_name] => Admin
			[last_name] => Admin
			[last_login] => 
			[is_active] => 1
			[date_joined] => 2017-12-02 00:09:00
			[sess_id] => 
			[secure_sess_id] => 
		)
		*/
		if(empty($user_info)) return false;
		if(isset($user_info['login']) && isset($user_info['password'])){
			$db=new Db('clonos');
			if($db->isConnected()){
				$pass=$this->getPasswordHash($user_info['password']);
				//echo "SELECT id,username,password FROM auth_user WHERE username=".$user_info['login']." AND is_active=1";
				$res=$db->selectOne("SELECT id,username,password FROM auth_user WHERE username=? AND is_active=1", array([$user_info['login']]));
				if(empty($res) || $res['password'] != $pass){
					sleep(3);
					return array('errorCode'=>1,'message'=>'user not found!');
				}
				$res['errorCode']=0;

				$id=(int)$res['id'];
				$memory_hash=md5($id.$res['username'].time());
				$secure_memory_hash=md5($memory_hash.$this->_client_ip);

				/*
				$query="update auth_user set sess_id=?, secure_sess_id=?, last_login=datetime('now','localtime') where id=?";
				$db->update($query);
				*/

				//$query="update auth_list set secure_sess_id=?,auth_time=datetime('now','localtime') where sess_id=?";	//sess_id='${memory_hash}',
				$query="UPDATE auth_list 
						SET sess_id=?,secure_sess_id=?,auth_time=datetime('now','localtime') 
						WHERE user_id=? AND user_ip=?";
				$qres=$db->update($query, array(
					[$memory_hash],
					[$secure_memory_hash],
					[$id],
					[$this->_client_ip]
				));
				//print_r($qres);
				if(isset($qres['rowCount'])){
					if($qres['rowCount']==0){
						$query="INSERT INTO auth_list
							(user_id,sess_id,secure_sess_id,user_ip,auth_time) VALUES
							(?,?,?,?,datetime('now','localtime'))";
						$qres=$db->insert($query, array(
							[$id],
							[$memory_hash],
							[$secure_memory_hash],
							[$this->_client_ip]
						));
					}
				}

				setcookie('mhash',$memory_hash,time()+1209600,'/');

				return $res;
			}
		}
		return array('message'=>'unregistered user','errorCode'=>1);
	}

	function getUserName(){
		return $this->_user_info['username'];
	}
	
	function getPasswordHash($password){
		return hash('sha256',hash('sha256',$password).$this->getSalt());
	}

	private function getSalt(){
		$salt_file='/var/db/clonos/salt';
		if(file_exists($salt_file)) return trim(file_get_contents($salt_file));
		return 'noSalt!';
	}

	function ccmd_usersAdd(){
		$form=$this->form;

		$res=$this->userRegister($form);
		if($res!==false){
			if(isset($res['user_exists']) && $res['user_exists']){
				return array('error'=>true,'errorType'=>'user-exists','errorMessage'=>'User always exists!');
			}
			return $res;
		}
		return array('form'=>$form);
	}

	function ccmd_usersEdit(){
		$form=$this->form;

		if(!isset($form['user_id']) || !is_numeric($form['user_id']) || $form['user_id']<1)
			return array('error'=>true,'error_message'=>'incorrect data!');

		$db=new Db('clonos');
		if(!$db->isConnected())	return array('error'=>true,'error_message'=>'db connection lost!');

		$user_id=(int)$form['user_id'];
		$username=$form['username'];
		$first_name=$form['first_name'];
		$last_name=$form['last_name'];
		$is_active=0;
		if(isset($form['actuser']) && $form['actuser']=='on') $is_active=1;

		$authorized_user_id=0;
		if(isset($_COOKIE['mhash'])){
			$mhash=$_COOKIE['mhash'];
			if(!preg_match('#^[a-f0-9]{32}$#',$mhash)) return array('error'=>true,'error_message'=>'Bad data');
			$query1="select user_id from auth_list WHERE sess_id=? limit 1";
			$res1=$db->selectOne($query1, array([$mhash]));
			if($res1['user_id']>0){
				$authorized_user_id=$res1['user_id'];
			} else {
				return array('error'=>true,'error_message'=>'you are still not authorized');
			}
		} else {
			return array('error'=>true,'error_message'=>'you must be authorized for this operation!');
		}

		if($user_id==0 || $user_id!=$authorized_user_id){
			return array('error'=>true,'error_message'=>'I think you\'re some kind of hacker');
		}

		if(isset($form['password'])){
			$password=$this->getPasswordHash($form['password']);
			$query="UPDATE auth_user SET username=?,password=?,first_name=?,last_name=?,is_active=? WHERE id=?";
			$res=$db->update($query, array(
				[$username],
				[$password],
				[$first_name],
				[$last_name],
				[$is_active],
				[(int)$user_id]
			));
		} else {
			$query="UPDATE auth_user SET username=?,first_name=?,last_name=?,is_active=? WHERE id=?";
			$res=$db->update($query, array(
				[$username],
				[$first_name],
				[$last_name],
				[$is_active],
				[(int)$user_id]
			));
		}
		return array('error'=>false,'res'=>$res);
	}

	function ccmd_userRemove(){
		$id=$this->form['user_id'];
		if(is_numeric($id) && $id>0){
			$query="DELETE FROM auth_user WHERE id=?";
			$db=new Db('clonos');
			if(!$db->isConnected()) return array('error'=>true,'error_message'=>'DB connection error!');

			$res=$db->select($query, array([(int)$id, PDO::PARAM_INT]));
			return $res;
		}
	}

	function ccmd_userEditInfo(){
		if(!isset($this->form['user_id'])) return array('error'=>true,'error_message'=>'incorrect data!');

		$db=new Db('clonos');
		if(!$db->isConnected()) return array('error'=>true,'error_message'=>'DB connection error!');
		$user_id=(int)$this->form['user_id'];

		$res=$db->selectOne("SELECT username,first_name,last_name,is_active AS actuser FROM auth_user WHERE id=?", array([$user_id]));
		return array(
			'dialog'=>$this->form['dialog'],
			'vars'=>$res,
			'error'=>false,
			'tblid'=>$this->form['tbl_id'],
			'user_id'=>$user_id,
		);
	}

	function ccmd_userGetInfo(){
		$db=new Db('clonos');
		if(!$db->isConnected()) return array('DB connection error!');

		$res=$db->selectOne("SELECT * FROM auth_user", array()); // TODO: What?!
		return $res;
	}

}