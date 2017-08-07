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
            static::moveWPContent($extra['site-url']);
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

    protected static function moveWPContent($site_url){
        $wp_content_dir = "wp-content";
        $wp_home = getcwd() . '/wp/';
        $define = "<?php" . "\r\n".
            "if( ! defined('WP_CONTENT_DIR') ) {" . "\r\n".
            "\tdefine( 'WP_CONTENT_DIR', '" . getcwd() . "/" . $wp_content_dir . "' );" . "\r\n".
            "\tdefine( 'WP_CONTENT_URL', '" . $site_url . "/" . $wp_content_dir . "');" . "\r\n".
            "}" . "\r\n";

        $wp_index = $wp_home . 'index.php';
        $index = file_get_contents($wp_index);
        if(strpos($index, "WP_CONTENT_DIR")===false){
            $index = str_ireplace( "<?php", $define, $index );
            file_put_contents( $wp_index, $index );
        }
    }
}
