<div id="HistoryLog">
	<h2>History</h2>
	<ul class="list-unstyled">
	<?php
        $log = new Log();
        $log->historyPrint();
    ?>
	</ul>
</div>
