<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class IILLC_Plugin {
    /**
     * Stores missing dependency names after checks run.
     * @var array
     */


    protected $missing_dependencies = array();


    public function init() {
        add_shortcode( 'wsacd_protected', [ $this, 'protected_shortcode' ] );

        // Run dependency checks and show admin notices when needed.
        $this->missing_dependencies = self::get_missing_dependencies();
        if ( is_admin() && ! empty( $this->missing_dependencies ) ) {
            add_action( 'admin_notices', [ $this, 'admin_dependency_notices' ] );
        }
        if ( is_admin() ) {
            include_once IILLC_WSACD_PATH . 'includes/class-admin.php';
            if ( class_exists( 'IILLC_Admin' ) ) {
                $admin = new IILLC_Admin();
                $admin->init();
            }
        }
    }


    //main shortcode handler for [wsacd_protected subscription_product_id="123" memberium_tag="VIP" upgrade_url="/upgrade"]Protected content here[/wsacd_protected]
    //additional messaging added for logged in users who don't have access, prompting them to upgrade or contact support, instead of just showing the "you must log in" message.
    public function protected_shortcode( $atts, $content = null ) {
        $atts = shortcode_atts(
            [
                'subscription_product_id' => '',
                'memberium_tag' => '',
                'upgrade_url' => '',
                'login_label' => 'Log in',
                'upgrade_label' => 'Upgrade',
            ], $atts, 'wsacd_protected'
        );

        $options = get_option( 'iillc_wsacd_options', array() );
        if ( empty( $atts['subscription_product_id'] ) && ! empty( $options['default_product_ids'] ) ) {
            $atts['subscription_product_id'] = $options['default_product_ids'];
        }
        if ( empty( $atts['memberium_tag'] ) && ! empty( $options['default_memberium_tag'] ) ) {
            $atts['memberium_tag'] = $options['default_memberium_tag'];
        }
        if ( empty( $atts['upgrade_url'] ) && ! empty( $options['default_upgrade_url'] ) ) {
            $atts['upgrade_url'] = $options['default_upgrade_url'];
        }

        if ( ! is_user_logged_in() ) {
            $login_url = wp_login_url( get_permalink() );
            $html = '<div class="alert alert-warning" role="alert">';
            $html .= esc_html__( 'You must be logged in to view this content.', 'iillc-wsacd' );
            $html .= ' <a href="' . esc_url( $login_url ) . '" class="btn btn-sm btn-primary ml-2">' . esc_html( $atts['login_label'] ) . '</a>';
            $html .= '</div>';
            return $html;
        }

        $user_id = get_current_user_id();
        $has_access = false;

        $required_subscription = trim( $atts['subscription_product_id'] );
        if ( $required_subscription && function_exists( 'wcs_user_has_subscription' ) ) {
            $ids = array_map( 'trim', explode( ',', $required_subscription ) );
            foreach ( $ids as $pid ) {
                if ( empty( $pid ) ) {
                    continue;
                }
                if ( wcs_user_has_subscription( $user_id, $pid, 'active' ) ) {
                    $has_access = true;
                    break;
                }
            }
        }

        $memberium_tag = trim( $atts['memberium_tag'] );
        if ( ! $has_access && $memberium_tag ) {
            $tags = array_map( 'trim', explode( ',', $memberium_tag ) );
            foreach ( $tags as $tag ) {
                if ( $this->user_has_memberium_tag( $user_id, $tag ) ) {
                    $has_access = true;
                    break;
                }
            }
        }

        if ( $has_access ) {
            return do_shortcode( $content );
        }

        // User is logged in but doesn't have required subscription or tag. Show upgrade message.
        $upgrade_url = trim( $atts['upgrade_url'] );
        $html = '<div class="alert alert-danger" role="alert">';
        $html .= esc_html__( 'You do not have access to this content. Please upgrade your subscription or contact support.', 'iillc-wsacd' );
        if ( $upgrade_url ) {
            $html .= ' <a href="' . esc_url( $upgrade_url ) . '" class="btn btn-sm btn-primary ml-2">' . esc_html( $atts['upgrade_label'] ) . '</a>';
        }
        $html .= '</div>';
        return $html;
    }


    // additional checks for Memberium tags in special cases such as "VIP" that may not have the paid subscription required but get the special tag.
    protected function user_has_memberium_tag( $user_id, $tag ) {
        $check = apply_filters( 'wsacd_member_tag_check', null, $user_id, $tag );
        if ( is_bool( $check ) ) {
            return $check;
        }

        $tag  = trim( (string) $tag );
        $keys = [ 'memberium_tags', 'memb_tags', 'memberium_user_tags', 'tags', 'memberium_tag' ];

        foreach ( $keys as $k ) {
            $val = get_user_meta( $user_id, $k, true );
            if ( empty( $val ) ) {
                continue;
            }

            if ( is_array( $val ) ) {
                $stored_tags = array_filter( array_map( 'trim', $val ) );

                if ( in_array( $tag, $stored_tags, true ) ) {
                    return true;
                }
            }

            if ( is_string( $val ) ) {
                $stored_tags = preg_split( '/[\s,|]+/', $val );
                $stored_tags = array_filter( array_map( 'trim', $stored_tags ) );

                if ( in_array( $tag, $stored_tags, true ) ) {
                    return true;
                }
            }
        }

        return false;
    }



    /**
     * Return an array of human-friendly missing dependency names.
     * Static so other admin classes can call it without an instance.
     *
     * @return array
     */


    public static function get_missing_dependencies() {
        $missing = array();

        // WooCommerce
        $has_wc = class_exists( 'WooCommerce' ) || function_exists( 'is_woocommerce' ) || function_exists( 'wc' );
        if ( ! $has_wc ) {
            $missing[] = 'WooCommerce';
        }

        // WooCommerce Subscriptions (used for wcs_user_has_subscription())
        if ( ! function_exists( 'wcs_user_has_subscription' ) ) {
            $missing[] = 'WooCommerce Subscriptions';
        }

        // Memberium — optional but used for tag checks. Use multiple heuristics.
        $memberium_found = false;
        if ( defined( 'MEMBERIUM_VERSION' ) ) {
            $memberium_found = true;
        }
        if ( function_exists( 'memberium_get_user_tags' ) || function_exists( 'memb_get_user_tags' ) ) {
            $memberium_found = true;
        }
        if ( class_exists( 'Memberium' ) || class_exists( 'Memberium_Plugin' ) ) {
            $memberium_found = true;
        }
        if ( ! $memberium_found ) {
            $missing[] = 'Memberium (optional — enables tag checks)';
        }

        return $missing;
    }




    /**
     * Output admin notice listing missing dependencies and a short instruction.
     */

    public function admin_dependency_notices() {
        if ( empty( $this->missing_dependencies ) ) {
            return;
        }
        $list = implode( ', ', array_map( 'esc_html', $this->missing_dependencies ) );
        ?>
        <div class="notice notice-warning">
            <p><?php echo sprintf( esc_html__( 'WooCommerce Subscription Access Control Demo: missing dependencies detected: %s. Some functionality will be limited until those plugins are activated.', 'iillc-wsacd' ), $list ); ?></p>
        </div>
        <?php
    }
}