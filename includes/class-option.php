<?php
/**
 * Created by PhpStorm.
 * User: yousan
 * Date: 10/20/15
 * Time: 6:28 PM
 */
class AP_Option
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    const APFT_OPTION_NAME = 'apft_options';

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * getter的ななにか
     * @param $varname
     * @return null
     */
    static public function get_($varname) {
        $options = get_option( self::APFT_OPTION_NAME );
        if (isset($options[$varname])) {
            return $options[$varname];
        } else {
            return null;
        }
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin',
            'Add Page From Template',
            'manage_options',
            'apft-setting-admin',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( self::APFT_OPTION_NAME );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2><?php _e('Add Page From Template', APFT_I18N_DOMAIN)?></h2>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields( 'apft_option_group' );
                do_settings_sections( 'apft-setting-admin' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'apft_option_group', // Option group
            self::APFT_OPTION_NAME, // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_apft', // ID
            __('APFT Custom Settings', APFT_I18N_DOMAIN), // Title
            array( $this, 'print_section_info' ), // Callback
            'apft-setting-admin' // Page
        );

        add_settings_field(
            'is_aggressive', // ID
            __("'Aggressive' flush_rewrite", APFT_I18N_DOMAIN), // Title
            array( $this, 'is_aggressive_callback' ), // Callback
            'apft-setting-admin', // Page
            'setting_apft' // Section
        );

        add_settings_field(
            'base_dir',
            __('Base Directory', APFT_I18N_DOMAIN),
            array( $this, 'base_dir_callback' ),
            'apft-setting-admin',
            'setting_apft'
        );

        add_settings_field(
            'template_files',
            __('Template Files', APFT_I18N_DOMAIN),
            array( $this, 'template_files_callback' ),
            'apft-setting-admin',
            'setting_apft'
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     * @return array
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['aggressive'] ) ) {
            $new_input['aggressive'] = $input['aggressive'];
        } else {
            $new_input['aggressive'] = false;
        }

        if( isset( $input['title'] ) )
            $new_input['title'] = sanitize_text_field( $input['title'] );

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        _e('Enter your settings below:');
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function is_aggressive_callback()
    {
        if ( isset($this->options['aggressive']) && false == $this->options['aggressive'] ) {
            $checked = '';
        } else {
            $checked = 'checked="checked"';
        }
        echo '<p>';
        printf(
            '<input type="checkbox" id="aggressive" name="apft_options[aggressive]" value="1" %s />',
            $checked
        );
        echo _("Aggressive means 'Do flush_rewrite when load a page.'").'</p>';
    }

    public function base_dir_callback() {
        if (isset( $this->options['base_dir'] )) {
            $base_dir = esc_attr( $this->options['base_dir']);
        }  else {
            $base_dir = 'pages/';
        }
        printf(
            '<input type="text" id="base_dir" name="apft_options[base_dir]" value="%s" style="width: 100%%;"/>',
            $base_dir
        );
        _e('Specify Base Directory. The paged template files should be located at the base directory. (Default: pages/)', APFT_I18N_DOMAIN);
    }

    public function template_files_callback() {
        ?>
        <table class="widefat" id="apft-templates">
            <thead><tr class="head" style="cursor: move;">
                <th scope="col"><?php _e('Template Name', APFT_I18N_DOMAIN); ?></th>
                <th scope="col"><?php _e('Status', APFT_I18N_DOMAIN);?></th>
            </tr>
            </thead>
            <tbody>
            <tr class="nodrag nodrop">
                <td>&nbsp;</td>
                <td>hoge</td>
            </tr>
<!--            <tr class="nodrag nodrop">-->
<!--                <td>&nbsp;</td>-->
<!--                <td><i>Registration IP</i></td>-->
<!--                <td><i>wpmem_reg_ip</i></td>-->
<!--                <td colspan="5">&nbsp;</td>-->
<!--                <td align="center">-->
<!--                    <input type="checkbox" name="ut_fields[wpmem_reg_ip]" value="Registration IP">-->
<!--                </td>-->
<!--            </tr>-->
            </tbody></table>
        <?php
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function title_callback()
    {
        printf(
            '<input type="text" id="title" name="my_option_name[title]" value="%s" />',
            isset( $this->options['title'] ) ? esc_attr( $this->options['title']) : ''
        );
    }
}