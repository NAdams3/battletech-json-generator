<?php

class Equipment {

    //location properties
    public $id;
    public $tonnage;
    public $slots;

    public function __construct(
        float $tonnage,
        int $slots
    ) {
        //constructor
        $this->tonnage = $tonnage;
        $this->slots = $slots;
    }

    public static function init( $plugin_path, $table_prefix ) {
        global $wpdb;

        //set table_prefix;
        Equipment::$table_name = `{$wpdb->prefix}{$table_prefix}equipment`;
        Equipment::$plugin_path = $plugin_path;

        // create table
        $wpdb->query("CREATE TABLE IF NOT EXISTS {Equipment::$table_name} (
            id VARCHAR(255) NOT NULL,
            tonnage DECIMAL(10,2),
            slots INT,
            PRIMARY KEY(id))");
    }

    public static function deactivate() {
        global $wpdb;

        $wpdb->query(`DROP TABLE IF EXISTS {Equipment::$table_name};`);
    }

    public function get_table_drop_string() {
        global $wpdb;
        return "DROP TABLE IF EXISTS {$wpdb->prefix}btjg_equipment;";
    }

    public function exists() {
        global $wpdb;

        if( $this->id ) {
            $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}btjg_equipment WHERE id = {$this->id};");
            if( !empty($row) ) {
                return true;
            }
        }
        
        return false;
    }

    public function insert() {
        global $wpdb;

        return $wpdb->query("INSERT INTO {$wpdb->prefix}btjg_equipment(
            tonnage,
            slots
        ) VALUES (" .
            "'{$this->tonnage}'," . 
            "'{$this->slots}');"
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
