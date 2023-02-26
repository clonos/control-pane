<?php
/*
(
	[idx] => 1
	[mytable] => forms
	[group_id] => 1
	[order_id] => 1
	[param] => ldap_host
	[desc] => LDAP server
	[def] => 192.168.1.3
	[cur] => 
	[new] => 
	[mandatory] => 1
	[attr] => maxlen=60
	[xattr] => 
	[type] => inputbox
	[link] => 
)
*/
class Forms
{
	private $db='';

	function __construct($jname,$helper='',$db_path=false)
	{
		if($jname==''){
			$database=$helper; 
		}else{
			$database=array('jname'=>$jname,'helper'=>$helper);
		}

		if($jname=='cbsd-settings'){
			$this->db=new Db('cbsd-settings');
		} else if($helper!=''){
			if($db_path!==false){
				$this->db=new Db('file',$db_path);
			}else{
				$this->db=new Db('helper',$database);
			}
		}
	}

	private function fetch_from_db($link)
	{
//		return $this->db->select("select * from ? order by order_id asc", array([$link]));
		return $this->db->select("select * from {$link} order by order_id asc", array());
	}

	function generate(){
		if($this->db->error) return;
		$fields=$this->db->select("select * from forms order by groupname asc, group_id asc, order_id asc", array());

		// Строим карту формы с группами элементов
		$groups=array();
		foreach($fields as $key=>$field){
			$group=$field['groupname'];
			if(!empty($group)){
				if($field['type']=='group_add'){	// Expand
					$groups[$group]['_title']=$field['desc'];
				}else if($field['type']=='delimer'){
					// Delimer
					$groups[$group][$field['group_id']]=$key;
				}else{
					// Other elements
					$groups[$group][$field['group_id']]['_group_id']=$field['group_id'];
					$groups[$group][$field['group_id']][$field['order_id']]=$key;
				}
			}else{
				$groups[]=$key;
			}
		}
		//print_r($fields);print_r($groups);exit;

		$arr=array();
		$last_type='';
		foreach($fields as $key=>$field){
			/*
			if($last_type=='delimer' && $field['type']!='delimer')
				$html.='<div class="pad-head"></div>';
			*/
			$last_type=$field['type'];

			if(isset($field['cur']) && isset($field['def'])){
				if(empty($field['cur'])) $field['cur']=$field['def'];
			}

			$tpl=$this->getElement($field['type'],$field);
			$params=array('param','desc','attr','cur');
			foreach($params as $param){
				if(isset($field[$param]))
					$tpl=str_replace('${'.$param.'}',$field[$param],$tpl);
			}

			//$value=$field['def'];
			//if(isset($field['cur']) && !empty($field['cur'])) $value=$field['cur'];
			$value=$field['cur'];
			$tpl=str_replace('${value}',$value,$tpl);

			$value=$field['def'];
			$tpl=str_replace('${def}',$value,$tpl);

			$required=($field['mandatory']==1)?' required':'';
			$tpl=str_replace('${required}',$required,$tpl);
			$arr[$key]=$tpl;

			//if($field['param']!='-') $currents[$field['param']]=$field['cur'];
			//if($field['param']!='-') $defaults[$field['param']]=$field['def'];
		}

		// Выстраиваем форму по карте
		$html='<form class="helper" name="" onsubmit="return false;"><div class="form-fields">';
		foreach($groups as $key=>$txt){
			if(is_numeric($key)){
				$html.=$arr[$key];
			}else if(is_array($txt)){
				$group_name=key($txt);
				$group_title=$txt['_title'];
				unset($txt['_title']);
				foreach($txt as $key1=>$val1){
					$group_id=$val1['_group_id'];
					unset($val1['_group_id']);
					if(is_array($val1)){
						$html.='<div class="form-field"><fieldset id="ind-'.$group_id.'"><legend>'.$group_title.'</legend>';
						foreach($val1 as $key2=>$val2){
							$html.=$arr[$val2];
						}
						$html.='<div><input type="button" value="delete group" class="fgroup-del-butt" /></div></fieldset></div>';
					}else{
						$html.=$arr[$key1];
					}
				}
				$html.='<div class="form-field"><input type="button" value="add group" class="fgroup-add-butt" /></div>';
			}
		}

		$html.='</div>';
		$html.='<div class="buttons"><input type="button" value="Apply" class="save-helper-values" title="Save and apply params" /> &nbsp; <input type="button" value="Clear" class="clear-helper" title="Restore loaded params" /></div>';
		$html.='</form>';

		return $html;
	}

