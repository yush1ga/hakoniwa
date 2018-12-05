<?php

declare(strict_types=1);

final class HtmlCore
{
    use HtmlUtility;

    public function head(string $title, string $opt = "")
    {
        global $init;
        $title = $title !== "" ? $title : $init->title;
        require ROOT."beta/ssi/_head.php";

        if (DEBUG) {
            dump($_GET, $_POST);
        }

        return $this;
    }



    public function gnav()
    {
        global $init;
        require ROOT."beta/ssi/_gnav.php";

        return $this;
    }




    public function h1(string $title, string $subtitle = ""): void
    {
        if ($subtitle === "") {
            echo '<h1 class="title">', $title, "</h1>\n";
        } else {
            echo '<h1 class="title">', $title, ' <small>', $subtitle, "</small></h1>\n";
        }
    }



    public function foot()
    {
        global $init;
        $processing_time = "";
        if ($init->performance) {
            [$tmp1, $tmp2] = array_pad(explode(" ", $init->CPU_start), 2, 0);
            [$tmp3, $tmp4] = array_pad(explode(" ", microtime()), 2, 0);
            $processing_time = " <span style=\"font-size:.76em\">".sprintf(" (CPU time: %.3fs)", $tmp4-$tmp2+$tmp3-$tmp1)."</small>";
            unset($tmp1, $tmp2, $tmp3, $tmp4);
        }
        require ROOT."beta/ssi/_foot.php";

        return $this;
    }
}


trait HtmlUtility
{
    final public function iniVal2rawVal(string $iniVal)
    {
        $iniVal = trim($iniVal);
        $suf = mb_strtolower(mb_substr($iniVal, -1));
        $base = intVal(mb_substr($iniVal, 0, mb_strlen($iniVal)-1), 10);
        $sufExp = ["k" => 1, "m" => 2, "g" => 3];

        return $base * (1024 ** ($sufExp[$suf] ?? 0));
    }
}
