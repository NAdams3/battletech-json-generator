<?php

namespace BTJG;

class Mech {

    public static $table_name;
    public static $plugin_path;

    //mech properties
    public $Id; //Description->Id
    public $ChassisID; //ChassisID
    public $Locations;
    public $inventory;
    public $Tonnage = 0; //$chassis->Tonnage
    public $InitialTonnage = 0; //$chassis->InitialTonnage

    public function __construct(
        string $Id,
        ?string $ChassisID = "",
        ?array $Locations = array(),
        ?array $inventory = array(),
        ?int $Tonnage = 0,
        ?float $InitialTonnage = 0
    ) {
        //constructor
        $this->Id = $Id;
        $this->ChassisID = $ChassisID;
        $this->Locations = $Locations;
        $this->inventory = $inventory;
        $this->Tonnage = $Tonnage;
        $this->InitialTonnage = $InitialTonnage;
    }

    public static function init( $plugin_path, $table_prefix ) {
        global $wpdb;

        //set table_prefix;
        Mech::$table_name = "{$wpdb->prefix}{$table_prefix}mechs";
        Mech::$plugin_path = $plugin_path;

        // create table
        $wpdb->query("CREATE TABLE IF NOT EXISTS " . Mech::$table_name . " (
            Id VARCHAR(255),
            ChassisID VARCHAR(255),
            Tonnage INT,
            InitialTonnage DECIMAL(10,2),
            PRIMARY KEY(Id))");
    }

    public static function deactivate() {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS " . Mech::$table_name . ";");
    }

    public static function from_json( string $json ): Mech {
        $mech = json_decode($json);
        return new Mech( $mech->Description->Id, $mech->ChassisID, $mech->Locations, $mech->inventory );
    }

    public static function from_db( $obj ): Mech {
        return new Mech(
            $obj->Id,
            $obj->ChassisID,
            null,
            null,
            $obj->Tonnage,
            $obj->InitialTonnage
        );
    }

