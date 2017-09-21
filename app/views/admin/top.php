<script>
function go(obj) {
    if(obj.menulist.value) {
        obj.action = obj.menulist.value;
    }
}
</script>

<h1>管理画面</h1>
<form method="post" onSubmit="go(this)">
    <b>パスワード：</b>
    <input type="password" size="32" maxlength="32" name="PASSWORD">
    <input type="hidden" name="mode" value="enter">

    <select name="menulist">
    <?php
        foreach ($menuList as $label => $url) {
            echo '<option value="'.$init->baseDir.$url.'">'.$label.'</option>'.PHP_EOL;
        }
    ?>
    </select>
    <input type="submit" value="管理室へ">
</form>
