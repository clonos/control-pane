<div class="row2col">
	<div class="column">
		<h1><translate>Summary statistics for cloud:</translate></h1>
		<table class="tfill" style="width:98%;">
			<thead>
				<tr>
					<td width="200"><translate>Param</translate></td><td width="200"><translate>Values<translate></td>
				</tr>
			</thead>
			<tbody>
				<tr><td><translate>Num of nodes:</translate></td><td id="num-nodes"></td></tr>
				<tr><td><translate>Online nodes:</translate></td><td id="online-nodes"></td></tr>
				<tr><td><translate>Offline nodes:</translate></td><td id="offline-nodes"></td></tr>
				<tr><td><translate>Num of jails:</translate></td><td id="num-jails"></td></tr>
				<tr><td><translate>Num of cores:</translate></td><td id="num-cores"></td></tr>
				<tr><td><translate>Average freq. Mhz:</translate></td><td id="average"></td></tr>
				<tr><td><translate>Summary RAM:</translate></td><td id="sum-ram"></td></tr>
				<tr><td><translate>Summary storage size:</translate></td><td id="sum-storage"></td></tr>
			</tbody>
			<tbody class="error" style="display:none;">
				<tr><td colspan="2" class="error_message"><translate>Unable to fetch net info!</translate></td></tr>
			</tbody>
		</table>
	</div>
	<div class="column">
		<h1><translate>Current node CPU usage:</translate></h1>
		<div class="graph g-local-pcpu v-black l-cpu" style="height:100px;width:100%"></div>
		
		<h1><translate>Current node RAM usage:</translate></h1>
		<div class="graph g-local-pmem v-black l-mem" style="height:100px;width:100%"></div>
	</div>
</div>

<p><translate>It is an open source and free product which powered by other project (major importance list):</translate></p>
<ul>
	<li><a href="https://github.com/cbsd/cbsd" target="_blank">CBSD Project</a> — <translate>FreeBSD OS virtual environment management framework</translate></li>
	<li><a href="https://www.freebsd.org/" target="_blank">FreeBSD Project</a> —  <translate>FreeBSD  is a free and open source Unix-like operating system descended from Research Unix created in <a href="https://en.wikipedia.org/wiki/Berkeley_Software_Distribution">University of California, Berkeley, U.S.</translate></li>
	<li><a href="https://puppet.com/" target="_blank">Puppet</a> — <translate>Puppet is an open-source configuration management tool.</translate></li>
	<li><translate>and many other..</translate></li>
</ul>

<!--
CHAT:
<input type="text" id="wsinp" />
<input type="button" onclick="clonos.wssend($('#wsinp').val());$('#wsinp').val('');" value="Send" />
-->
