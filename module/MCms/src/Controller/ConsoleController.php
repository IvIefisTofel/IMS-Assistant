<?php

namespace MCms\Controller;

class ConsoleController extends MCmsController
{
    public function compileMoAction()
    {
        $console = new \Console();
        echo $console->colorizeTaggedText("<black><bg_yellow>Generating translation files</bg_yellow></black><br>");
        foreach (glob("./languages/*.po") as $fileName) {
            $fileInfo = pathinfo($fileName);
            if (phpmo_convert($fileName)) {
                echo $console->colorizeTaggedText("    <green>" . $fileInfo['filename'] . ".mo</green> generated from <green>" . $fileInfo['basename'] . "</green>.<br>");
            } else {
                echo $console->colorizeTaggedText("    Error <red>" . $fileInfo['basename'] . "</red> generation.<br>");
            }
        }
        return;
    }
}