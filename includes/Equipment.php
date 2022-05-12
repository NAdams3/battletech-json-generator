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

    public static function mold(): Equipment {
        return new Equipment(0.0, 0);
    }

    public function get_table_create_string() {
        global $wpdb;
        return "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}btjg_equipment (
            id VARCHAR(255) NOT NULL,
            tonnage DECIMAL(10,2),
            slots INT,
            PRIMARY KEY(id))";
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
