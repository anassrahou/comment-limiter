<?php
/**
 * If accessed directly, then exit
 */
defined( 'ABSPATH' ) || exit;


/**
 * If class does not exist, then create it
 */
if ( ! class_exists( 'Comment_Limiter_Settings' ) ) {

    /**
     * Class that holds Comment Limiter settings
     */
    class Comment_Limiter_Settings
    {
        /**
         * Property instance
         *
         * @since 1.0
         * @var object
         */
        private static $_instance;

        /**
         * Handles get_option values
         *
         * @since 1.0
         * @var array
         */
        private $_comment_limiter_options;

        /**
         * Handles global configuration class
         *
         * @since 1.3
         * @var array
         */
        private $_config;

        /**
         * handles default values
         *
         * @since 1.0
         * @var array
         */
        public $defaults = array();

        /**
         * Constructor
         *
         * @since 1.0
         */
        public function __construct() {
            // ...
        }

        /**
         * Setup action and filter hooks
         *
         * @since 1.0
         * @return void
         */
        public function setup() {

            add_action( 'admin_init',            array( $this, 'comment_limiter_page_init' ) );
            add_action( 'admin_menu',            array( $this, 'comment_limiter_add_submenu_page' ) );
            add_filter( 'preprocess_comment',    array( $this, 'comment_limiter_checker' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ) );
            
            add_action('wp_enqueue_scripts',        array( $this, 'comment_limiter_localize_scripts') );

            $this->_config = Comment_Limiter_Config::factory()->get();
            $this->_comment_limiter_options = $this->_config;
        }

        /**
         * Enqueue style
         *
         * @since 1.0
         * @return void
         */
        public function admin_enqueue_styles() {
            $page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';

            if ( 'comment-limiter' === $page ) {
                wp_enqueue_style( 'cl-settings-css', plugins_url( '/assets/css/settings.css', dirname( __FILE__ ) ), array(), CL_VERSION, 'all' );
            }
        }

        /**
         * Add submenu page in the dashboard
         *
         * @since 1.0
         * @return string
         */
        public function comment_limiter_add_submenu_page() {

            add_submenu_page(
                'edit-comments.php',
                __( 'Comment Limiter', 'comment-limiter' ),
                __( 'Comment Limiter', 'comment-limiter' ),
                'manage_options',
                'comment-limiter',
                array( $this, 'comment_limiter_admin_page' )
            );
        }

        /**
         * Setup HTML form
         *
         * @since 1.0
         * @return void
         */
        public function comment_limiter_admin_page() {

            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            if ( isset( $_GET['settings-updated'] ) ) {
                add_settings_error( 'cl_messages', 'cl_message', __( 'Comment Limiter settings saved correctly.', 'comment-limiter' ), 'updated' );
            }
            ?>
            <div class="wrap" id="comment-limiter">
                <h2><?php esc_html_e( 'Comment Limiter', 'comment-limiter' ); ?></h2>
                    <?php settings_errors( 'comment-limiter-messages' ); ?>

                <form method="post" action="options.php">
                    <?php
                    settings_fields( 'comment_limiter_group' );
                    do_settings_sections( 'comment-limiter-admin' );
                    submit_button( esc_html__( 'Save Comments Changes', 'comment-limiter' ), 'primary', '' );
                    ?>
                </form>
            </div>
            <?php
        }

        /**
         * Register settings
         *
         * @since 1.0
         * @return void
         */
        public function comment_limiter_page_init() {

            register_setting(
                'comment_limiter_group',
                'comment_limiter_settings',
                array(
                    'type'              => 'array',
                    'sanitize_callback' => array( $this, 'comment_limiter_sanitize' ),
                    'default'           => Comment_Limiter_Config::factory()->get_defaults(),
                )
            );

            add_settings_section(
                'comment_limiter_section',
                '', // Description omitted
                array( $this, 'comment_limiter_section_info' ),
                'comment-limiter-admin'
            );

            add_settings_field(
                'maximum_characters',
                __( 'Maximum Characters Number', 'comment-limiter' ),
                array( $this, 'maximum_characters_callback' ),
                'comment-limiter-admin',
                'comment_limiter_section',
                array(
                    'label_for' => 'maximum_characters',
                    'class'     => 'maximum_characters',
                )
            );

            add_settings_field(
                'maximum_message',
                __( 'Maximum Message Error', 'comment-limiter' ),
                array( $this, 'maximum_message_callback' ),
                'comment-limiter-admin',
                'comment_limiter_section',
                array(
                    'label_for' => 'maximum_message',
                    'class'     => 'maximum_message',
                )
            );

            add_settings_field(
                'minimum_characters',
                __( 'Minimum Characters Number', 'comment-limiter' ),
                array( $this, 'minimum_characters_callback' ),
                'comment-limiter-admin',
                'comment_limiter_section',
                array(
                    'label_for' => 'minimum_characters',
                    'class'     => 'minimum_characters',
                )
            );

            add_settings_field(
                'minimum_message',
                __( 'Minimum Message Error', 'comment-limiter' ),
                array( $this, 'minimum_message_callback' ),
                'comment-limiter-admin',
                'comment_limiter_section',
                array(
                    'label_for' => 'minimum_message',
                    'class'     => 'minimum_message',
                )
            );

            add_settings_field(
                'enable_admin_feature',
                __( 'Apply Settings to Admins', 'comment-limiter' ),
                array( $this, 'comment_limiter_dropdown' ),
                'comment-limiter-admin',
                'comment_limiter_section',
                array(
                    'label_for' => 'enable_admin_feature',
                    'class'     => 'enable_admin_feature',
                )
            );

        }




/**
         * Sanitize and validate fields
         *
         * @since 1.0
         * @param  array
         * @return array
         */
        public function comment_limiter_sanitize( $input ) {

            $defaults = Comment_Limiter_Config::factory()->get_defaults();
            $existing = is_array( $this->_comment_limiter_options ) ? $this->_comment_limiter_options : $defaults;
            $output   = wp_parse_args( $existing, $defaults );
            $input    = is_array( $input ) ? wp_unslash( $input ) : array();

            // Track if we hit any errors during sanitization
            $has_error = false;

            $maximum_characters = isset( $input['maximum_characters'] ) ? absint( $input['maximum_characters'] ) : absint( $output['maximum_characters'] );
            $minimum_characters = isset( $input['minimum_characters'] ) ? absint( $input['minimum_characters'] ) : absint( $output['minimum_characters'] );

            if ( $maximum_characters <= $minimum_characters ) {
                add_settings_error( 'comment-limiter-messages', 'invalid-values', __( 'Invalid lengths. Please insert logical values.', 'comment-limiter' ) );
                return $output; // Return early on critical logic flaw
            }

            // Check and clean Maximum Message
            if ( ! empty( trim( $input['maximum_message'] ?? '' ) ) ) {
                $output['maximum_message'] = sanitize_text_field( $input['maximum_message'] );
            } else {
                add_settings_error( 'comment-limiter-messages', 'empty-values', __( 'Maximum message error is required.', 'comment-limiter' ) );
                $has_error = true;
            }

            // Check and clean Minimum Message
            if ( ! empty( trim( $input['minimum_message'] ?? '' ) ) ) {
                $output['minimum_message'] = sanitize_text_field( $input['minimum_message'] );
            } else {
                add_settings_error( 'comment-limiter-messages', 'empty-values', __( 'Minimum message error is required.', 'comment-limiter' ) );
                $has_error = true;
            }

            // If any errors were triggered above, return the previous clean working state data instead of saving empty strings
            if ( $has_error ) {
                return $output; 
            }

            // No errors found! Apply changes and proceed to save safely
            $output['maximum_characters'] = $maximum_characters;
            $output['minimum_characters'] = $minimum_characters;
            $output['enable_admin_feature'] = isset( $input['enable_admin_feature'] ) && 'yes' === $input['enable_admin_feature'] ? 'yes' : 'no';

            add_settings_error( 'comment-limiter-messages', 'success-message', __( 'Comment Limiter settings saved correctly.', 'comment-limiter' ), 'updated' );

            return $output;
        }

        /**
         * Section description
         *
         * @since 1.0
         * @return void
         */
        public function comment_limiter_section_info() {
            ?>
            <p class="description"><?php esc_html_e( 'Configure the minimum and maximum comment character limits and customize their error messages using {current}, {min}, and {max} to show counts dynamically.', 'comment-limiter' ); ?></p>
            <?php
        }

        /**
         * Count comment characters in a multibyte-safe way.
         *
         * @since 2.3.0
         * @param string $content Comment content.
         * @return int
         */
        private function comment_limiter_character_count( $content ) {
            $content = trim( wp_strip_all_tags( (string) $content ) );

            if ( function_exists( 'mb_strlen' ) ) {
                return mb_strlen( $content, get_bloginfo( 'charset' ) );
            }

            return strlen( $content );
        }

        /**
         * Check comment length
         *
         * @since 1.0
         * @param  array
         * @return array
         */
        public function comment_limiter_checker( $commentdata ) {
            $options = Comment_Limiter_Config::factory()->get();

            if ( current_user_can( 'manage_options' ) && 'no' === $options['enable_admin_feature'] ) {
                return $commentdata;
            }

            $comment_length = $this->comment_limiter_character_count( $commentdata['comment_content'] ?? '' );
            $maximum        = absint( $options['maximum_characters'] );
            $minimum        = absint( $options['minimum_characters'] );

            // Define the replacement map for tokens
            $placeholders = array(
                '{min}'     => $minimum,
                '{max}'     => $maximum,
                '{current}' => $comment_length,
            );

            // If comment is too long
            if ( $comment_length > $maximum ) {
                $error_message = str_replace( array_keys( $placeholders ), array_values( $placeholders ), $options['maximum_message'] );
                
                // Return WP_Error instead of wp_die() so the theme handles the display gracefully if JS is bypassed
                return new WP_Error( 'comment_too_long', esc_html( $error_message ) );
            }

            // If comment is too short
            if ( $comment_length < $minimum ) {
                $error_message = str_replace( array_keys( $placeholders ), array_values( $placeholders ), $options['minimum_message'] );

                // Return WP_Error instead of wp_die() so the theme handles the display gracefully if JS is bypassed
                return new WP_Error( 'comment_too_short', esc_html( $error_message ) );
            }

            return $commentdata;
        }

        /**
         * Maximum Characters callback
         *
         * @since 1.0
         * @return string
         */
        public function maximum_characters_callback() {
            ?>
            <input type="number" name="comment_limiter_settings[maximum_characters]" id="maximum_characters" class="regular-text" value="<?php echo esc_attr( $this->_config['maximum_characters'] ); ?>" />
            <span class="description"><?php esc_html_e( 'Accepts only numbers', 'comment-limiter' ); ?></span>
            <?php
        }

        /**
         * Maximum Message callback
         *
         * @since 1.0
         * @return string
         */
        public function maximum_message_callback() {
            ?>
            <textarea rows="2" cols="50" name="comment_limiter_settings[maximum_message]" id="maximum_message"><?php echo esc_textarea( $this->_config['maximum_message'] ); ?></textarea>
            <p class="description"><?php esc_html_e( 'This is the error message that will be displayed to the user when the comment length is more than the maximum value indicated above.', 'comment-limiter' ); ?></p>
            <?php
        }

        /**
         * Minimum Characters callback
         *
         * @since 1.0
         * @return string
         */
        public function minimum_characters_callback() {
            ?>
            <input type="number" name="comment_limiter_settings[minimum_characters]" id="minimum_characters" class="regular-text" value="<?php echo esc_attr( $this->_config['minimum_characters'] ); ?>" />
            <span class="description"><?php esc_html_e( 'Accepts only numbers', 'comment-limiter' ); ?></span>
            <?php
        }

        /**
         * Minimum Message callback
         *
         * @since 1.0
         * @return string
         */
        public function minimum_message_callback() {
            ?>
            <textarea rows="2" cols="50" name="comment_limiter_settings[minimum_message]" id="minimum_message"><?php echo esc_textarea( $this->_config['minimum_message'] ); ?></textarea>
            <p class="description"><?php esc_html_e( 'This is the error message that will be displayed to the user when the comment length is less than the minimum value indicated above.', 'comment-limiter' ); ?></p>
            <?php
        }

        /**
         * Apply settings to admins dropdown callback
         *
         * @since 1.0
         * @return string
         */
        public function comment_limiter_dropdown() {
            ?>
            <select name="comment_limiter_settings[enable_admin_feature]" id="enable_admin_feature">
                <option value="yes" <?php selected( $this->_config['enable_admin_feature'], 'yes' ); ?>>
                    <?php esc_html_e( 'Yes', 'comment-limiter' ); ?>
                </option>
                <option value="no" <?php selected( $this->_config['enable_admin_feature'], 'no' ); ?>>
                    <?php esc_html_e( 'No', 'comment-limiter' ); ?>
                </option>
            </select>
            <p class="description"><?php esc_html_e( 'This will allows users with administrator capabilities to publish comments despite the configuration of Comment Limiter plugin.', 'comment-limiter' ); ?></p>
            <?php
        }


        public function comment_limiter_localize_scripts() {
        
            $is_admin_user = current_user_can('manage_options') ? 'yes' : 'no';
        

            if (current_user_can('manage_options') && $this->_config['enable_admin_feature'] === 'no') {
                return;
            }
        
            if (is_single() && comments_open()) {
                wp_enqueue_script('cl-script', plugins_url('/assets/js/word-counter.js', dirname(__FILE__)), array('jquery'), CL_VERSION, true);
                wp_enqueue_style( 'cl-frontend-css', plugins_url( '/assets/css/frontend.css', dirname( __FILE__ ) ), array(), CL_VERSION, 'all' );

                wp_localize_script('cl-script', 'cl_vars', array(
                    'is_admin_user'      => esc_attr($is_admin_user),
                    'enableAdminFeature' => $this->_config['enable_admin_feature'],
                    'minChars'           => absint( $this->_config['minimum_characters'] ),
                    'maxChars'           => absint( $this->_config['maximum_characters'] ),
                    'minMessage'         => $this->_config['minimum_message'],
                    'maxMessage'         => $this->_config['maximum_message'],
                    'counterFormat'      => __('Characters: %1$d of %2$d', 'comment-limiter'),
                    'minRequired'        => __( $this->_config['minimum_message'], 'comment-limiter'),
                    'maxAllowed'         => __( $this->_config['maximum_message'], 'comment-limiter'),
                ));
            }
        }

        /**
         * Factory Method
         *
         * @since 1.0
         * @return object
         */
        public static function factory() {

            if ( ! self::$_instance ) {
                self::$_instance = new self;
                self::$_instance->setup();
            }

            return self::$_instance;
        }

    }

}
