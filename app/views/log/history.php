<div id="HistoryLog">
	<h2>海域の近況</h2>
	<ul class="list-unstyled">
	<?php
        $log = new Log;
        $log->historyPrint();
    ?>
	</ul>
</div>
