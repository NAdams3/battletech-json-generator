<?php

namespace BTJG;

class Hardpoint {

    public static $table_name;
    public static $plugin_path;

    //location properties
    public $id;
    public $location_id;
    public $WeaponMount;
    public $Omni;

    public function __construct(
        int $location_id,
        string $WeaponMount,
        ?bool $Omni = false
    ) {
        //constructor
        $this->location_id = $location_id;
        $this->WeaponMount = $WeaponMount;
        $this->Omni = $Omni;
    }

    public static function init( $plugin_path, $table_prefix ) {
        global $wpdb;

        //set table_prefix;
        Hardpoint::$table_name = "{$wpdb->prefix}{$table_prefix}hardpoints";
        Hardpoint::$plugin_path = $plugin_path;

        // create table
        $wpdb->query("CREATE TABLE IF NOT EXISTS " . Hardpoint::$table_name . " (
            id INT AUTO_INCREMENT NOT NULL,
            location_id INT,
            WeaponMount VARCHAR(255),
            Omni BOOLEAN,
            PRIMARY KEY(id))");
    }

    public static function deactivate() {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS " . Hardpoint::$table_name . ";");
    }

    public function get_table_create_string() {
        global $wpdb;
        return "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}btjg_hardpoints (
            id INT AUTO_INCREMENT NOT NULL,
            location_id INT,
            WeaponMount VARCHAR(255),
            Omni BOOLEAN,
            PRIMARY KEY(id))";
    }

    public function get_table_drop_string() {
        global $wpdb;
        return "DROP TABLE IF EXISTS {$wpdb->prefix}btjg_hardpoints;";
    }

    public function exists() {
        global $wpdb;

        if( $this->id ) {
            $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}btjg_hardpoints WHERE id = {$this->id};");
            if( !empty($row) ) {
                return true;
            }
        }
        
        return false;
    }

    public function insert() {
        global $wpdb;

        return $wpdb->query("INSERT INTO {$wpdb->prefix}btjg_hardpoints(
            location_id,
            WeaponMount,
            Omni
        ) VALUES (" .
            "'{$this->location_id}'," . 
            "'{$this->WeaponMount}'," . 
            "'{$this->Omni}');"
        );

    }

    public function update() {
        //fix
        return true;
    }

    public function save() {
        if( $this->exists() ) {
            return $this->update();
        }
        return $this->insert();
    }

    public function format() {

    }

}