    public function get_table_create_string() {
        global $wpdb;
        return "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}btjg_mechs (
            Id VARCHAR(255),
            ChassisID VARCHAR(255),
            Tonnage INT,
            InitialTonnage DECIMAL(10,2),
            PRIMARY KEY(Id))";
    }

    public function get_table_drop_string() {
        global $wpdb;
        return "DROP TABLE IF EXISTS {$wpdb->prefix}btjg_mechs;";
    }

    public function exists() {
        global $wpdb;
        // $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}btjg_mechs WHERE Id = '{$this->Id}';");
        // if( !empty($row) ) {
        //     return true;
        // }
        return false;
    }

    public function insert() {
        global $wpdb;

        if( $this->ChassisID == null ) {
            return "Failed: no chassis id.";
        }

        $chassis = Chassis::from_json_by_id($this->ChassisID);

        foreach( $this->Locations as $location ) {
            //get inventory
            $location_inventory = array();
            if( $this->inventory ) {
                $location_inventory = get_sub_array_by_key( $location->Location,  $this->inventory, 'MountedLocation' );
            }

            //get chassis location info
            $chassis_location_index = array_search($location->Location, array_column($chassis->Locations, 'Location'));
            $chassis_location = $chassis->Locations[$chassis_location_index];

            //get fixed equipment
            if( $chassis->FixedEquipment ) {
                $chassis_fixed_equipment_index = array_search($location->Location, array_column($chassis->FixedEquipment, 'MountedLocation'));
                if( $chassis_fixed_equipment_index !== false ) {
                    array_push($location_inventory, $chassis->FixedEquipment[$chassis_fixed_equipment_index]);
                }
            }

            //save
            $location = new Location(
                $this->Id,
                $location->Location,
                $location->DamageLevel,
                $location->CurrentArmor,
                $location->CurrentRearArmor,
                $location->CurrentInternalStructure,
                $location->AssignedArmor,
                $location->AssignedRearArmor,
                $chassis_location->Hardpoints,
                $chassis_location->InventorySlots,
                $chassis_location->MaxArmor,
                $chassis_location->MaxRearArmor,
                $location_inventory    
            );

            $success = $location->save();

            if( $success != true ) {
                return "Unable to save {$location->Location} for {$this->Id} : {$success}";
            }
        }

        return $wpdb->query("INSERT INTO {$wpdb->prefix}btjg_mechs VALUES (" .
            "'{$this->Id}'," . 
            "'{$this->ChassisID}'," . 
            "'{$chassis->Tonnage}'," . 
            "'{$chassis->InitialTonnage}');"
        );

    }

    public function update() {
        //fix
        return "this has already been inserted";
    }

    public function save() {
        if( $this->exists() ) {
            return $this->update();
        }
        return $this->insert();
    }

    public function is_valid() {
        return $this->is_hardpoints_valid() && $this->is_slots_valid() && $this->is_tonnage_valid();
    }

    public function is_hardpoints_valid() {
        return true;
    }

    public function is_slots_valid() {
        return true;
    }

    public function is_tonnage_valid() {
        //tonnage available
        $tonnage_available = $this->get_available_tonnage();

        //armor tonnage
        $armor_tonnage = $this->get_armor_tonnage();

        //equipment tonnage
        $equipment_tonnage = $this->get_equipment_tonnage();

        //weapon tonnnage
        $weapon_tonnage = $this->get_weapon_tonnage();

        return false;
    }

    public function get_available_tonnage() {
        return $this->Tonnage - $this->InitialTonnage;
    }

    public function get_locations() {
        global $wpdb;

        $locations = array();

        $results = $wpdb->get_rows("SELECT * 
            FROM {$wpdb->prefix}btjg_locations
            WHERE mech_id = {$this->Id};"
        );

        foreach( $results as $result ) {
            array_push( $locations, Location::from_db($result) );
        }

        return $locations;

    }

    public function get_armor_tonnage() {
        global $wpdb;

        return $wpdb->get_var("SELECT
                (SUM({$wpdb->prefix}btjg_locations.AssignedArmor) + SUM(greatest({$wpdb->prefix}btjg_locations.AssignedRearArmor, 0))) / 80
            FROM {$wpdb->prefix}btjg_mechs
            LEFT JOIN {$wpdb->prefix}btjg_locations ON {$wpdb->prefix}btjg_locations.mech_id = {$wpdb->prefix}btjg_mechs.Id
            WHERE {$wpdb->prefix}btjg_mechs.Id = '{$this->Id}'
            GROUP BY {$wpdb->prefix}btjg_mechs.Id;"
        );
    }

    public function get_equipment_tonnage() {
        global $wpdb;

        return $wpdb->get_var("SELECT
                SUM({$wpdb->prefix}btjg_equipment.tonnage)
            FROM {$wpdb->prefix}btjg_mechs
            LEFT JOIN {$wpdb->prefix}btjg_locations ON {$wpdb->prefix}btjg_locations.mech_id = {$wpdb->prefix}btjg_mechs.Id
            LEFT JOIN {$wpdb->prefix}btjg_location_items ON {$wpdb->prefix}btjg_locations.id = {$wpdb->prefix}btjg_location_items.location_id 
            LEFT JOIN {$wpdb->prefix}btjg_equipment ON {$wpdb->prefix}btjg_equipment.id = {$wpdb->prefix}btjg_location_items.ComponentDefID
            WHERE {$wpdb->prefix}btjg_mechs.Id = '{$this->Id}'
            GROUP BY {$wpdb->prefix}btjg_mechs.Id;"
        );
    }

    public function get_weapon_tonnage() {
        global $wpdb;

        return $wpdb->get_var("SELECT
                SUM({$wpdb->prefix}btjg_weapon_variations.Tonnage) + SUM({$wpdb->prefix}btjg_weapon_sub_types.Tonnage)
            FROM {$wpdb->prefix}btjg_mechs
            LEFT JOIN {$wpdb->prefix}btjg_locations ON {$wpdb->prefix}btjg_locations.mech_id = {$wpdb->prefix}btjg_mechs.Id
            LEFT JOIN {$wpdb->prefix}btjg_location_items ON {$wpdb->prefix}btjg_locations.id = {$wpdb->prefix}btjg_location_items.location_id 
            LEFT JOIN {$wpdb->prefix}btjg_weapon_variations ON {$wpdb->prefix}btjg_weapon_variations.Id = {$wpdb->prefix}btjg_location_items.ComponentDefID
            LEFT JOIN {$wpdb->prefix}btjg_weapon_sub_types ON {$wpdb->prefix}btjg_weapon_sub_types.Name = {$wpdb->prefix}btjg_weapon_variations.WeaponSubType
            WHERE {$wpdb->prefix}btjg_mechs.Id = '{$this->Id}'
            GROUP BY {$wpdb->prefix}btjg_mechs.Id;"
        );
    }

    public function format() {

    }

}

