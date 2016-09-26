<?php
require_once 'cbsd.inc.php';
//=============================================================================
// universal exec
function ex($cfe) {
//    $objResponse=new xajaxResponse();
    
    $res = "";
    if (!empty($cfe)){
        if(function_exists('exec')){
            @exec($cfe,$res);
            $res = join("\n",$res);
        }
        elseif(function_exists('shell_exec')){
            $res = @shell_exec($cfe);
        }
        elseif(function_exists('system')){
            @ob_start();
            @system($cfe);
            $res = @ob_get_contents();
            @ob_end_clean();
        }
        elseif(function_exists('passthru')){
            @ob_start();
            @passthru($cfe);
            $res = @ob_get_contents();
            @ob_end_clean();
        }
        elseif(@is_resource($f = @popen($cfe,"r"))){
            $res = "";
            while(!@feof($f)) {
        	$res .= @fread($f,2096);
    	    }
            @pclose($f);
        }
    }

//    $objResponse->assign("res","value",$res);
   
//    return $objResponse;
    return $res;
}


//=============================================================================
// stop jail
function launchvnc($jname) {
	global $workdir;
	global $client_ip;
	global $server_name;

//	$objResponse=new xajaxResponse();
//	$objResponseManager = xajaxResponseManager::getInstance();

	$action=array();

//	if ($objResponseManager->getConfiguration('debug') == true) {
//		$objResponse->alert("Jname '".$jname."'.");
//	}

	$action=cmd(CBSD_CMD."vm_vncwss jname=$jname permit=$client_ip");

	$retstr="";
	if($action['retval']==0) {
		$lst=explode("\n",$action['message']);
		if(!empty($lst)) foreach($lst as $item) {
			$retstr.=$item;
		}
	}

//	if ($objResponseManager->getConfiguration('debug') == true) {
//		$objResponse->alert("action '".print_r($action, true)."'.");
//	}

	$db = new SQLite3("$workdir/var/db/local.sqlite"); $db->busyTimeout(5000);
	$sql = "SELECT nodeip FROM local;";
	$result = $db->query($sql);//->fetchArray(SQLITE3_ASSOC);
	$row = $result->fetchArray();
	list($nodeip)=$row;
	if (strlen($nodeip)<10)
		$nodeip="127.0.0.1";
	$db->close();

	header('Location: http://' . $nodeip . ':6080/vnc_auto.html?host=' . $nodeip . '&port=6080');
	return;
}



//=============================================================================
// stop jail
function jstop($jname) {
    $objResponse=new xajaxResponse();
    $objResponseManager = xajaxResponseManager::getInstance();
    
    $action=array();

//    session_start();

    if ($objResponseManager->getConfiguration('debug') == true) {
	$objResponse->alert("Jname '".$jname."'.");
    }
    
    $action=cmd(CBSD_CMD."task owner=cbsdweb mode=new /usr/local/bin/cbsd jstop inter=0 jname=$jname");
sleep(10);
    $objResponse->call('reloadPage', true);

    $retstr="";
////    cmd retval message error error_message
//    if($action['cmd']==0) {
//	$lst=explode("\n",$action['cmd']);
//        if(!empty($lst)) foreach($lst as $item) {
//	    $retstr.=$item;
//        }
//    }
//    if($action['retval']==0) {
//	$lst=explode("\n",$action['retval']);
//        if(!empty($lst)) foreach($lst as $item) {
//	    $retstr.=$item;
//        }
//    }
    if($action['retval']==0) {
	$lst=explode("\n",$action['message']);
        if(!empty($lst)) foreach($lst as $item) {
	    $retstr.=$item;
        }
    }
//    if($action['error']==0) {
//	$lst=explode("\n",$action['error']);
//        if(!empty($lst)) foreach($lst as $item) {
//	    $retstr.=$item;
//        }
//    }
//    if($action['error_message']==0) {
//	$lst=explode("\n",$action['error_message']);
//        if(!empty($lst)) foreach($lst as $item) {
//	    $retstr.=$item;
//        }
//    }

//    $objResponse->appendResponse(ex("env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd task owner=cbsdweb mode=new /usr/local/bin/cbsd jstop inter=0 jname=$jname"));
//    $objResponse->appendResponse(cmd(CBSD_CMD."task owner=cbsdweb mode=new /usr/local/bin/cbsd jstop inter=0 jname=$jname"));

    if ($objResponseManager->getConfiguration('debug') == true) {
	$objResponse->alert("action '".print_r($action, true)."'.");
//        $objResponse->alert("action '".$retstr."'.");
    }
//    $objResponse->script("window.location.reload(true);");
//    if($action!=0){
//	$objResponse->redirect("https://samson.bsdstore.ru/jailscontainers/");
//    }
            
    return $objResponse;   
}








