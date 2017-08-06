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
        static::moveWPContent();
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

    protected static function moveWPContent(){
        $wp_content_dir = "wp-content";
        $wp_home = getcwd() . '/wp/';
        $define = "<?php\r\n".
            "if( ! defined('WP_CONTENT_URL') )\r\n".
            "\tdefine( 'WP_CONTENT_URL', '" . getcwd() . "/" . $wp_content_dir . "' );";

        $wp_index = $wp_home . 'index.php';
        $index = file_get_contents($wp_index);
        if(strpos($index, "WP_CONTENT_URL")===false){
            $index = str_ireplace( "<?php", $define, $index );
            file_put_contents( $wp_index, $index );
        }
    }
}
