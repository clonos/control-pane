<?php

require_once("../php/cbsd.php");

$hash=$this->url_hash;	//=preg_replace('/^#/','',$this->_vars['hash']);
 
$db_path=false;
if(!isset($this->_vars['db_path']))
{
	//$db_path=$this->_vars['db_path'];
	$res=CBSD::run('make_tmp_helper module=%s', [$hash]);
	if($res['retval']==0){
		$db_path=$res['message'];
	}else{
		echo json_encode(array('error'=>true,'errorMessage'=>'Error on open temporary form database!'));
		return;
	}
}else{
	$db_path=$this->_vars['db_path'];
}

$freejname='';
$jres=$this->ccmd_getFreeJname(false,'jail');
if(!$jres['error']) $freejname=$jres['freejname'];

$jname_desc=$this->translate('will be created new jail with helper inside');
$jail_sett=$this->translate('Jail Settings');
$jail_name=$this->translate('Jail name');
$ip_address=$this->translate('IP address');
$html=<<<EOT
	<form class="win" method="post" id="newJailSettings" onsubmit="return false;">
		<div class="form-fields">
			<h1>{$jail_sett} <small>({$jname_desc})</small></h1>
			<p>
				<span class="field-name">{$jail_name}:</span>
				<input type="text" name="jname" value="{$freejname}" pattern="[^0-9]{1}[a-zA-Z0-9]{2,}" required="required" class="edit-disable" />
			</p>
			<p>
				<span class="field-name">{$ip_address}:</span>
				<input type="text" name="ip4_addr" value="DHCP" pattern="^DHCP$|^DHCP[vV]6$|^(?:[0-9]{1,3}\.){3}[0-9]{1,3}(\/[\d]{1,3})?$" required="required" />
			</p>
		</div>
	</form>
EOT;

$res_html=(new Forms('',$hash,$db_path))->generate();
$html.='<h1>Helper: '.$hash.'</h1>'.$res_html;

return array('html'=>$this->html);
//echo json_encode(array('html'=>$html,'func'=>'fillTab'));