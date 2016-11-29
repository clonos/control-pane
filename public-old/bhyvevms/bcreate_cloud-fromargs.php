<?php
require_once('../head.inc.php');
require_once('../cbsd.inc.php');

include('../nodes.inc.php');
?>

        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <h1 class="page-header">Bhyve VMs</h1>

<?php
if (isset($_POST['vm_os_type'])) {
	$vm_os_type = $_POST['vm_os_type'];
}

if (isset($_POST['jname'])) {
	$jname = $_POST['jname'];
}

if (isset($_POST['vm_size'])) {
	$vm_size = $_POST['vm_size'];
}

if (isset($_POST['vm_cpus'])) {
	$vm_cpus = $_POST['vm_cpus'];
}

if (isset($_POST['vm_ram'])) {
	$vm_ram = $_POST['vm_ram'];
}

if (isset($_POST['ip4_addr'])) {
	$ip4_addr = $_POST['ip4_addr'];
}

if (isset($_POST['vm_authkey'])) {
	$vm_authkey = $_POST['vm_authkey'];
} else {
	$vm_authkey = "0";
}

if (isset($_POST['vm_pw'])) {
	$vm_pw = $_POST['vm_pw'];
} else {
	$vm_pw = "0";
}

if ((strlen($vm_os_type)<2)) {
	echo "No vm_os_type";
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

if ((strlen($vm_authkey)<2)) {
	$vm_authkey = "0";
}


$handle=popen("env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd task owner=cbsdweb mode=new /usr/local/bin/cbsd vm_obtain jname=$jname vm_size=$vm_size vm_cpus=$vm_cpus vm_ram=$vm_ram vm_os_type=$vm_os_type ip4_addr=$ip4_addr gw=10.0.0.1 authkey=/usr/home/olevole/.ssh/authorized_keys pw=$vm_pw", "r");
$read = fgets($handle, 4096);
echo "Job Queued: $read";
pclose($handle);
fflush();
sleep(3);
?>
<script type="text/javascript">
window.location="/bhyvevms/"
</script>
