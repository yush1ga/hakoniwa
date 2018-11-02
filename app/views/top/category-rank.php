<?php declare(strict_types=1);
$elements = [
    'point', 'money', 'food', 'pop', 'area', 'fire',
    'pots', 'gold', 'rice', 'peop', 'monster', 'taiji',
    'farm', 'factory', 'commerce', 'hatuden', 'mountain', 'team'
];
$category_name = [
    "総合ポイント", $init->nameFunds, $init->nameFood, $init->namePopulation, $init->nameArea, "軍事力",
    "成長度", "収入", "収穫", "人口増加", "怪獣出現数", "怪獣退治数",
    "農場", "工場", "商業", "発電所", "採掘場", "サッカー"
];
$category_unit = [
    'pts', $init->unitMoney, $init->unitFood, $init->unitPop, $init->unitArea, "機密事項",
    'pts', $init->unitMoney, $init->unitFood, $init->unitPop, $init->unitMonster, $init->unitMonster,
    '0'.$init->unitPop, '0'.$init->unitPop, '0'.$init->unitPop, '000kw', '0'.$init->unitPop, 'pts'
];
$category_1st = [];



// トップ判定（同値はID降順）
for ($i = 0, $elements_length = count($elements); $i < $elements_length; $i++) {
    $max = 1;//elementsごとに$maxを初期化
    for ($ii = 0; $ii < $hako->islandNumber; $ii++) {
        $island = $hako->islands[$ii];

        if ($island['isBF'] === 1) {
            continue;
        }

        if ($island[$elements[$i]] >= $max) {
            $max = $island[$elements[$i]];
            $category_1st[$i] = [
                'id'    => $island["id"],
                'name'  => Util::islandName($island, $hako->ally, $hako->idToAllyNumber),
                'value' => $i !== 5 ? $max : ''// 軍事力は機密情報のため
            ];
        }
    }
    unset($max);

    if (!isset($category_1st[$i])) {
        $category_1st[$i] = [
            'id'    => -1,
            'name'  => '-',
            'value' => '-'
        ];
    }
}

?>
<section id="Ranking">
    <h2>部門トップ</h2>
    <div class="table-responsive">
        <table class="table table-condensed">
            <colgroup span=6 style="width:15%"></colgroup>
            <tbody>
<?php for ($i = 0, $cnt = count($elements); $i < $cnt; $i++):?>
<?php if (($i % 6) === 0):?>
                <tr>
<?php endif;?>
                    <td>
                        <table class="table table-condensed m-b-0">
                        <thead><tr><th><?=$category_name[$i]?></th></tr></thead>
                        <tbody>
<?php if ($category_1st[$i]['id'] === -1):?>
                            <tr><td class="TenkiCell islName">-</td></tr>
                            <tr><td class="TenkiCell">-</td></tr>
<?php else:?>
                            <tr><td class="TenkiCell"><a class="islName" href="<?=$this_file?>?Sight=<?=$category_1st[$i]['id']?>"><?=$category_1st[$i]['name']?></a></td></tr>
                            <tr><td class="TenkiCell"><?=$category_1st[$i]['value'].$category_unit[$i]?></td></tr>
<?php endif;?>
                        </tbody>
                        </table>
                    </td>
<?php if (($i % 6) === 5):?>
                </tr>
<?php endif;?>
<?php endfor;?>
            </tbody>
		</table>
	</div>
</section>

<hr>
<?php
unset($elements, $category_name, $category_unit, $category_1st, $elements_length);
