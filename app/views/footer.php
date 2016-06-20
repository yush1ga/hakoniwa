<hr>
<script>
// JavaScriptモード関連
if (document.addEventListener) {
    if (typeof(init) == "function") {
        document.addEventListener("DOMContentLoaded", init(), false);
    }
} else {
    if (typeof(init) == "function") {
        window.onload = init;
    }
}
</script>

<footer class="row">
<div class="col-xs-12">
<p>Managed by <a href="https://twitter.com/<?= $init->twitterID ?>" target="_blank"><?= $init->adminName ?></a>
(<a href="<?= $init->urlTopPage ?>" target="_blank"><?= $init->urlTopPage ?></a>)

<?php if($init->performance) : ?>
<small class="pull-xs-right">
<?php
list($tmp1, $tmp2) = array_pad( explode(" ", $init->CPU_start), 2, 0);
list($tmp3, $tmp4) = array_pad( explode(" ", microtime()), 2, 0);
printf("(CPU sec: %.4f)", $tmp4-$tmp2+$tmp3-$tmp1);
?>
</small>
<?php endif; ?>
</p>
</div>
</footer>

</div><!-- container -->
</body>
</html>
