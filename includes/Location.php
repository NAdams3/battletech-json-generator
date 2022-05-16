<?php

namespace BTJG;

class Location {

    public static $plugin_path;
    public static $table_name;

    //location properties
    public $id = 0; //id
    public $mech_id = ""; //$mech->Id
    public $Location = 0; //Location
    public $DamageLevel;
    public $CurrentArmor;
    public $CurrentRearArmor;
    public $CurrentInternalStructure;
    public $AssignedArmor;
    public $AssignedRearArmor;
    public $Hardpoints;
    public $InventorySlots;
    public $MaxArmor;
    public $MaxRearArmor;
    public $Inventory;  

    public function __construct(
        string $mech_id,
        string $Location,
        ?string $DamageLevel = "",
        ?int $CurrentArmor = 0,
        ?int $CurrentRearArmor = 0,
        ?int $CurrentInternalStructure = 0,
        ?int $AssignedArmor = 0,
        ?int $AssignedRearArmor = 0,
        ?array $Hardpoints = array(),
        ?int $InventorySlots = 0,
        ?int $MaxArmor = 0,
        ?int $MaxRearArmor = 0,
        ?array $Inventory = array() 
    ) {
        //constructor
        $this->mech_id = $mech_id;
        $this->Location = $Location;
        $this->DamageLevel = $DamageLevel;
        $this->CurrentArmor = $CurrentArmor;
        $this->CurrentRearArmor = $CurrentRearArmor;
        $this->CurrentInternalStructure = $CurrentInternalStructure;
        $this->AssignedArmor = $AssignedArmor;
        $this->AssignedRearArmor = $AssignedRearArmor;
        $this->Hardpoints = $Hardpoints;
        $this->InventorySlots = $InventorySlots;
        $this->MaxArmor = $MaxArmor;
        $this->MaxRearArmor = $MaxRearArmor;
        $this->Inventory = $Inventory;  
    }

    public static function init( $plugin_path, $table_prefix ) {
        global $wpdb;

        //set table_prefix;
        Location::$table_name = "{$wpdb->prefix}{$table_prefix}locations";
        Location::$plugin_path = $plugin_path;

        // create table
        $wpdb->query("CREATE TABLE IF NOT EXISTS " . Location::$table_name . " (
            id INT AUTO_INCREMENT NOT NULL,
            mech_id VARCHAR(255),
            Location VARCHAR(255),
            DamageLevel VARCHAR(255),
            CurrentArmor INT,
            CurrentRearArmor INT,
            CurrentInternalStructure INT,
            AssignedArmor INT,
            AssignedRearArmor INT,
            InventorySlots INT,
            MaxArmor INT,
            MaxRearArmor INT,
            PRIMARY KEY(id))");
    }

    public static function deactivate() {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS " . Location::$table_name . ";");
    }

    public static function from_db( $db_object ): Location {
        $location = new Location(
            $db_object->mech_id,
            $db_object->Location,
            $db_object->DamageLevel,
            $db_object->CurrentArmor,
            $db_object->CurrentRearArmor,
            $db_object->CurrentInternalStructure,
            $db_object->AssignedArmor,
            $db_object->AssignedRearArmor,
            null,
            $db_object->InventorySlots,
            $db_object->MaxArmor,
            $db_object->MaxRearArmor
        );
        $location->id = $db_object->id;
        return $location;
    }

    public function get_table_create_string() {
        global $wpdb;
        return "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}btjg_locations (
            id INT AUTO_INCREMENT NOT NULL,
            mech_id VARCHAR(255),
            Location VARCHAR(255),
            DamageLevel VARCHAR(255),
            CurrentArmor INT,
            CurrentRearArmor INT,
            CurrentInternalStructure INT,
            AssignedArmor INT,
            AssignedRearArmor INT,
            InventorySlots INT,
            MaxArmor INT,
            MaxRearArmor INT,
            PRIMARY KEY(id))";
    }

    public function get_table_drop_string() {
        global $wpdb;
        return "DROP TABLE IF EXISTS {$wpdb->prefix}btjg_locations;";
    }

    public function exists() {
        global $wpdb;
        // $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}btjg_locations WHERE id = {$this->id};");
        // if( !empty($row) ) {
        //     return true;
        // }
        return false;
    }

    public function insert() {
        global $wpdb;

        $success = $wpdb->query("INSERT INTO {$wpdb->prefix}btjg_locations(
            mech_id,
            Location,
            DamageLevel,
            CurrentArmor,
            CurrentRearArmor,
            CurrentInternalStructure,
            AssignedArmor,
            AssignedRearArmor,
            InventorySlots,
            MaxArmor,
            MaxRearArmor
        ) VALUES (" .
            "'{$this->mech_id}'," . 
            "'{$this->Location}'," . 
            "'{$this->DamageLevel}'," .
            "'{$this->CurrentArmor}'," . 
            "'{$this->CurrentRearArmor}'," .  
            "'{$this->CurrentInternalStructure}'," . 
            "'{$this->AssignedArmor}'," . 
            "'{$this->AssignedRearArmor}'," . 
            "'{$this->InventorySlots}'," . 
            "'{$this->MaxArmor}'," . 
            "'{$this->MaxRearArmor}');"
        );

        if( !$success ) {
            return "Unable to insert " . $this->Location . " for " . $this->mech_id;
        }

        $location_id = $wpdb->insert_id;

        //handle hardpoints
        if( $this->Hardpoints ) {
            foreach( $this->Hardpoints as $hp) {
                $hardpoint = new Hardpoint( $location_id, $hp->WeaponMount, $hp->Omni );
                $success = $hardpoint->save();
                if( $success != true ) {
                    return "Unable to save hardpoint for location id: {$location_id} for {$this->mech_id} : {$success}";
                }
            }
        }

        //handle fixed equipment
        // array_push( $this->Inventory, $this->FixedEquipment );

        //handle inventory
        if( $this->Inventory ) {
            foreach( $this->Inventory as $inventory ) {
                $item = new LocationItem(
                    $location_id,
                    $inventory->ComponentDefID,
                    $inventory->SimGameUID,
                    $inventory->ComponentDefType,
                    $inventory->HardpointSlot,
                    $inventory->GUID,
                    $inventory->DamageLevel,
                    $inventory->prefabName,
                    $inventory->hasPrefabName,
                    $inventory->IsFixed
                );
                $success = $item->save();
                if( $success != true ) {
                    return "Unable to save {$inventory->ComponentDefID} for {$location_id} : {$success}";
                }
            }
        }

        return true;

    }

    public function update() {
        //fix
        return "Updating " . $this->location . " for " . $this->mech_id;
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
