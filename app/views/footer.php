<hr>

<footer class="container">
<p>"<?= $init->title ?>" managed by <a href="<?= $init->admin_address ?>" target="_blank"><?= $init->admin_name ?>（<?= $init->admin_address ?>）</a>

<?php if ($init->performance): ?>
<span class="small text-muted"><?php
[$tmp1, $tmp2] = array_pad(explode(" ", $init->CPU_start), 2, 0);
[$tmp3, $tmp4] = array_pad(explode(" ", microtime()), 2, 0);
printf(" (CPU time: %.3fs)", $tmp4-$tmp2+$tmp3-$tmp1);
?></span>
<?php endif; ?>

<a href="<?= $init->baseDir ?>/hako-admin.php"><span class="small label label-default">管理画面</span></a>
</p>

<section id="©">
<style>#©>*{color:#777}#© a{text-decoration:underline!important}</style>
<p><a href="https://www.github.com/sotalbireo/hakoniwa" target="_blank">"hakoniwa"</a> © 2016 Sotalbireo, <a href="https://cgi-game-preservations.org/" target="_blank">CGI Game Preservations Org.</a><br>Licensed by <a href="https://www.gnu.org/licenses/agpl.html" target="_blank">AGPL v3.0. <img src="<?=$init->baseDir?>/public/image/agplv3.png" style="width:66px"></a></p>
</section>
</footer>

<script>if(typeof(init)=="function")document.addEventListener("DOMContentLoaded",init());</script>
</div><!-- container -->
</body>
</html>
