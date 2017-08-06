<?php

namespace WPScript;

use Composer\Script\Event;

class Project{
    public static function postInstall(Event $e){
        echo "POST INSTALL\r\n";
        foreach($e as $key => $value){
            echo "    $key => $value\r\n";
        }
        echo "END\r\n";
    }
}
