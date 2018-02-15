<h1>管理画面</h1>
<form method="post" onSubmit="return(function(f,v){if(v){f.action=v;return !0;}else{return !1;}})(this,this.menulist.value);">
    <label>パスワード：
        <input type="password" size="32" maxlength="32" name="PASSWORD"></label>
    <input type="hidden" name="mode" value="enter">

    <select name="menulist">
    <?php foreach ($menuList as $label => $url):?>
         <option value="<?=$init->baseDir,$url?>"><?=$label?></option>
    <?php endforeach;?>
    </select>
    <input type="submit" value="管理室へ">
</form>
