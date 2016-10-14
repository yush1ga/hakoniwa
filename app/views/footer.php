<hr>

<footer class="row">
<div class="col-xs-12">
<p>Managed by <a href="https://twitter.com/<?= $init->twitterID ?>" target="_blank"><?= $init->adminName ?></a> （<a href="<?= $init->urlTopPage ?>" target="_blank"><?= $init->urlTopPage ?></a>）

<?php if($init->performance) : ?>
<small class="text-muted"><?php
list($tmp1, $tmp2) = array_pad( explode(" ", $init->CPU_start), 2, 0);
list($tmp3, $tmp4) = array_pad( explode(" ", microtime()), 2, 0);
printf("(CPU sec: %.4f)", $tmp4-$tmp2+$tmp3-$tmp1);
?></small>
<?php endif; ?>
</p>
</div>
</footer>

<script>
if (typeof(init) === "function") {
    document.addEventListener("DOMContentLoaded", init(), false);
}
</script>
</div><!-- container -->
</body>
</html>
