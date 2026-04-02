<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class IILLC_Plugin {

    public function init() {
        add_shortcode( 'wsacd_protected', [ $this, 'protected_shortcode' ] );
    }

    public function protected_shortcode( $atts, $content = null ) {
        if ( ! is_user_logged_in() ) {
            return 'You must be logged in.';
        }
        return do_shortcode( $content );
    }
}
