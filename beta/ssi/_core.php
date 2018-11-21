<?php declare(strict_types=1);

final class HtmlCore
{
    public static function head(string $title, string $opt = "")
    {
        global $init;
        $title = $title !== "" ? $title : $init->title;
        require ROOT."beta/ssi/_head.php";
    }



    public static function gnav()
    {
        global $init;
        require ROOT."beta/ssi/_gnav.php";
    }




    public static function h1(string $title, string $subtitle = "")
    {
        if ($subtitle === "") {
            echo '<h1 class="title">', $title, "</h1>\n";
        } else {
            echo '<h1 class="title">', $title, ' <small>', $subtitle, "</small></h1>\n";
        }
    }



    public static function foot()
    {
        global $init;
        $processing_time = "";
        if ($init->performance) {
            [$tmp1, $tmp2] = array_pad(explode(" ", $init->CPU_start), 2, 0);
            [$tmp3, $tmp4] = array_pad(explode(" ", microtime()), 2, 0);
            $processing_time = " <span style=\"font-size:.8em\">".sprintf(" (CPU time: %.3fs)", $tmp4-$tmp2+$tmp3-$tmp1)."</small>";
            unset($tmp1, $tmp2, $tmp3, $tmp4);
        }
        require ROOT."beta/ssi/_foot.php";
    }
}