//=============================================================================
// start jail
function jstart($jname) {
    $objResponse=new xajaxResponse();
    $objResponseManager = xajaxResponseManager::getInstance();
    
    $action=array();
    
//    session_start();
    
    if ($objResponseManager->getConfiguration('debug') == true) {
	$objResponse->alert("Jname '".$jname."'.");
    }
    
    $action=cmd(CBSD_CMD."task owner=cbsdweb mode=new /usr/local/bin/cbsd jstart inter=0 jname=$jname");
    sleep(10);
    $objResponse->call('reloadPage', true);
    
    $retstr="";
////    cmd retval message error error_message
//    if($action['cmd']==0) {
//	$lst=explode("\n",$action['cmd']);
//        if(!empty($lst)) foreach($lst as $item) {
//	    $retstr.=$item;
//        }
//    }
//    if($action['retval']==0) {
//	$lst=explode("\n",$action['retval']);
//        if(!empty($lst)) foreach($lst as $item) {
//	    $retstr.=$item;
//        }
//    }
    if($action['retval']==0) {
	$lst=explode("\n",$action['message']);
        if(!empty($lst)) foreach($lst as $item) {
	    $retstr.=$item;
        }
    }
//    if($action['error']==0) {
//	$lst=explode("\n",$action['error']);
//        if(!empty($lst)) foreach($lst as $item) {
//	    $retstr.=$item;
//        }
//    }
//    if($action['error_message']==0) {
//	$lst=explode("\n",$action['error_message']);
//        if(!empty($lst)) foreach($lst as $item) {
//	    $retstr.=$item;
//        }
//    }

//    $objResponse->appendResponse(ex("env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd task owner=cbsdweb mode=new /usr/local/bin/cbsd jstart inter=0 jname=$jname"));
//    $objResponse->appendResponse(cmd(CBSD_CMD."task owner=cbsdweb mode=new /usr/local/bin/cbsd jstart inter=0 jname=$jname"));

    if ($objResponseManager->getConfiguration('debug') == true) {
        $objResponse->alert("action '".print_r($action, true)."'.");
//        $objResponse->alert("action '".$retstr."'.");
    }
//    $objResponse->script("window.location.reload(true);");
//    if($action!=0){
//	$objResponse->redirect("https://samson.bsdstore.ru/jailscontainers/");
//    }
            
    return $objResponse;   
}

//=============================================================================
// remove jail
function jremove($jname) {
    $objResponse=new xajaxResponse();
    $objResponseManager = xajaxResponseManager::getInstance();
    
    $action=array();
    
//    session_start();

    if ($objResponseManager->getConfiguration('debug') == true) {
        $objResponse->alert("Jname '".$jname."'.");
    }
    
    $action=cmd(CBSD_CMD."task owner=cbsdweb mode=new /usr/local/bin/cbsd jremove inter=0 jname=$jname");
sleep(10);
    $objResponse->call('reloadPage', true);

    if ($objResponseManager->getConfiguration('debug') == true) {
        $objResponse->alert("action '".print_r($action, true)."'.");
    }
//    $objResponse->script("window.location.reload(true);");
//    if($action!=0){
//	$objResponse->redirect("https://samson.bsdstore.ru/jailscontainers/");
//    }
            
    return $objResponse;   
}

//=============================================================================
// jail create
function jcreate($formvalues) {
    $objResponse=new xajaxResponse();
    $objResponseManager = xajaxResponseManager::getInstance();
    
    $action=array();

    $tpl=getJailTemplate($formvalues);

    if ($objResponseManager->getConfiguration('debug') == true) {
        $objResponse->alert("tpl:'".$tpl."'.");
    }
        
    $file_name='/tmp/'.$formvalues['jname'].'.conf';
    file_put_contents($file_name,$tpl);
    $action=cmd(CBSD_CMD.'task owner=cbsdweb mode=new /usr/local/bin/cbsd jcreate inter=0 jconf='.$file_name);
    sleep(10);
    $objResponse->call( 'reloadPage', true );

    if ($objResponseManager->getConfiguration('debug') == true) {
//        $objResponse->alert("prob jcreate'".$formvalues['jname']."'.");
	$objResponse->alert("prob jcreate'".print_r($formvalues, true)."'.");
    }

    return $objResponse;
}


