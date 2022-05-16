<?php

namespace BTJG;

class LocationItem {

    public static $table_name;
    public static $plugin_path;

    //location properties
    public $id;
    public $location_id;
    public $ComponentDefID;
    public $SimGameUID;
    public $ComponentDefType;
    public $HardpointSlot;
    public $GUID;
    public $DamageLevel;
    public $prefabName;
    public $hasPrefabName;
    public $IsFixed;

    public function __construct(
        int $location_id,
        string $ComponentDefID,
        ?string $SimGameUID = null,
        ?string $ComponentDefType = "",
        ?int $HardpointSlot = 0,
        ?string $GUID = null,
        ?string $DamageLevel = "",
        ?string $prefabName = null,
        ?bool $hasPrefabName = false,
        ?bool $IsFixed = false
    ) {
        //constructor
        $this->location_id = $location_id;
        $this->ComponentDefID = $ComponentDefID;
        $this->SimGameUID = $SimGameUID;
        $this->ComponentDefType = $ComponentDefType;
        $this->HardpointSlot = $HardpointSlot;
        $this->GUID = $GUID;
        $this->DamageLevel = $DamageLevel;
        $this->prefabName = $prefabName;
        $this->hasPrefabName = $hasPrefabName;
        $this->IsFixed = $IsFixed;
    }

    public static function init( $plugin_path, $table_prefix ) {
        global $wpdb;

        //set table_prefix;
        LocationItem::$table_name = "{$wpdb->prefix}{$table_prefix}location_items";
        LocationItem::$plugin_path = $plugin_path;

        // create table
        $wpdb->query("CREATE TABLE IF NOT EXISTS " . LocationItem::$table_name . " (
            id INT AUTO_INCREMENT NOT NULL,
            location_id INT,
            ComponentDefID VARCHAR(255),
            SimGameUID VARCHAR(255),
            ComponentDefType VARCHAR(255),
            HardpointSlot INT,
            GUID VARCHAR(255),
            DamageLevel VARCHAR(255),
            prefabName VARCHAR(255),
            hasPrefabName BOOLEAN,
            IsFixed BOOLEAN,
            PRIMARY KEY(id))");
    }

    public static function deactivate() {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS " . LocationItem::$table_name . ";");
    }

    public static function from_object( $obj ): LocationItem {
        return new LocationItem(
            $obj->location_id,
            $obj->ComponentDefID,
            $obj->SimGameUID,
            $obj->ComponentDefType,
            $obj->HardpointSlot,
            $obj->GUID,
            $obj->DamageLevel,
            $obj->prefabName,
            $obj->hasPrefabName,
            $obj->IsFixed
        );
    }

    public function get_table_create_string() {
        global $wpdb;
        return "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}btjg_location_items (
            id INT AUTO_INCREMENT NOT NULL,
            location_id INT,
            ComponentDefID VARCHAR(255),
            SimGameUID VARCHAR(255),
            ComponentDefType VARCHAR(255),
            HardpointSlot INT,
            GUID VARCHAR(255),
            DamageLevel VARCHAR(255),
            prefabName VARCHAR(255),
            hasPrefabName BOOLEAN,
            IsFixed BOOLEAN,
            PRIMARY KEY(id))";
    }

    public function get_table_drop_string() {
        global $wpdb;
        return "DROP TABLE IF EXISTS {$wpdb->prefix}btjg_location_items;";
    }

    public function exists() {
        global $wpdb;

        if( $this->id ) {
            $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}btjg_location_items WHERE id = {$this->id};");
            if( !empty($row) ) {
                return true;
            }
        }

        return false;
    }

    public function insert() {
        global $wpdb;

        return $wpdb->query("INSERT INTO {$wpdb->prefix}btjg_location_items(
            location_id,
            ComponentDefID,
            SimGameUID,
            ComponentDefType,
            HardpointSlot,
            GUID,
            DamageLevel,
            prefabName,
            hasPrefabName,
            IsFixed
        ) VALUES (" .
            "'{$this->location_id}'," . 
            "'{$this->ComponentDefID}'," . 
            "'{$this->SimGameUID}'," . 
            "'{$this->ComponentDefType}'," . 
            "'{$this->HardpointSlot}'," . 
            "'{$this->GUID}'," . 
            "'{$this->DamageLevel}'," . 
            "'{$this->prefabName}'," . 
            "'{$this->hasPrefabName}'," . 
            "'{$this->IsFixed}');"
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
