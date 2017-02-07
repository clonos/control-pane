<?php
class Forms
{
	private $name='';
	private $db='';
	private $html='';
	
	function __construct($jname,$helper)
	{
		$this->name=$jname;
		$this->db=new Db('helpers',array('jname'=>$jname,'helper'=>$helper));
	}
	
	function generate()
	{
		if($this->db->error) return;
		$query="select * from forms order by group_id asc, order_id asc";
		$fields=$this->db->select($query);
		//echo '<pre>';print_r($fields);
		$defaults=array();

		$last_type='';
		$this->html='<form name=""><div class="form-fields">';
		foreach($fields as $key=>$field)
		{
			/*
			if($last_type=='delimer' && $field['type']!='delimer')
				$this->html.='<div class="pad-head"></div>';
			*/
			$last_type=$field['type'];
				
			$tpl=$this->getElement($field['type'],$field);
			$params=array('param','desc','attr','cur');
			foreach($params as $param)
			{
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
			$this->html.=$tpl;
			
			if(!empty($field['def'])) $defaults[$key]=$field['def'];
		}
		$this->html.='</div>';
		
		$this->setButtons();
		$this->html.='</form>';
		return array('html'=>$this->html,'defaults'=>$defaults);
	}
	
	function getElement($el,$arr=array())
	{
		$tpl='';
		switch(trim($el))
		{
			case 'inputbox':
				$tpl='<div class="form-field"><input type="text" name="${param}" value="${value}" ${attr}${required} /><span class="default val-${def}" title="Click to fill dafault value">[default]</span><span class="small">${desc}</span></div>';
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
	
	function getSelect($el,$arr)
	{
		$tpl='<div class="form-field"><select name="${param}">';
		if(isset($arr['link']))
		{
			$query="select * from {$arr['link']} order by order_id asc";
			$opts=$this->db->select($query);
			array_unshift($opts,array('id'=>0,'text'=>'','order_id'=>-1));
			if(!empty($opts))foreach($opts as $key=>$opt)
			{
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
		if(isset($arr['link']))
		{
			$query="select * from {$arr['link']} order by order_id asc";
			$opts=$this->db->select($query);
			if(!empty($opts))foreach($opts as $key=>$opt)
			{
				$checked=($opt['id']==$arr['cur'])?' checked':'';
				$tpl.='<label for="${param}-'.$opt['id'].'">'.$opt['text'].':</label><input type="radio" name="${param}" value="'.$opt['id'].'" id="${param}-'.$opt['id'].'"'.$checked.' />';
			}
		}
		$tpl.='</fieldset></div>';
		return $tpl;
	}
	
	function setButtons($arr=array())
	{
		$this->html.='<div class="buttons"><input type="button" value="Apply" class="save-helper-values" title="Save and apply params" /> &nbsp; <input type="button" value="Clear" class="clear-helper" title="Restore loaded params" /></div>';
	}
}

/*

$form=new Forms('php');
?>
<html>
<style>
body {font-size:100%;font-family:Tahoma,'Sans-Serif',Arial;}
h1 {color:white;background:silver;margin:0;padding:10px;}
.small {font-size:x-small;}
.form-field {padding:4px 10px 0 10px;margin:0 4px; background:#fafafa;}
.form-field span {margin-left:10px;}
.form-field input {width:300px;}
form {border:1px solid gray;padding:0;margin-bottom:10px;width:500px;border-radius:8px;overflow:hidden;box-shadow:4px 4px 6px rgba(0,0,0,0.2);}
.buttons {padding:20px 10px;text-align:center;}
</style>
<?php
$form->generate();
//$form->setButtons(array('save','cancel'));

*/