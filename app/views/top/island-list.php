<section class="IslandView">
	<h2>諸島の状況</h2>
	<p>島の名前を選択すると、<strong>観光</strong>することができます。</p>
<?php

    /**
     * 島一覧pagenation
     * @var integer
     */
    $islandListStart = 0;
    $islandListSentinel = 0;

    if ($hako->islandNumber != 0) {
        $islandListStart = $data['islandListStart'];
        if ($init->islandListRange == 0) {
            $islandListSentinel = $hako->islandNumberNoBF;
        } else {
            $islandListSentinel = min(($islandListStart + $init->islandListRange - 1), $hako->islandNumberNoBF);
        }
    }

    if (($islandListStart != 1) || ($islandListSentinel != $hako->islandNumberNoBF)) {
        println('<nav aria-label="Page navigation"><ul class="pagination">');
        for ($i = 1; $i <= $hako->islandNumberNoBF; $i += $init->islandListRange) {
            $j = min($i + $init->islandListRange - 1, $hako->islandNumberNoBF);

            $active = $i == $islandListStart ? ' class="active"' : '';
            println('<li', $active, '><a href="', $this_file, '?islandListStart=', $i, "\">[$i - $j]</a></li>");
        }
        println('</ul></nav>');
    }
    $islandListStart--;
    $this->islandTable($hako, $islandListStart, $islandListSentinel);
?>
</section>
<hr>
