<div class="row2col">
	<div class="column">
		<h1><span id="trlt-186">Общая статистика облака:</span></h1>
		<table class="tfill" style="width:98%;">
			<thead>
				<tr>
					<td width="200"><span id="trlt-187">Параметр</span></td><td width="200"><span id="trlt-188">Значение</span></td>
				</tr>
			</thead>
			<tbody>
				<tr><td><span id="trlt-189">Количество нод:</span></td><td id="num-nodes"></td></tr>
				<tr><td><span id="trlt-190">Нод онлайн:</span></td><td id="online-nodes"></td></tr>
				<tr><td><span id="trlt-191">Нод офлайн:</span></td><td id="offline-nodes"></td></tr>
				<tr><td><span id="trlt-192">Количество клеток:</span></td><td id="num-jails"></td></tr>
				<tr><td><span id="trlt-193">Количество ядер:</span></td><td id="num-cores"></td></tr>
				<tr><td><span id="trlt-194">Средняя частота, Mhz:</span></td><td id="average"></td></tr>
				<tr><td><span id="trlt-195">Всего RAM:</span></td><td id="sum-ram"></td></tr>
				<tr><td><span id="trlt-196">Объём хранилища:</span></td><td id="sum-storage"></td></tr>
			</tbody>
			<tbody class="error" style="display:none;">
				<tr><td colspan="2" class="error_message"><span id="trlt-197">Unable to fetch net info!</span></td></tr>
			</tbody>
		</table>
	</div>
	<div class="column">
		<h1><span id="trlt-198">Загрузка процессоров в кластере:</span></h1>
		<div class="graph g-local-pcpu v-black l-cpu" style="height:100px;width:100%"></div>
		
		<h1><span id="trlt-199">Использование памяти в кластере:</span></h1>
		<div class="graph g-local-pmem v-black l-mem" style="height:100px;width:100%"></div>
	</div>
</div>

<p><span id="trlt-200">Это открытый и свободный проект, использующий в своей работе такие проекты, как (наиболее значимые):</span></p>
<ul>
	<li><a href="https://github.com/cbsd/cbsd" target="_blank">CBSD Project</a> — <span id="trlt-201">Фреймворк для управления виртуальными окружениями FreeBSD ОС</span></li>
	<li><a href="https://www.freebsd.org/" target="_blank">FreeBSD Project</a> —  <span id="trlt-202">свободная Unix-подобная операционная система, потомок AT&T Unix по линии BSD, созданной в <a href="https://en.wikipedia.org/wiki/Berkeley_Software_Distribution">Калифорнийском университете Беркли, США</a></span></a></li>
	<li><a href="https://puppet.com/" target="_blank">Puppet</a> — <span id="trlt-203">Система управления конфигурациями</span></li>
	<li><span id="trlt-204">и много остального...</span></li>
</ul>

<!--
CHAT:
<input type="text" id="wsinp" />
<input type="button" onclick="clonos.wssend($('#wsinp').val());$('#wsinp').val('');" value="Send" />
-->
