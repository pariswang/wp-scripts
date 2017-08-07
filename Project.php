<?php

namespace WPScript;

use Composer\Script\Event;

class Project{
    public static function postInstall(Event $e){
        $composer = $e->getComposer();
        // vendor-dir
        // $config = $composer->getConfig();
        $package = $composer->getPackage();
        $extra = $package->getExtra();
        if(isset($extra['site-domain'])){
            static::rewriteServer($extra['site-domain']);
        }
    }

    protected static function rewriteServer($site_domain){
        $apacheRewrite = "<IfModule mod_rewrite.c>\r\n".
            "RewriteEngine on\r\n".
            "RewriteCond %{HTTP_HOST} ^(www.)?" . $site_domain . "$\r\n".
            "RewriteCond %{REQUEST_URI} !^/wp/\r\n".
            "RewriteCond %{REQUEST_FILENAME} !-f\r\n".
            "RewriteCond %{REQUEST_FILENAME} !-d\r\n".
            "RewriteRule ^(.*)$ /wp/$1 [L]\r\n".
            "RewriteCond %{HTTP_HOST} ^(www.)?" . $site_domain . "$\r\n".
            "RewriteRule ^(/)?$ wp/index.php [L]\r\n".
            "</IfModule>";
        $filename = getcwd() . '/.htaccess';
        if(!file_exists($filename)){
            file_put_contents($filename, $apacheRewrite);
        }
    }
}
