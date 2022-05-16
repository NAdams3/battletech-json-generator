<?php
/*
Plugin Name: BattleTech JSON Generator
Plugin URI: www.blockade.dev
Description: Should make JSON for BattleTech game when finished.
Version: 0.0.1
Author: Nick Adams
Author URI: www.blockade.dev
License: GPLv2 or later
Text Domain: battletech-json-generator
*/

namespace BTJG;

Plugin::init();

class Plugin {

    // const PLUGIN_PREFIX = 'btjg_';

    public $plugin_path;
    public $plugin_prefix;

    public function __construct() {

        // set plugin path
        $this->plugin_path = plugin_dir_path( __FILE__ );

        //set plugin_prefix
        $this->plugin_prefix = strtolower(__NAMESPACE__) . '_';

        // register autoloader
        spl_autoload_register(array($this, 'autoloader'));

        // register activate
        register_activation_hook(__FILE__, array($this, 'activate'));

        // register deactivate
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));        

    }

    public static function init() {
        return new Plugin();
    }

    public function activate() {
        //init classes
        Weapon::init( $this->plugin_path, $this->plugin_prefix );
        Mech::init( $this->plugin_path, $this->plugin_prefix );
        Location::init( $this->plugin_path, $this->plugin_prefix );
        Hardpoint::init( $this->plugin_path, $this->plugin_prefix );
        LocationItem::init( $this->plugin_path, $this->plugin_prefix );
        Equipment::init( $this->plugin_path, $this->plugin_prefix );
    }

    public function deactivate() {
        // deactivate classes
        Weapon::deactivate();
        Mech::deactivate();
        Location::deactivate();
        Hardpoint::deactivate();
        LocationItem::deactivate();
        Equipment::deactivate();
        // Unregister post types
	    // unregister_post_type("custom post type");
	    // Clear the permalinks to remove our post type's rules from the database
	    flush_rewrite_rules();
    }

    public function autoloader( $class ) {

        // check if class is part of our plugin
        if( !str_contains( strtolower($class), strtolower(__NAMESPACE__) ) ) {
            return;
        }

        // format path to class
        $class_path = $this->plugin_path . 'includes/' . str_replace( __NAMESPACE__ . '\\', '', $class ) . '.php';

        // if file exists
        if( file_exists($class_path) ) {
            // require file
            require_once( $class_path );
        }

    }
}

//import shortcode
function import_weapon_json_shortcode() {
    return Weapon::import();
}
add_shortcode('import-weapons', 'import_weapon_json_shortcode');







// function get_sub_array_by_key( $value, $haystack, $key ) {

//     $needles = array();

//     $match_keys = array_keys(array_column( $haystack, $key), $value);

//     foreach( $match_keys as $match_key ) {
//         array_push( $needles, $haystack[$match_key] );
//     }

//     // while( array_search( $value, array_column( $haystack, $key) ) ) {
//     //     $index = array_search( $value, array_column( $haystack, $key) );
//     //     array_push( $needles, $haystack[$index] );
//     //     // unset( $haystack[$index] );
//     //     array_splice( $haystack, $index, 1 );
//     // }

//     return $needles;

// }