//=============================================================================
// node add
function nodeadd($formvalues) {
    $objResponse=new xajaxResponse();
    $objResponseManager = xajaxResponseManager::getInstance();

    $action=array();

    if ($objResponseManager->getConfiguration('debug') == true) {
        $objResponse->alert("tpl:'".$tpl."'.");
    }

    $action=cmd(CBSD_CMD.'task owner=cbsdweb mode=new /usr/local/bin/cbsd node inter=0 mode=add node='.$formvalues['address'].' pw='.$formvalues['password'].' port='.$formvalues['sshport']);
    sleep(10);
//    $handle=popen("env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd task owner=cbsdweb mode=new /usr/local/bin/cbsd node inter=0 mode=add node=$address pw=$password port=$sshport", "r");
    $objResponse->call( 'reloadPage', true );

    if ($objResponseManager->getConfiguration('debug') == true) {
//        $objResponse->alert("prob jcreate'".$formvalues['jname']."'.");
	$objResponse->alert("prob nodeadd'".print_r($formvalues, true)."'.");
    }

    return $objResponse;
}




//=============================================================================
// jail clonerename
function jclone($formvalues) {
	$objResponse=new xajaxResponse();
	$objResponseManager = xajaxResponseManager::getInstance();

	$action=array();

	if ($objResponseManager->getConfiguration('debug') == true) {
		$objResponse->alert("tpl:'".$tpl."'.");
	}

	$new_hostname="";

	$newname=$formvalues['new_jname'];
	$oldname=$formvalues['old_jname'];
	$new_hostname=$formvalues['new_hostname_cl'];

	if ($objResponseManager->getConfiguration('debug') == true) {
		$objResponse->alert("prob jclone'".print_r($formvalues, true)."'.");
	}

	if (strlen($new_hostname)>2) {
		$action=cmd(CBSD_CMD."task owner=cbsdweb mode=new /usr/local/bin/cbsd jclone old=$oldname new=$newname host_hostname=$new_hostname");
	} else {
		$action=cmd(CBSD_CMD."task owner=cbsdweb mode=new /usr/local/bin/cbsd jclone old=$oldname new=$newname");
	}

	sleep(2);

	return $objResponse;
}



//=============================================================================
// jail rename
function jrename($formvalues) {
	$objResponse=new xajaxResponse();
	$objResponseManager = xajaxResponseManager::getInstance();

	$action=array();

	if ($objResponseManager->getConfiguration('debug') == true) {
		$objResponse->alert("tpl:'".$tpl."'.");
	}

	$new_hostname="";

	$newname=$formvalues['new_jname_rn'];
	$oldname=$formvalues['old_jname_rn'];
	$new_hostname=$formvalues['new_hostname_rn'];

	if ($objResponseManager->getConfiguration('debug') == true) {
		$objResponse->alert("prob jrename'".print_r($formvalues, true)."'.");
	}

	if (strlen($new_hostname)>2) {
		$action=cmd(CBSD_CMD."task owner=cbsdweb mode=new /usr/local/bin/cbsd jrename old=$oldname new=$newname host_hostname=$new_hostname");
	} else {
		$action=cmd(CBSD_CMD."task owner=cbsdweb mode=new /usr/local/bin/cbsd jrename old=$oldname new=$newname");
	}
	
	sleep(2);

	if ($objResponseManager->getConfiguration('debug') == true) {
//        $objResponse->alert("prob jclone'".$formvalues['jname']."'.");
//	$objResponse->alert("prob jclone'".print_r($formvalues, true)."'.");
	}

	return $objResponse;
}

//=============================================================================
// language select
function lang_select($id, $lang_sel) {
    $objResponse=new xajaxResponse();

    $lang = "";

    switch ($lang_sel) {
	case "0":
		    $lang = "en";
		    break;
	case "1":
		    $lang = "ru";
		    break;
	case "2":
		    $lang = "de";
		    break;
    }
    
    $objResponse->script("document.cookie = 'lang=$lang; path=/; secure=true; domain=samson.bsdstore.ru';");
    $objResponse->script("window.location.reload(true);");
    
    return $objResponse;
}

