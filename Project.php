<?php

namespace WPScript;

use Composer\Script\Event;

class Project{
    public static function postInstall(Event $e){
        // $composer = $e->getComposer();
        // vendor-dir
        // $config = $composer->getConfig();
        // $package = $composer->getPackage();
        static::rewriteServer();
    }

    protected static function rewriteServer(){
        $apacheRewrite = "<IfModule mod_rewrite.c>\r\n".
            "RewriteEngine on\r\n".
            // "RewriteCond %{HTTP_HOST} ^(www.)?wp.com$\r\n".
            "RewriteCond %{REQUEST_URI} !^/wp/\r\n".
            "RewriteCond %{REQUEST_FILENAME} !-f\r\n".
            "RewriteCond %{REQUEST_FILENAME} !-d\r\n".
            "RewriteRule ^(.*)$ /wp/$1 [L]\r\n".
            // "RewriteCond %{HTTP_HOST} ^(www.)?wp.com$\r\n".
            "RewriteRule ^(/)?$ wp/index.php [L]\r\n".
            "</IfModule>";
        $filename = getcwd() . '/.htaccess';
        if(!file_exists($filename)){
            file_put_contents($filename, $apacheRewrite);
        }
    }
}
