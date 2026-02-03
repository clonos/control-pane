<?php
trait tRoute {

	public $url_hash;
	public $mode;
	public $json_path;
	public $form;

	function route_test()
	{
		echo 'test';
	}
	
	function route_json()
	{
		if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
			strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest' ||
				!isset($_POST['path']))
					{echo '{}';exit;}

		$_ds=DIRECTORY_SEPARATOR;
		$path=trim($_POST['path'],$_ds);
			
		$this->json_req=true;

		if(isset($this->_vars['path'])){	
			self::$realpath_page=self::$realpath_pages.$this->_uri_chunks[0].'/';
			$this->json_path=self::$realpath_pages.$path.$_ds;
			self::$json_name=$this->json_path.'a.json.php';
		}else if($_SERVER['REQUEST_URI']){
			if(isset($this->_uri_chunks[0])){
				self::$realpath_page=self::$realpath_public.'pages/'.$this->_uri_chunks[0].'/';
			}
		}

		if(isset($this->_vars['hash'])) $this->url_hash=preg_replace('/^#/','',$this->_vars['hash']);
		if(isset($this->_vars['mode'])) $this->mode=$this->_vars['mode'];
		if(isset($this->_vars['form_data'])) $this->form=$this->_vars['form_data'];

		if($this->_post && isset($this->mode)){
			if(isset($this->_user_info['error']) && $this->_user_info['error']){
				if($this->mode!='login'){
					echo json_encode(array('error'=>true,'unregistered_user'=>true));
					exit;
				}
			}

			if($this->_user_info['unregistered'] && $this->mode!='login'){
				echo json_encode(array('error'=>true,'unregistered_user'=>true));
				exit;
			}

			// JSON functions, running without parameters
			$new_array=array();
			$cfunc='ccmd_'.$this->mode;
			if(method_exists($this,$cfunc)){
				$ccmd_res=array();
				$ccmd_res=$this->$cfunc();
				
				if(is_array($ccmd_res)){
					$new_array=array_merge($this->sys_vars,$ccmd_res);
				} else {
					echo json_encode($ccmd_res);
					return;
				}
				echo json_encode($new_array);
				return;
				//exit;
			}else{
				echo json_encode(['error'=>true,'error_message'=>'method «'.$this->mode.'» not exists...']);
				return;
			}

			$included_result_array='';
			switch($this->mode){
				case 'getTasksStatus':
					echo json_encode($this->_getTasksStatus($this->form['jsonObj'])); return;
				case 'helpersAdd':
					echo json_encode($this->helpersAdd($this->mode)); return;
				case 'addHelperGroup':
					echo json_encode($this->addHelperGroup($this->mode)); return;
				case 'deleteHelperGroup':
					echo json_encode($this->deleteHelperGroup($this->mode)); return;
				case 'saveHelperValues':
					$redirect='/jailscontainers/';
				case 'jailAdd':
					if(!isset($redirect)) $redirect=''; echo json_encode($this->jailAdd($redirect)); return;

				if(!method_exists($this,$cfunc))
				{
					$this->vars['error']=true;
					$this->vars['error_message']='PHP Method is not exists: '.$this->mode;
				}
			}
		}
		exit;
	}
	
	function route_download()
	{
	#	Функцию нужно доделать. Она просто скопирована из старого файла
		if(isset($_GET['file'])){
			$file=$_GET['file'];
			$filename=$file;
		}else{
			header('HTTP/1.0 404 Not Found');
			exit;
		}

		//$res=$clonos->userAutologin();
		$res=$this->userInfo;

		if(isset($res['id']) && $res['id']>0){

			$file=$this->media_import.$file;

			header('Content-disposition: attachment; filename='.$filename);
			header('Content-type: application/octet-stream');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: '.filesize($file));
			header("Pragma: no-cache");
			header("Expires: 0");

			$chunkSize = 1024 * 1024;
			$handle = fopen($file, 'rb');
			while (!feof($handle))
			{
				$buffer = fread($handle, $chunkSize);
				echo $buffer;
				ob_flush();
				flush();
			}
			fclose($handle);

			exit;
		}

		header('HTTP/1.1 401 Unauthorized');
		exit;
	}
	
	function route_upload()
	{
	#	Доделать функцию!
		header('Content-Type: application/json');

		$cmd='';
		$status = '';

		if($_SERVER['REQUEST_METHOD'] === 'POST'){
			$path=self::$realpath_media;
			if(isset($_POST['uplace'])){
				$res=strpos($_POST['uplace'],'jailscontainers');
				if($res!==false){
					$path=self::$media_import;
					$cmd='import';
				}
				$res=strpos($_POST['uplace'],'imported');
				if($res!==false){
					$path=self::$media_import;
					$cmd='import';
				}
			}

			// https://www.php.net/manual/en/features.file-upload.php
			// Undefined | Multiple Files | $_FILES Corruption Attack
			// If this request falls under any of them, treat it invalid.
			if (
				!isset($_FILES['file']['error']) ||
				is_array($_FILES['file']['error'])
			) {
				echo json_encode(array('status' => 'Upload Fail: An error occurred!'));
				exit;
			}

			if(is_uploaded_file($_FILES['file']['tmp_name'])){
				$basename = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_BASENAME));

				if (move_uploaded_file($_FILES['file']['tmp_name'], $path.$basename)){
					$status = 'ok';	//'Successfully uploaded!';
					if($cmd=='import'){
						$res=CBSD::run('task owner=%s mode=new /usr/local/bin/cbsd jimport jname=%s inter=0', [$this->getUserName(), $path.$basename]);
					}
				} else {
					$status = 'Upload Fail: Unknown error occurred!';
				}
			}
		}

		if($status!='ok'){
			echo json_encode(array('status' => $status));
			exit;
		}
	}
	
	function route_vnc()
	{
		if(!isset($_GET['jname'])){
			echo 'You forgot to specify a name of jail!';
			exit;
		}
		
		$jname=trim(preg_replace('/\t+|\r|\n/', '', $_GET['jname']));
		$this->_jname=$jname;
		
		if ($jname != escapeshellcmd($jname)){
			ClonOS::syslog("cmd.php SHELL ESCAPE:". $jname);
			die("Shell escape attempt");
		}
		
		include($this->realpath_php.'vnc.validate.php');
		$res = (new Db('base','local'))->selectOne("SELECT vnc_password FROM bhyve WHERE jname=?", array([$jname]));

		$pass = ($res !== false) ? $res['vnc_password'] : 'cbsd';

		$permit=$_SERVER['REMOTE_ADDR'];

	//	clonos_syslog("vnc.php run: vm_vncwss jname={$jname} permit={$permit}");

		$res=CBSD::run("vm_vncwss jname={$jname} permit={$permit}",array());
	//	, array($jname, $_SERVER['REMOTE_ADDR']));

		// HTTP_HOST is preferred for href
		if (isset($_SERVER['HTTP_HOST']) && !empty(trim($_SERVER['HTTP_HOST']))){
			$nodeip = $_SERVER['HTTP_HOST'];
			$nodeip = parse_url($nodeip, PHP_URL_HOST);
		} else {
			# use localhost as fallback in case the HTTP_HOST header is not set
			$nodeip = '127.0.0.1';
		}

		// HTTP_HOST is IP, try to check SERVER_NAME
		if (filter_var($nodeip, FILTER_VALIDATE_IP)) {
			$nodeip = $_SERVER['SERVER_ADDR'];
			// https://www.php.net/manual/en/reserved.variables.server.php
			// Note: Under Apache 2, you must set UseCanonicalName = On and ServerName. 
			// handle when 'server_name _;' - use IP instead
			if(isset($_SERVER['SERVER_NAME']) && !empty(trim($_SERVER['SERVER_NAME'])) && (strcmp($_SERVER['SERVER_NAME'], "_") != 0)){
				$nodeip = $_SERVER['SERVER_NAME'];
			}
		} else {
			$nodeip = $_SERVER['SERVER_ADDR'];
		}

		# TODO: This will send the pass in clear text
		header('Location: http://'.$nodeip.':6081/vnc_lite.html?scale=true&host='.$nodeip.'&port=6081?password='.$pass);
		exit;
		
	}

}