<div class="row2col">
	<div class="column">
		<h1>Summary statistics for cloud:</h1>
		<table class="tfill" style="width:98%;">
			<thead>
				<tr>
					<td width="200">Param</td><td width="200">Values</td>
				</tr>
			</thead>
			<tbody>
				<tr><td>Num of nodes:</td><td id="num-nodes"></td></tr>
				<tr><td>Online nodes:</td><td id="online-nodes"></td></tr>
				<tr><td>Offline nodes:</td><td id="offline-nodes"></td></tr>
				<tr><td>Num of jails:</td><td id="num-jails"></td></tr>
				<tr><td>Num of cores:</td><td id="num-cores"></td></tr>
				<tr><td>Average freq. Mhz:</td><td id="average"></td></tr>
				<tr><td>Summary RAM:</td><td id="sum-ram"></td></tr>
				<tr><td>Summary storage size:</td><td id="sum-storage"></td></tr>
			</tbody>
			<tbody class="error" style="display:none;">
				<tr><td colspan="2" class="error_message">Unable to fetch net info!</td></tr>
			</tbody>
		</table>
	</div>
	<div class="column">
		<h1>Current node CPU usage:</h1>
		<div class="graph g-local-pcpu v-black l-cpu" style="height:100px;width:100%"></div>
		
		<h1>Current node RAM usage:</h1>
		<div class="graph g-local-pmem v-black l-mem" style="height:100px;width:100%"></div>
	</div>
</div>

<p>It is an open source and free product which powered by other project (major importance list):</p>
<ul>
	<li><a href="https://github.com/cbsd/cbsd" target="_blank">CBSD Project</a> — — FreeBSD OS virtual environment management framework</li>
	<li><a href="https://www.freebsd.org/" target="_blank">FreeBSD Project</a> —  FreeBSD  is a free and open source Unix-like operating system descended from Research Unix created in <a href="https://en.wikipedia.org/wiki/Berkeley_Software_Distribution">University of California, Berkeley, U.S.</li>
	<li><a href="https://puppet.com/" target="_blank">Puppet</a> — Puppet is an open-source configuration management tool.</li>
	<li>and many other..</li>
</ul>

<!--
CHAT:
<input type="text" id="wsinp" />
<input type="button" onclick="clonos.wssend($('#wsinp').val());$('#wsinp').val('');" value="Send" />
-->
