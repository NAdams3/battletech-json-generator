<?php

namespace BTJG;

class Equipment {

    const TABLE_NAME = 'btjg_equipment';

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

    public static function init() {
        global $wpdb;

        // create table
        $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}" . self::TABLE_NAME . " (
            id VARCHAR(255) NOT NULL,
            tonnage DECIMAL(10,2),
            slots INT,
            PRIMARY KEY(id))");
    }

    public static function deactivate() {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}" . self::TABLE_NAME);
    }

    public function exists() {
        global $wpdb;

        if( $this->id ) {
            $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}" . self::TABLE_NAME . " WHERE id = {$this->id};");
            if( !empty($row) ) {
                return true;
            }
        }
        
        return false;
    }

    public function insert() {
        global $wpdb;

        return $wpdb->query("INSERT INTO {$wpdb->prefix}" . self::TABLE_NAME . " (
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