	function getElement($el,$arr=array())
	{
		$tpl='';
		switch(trim($el)){
			case 'inputbox':
				$res=$this->getInputAutofill($arr);
				if($res===false){
					$list='';
					$datalist='';
				}else{
					$list=' list="'.$res['list'].'"';
					$datalist=$res['datalist'];
				}
				$tpl='<div class="form-field"><input type="text" name="${param}" value="${value}" ${attr}${required}'.$list.' /><span class="default val-${def}" title="Click to fill dafault value">[default]</span><span class="small">${desc}</span>'.$datalist.'</div>';
				//'.$default.'
				break;
			case 'password':
				$tpl='<div class="form-field"><input type="password" name="${param}" value="${value}" ${attr}${required} /><span class="default val-${def}" title="Click to fill dafault value">[default]</span><span class="small">${desc}</span></div>';
				break;
			case 'delimer':
				$tpl='<h1>${desc}</h1>';
				break;
			case 'checkbox':
				$tpl='<input type="checkbox" id="chk-${idx}" name="${param}" /><label for="chk-${idx}">${desc}</label>';
				break;
			case 'select':
				$tpl=$this->getSelect($el,$arr);
				break;
			case 'radio':
				$tpl=$this->getRadio($el,$arr);
				break;
		}
		return $tpl;
	}

	function getInputAutofill($arr)
	{
		if(isset($arr['link']) && $arr['link']!=''){
			$id=$arr['link'];	//$arr['param'].'-'.
			$tpl='<datalist id="'.$id.'">';
			$opts = $this->fetch_from_db($arr['link']);
			foreach($opts as $key=>$opt){
				$tpl.='<option>'.$opt['text'].'</option>';
			}
			$tpl.='</datalist>';

			Utils::clonos_syslog("forms.php template: " . $tpl);

			return array('list'=>$id,'datalist'=>$tpl);
		}else {
			return false;
		}
	}

	function getSelect($el,$arr)
	{
		$tpl='<div class="form-field"><select name="${param}">';
		if(isset($arr['link'])){
			$opts = $this->fetch_from_db($arr['link']);
			// Пустое поле в списках оказалось ненужным!
			//array_unshift($opts,array('id'=>0,'text'=>'','order_id'=>-1));
			foreach($opts as $key=>$opt){
				$selected=($opt['id']==$arr['cur'])?' selected':'';
				$tpl.='<option value="'.$opt['id'].'"'.$selected.'>'.$opt['text'].'</option>';
			}
		}
		$tpl.='</select><span class="default val-${def}" title="Click to fill dafault value">[default]</span><span class="small">${desc}</span></div>';
		return $tpl;
	}

	function getRadio($el,$arr)
	{
		$tpl='<div class="form-field"><fieldset><legend>${desc}</legend>';
		if(isset($arr['link'])){
			$opts = $this->fetch_from_db($arr['link']);
			foreach($opts as $key=>$opt){
				$checked=($opt['id']==$arr['cur'])?' checked':'';
				$tpl.='<label for="${param}-'.$opt['id'].'">'.$opt['text'].':</label><input type="radio" name="${param}" value="'.$opt['id'].'" id="${param}-'.$opt['id'].'"'.$checked.' />';
			}
		}
		$tpl.='</fieldset></div>';
		return $tpl;
	}

}