//import shortcode
function import_mech_json_shortcode() {
    return import_mech_json();
}
add_shortcode('import-mechs', 'import_mech_json_shortcode');

//export shortcode
function export_mech_json_shortcode() {
    return export_mechs_to_json();
}

add_shortcode('export-mechs', 'export_mech_json_shortcode');

//import function
function import_mech_json() {

    $json_path = WP_PLUGIN_DIR . "/battletech-json-generator/original-json/mech";

    $json_files = new DirectoryIterator($json_path);

    ob_start(); 
    
    foreach( $json_files as $json_file ) {
        if($json_file->isDot()) continue; ?>

    <div>
        <p>Importing <?php echo $json_file->getFilename(); ?>...


    <?php
        $file_path = $json_path . "/" . $json_file->getFilename();
        $file_contents = file_get_contents($file_path);
        // $success = save_weapon_json( json_decode($file_contents) );
        $mech = Mech::from_json( $file_contents );
        // $chassis_file_path = WP_PLUGIN_DIR . "/battletech-json-generator/original-json/chassis/" . $mech->ChassisID . ".json";
        // $chassis_file_contents = file_get_contents( $chassis_file_path );
        // $chassis = new Chassis( json_decode($chassis_file_contents) );
        $success = $mech->save();
        
    ?>

        <?php echo $success; ?></p>
    </div>

    <?php

    }


    return ob_get_clean();
}

//list mech tonnage shortcode
function list_mech_tonnage_shortcode() {
    return list_mech_tonnage();
}
add_shortcode('list-mech-tonnage', 'list_mech_tonnage_shortcode');

function list_mech_tonnage() {
    global $wpdb;

    $mechs =  $wpdb->get_results("SELECT * 
        FROM {$wpdb->prefix}btjg_mechs;");

    ob_start();?>

    <table>
        <tr>
            <th>ID</th>
            <th>Tonnage</th>
            <th>Initial Tonnage</th>
            <th>Available Tonnage</th>
            <th>Armor Tonnage</th>
            <th>Equipment Tonnage</th>
            <th>Weapon Tonnage</th>
            <th>Remaining Tonnage</th>
        </tr>

    <?php

    foreach( $mechs as $obj ) {
        $mech = Mech::from_db($obj);
        $available_tonnage = $mech->get_available_tonnage();
        $armor_tonnage = $mech->get_armor_tonnage();
        $equipment_tonnage = $mech->get_equipment_tonnage();
        $weapon_tonnage = $mech->get_weapon_tonnage();
        $remaining_tonnage = $available_tonnage - $armor_tonnage - $equipment_tonnage - $weapon_tonnage;

        ?>

        <tr>
            <td><?php echo $mech->Id; ?></td>
            <td><?php echo $mech->Tonnage; ?></td>
            <td><?php echo $mech->InitialTonnage; ?></td>
            <td><?php echo $available_tonnage; ?></td>
            <td><?php echo $armor_tonnage; ?></td>
            <td><?php echo $equipment_tonnage; ?></td>
            <td><?php echo $weapon_tonnage; ?></td>
            <td><?php echo $remaining_tonnage; ?></td>
        </tr>


        <?php

    }

    ?>
    </table>
    <?php

    return ob_get_clean();


}