//=============================================================================
// check IP
function check_ip($ip) {
    $objResponse=new xajaxResponse();
    $objResponseManager = xajaxResponseManager::getInstance();
    
    $aValues=array();
    
    $aValues=cmd(CBSD_CMD."checkip ip=$ip check=1");

    if($aValues['retval']==2) {
	$objResponse->setReturnValue(false);
    } else {
	$objResponse->setReturnValue(true);
    }

    if ($objResponseManager->getConfiguration('debug') == true) {
        $objResponse->alert("Return check '".print_r($aValues, true)."'.");
    }

    return $objResponse;
}

//=============================================================================
// stop bhyve
function bstop($jname) {
    $objResponse=new xajaxResponse();
    $objResponseManager = xajaxResponseManager::getInstance();
    
    $action=array();

    if ($objResponseManager->getConfiguration('debug') == true) {
	$objResponse->alert("Jname '".$jname."'.");
    }
    
    $action=cmd(CBSD_CMD."task owner=cbsdweb mode=new /usr/local/bin/cbsd bstop inter=0 jname=$jname");
    sleep(10);
    $objResponse->call('reloadPage', true);

    $retstr="";

    if($action['retval']==0) {
	$lst=explode("\n",$action['message']);
        if(!empty($lst)) foreach($lst as $item) {
	    $retstr.=$item;
        }
    }

    if ($objResponseManager->getConfiguration('debug') == true) {
	$objResponse->alert("action '".print_r($action, true)."'.");
    }
            
    return $objResponse;   
}


//=============================================================================
// start bhyve
function bstart($jname) {
    $objResponse=new xajaxResponse();
    $objResponseManager = xajaxResponseManager::getInstance();
    
    $action=array();
    
    if ($objResponseManager->getConfiguration('debug') == true) {
	$objResponse->alert("Jname '".$jname."'.");
    }
    
    $action=cmd(CBSD_CMD."task owner=cbsdweb mode=new /usr/local/bin/cbsd bstart inter=0 jname=$jname");
    sleep(10);
    $objResponse->call('reloadPage', true);
    
    $retstr="";

    if($action['retval']==0) {
	$lst=explode("\n",$action['message']);
        if(!empty($lst)) foreach($lst as $item) {
	    $retstr.=$item;
        }
    }

    if ($objResponseManager->getConfiguration('debug') == true) {
        $objResponse->alert("action '".print_r($action, true)."'.");
    }

    return $objResponse;   
}


//=============================================================================
// remove bhyve
function bremove($jname) {
    $objResponse=new xajaxResponse();
    $objResponseManager = xajaxResponseManager::getInstance();
    
    $action=array();
    
    if ($objResponseManager->getConfiguration('debug') == true) {
        $objResponse->alert("Jname '".$jname."'.");
    }
    
    $action=cmd(CBSD_CMD."task owner=cbsdweb mode=new /usr/local/bin/cbsd bremove inter=0 jname=$jname");
    sleep(10);
    $objResponse->call('reloadPage', true);

    if ($objResponseManager->getConfiguration('debug') == true) {
        $objResponse->alert("action '".print_r($action, true)."'.");
    }
            
    return $objResponse;   
}

//=============================================================================
// jail create
function bcreate($formvalues) {
    $objResponse=new xajaxResponse();
    $objResponseManager = xajaxResponseManager::getInstance();

    $action=array();

    $tpl=getVmTemplate($formvalues);

    if ($objResponseManager->getConfiguration('debug') == true) {
        $objResponse->alert("tpl:'".$tpl."'.");
    }

    $file_name='/tmp/'.$formvalues['jname'].'.conf';
    file_put_contents($file_name,$tpl);
    $action=cmd(CBSD_CMD.'task owner=cbsdweb mode=new /usr/local/bin/cbsd bcreate inter=0 jconf='.$file_name);
    sleep(10);
    $objResponse->call( 'reloadPage', true );

    if ($objResponseManager->getConfiguration('debug') == true) {
	$objResponse->alert("prob jcreate'".print_r($formvalues, true)."'.");
    }

    return $objResponse;
}


