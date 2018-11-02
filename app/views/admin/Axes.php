<hr>

<form>
    <div class="form-group">
        <button class="btn btn-default" onclick="Button_DispFilter(this, 'DATA-TABLE');return false;">オートフィルタ表示</button>
    </div>

    <table id="DATA-TABLE" class="table table-condensed">
    <thead>
    <tr class="NumberCell">
        <th scope="col"><button class="btn btn-default" onclick="g_cSortTable.Button_Sort('DATA-TABLE',[0]);return false;">ログイン日時</button></th>
        <th scope="col"><button class="btn btn-default" onclick="g_cSortTable.Button_Sort('DATA-TABLE',[1,0]);return false;">島ID</button></th>
        <th scope="col"><button class="btn btn-default" onclick="g_cSortTable.Button_Sort('DATA-TABLE',[2,0]);return false;">島の名前</button></th>
        <th scope="col"><button class="btn btn-default" onclick="g_cSortTable.Button_Sort('DATA-TABLE',[3,0]);return false;">IP情報</button></th>
        <th scope="col"><button class="btn btn-default" onclick="g_cSortTable.Button_Sort('DATA-TABLE',[4,0]);return false;">ホスト情報</button></th>
    </tr>
    </thead>
    <tbody>
<?php $fp = fopen($init->dirName.'/'.$init->logname, 'r');
while (false !== ($line = fgets($fp))):?>
        <tr><td scope="row"><?= str_replace(',', "</td><td>", $line) ?></td></tr>
<?php endwhile;
fclose($fp);?>
    </tbody>
    </table>
</form>
<script>
<?php require ROOT.'/public/script/axes.js';?>
</script>
