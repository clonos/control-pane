<div class="row2col">
	<div class="column">
		<h1><translate id="1">Summary statistics for cloud:</translate></h1>
		<table class="tfill" style="width:98%;">
			<thead>
				<tr>
					<td width="200"><translate id="2">Param</translate></td><td width="200"><translate>Values<translate></td>
				</tr>
			</thead>
			<tbody>
				<tr><td><translate id="3">Num of nodes:</translate></td><td id="num-nodes"></td></tr>
				<tr><td><translate id="4">Online nodes:</translate></td><td id="online-nodes"></td></tr>
				<tr><td><translate id="5">Offline nodes:</translate></td><td id="offline-nodes"></td></tr>
				<tr><td><translate id="6">Num of jails:</translate></td><td id="num-jails"></td></tr>
				<tr><td><translate id="7">Num of cores:</translate></td><td id="num-cores"></td></tr>
				<tr><td><translate id="8">Average freq. Mhz:</translate></td><td id="average"></td></tr>
				<tr><td><translate id="9">Summary RAM:</translate></td><td id="sum-ram"></td></tr>
				<tr><td><translate id="10">Summary storage size:</translate></td><td id="sum-storage"></td></tr>
			</tbody>
			<tbody class="error" style="display:none;">
				<tr><td colspan="2" class="error_message"><translate id="11">Unable to fetch net info!</translate></td></tr>
			</tbody>
		</table>
	</div>
	<div class="column">
		<h1><translate id="12">Current node CPU usage:</translate></h1>
		<div class="graph g-local-pcpu v-black l-cpu" style="height:100px;width:100%"></div>
		
		<h1><translate id="13">Current node RAM usage:</translate></h1>
		<div class="graph g-local-pmem v-black l-mem" style="height:100px;width:100%"></div>
	</div>
</div>

<p><translate id="14">It is an open source and free product which powered by other project (major importance list):</translate></p>
<ul>
	<li><a href="https://github.com/cbsd/cbsd" target="_blank">CBSD Project</a> — <translate id="15">FreeBSD OS virtual environment management framework</translate></li>
	<li><a href="https://www.freebsd.org/" target="_blank">FreeBSD Project</a> —  <translate id="16">FreeBSD  is a free and open source Unix-like operating system descended from Research Unix created in <a href="https://en.wikipedia.org/wiki/Berkeley_Software_Distribution">University of California, Berkeley, U.S.</translate></li>
	<li><a href="https://puppet.com/" target="_blank">Puppet</a> — <translate id="17">Puppet is an open-source configuration management tool.</translate></li>
	<li><translate id="18">and many other..</translate></li>
</ul>

<!--
CHAT:
<input type="text" id="wsinp" />
<input type="button" onclick="clonos.wssend($('#wsinp').val());$('#wsinp').val('');" value="Send" />
-->
