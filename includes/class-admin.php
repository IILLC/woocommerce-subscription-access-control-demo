<?php
if ( ! defined( 'ABSPATH' ) ) exit;


//This class handles the admin settings page for the plugin, allowing site admins to set default product IDs, memberium tags, and upgrade URLs that will be used by the shortcode when those attributes are not provided. It also shows admin notices if required dependencies are missing.

class IILLC_Admin {

    public function init() {
        add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }


    public function add_settings_page() {
        add_options_page(
            __( 'WSACD Settings', 'iillc-wsacd' ),
            __( 'Subscription Access', 'iillc-wsacd' ),
            'manage_options',
            'iillc-wsacd',
            [ $this, 'settings_page_html' ]
        );
    }


    public function register_settings() {
        register_setting( 'iillc_wsacd', 'iillc_wsacd_options', [ $this, 'sanitize_options' ] );

        add_settings_section(
            'iillc_wsacd_main',
            __( 'Default Access Controls', 'iillc-wsacd' ),
            function() { echo '<p>' . esc_html__( 'Default values used by the shortcode when attributes are not provided.', 'iillc-wsacd' ) . '</p>'; },
            'iillc-wsacd'
        );

        add_settings_field(
            'default_product_ids',
            __( 'Default Subscription Product IDs', 'iillc-wsacd' ),
            [ $this, 'field_textarea_cb' ],
            'iillc-wsacd',
            'iillc_wsacd_main',
            [ 'label_for' => 'default_product_ids', 'option_key' => 'default_product_ids', 'description' => __( 'Comma-separated product IDs for Woo Subscriptions.', 'iillc-wsacd' ) ]
        );

        add_settings_field(
            'default_memberium_tag',
            __( 'Default Memberium Tag', 'iillc-wsacd' ),
            [ $this, 'field_text_cb' ],
            'iillc-wsacd',
            'iillc_wsacd_main',
            [ 'label_for' => 'default_memberium_tag', 'option_key' => 'default_memberium_tag' ]
        );

        add_settings_field(
            'default_upgrade_url',
            __( 'Default Upgrade URL', 'iillc-wsacd' ),
            [ $this, 'field_text_cb' ],
            'iillc-wsacd',
            'iillc_wsacd_main',
            [ 'label_for' => 'default_upgrade_url', 'option_key' => 'default_upgrade_url' ]
        );
    }


    public function sanitize_options( $input ) {
        $out = array();
        $out['default_product_ids'] = isset( $input['default_product_ids'] ) ? sanitize_text_field( $input['default_product_ids'] ) : '';
        $out['default_memberium_tag'] = isset( $input['default_memberium_tag'] ) ? sanitize_text_field( $input['default_memberium_tag'] ) : '';
        $out['default_upgrade_url'] = isset( $input['default_upgrade_url'] ) ? esc_url_raw( $input['default_upgrade_url'] ) : '';
        return $out;
    }


    public function field_textarea_cb( $args ) {
        $options = get_option( 'iillc_wsacd_options', array() );
        $key = $args['option_key'];
        $val = isset( $options[ $key ] ) ? $options[ $key ] : '';
        printf( '<textarea id="%1$s" name="iillc_wsacd_options[%1$s]" rows="3" cols="60">%2$s</textarea><p class="description">%3$s</p>', esc_attr( $key ), esc_textarea( $val ), esc_html( $args['description'] ?? '' ) );
    }


    public function field_text_cb( $args ) {
        $options = get_option( 'iillc_wsacd_options', array() );
        $key = $args['option_key'];
        $val = isset( $options[ $key ] ) ? $options[ $key ] : '';
        printf( '<input id="%1$s" name="iillc_wsacd_options[%1$s]" type="text" value="%2$s" class="regular-text" />', esc_attr( $key ), esc_attr( $val ) );
    }


    public function settings_page_html() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $missing = array();
        if ( class_exists( 'IILLC_Plugin' ) && method_exists( 'IILLC_Plugin', 'get_missing_dependencies' ) ) {
            $missing = IILLC_Plugin::get_missing_dependencies();
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Subscription Access Settings', 'iillc-wsacd' ); ?></h1>
            <?php if ( ! empty( $missing ) ) : ?>
                <div class="notice notice-warning inline">
                    <p><?php echo sprintf( esc_html__( 'This plugin may not work correctly unless these plugins are active: %s.', 'iillc-wsacd' ), esc_html( implode( ', ', $missing ) ) ); ?></p>
                </div>
            <?php endif; ?>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'iillc_wsacd' );
                do_settings_sections( 'iillc-wsacd' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}
