<footer class="ui inverted vertical footer segment mw-100">
    <div class="ui container">
        <div class="ui stackable inverted divided equal height stackable grid">
            <div class="eight wide column">
                <h4>Main menu</h4>
                <p><a href="<?= $init->baseDir ?>/hako-admin.php" class="ui small label"><i class="cog icon"></i> 管理画面</a> <?=$processing_time?></p>
            </div>
            <div class="eight wide column">
                <p>
                    "<?= $init->title ?>" being managed by <a href="<?= $init->admin_address ?>" target="_blank"><?= $init->admin_name ?>（<?= $init->admin_address ?>）</a>.</p>
                <section id="©">
                    <style>#© a{text-decoration:underline!important}</style>
                    <p><a href="https://www.github.com/sotalbireo/hakoniwa" target="_blank">"hakoniwa"</a> © 2016 Sotalbireo, <a href="https://cgi-game-preservations.org/" target="_blank">CGI Game Preservations Org.</a> Licensed by <a href="https://www.gnu.org/licenses/agpl.html" target="_blank">AGPL v3.0.</a></p>
                </section>
            </div>
        </div>
    </div>
</footer>
</body>
</html>
