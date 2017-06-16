<div id="islandInfo" class="table-responsive">
    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th><?= $init->nameRank ?></th>
                <th><?= $init->namePopulation ?></th>
                <th><?= $init->nameArea ?></th>
                <th><?= $init->nameFunds ?><?= $lots ?></th>
                <th><?= $init->nameFood ?></th>
                <th><?= $init->nameUnemploymentRate ?></th>
                <th><?= $init->nameFarmSize ?></th>
                <th><?= $init->nameFactoryScale ?></th>
                <th><?= $init->nameCommercialScale ?></th>
                <th><?= $init->nameMineScale ?></th>
                <th><?= $init->namePowerPlantScale ?></th>
                <th><?= $init->namePowerSupplyRate ?></th>
            </tr>
        </thead>
        <tr>
            <th <?= $init->bgNumberCell ?> rowspan="4">
                <?= $init->tagNumber_ ?><?= $rank ?><?= $init->_tagNumber ?>
            </th>
            <td <?= $init->bgInfoCell ?>>
                <?= $pop ?>
            </td>
            <td <?= $init->bgInfoCell ?>>
                <?= $area ?>
            </td>
            <td <?= $init->bgInfoCell ?>>
                <?= $money ?>
            </td>
            <td <?= $init->bgInfoCell ?>>
                <?= $food ?>
            </td>
            <td <?= $init->bgInfoCell ?>>
                <?= $unemployed ?>
            </td>
            <td <?= $init->bgInfoCell ?>>
                <?= $farm ?>
            </td>
            <td <?= $init->bgInfoCell ?>>
                <?= $factory ?>
            </td>
            <td <?= $init->bgInfoCell ?>>
                <?= $commerce ?>
            </td>
            <td <?= $init->bgInfoCell ?>>
                <?= $mountain ?>
            </td>
            <td <?= $init->bgInfoCell ?>>
                <?= $hatuden ?>
            </td>
            <td <?= $init->bgInfoCell ?>>
                <?= $ene ?>
            </td>
        </tr>
        <tr>
            <th><?= $init->nameWeather ?></th>
            <td class="TenkiCell"><?= $sora ?></td>
            <th><?= $init->nameMilitaryTechnology ?></th>
            <td <?= $init->bgInfoCell ?>><?= $arm ?></td>
            <th><?= $init->nameMonsterExterminationNumber ?></th>
            <td <?= $init->bgInfoCell ?>><?= $taiji ?></td>
            <th><?= $init->nameSatellite ?></th>
            <td class="ItemCell" colspan="4"><?= $eiseis ?></td>
        </tr>
        <tr>
            <th>ジン</th>
            <td class="ItemCell" colspan="5"><?= $zins ?></td>
            <th>アイテム</th>
            <td class="ItemCell" colspan="4"><?= $items ?></td>
        </tr>
        <tr>
            <td colspan="11" <?= $init->bgCommentCell ?>><?= $comment ?></td>
        </tr>
    </table>
</div>
