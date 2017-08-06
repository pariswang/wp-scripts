<?php

namespace WPScript;

use Composer\Script\Event;

class Project{
    public static function postInstall(Event $e){
        echo "POST INSTALL\r\n";
        var_dump($e);
    }
}
