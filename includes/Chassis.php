<?php

class Chassis {
    
    public static $table_name;
    public static $plugin_path;

    //chassis properties
    public $Cost = 0; //Description->Cost 
    public $Rarity = 0; //Description->Rarity
    public $Purchasable = false; //Description->Purchasable
    public $Manufacturer = ""; //Description->Manufacturer
    public $Model = ""; //Description->Model
    public $UIName = ""; //Description->UIName
    public $Id; //Description->Id
    public $Name = ""; //Description->Name
    public $Details = ""; //Description->Details
    public $Icon = ""; //Description->Icon
    public $MovementCapDefID = ""; //MovementCapDefID
    public $PathingCapDefID = ""; //PathingCapDefID
    public $HardpointDataDefID = ""; //HardpointDataDefID
    public $PrefabIdentifier = ""; //PrefabIdentifier
    public $PrefabBase = ""; //PrefabBase
    public $Tonnage = 0; //Tonnage
    public $InitialTonnage = 0; //InitialTonnage
    public $weightClass = ""; //weightClass
    public $BattleValue = 0; //BattleValue
    public $Heatsinks = 0; //Heatsinks
    public $TopSpeed = 0; //TopSpeed
    public $TurnRadius = 0; //TurnRadius
    public $MaxJumpjets = 0; //MaxJumpjets
    public $Stability = 0; //Stability
    public $Locations = []; //Locations
    public $FixedEquipment;

    public function __construct( string $Id, $Tonnage, $InitialTonnage, $Locations, $FixedEquipment ) {
        //constructor
        $this->Id = $Id;
        $this->Tonnage = $Tonnage;
        $this->InitialTonnage = $InitialTonnage;
        $this->Locations = $Locations;
        $this->FixedEquipment = $FixedEquipment;
    }

    public static function init( $plugin_path, $table_prefix ) {

        //set table_prefix;
        Chassis::$table_name = "{$wpdb->prefix}{$table_prefix}chassis";
        Chassis::$plugin_path = $plugin_path;
    }

    public static function from_json_by_id( string $id ): Chassis {
        $chassis_file_path = WP_PLUGIN_DIR . "/battletech-json-generator/original-json/chassis/" . $id . ".json";
        $chassis_file_contents = file_get_contents( $chassis_file_path );
        return Chassis::from_json( $chassis_file_contents );
    }

    public static function from_json( string $json ): Chassis {
        $chassis = json_decode($json);
        return new Chassis( $chassis->Description->Id, $chassis->Tonnage, $chassis->InitialTonnage, $chassis->Locations, $chassis->FixedEquipment );
    }

    public function exists() {

    }

    public function insert() {

    }

    public function update() {

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

//import shortcode
function import_chassis_json_shortcode() {
    import_chassis_json();
}
add_shortcode('import-chassis', 'import_chassis_json_shortcode');

//export shortcode
function export_chassis_json_shortcode() {
    export_chassis_to_json();
}

add_shortcode('export-chassis', 'export_chassis_json_shortcode');

//import function
function import_chassis_json() {

    $json_path = WP_PLUGIN_DIR . "/battletech-json-generator/original-json/chassis";

    $json_files = new DirectoryIterator($json_path);

    ob_start(); 
    
    foreach( $json_files as $json_file ) {
        if($json_file->isDot()) continue;
        $file_path = WP_PLUGIN_DIR . "/battletech-json-generator/battletech-weapon-json/" . $json_file->getFilename();
        $file_contents = file_get_contents($file_path);
        // $success = save_weapon_json( json_decode($file_contents) );
        $chassis = new Chassis( json_decode($file_contents) );
        $success = $chassis->save();
        
    ?>

    <div>
        <p>Importing <?php echo $json_file->getFilename(); ?>... <?php if( $success != true ) { echo $success; } ?></p>
    </div>

    <?php

    }


    return ob_get_clean();
}