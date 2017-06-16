<section id="Ranking">
	<h2>各部門ランキング</h2>
	<div class="table-responsive">
		<table class="table table-condensed">
<?php
		$element   = array('point', 'money', 'food', 'pop', 'area', 'fire', 'pots', 'gold', 'rice', 'peop', 'monster', 'taiji', 'farm', 'factory', 'commerce', 'hatuden', 'mountain', 'team');
		$bumonName = array("総合ポイント", $init->nameFunds, $init->nameFood, $init->namePopulation, $init->nameArea, "軍事力", "成長", "収入", "収穫", "人口増加", "怪獣出現数", "怪獣退治数", "農場", "工場", "商業", "発電所", "採掘場", "サッカー");
		$bumonUnit = array('pts', $init->unitMoney, $init->unitFood, $init->unitPop, $init->unitArea, "機密事項", "pts↑", $init->unitMoney, $init->unitFood, $init->unitPop, $init->unitMonster, $init->unitMonster, "0{$init->unitPop}", "0{$init->unitPop}", "0{$init->unitPop}", "000kw", "0{$init->unitPop}", 'pts');

		for($r = 0, $rank_len = sizeof($element); $r < $rank_len; $r++) {
			$max = 0;

			// トップ判定（同値は	ID順）
			for($i = 0; $i < $hako->islandNumber; $i++) {
				$island = $hako->islands[$i];
				if($island['isBF'] === 1) continue;

				if($island[$element[$r]] > $max) {
					$max = ($r === 5)? '' : $island[$element[$r]]; //軍事レベルは非公開
					$rankid[$r] = $i;
				}
			}

			$island = isset($rankid[$r])? $hako->islands[$rankid[$r]] : false;
			$name   = ($island)? Util::islandName($island, $hako->ally, $hako->idToAllyNumber) : '';

			if(($r % 6) === 0) {
				echo '<tr>', PHP_EOL;
			}

			echo '<td width="15%" class="M">';
			echo '<table class="table table-bordered" style="border:0">', PHP_EOL;

			echo '<thead><tr><th>', $bumonName[$r], '</th></tr></thead>', PHP_EOL;
			if($island) {
				echo '<tr><td class="TenkiCell"><a class="islName" href="', $this_file, '?Sight=', $island['id'], '">', $name, '</a></td></tr>', PHP_EOL;
				echo '<tr><td class="TenkiCell">', $max, $bumonUnit[$r], '</td></tr>', PHP_EOL;
			} else {
				echo '<tr><td class="TenkiCell islName">-</td></tr>', PHP_EOL;
				echo '<tr><td class="TenkiCell islName">-</td></tr>', PHP_EOL;
			}

			echo "</table>";
			echo "</td>";

			if(($r % 6) === 5) {
				echo '</tr>', PHP_EOL;
			}

		}
?>
		</table>
	</div>
</section>
<hr>
