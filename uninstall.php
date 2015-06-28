<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}
 
$option_name = 'mobiad_js_adtag';
 
delete_option( $option_name );
?>
