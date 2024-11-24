<?php

class Localization
{
	private $language='en';
	private $translate_arr=array();

	function __construct($realpath_public)
	{
		(isset($_COOKIE['lang'])) AND $this->language=$_COOKIE['lang'];
		(!array_key_exists($this->language, Config::$languages)) AND $this->language='en';
		include($realpath_public.'/lang/'.$this->language.'.php');
		$this->translate_arr=$lang;
	}

	public function get_lang()
	{
		return $this->language;
	}

	public function translate($phrase)
	{
		return (isset($this->translate_arr[$phrase])) ? $this->translate_arr[$phrase] : $phrase;
	}
}



class Translate
{
	private $locale='';
	private $language='';
	private $realpath='';
	
	function __construct($locale,$realpath)
	{
		
		$this->locale=$locale;
		$this->language=$this->locale->get_lang();
		$this->realpath=$realpath;
	}
	
	public function translate($path,$file_name)
	{
		$file=$path.$file_name;
		$db=new Db('clonos');
		if(!$db->isConnected())	return array('error'=>true,'error_message'=>'db connection lost!');
		//$status=(new Db('base','cbsdtaskd'))->selectOne("SELECT status,logfile,errcode 
		//			FROM taskd WHERE id=?", array([$task_id]);

		if(file_exists($file))
		{
			$is_changed=false;
			$txt=file_get_contents($file);
			preg_match_all('#<translate([^>]*)>(.*)</translate>#',$txt,$res,PREG_SET_ORDER);
//			var_dump($res);exit;
			
			foreach($res as $item)
			{
				$id=-1;
				//$text='';
				$update=false;
				$params=[];
				$tag=$item[0];
				$attrs=$item[1];
				$text=$item[2];
				
				if($attrs!='')
				{
					//$txt=$item[2];
		//echo '<pre>'.$txt;
					preg_match_all('#((id)="?([\d]+)"?|update)#',$attrs,$params,PREG_SET_ORDER);
					if(is_array($params) && $this->language!='en')
					{
						// если у пользователя язык интерфейса не английский, то переводим
						foreach($params as $p)
						{
							if(isset($p[2]) && $p[2]=='id')
							{
								if(is_numeric($p[3]))
								{
									$id=$p[3];
									$dbres=$db->selectOne("select text from lang_en where id=?",[[$id,PDO::PARAM_INT]]);
									if(!empty($dbres))
									{
										var_dump($dbres);exit;
									}else{
										echo 'no data';
									}
									
									
								}else{
								}
							}
							
							if($p[0]=='update' && $id>0)
							{
								echo "\tupdate id: ",$id,"\n";
							}

							
			//echo "\tupdate: ",var_dump($update),"\n";
						}
						
					}else{
						
					}
					//print_r($params);
				}else{
					

/*
delete FROM "lang_en";
VACUUM;
UPDATE SQLITE_SEQUENCE SET seq = 0 WHERE name = 'lang_en'
*/
					
					$dbres=$db->selectOne("select id from lang_en where text=?",[[$text, PDO::PARAM_STR]]);	//,[[$text, PDO::PARAM_STR]]);
					if(isset($dbres['error']) && $dbres['error'])
					{
						echo 'error db: ',$dbres['info'];
						exit;
					}
					if(is_numeric($dbres['id']))
					{
						// если фраза есть в базе, то вписываем её ID в тэг
						$new_text='<translate id="'.$dbres['id'].'">'.$text."</translate>";
						$txt=str_replace($tag,$new_text,$txt);
						$is_changed=true;
					}else{
						if($dbres===false)
						{
							// если фразы нет в базе, то добавляем её туда и вписываем новый ID в тэг
							$dbres=$db->insert("insert into lang_en (text) values (?)",[[$text, PDO::PARAM_STR]]);
							
							if($dbres['error'])
								return array('error'=>true,'error_message'=>$dbres['info']);
							
							$new_text='<translate id="'.$dbres['lastID'].'">'.$text."</translate>";
							$txt=str_replace($tag,$new_text,$txt);
							$is_changed=true;
						}
					}
				}
			}
			
			if($is_changed)
			{
				rename($path.$file_name,$path.'orig.'.$file_name);
				file_put_contents($path.$file_name,$txt);
			}
			echo $txt;
			exit;
		}else{
			
		}

		/*
			<translate([^>]*)>(.*)</translate>
		*/
	}
}