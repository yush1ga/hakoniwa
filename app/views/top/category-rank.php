<section id="Ranking">
	<h2>各部門ランキング</h2>
	<div class="table-responsive">
		<table class="table table-condensed">
            <colgroup span=6 style="width:15%"></colgroup>
<?php
        $element   = ['point', 'money', 'food', 'pop', 'area', 'fire', 'pots', 'gold', 'rice', 'peop', 'monster', 'taiji', 'farm', 'factory', 'commerce', 'hatuden', 'mountain', 'team'];
        $bumonName = ["総合ポイント", $init->nameFunds, $init->nameFood, $init->namePopulation, $init->nameArea, "軍事力", "成長", "収入", "収穫", "人口増加", "怪獣出現数", "怪獣退治数", "農場", "工場", "商業", "発電所", "採掘場", "サッカー"];
        $bumonUnit = ['pts', $init->unitMoney, $init->unitFood, $init->unitPop, $init->unitArea, "機密事項", "pts↑", $init->unitMoney, $init->unitFood, $init->unitPop, $init->unitMonster, $init->unitMonster, "0{$init->unitPop}", "0{$init->unitPop}", "0{$init->unitPop}", "000kw", "0{$init->unitPop}", 'pts'];

        for ($r = 0, $rank_len = sizeof($element); $r < $rank_len; $r++) {
            $max = 0;

            // トップ判定（同値はID順）
            for ($i = 0; $i < $hako->islandNumber; $i++) {
                $island = $hako->islands[$i];
                if ($island['isBF'] === 1) {
                    continue;
                }

                if ($island[$element[$r]] > $max) {
                    $max = $island[$element[$r]];
                    $rankid[$r] = $i;
                }
            }

            $island = isset($rankid[$r])? $hako->islands[$rankid[$r]] : false;
            $name   = ($island)? Util::islandName($island, $hako->ally, $hako->idToAllyNumber) : '';

            if (($r % 6) === 0) {
                println('<tr>');
            }
            $max = $r !== 5 ? $max : '';
            echo <<<END
<td>
    <table class="table table-condensed m-b-0">
        <thead><tr><th>{$bumonName[$r]}</th></tr></thead>
        <tbody>

END;
            if ($island) {
                echo <<<END
            <tr><td class="TenkiCell"><a class="islName" href="$this_file?Sight={$island['id']}">$name</a></td></tr>
            <tr><td class="TenkiCell">$max{$bumonUnit[$r]}</td></tr>

END;
            } else {
                echo <<<END
            <tr><td class="TenkiCell islName">-</td></tr>
            <tr><td class="TenkiCell islName">-</td></tr>

END;
            }
            echo <<<END
        </tbody>
    </table>
</td>

END;

            if (($r % 6) === 5) {
                println('</tr>');
            }
        }
?>
		</table>
	</div>
</section>
<hr>
