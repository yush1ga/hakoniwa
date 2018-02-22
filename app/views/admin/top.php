<h1>管理画面</h1>
<form method="post" onSubmit="return(function(f,v){if(v){f.action=v;return !0;}else{return !1;}})(this,this.menulist.value);" class="form-inline">
    <label>パスワード：
        <input type="password" size="32" name="PASSWORD" class="form-control"></label>
    <input type="hidden" name="mode" value="enter">

    <select name="menulist" class="form-control">
    <?php foreach ($menuList as $label => $url):?>
         <option value="<?=$init->baseDir,$url?>"><?=$label?></option>
    <?php endforeach;?>
    </select>

    <button type="submit" class="btn btn-default">管理室へ</button>
</form>
