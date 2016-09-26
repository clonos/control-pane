<?php
require_once($rp.'/cbsd.inc.php');
require_once($rp.'/left_menu.inc.php');
include_once($rp.'/nodes.inc.php');
?>

        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h2 class="sub-header">Overview</h2>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Item</th>
                  <th>Value</th>
                </tr>
              </thead>
              <tbody>

<?php
require_once('overview.php');

show_overview($workdir);
?>

              </tbody>
            </table>
	   <p>It is an open source and free product which powered by other project (major importance list):</p>
	   <ul>
	   <li><p><strong><a href="https://www.bsdstore.ru">CBSD Project</a> -- FreeBSD virtual environment management framework</strong></p></li>
	   <li><p><strong><a href="https://www.freebsd.org">FreeBSD Project</a> -- FreeBSD is an advanced computer operating system</strong></p></li>
	   <li><p><strong><a href="https://puppet.com/">Puppet</a> -- Puppet is an open-source configuration management tool.</strong></p></li>
	   <li><p>and many other..</li>
	   </ul>
          </div>
        </div>
      </div>
    </div>


<?php
?>
