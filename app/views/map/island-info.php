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
    <tbody>
        <tr>
            <th class="NumberCell number" rowspan="4"><?= $rank ?></th>
            <td class="InfoCell"><?= $pop ?></td>
            <td class="InfoCell"><?= $area ?></td>
            <td class="InfoCell"><?= $money ?></td>
            <td class="InfoCell"><?= $food ?></td>
            <td class="InfoCell"><?= $unemployed ?></td>
            <td class="InfoCell"><?= $farm ?></td>
            <td class="InfoCell"><?= $factory ?></td>
            <td class="InfoCell"><?= $commerce ?></td>
            <td class="InfoCell"><?= $mountain ?></td>
            <td class="InfoCell"><?= $hatuden ?></td>
            <td class="InfoCell"><?= $ene ?></td>
        </tr>
        <tr>
            <th><?= $init->nameWeather ?></th>
            <td class="TenkiCell"><?= $sora ?></td>
            <th><?= $init->nameMilitaryTechnology ?></th>
            <td class="InfoCell"><?= $arm ?></td>
            <th><?= $init->nameMonsterExterminationNumber ?></th>
            <td class="InfoCell"><?= $taiji ?></td>
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
            <td colspan="11" class="CommentCell"><?= $comment ?></td>
        </tr>
    </tbody>
    </table>
</div>
