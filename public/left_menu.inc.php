<?php
require_once'cbsd.inc.php';
?>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar lang-lst"></span>
          </button>
          <a class="navbar-brand" href="#">ClonOS::<?php if(!isset($_GET['mod'])){$mod="homepage";} echo "$mod";?></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="#"><?php translate('Settings');?></a></li>
            <li><a href="#"><?php translate('Profile');?></a></li>
            <li><a href="#"><?php translate('Support');?></a></li>
	    <li><a href="#">
		<select name="lang_select" id="lang_select" class="inp" OnChange="xajax_lang_select(id, document.getElementById('lang_select').options.selectedIndex);">
        	    <option value="en"<?php if($lang=='en')echo ' selected="selected"'; ?>>English</option>
                    <option value="ru"<?php if($lang=='ru')echo ' selected="selected"'; ?>>Russian</option>
                    <option value="de"<?php if($lang=='de')echo ' selected="selected"'; ?>>Deutch</option>
    		</select>
	    </a></li>
          </ul>
          <form class="navbar-form navbar-right">
            <input type="text" class="form-control" placeholder=<?php translate('Search...');?>>
          </form>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
    	    <?php
    		$modules["overview"] = "Overview";
    		$modules["jailscontainers"] = "Jails containers";
    		$modules["bhyvevms"] = "Bhyve VMs";
    		$modules["nodes"] = "Nodes";
    		$modules["vpnet"] = "Virtual Private Network";
    		$modules["authkey"] = "Authkey";
    		$modules["repo"] = "Repository";
    		$modules["bases"] = "FreeBSD Bases";
    		$modules["sources"] = "FreeBSD Sources";
    		$modules["jail_marketplace"] = "Jail Marketplace";
    		$modules["bhyve_marketplace"] = "Bhyve Marketplace";
    		$modules["tasklog"] = "TaskLog";
    		foreach ($modules as $mod_name => $mod_descr) {
		    $item=$mod_name; $item_desc=$mod_descr;
		    if ($url_path == "/$item/") echo '<li class="active">'; else  echo '<li>' ?><a href='index.php?mod=<?php echo $item;?>'><?php echo "$item_desc"; if ($url_path == "/$item/") echo '<span class="sr-only">(current)</span>'?></a></li><?php
		}
    	    ?>
        </ul>
        </div>
