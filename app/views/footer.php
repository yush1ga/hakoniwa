<hr>

<footer>
<p>"<?= $init->title ?>" managed by <a href="https://twitter.com/<?= $init->twitterID ?>" target="_blank"><?= $init->adminName ?></a> （<a href="<?= $init->urlTopPage ?>" target="_blank"><?= $init->urlTopPage ?></a>）

<?php if ($init->performance): ?>
<small class="text-muted"><?php
list($tmp1, $tmp2) = array_pad(explode(" ", $init->CPU_start), 2, 0);
list($tmp3, $tmp4) = array_pad(explode(" ", microtime()), 2, 0);
printf(" (CPU time: %.3fs)", $tmp4-$tmp2+$tmp3-$tmp1);
?></small>
<?php endif; ?>

<a href="<?= $init->baseDir ?>/hako-admin.php"><small class="label label-default">管理画面</small></a>
</p>
<p><small class="text-muted">sources: <a href="https://www.github.com/sotalbireo/hakoniwa" target="_blank">hakoniwa - GitHub</a></small> // <small class="text-muted"><a href="<?= $init->baseDir ?>/beta/" target="_blank">開発版</a></small></p>
</footer>

<script>if(typeof(init)=="function")document.addEventListener("DOMContentLoaded",init());</script>
</div><!-- container -->
</body>
</html>
