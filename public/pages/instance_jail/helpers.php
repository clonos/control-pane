<?php

$hash=$this->url_hash;	//=preg_replace('/^#/','',$this->_vars['hash']);

$db_path=false;
if(isset($this->_vars['db_path']) && !empty($this->_vars['db_path']))
{
	$db_path=$this->_vars['db_path'];
	$res=$this->cbsd_cmd('make_tmp_helper module='.$hash);
	if($res['retval']==0)
	{
		$db_path=$res['message'];
	}else{
		echo json_encode(array('error'=>true,'errorMessage'=>'Error on open temporary form file!'));
		return;
	}
}

$form=new Forms('',$hash,$db_path);
$res=$form->generate();

$freejname='';
$jres=$this->getFreeJname(true);
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
				<input type="text" name="ip4_addr" value="DHCP" pattern="^DHCP$|^(?:[0-9]{1,3}\.){3}[0-9]{1,3}(\/[\d]{1,3})?$" required="required" />
			</p>
		</div>
	</form>
EOT;
	

$html.='<h1>Helper: '.$hash.'</h1>'.$res['html'];

echo json_encode(array('html'=>$html,'func'=>'fillTab'));	//,'currents'=>$res['currents']