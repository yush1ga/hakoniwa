<section class="IslandView">
	<h2>諸島の状況</h2>
	<p>島の名前を選択すると、<strong>観光</strong>することができます。</p>
<?php

/**
 * 島一覧pagenation
 */
$list_begin = 0;
$islandListSentinel = 0;

if ($hako->islandNumber !== 0) {
    $list_begin = $data['islandListStart'];

    $islandListSentinel = ($init->islandListRange === 0)
        ? $hako->islandNumberNoBF
        : min(($list_begin + $init->islandListRange - 1), $hako->islandNumberNoBF);
}

if (($list_begin !== 1) || ($islandListSentinel != $hako->islandNumberNoBF)) {
    println('<nav aria-label="Page navigation"><ul class="pagination">');
    for ($i = 1; $i <= $hako->islandNumberNoBF; $i += $init->islandListRange) {
        $j = min($i + $init->islandListRange - 1, $hako->islandNumberNoBF);
        $active = $i == $list_begin ? ' class="active"' : '';

        println('<li', $active, '><a href="', $this_file, '?islandListStart=', $i, "\">[$i - $j]</a></li>");
    }
    println('</ul></nav>');
}
$list_begin--;
$this->islandTable($hako, $list_begin, $islandListSentinel);
?>
</section>
<hr>