//=============================================================================
function authkeyadd($formvalues) {
    global $workdir;
    $objResponse=new xajaxResponse();
    $objResponseManager = xajaxResponseManager::getInstance();

    $action=array();

    if ($objResponseManager->getConfiguration('debug') == true) {
        $objResponse->alert("tpl:'".$tpl."'.");
    }


    $name=$formvalues['keyname'];
    $key=$formvalues['key'];

    $dbfilepath=$workdir."/var/db/authkey.sqlite";
    $db = new SQLite3($dbfilepath); $db->busyTimeout(5000);
    $db->exec("INSERT INTO authkey (name, authkey) VALUES ('{$name}','{$key}')");
    $db->close();

    $objResponse->call( 'reloadPage', true );

    if ($objResponseManager->getConfiguration('debug') == true) {
	$objResponse->alert("prob authkeyadd'".print_r($formvalues, true)."'.");
    }

    return $objResponse;
}


//=============================================================================
function vpnetadd($formvalues) {
    global $workdir;
    $objResponse=new xajaxResponse();
    $objResponseManager = xajaxResponseManager::getInstance();

    $action=array();

    if ($objResponseManager->getConfiguration('debug') == true) {
        $objResponse->alert("tpl:'".$tpl."'.");
    }

    $name=$formvalues['netname'];
    $net=$formvalues['net'];

    $dbfilepath=$workdir."/var/db/vpnet.sqlite";
    $db = new SQLite3($dbfilepath); $db->busyTimeout(5000);
    $db->exec("INSERT INTO vpnet (name, vpnet) VALUES ('{$name}','{$net}')");
    $db->close();

    $objResponse->call( 'reloadPage', true );

    if ($objResponseManager->getConfiguration('debug') == true) {
	$objResponse->alert("prob vpnetadd'".print_r($formvalues, true)."'.");
    }

    return $objResponse;
}


//=============================================================================
// jail create
function bobtain($formvalues) {
    $objResponse=new xajaxResponse();
    $objResponseManager = xajaxResponseManager::getInstance();

    $action=array();

    if ($objResponseManager->getConfiguration('debug') == true) {
        $objResponse->alert("tpl:'".$tpl."'.");
    }

if (isset($formvalues['jname'])) {
	$jname = $formvalues['jname'];
}

if (isset($formvalues['bobtain_vm_os_type'])) {
	$vm_os_type = $formvalues['bobtain_vm_os_type'];
}

sscanf($formvalues['bobtain_vm_os_type'],"%s %s",$vm_os_type,$nop);

if (isset($formvalues['jname'])) {
	$jname = $formvalues['jname'];
}

if (isset($formvalues['vm_size'])) {
	$vm_size = $formvalues['vm_size'];
}

if (isset($formvalues['vm_cpus'])) {
	$vm_cpus = $formvalues['vm_cpus'];
}

if (isset($formvalues['vm_ram'])) {
	$vm_ram = $formvalues['vm_ram'];
}

if (isset($formvalues['ip4_addr'])) {
	$ip4_addr = $formvalues['ip4_addr'];
}

if (isset($formvalues['mask'])) {
	$mask = $formvalues['mask'];
}

if (isset($formvalues['gw'])) {
	$gw = $formvalues['gw'];
}

if (isset($formvalues['vm_authkey'])) {
	$vm_authkey = $formvalues['vm_authkey'];
} else {
	$vm_authkey = "0";
}

if (isset($formvalues['vm_pw'])) {
	$vm_pw = $formvalues['vm_pw'];
} else {
	$vm_pw = "0";
}

if ((strlen($vm_os_type)<2)) {
	echo "No bobtain_vm_os_type";
	die;
}

if ((strlen($jname)<2)) {
	echo "No jname";
	die;
}

if ((strlen($vm_size)<1)) {
	echo "No vm_size";
	die;
}

if ((strlen($vm_cpus)<1)) {
	echo "No vm_cpus";
	die;
}

if ((strlen($vm_ram)<1)) {
	echo "No vm_ram";
	die;
}

if ((strlen($ip4_addr)<1)) {
	$ip4_add="DHCP";
}

if ((strlen($mask)<1)) {
	$mask="255.255.255.0";
}

if ((strlen($gw)<1)) {
	$gw="10.0.0.1";
}

if ((strlen($vm_authkey)<2)) {
	$vm_authkey = "0";
}

    $action=cmd(CBSD_CMD."task owner=cbsdweb mode=new /usr/local/bin/cbsd vm_obtain jname=$jname vm_size=$vm_size vm_cpus=$vm_cpus vm_ram=$vm_ram vm_os_type=$vm_os_type mask=$mask ip4_addr=$ip4_addr gw=$gw authkey=/usr/home/olevole/.ssh/authorized_keys pw=$vm_pw");
    sleep(10);
    $objResponse->call( 'reloadPage', true );

    if ($objResponseManager->getConfiguration('debug') == true) {
	$objResponse->alert("prob jcreate'".print_r($formvalues, true)."'.");
    }

    return $objResponse;
}


//=============================================================================
// node remove
function noderemove($nodename) {
    $objResponse=new xajaxResponse();
    $objResponseManager = xajaxResponseManager::getInstance();

    $action=array();

    if ($objResponseManager->getConfiguration('debug') == true) {
        $objResponse->alert("Node '".$nodename."'.");
    }

    $action=cmd(CBSD_CMD."task owner=cbsdweb mode=new /usr/local/bin/cbsd node mode=remove inter=0 node=$nodename");
    sleep(10);
    $objResponse->call('reloadPage', true);

    if ($objResponseManager->getConfiguration('debug') == true) {
        $objResponse->alert("action '".print_r($action, true)."'.");
    }

    return $objResponse;
}


//=============================================================================
// authkey remove
function authkeyremove($name) {
    global $workdir;
    $objResponse=new xajaxResponse();
    $objResponseManager = xajaxResponseManager::getInstance();

    $action=array();

    if ($objResponseManager->getConfiguration('debug') == true) {
        $objResponse->alert("Node '".$name."'.");
    }

   $dbfilepath=$workdir."/var/db/local.sqlite";
   $db = new SQLite3($dbfilepath); $db->busyTimeout(5000);
   $db->exec("DELETE FROM authkey WHERE idx='{$name}';");
   $db->close();

    $objResponse->call('reloadPage', true);

    if ($objResponseManager->getConfiguration('debug') == true) {
        $objResponse->alert("action '".print_r($action, true)."'.");
    }

    return $objResponse;
}


//=============================================================================
// flushtasklog
function flushtasklog($name) {
    global $workdir;
    $objResponse=new xajaxResponse();
    $objResponseManager = xajaxResponseManager::getInstance();

    $action=array();

    if ($objResponseManager->getConfiguration('debug') == true) {
        $objResponse->alert("Node '".$name."'.");
    }

    $action=cmd(CBSD_CMD."task mode=flushall");
    $objResponse->call('reloadPage', true);

    if ($objResponseManager->getConfiguration('debug') == true) {
        $objResponse->alert("action '".print_r($action, true)."'.");
    }

    return $objResponse;
}


//=============================================================================
// vpnet remove
function vpnetremove($name) {
    global $workdir;
    $objResponse=new xajaxResponse();
    $objResponseManager = xajaxResponseManager::getInstance();

    $action=array();

    if ($objResponseManager->getConfiguration('debug') == true) {
        $objResponse->alert("Node '".$name."'.");
    }

   $dbfilepath=$workdir."/var/db/vpnet.sqlite";
   $db = new SQLite3($dbfilepath); $db->busyTimeout(5000);
   $db->exec("DELETE FROM vpnet WHERE idx='{$name}';");
   $db->close();

    $objResponse->call('reloadPage', true);

    if ($objResponseManager->getConfiguration('debug') == true) {
        $objResponse->alert("action '".print_r($action, true)."'.");
    }

    return $objResponse;
}

//=============================================================================
// srcupdate
function srcupdate($idx) {


    $objResponse=new xajaxResponse();
    $objResponseManager = xajaxResponseManager::getInstance();

    $action=array();
    return $objResponse;
}



//=============================================================================
// src remove
function srcremove($idx) {
    $objResponse=new xajaxResponse();
    $objResponseManager = xajaxResponseManager::getInstance();

    $action=array();
    return $objResponse;
}


//=============================================================================
//
if (isset($_GET['launchvnc']) && $_GET['launchvnc']) {
	launchvnc($_GET['launchvnc']);
	die();
}
require('fun.server.common.php');

$xajax->processRequest();
?>
