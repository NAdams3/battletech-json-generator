<?php

class Weapon {

    public static $json_path;

    public $HeatGenerated int;
    public $Damage int;
    public $HeatDamage int;
    public $AccuracyModifier int;
    public $CriticalChanceMultiplier int;
    public $RefireModifier int;
    public $ShotsWhenFired int;
    public $ProjectilesPerShot int;
    public $AttackRecoil int;
    public $Instability int;
    public $Cost int;
    public $Rarity int;
    public $BattleValue int;
    public $InventorySize int;
    public $Tonnage float;
    

    public function __construct(
        ?int $HeatGenerated = 0,
        ?int $Damage = 0,
        ?int $HeatDamage = 0,
        ?int $AccuracyModifier = 0,
        ?int $CriticalChanceMultiplier = 0,
        ?int $RefireModifier = 0,
        ?int $ShotsWhenFired = 0,
        ?int $ProjectilesPerShot = 0,
        ?int $AttackRecoil = 0,
        ?int $Instability = 0,
        ?int $Cost = 0,
        ?int $Rarity = 0,
        ?int $BattleValue = 0,
        ?int $InventorySize = 0,
        ?float $Tonnage = 0
    ) {
        $this->HeatGenerated = $HeatGenerated;
        $this->Damage = $Damage;
        $this->HeatDamage = $HeatDamage;
        $this->AccuracyModifier = $AccuracyModifier;
        $this->CriticalChanceMultiplier = $CriticalChanceMultiplier;
        $this->RefireModifier = $RefireModifier;
        $this->ShotsWhenFired = $ShotsWhenFired;
        $this->ProjectilesPerShot = $ProjectilesPerShot;
        $this->AttackRecoil = $AttackRecoil;
        $this->Instability = $Instability;
        $this->Cost = $Cost;
        $this->Rarity = $Rarity;
        $this->BattleValue = $BattleValue;
        $this->InventorySize = $InventorySize;
        $this->Tonnage = $Tonnage;
    }

    public static function init( $plugin_path, $table_prefix ) {
        WeaponSubType::init($table_prefix);
        WeaponVariation::init($table_prefix);
        $this->json_path = `{plugin_path}/original-json/weapon`;
    }

    public static function import() {

        $json_files = new DirectoryIterator($this->json_path);
        
        foreach( $json_files as $json_file ) {
            if($json_file->isDot()) {
                continue;
            }
            $file_path = `{$this->json_path}/{$json_file->getFilename()}`;
            $file_contents = file_get_contents($file_path);

            $sub_type = WeaponSubType::from_json( $file_contents );
            $sub_type->save();

            $variation = Weapon::from_json( $file_contents );
            $variation->save();
    
        }
    
    }

}


    
    

function insert_weapon_json( $variation ) {
    global $wpdb;

    $weaponSubType;
    if(str_contains($variation->Description->Id, "STOCK")) {
        //is WeaponSubType
        // $weaponSubType = $variation;
        insert_sub_type_with_variation( $variation );

    } else {
        //is not WeaponSubType
        //get WeaponSubType from db
        $weaponSubType = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}btjg_weapon_sub_types WHERE Name = '{$variation->WeaponSubType}';");
        if(empty($weaponSubType)) {
            //no WeaponSubType in db cannot insert
            print("no weapon sub type in db cannot insert: " . $variation->Description->Id);
            return;
        }

        insert_variation( $variation, $weaponSubType );

    }

}

function insert_variation( $variation, $weaponSubType ) {
    global $wpdb;

    $heatGeneratedDiff = $variation->HeatGenerated - $weaponSubType->HeatGenerated;
    $damageDiff = $variation->Damage - $weaponSubType->Damage;
    $heatDamageDiff = $variation->HeatDamage - $weaponSubType->HeatDamage;
    $accuracyModifierDiff = $variation->AccuracyModifier - $weaponSubType->AccuracyModifier;
    $criticalChanceMultiplierDiff = $variation->CriticalChanceMultiplier - $weaponSubType->CriticalChanceMultiplier;
    $refireModifierDiff = $variation->RefireModifier - $weaponSubType->RefireModifier;
    $shotsWhenFiredDiff = $variation->ShotsWhenFired - $weaponSubType->ShotsWhenFired;
    $projectilesPerShotDiff = $variation->ProjectilesPerShot - $weaponSubType->ProjectilesPerShot;
    $attackRecoilDiff = $variation->AttackRecoil - $weaponSubType->AttackRecoil;
    $instabilityDiff = $variation->Instability - $weaponSubType->Instability;
    $costDiff = $variation->Cost - $weaponSubType->Cost;
    $rarityDiff = $variation->Rarity - $weaponSubType->Rarity;
    $battleValueDiff = $variation->BattleValue - $weaponSubType->BattleValue;
    $inventorySizeDiff = $variation->InventorySize - $weaponSubType->InventorySize;
    $tonnageDiff = $variation->Tonnage - $weaponSubType->Tonnage;

    $wpdb->query("INSERT INTO {$wpdb->prefix}btjg_weapon_variations VALUES (" . 
    "'{$variation->WeaponSubType}'," . 
    "{$heatGeneratedDiff}," . 
    "{$damageDiff}," . 
    "{$heatDamageDiff}," . 
    "{$accuracyModifierDiff}," . 
    "{$criticalChanceMultiplierDiff}," . 
    "{$refireModifierDiff}," . 
    "{$shotsWhenFiredDiff}," . 
    "{$projectilesPerShotDiff}," . 
    "{$attackRecoilDiff}," . 
    "{$instabilityDiff}," . 
    "{$costDiff}," . 
    "{$rarityDiff}," . 
    "'{$variation->Description->Manufacturer}'," . 
    "'{$variation->Description->UIName}'," . 
    "'{$variation->Description->Id}'," . 
    "'{$variation->Description->Name}'," . 
    "'{$variation->BonusValueA}'," . 
    "'{$variation->BonusValueB}'," . 
    "{$battleValueDiff}," . 
    "{$inventorySizeDiff}," . 
    "{$tonnageDiff}," . 
    "'{$variation->ComponentTags->items[0]}'," . 
    "'{$variation->ComponentTags->items[1]}'," . 
    "'{$variation->ComponentTags->items[2]}');");
}

function insert_sub_type_with_variation( $variation ) {
    global $wpdb;

    //insert sub_type
    save_sub_type( $variation );

    //insert variation
    $wpdb->query("INSERT INTO {$wpdb->prefix}btjg_weapon_variations VALUES (" . 
    "'{$variation->WeaponSubType}'," . 
    "0," . 
    "0," . 
    "0," . 
    "0," . 
    "0," . 
    "0," . 
    "0," . 
    "0," . 
    "0," . 
    "0," . 
    "0," . 
    "0," . 
    "'{$variation->Description->Manufacturer}'," . 
    "'{$variation->Description->UIName}'," . 
    "'{$variation->Description->Id}'," . 
    "'{$variation->Description->Name}'," . 
    "'{$variation->BonusValueA}'," . 
    "'{$variation->BonusValueB}'," . 
    "0," . 
    "0," . 
    "0," . 
    "'{$variation->ComponentTags->items[0]}'," . 
    "'{$variation->ComponentTags->items[1]}'," . 
    "'{$variation->ComponentTags->items[2]}');");


}





function read_json_shortcode() {

    $json_path = WP_PLUGIN_DIR . "/battletech-json-generator/battletech-weapon-json";

    $json_dir = new DirectoryIterator($json_path);

    

    // $my_file_path = WP_PLUGIN_DIR . "/battletech-json-generator/battletech-weapon-samples/Weapon_Autocannon_AC2_0-STOCK.json";
    // $my_file_path = WP_PLUGIN_DIR . "/battletech-json-generator/battletech-weapon-samples/Weapon_Laser_LargeLaser_2-Diverse_Optics.json";

    // $my_file = fopen($my_file_path, "r") or die("Unable to open file.");

    // print_r($my_file);

    // $my_contents = file_get_contents($my_file_path);

    // $my_object = json_decode($my_contents, false);
    // print_r($my_object);


    ob_start(); 
    
    foreach( $json_dir as $json_file ) {
        if($json_file->isDot()) continue; ?>

    <div>
        <?php //echo fread($my_file,filesize($my_file_path)); ?>
        <?php //echo "here"; ?>
        <?php echo $json_file->getFilename(); ?>
    </div>

    <?php

        $file_path = WP_PLUGIN_DIR . "/battletech-json-generator/battletech-weapon-json/" . $json_file->getFilename();
        $file_contents = file_get_contents($file_path);
        save_weapon_json( json_decode($file_contents) );


    }


    return ob_get_clean();
}

add_shortcode('read-json', 'read_json_shortcode');

//weapon shortcodes
function import_weapon_json_shortcode() {
    import_weapon_json();
}
add_shortcode('import-weapons', 'import_weapon_json_shortcode');

function export_weapon_json_shortcode() {
    export_weapons_to_json();
}

add_shortcode('export-weapons', 'export_weapon_json_shortcode');


function export_to_json() {

    $variations = get_variations();
    $sub_types = get_sub_types();

    foreach( $variations as $variation ) {
        $sub_type_index = array_search($variation->WeaponSubType, array_column($sub_types, 'Name'));
        $formatted_variation = format_variation( $variation, $sub_types[$sub_type_index] );
        //json_encode
        //write file
        file_put_contents(WP_PLUGIN_DIR . "/battletech-json-generator/battletech-json-output/" . $variation->Id . ".json", json_encode($formatted_variation));
    }

}

function format_variation( $variation, $sub_type ) {

    $componentTypeTag = 'component_type_stock';
    if( !str_contains($variation->ID, 'STOCK') ) {
        $componentTypeTag = 'component_type_variant';
    }

    $componentRangeTag = 'range_standard';
    if( str_contains($variation->ComponentTags2, 'range') ) {
        $componentRangeTag = $variation->ComponentTags2;
    }
    if( str_contains($variation->ComponentTags3, 'range') ) {
        $componentRangeTag = $variation->ComponentTags3;
    }

    return (object) array(
        'MinRange' => (int) $sub_type->MinRange,
        'MaxRange' => (int) $sub_type->MaxRange,
        'RangeSplit' => array(
            (int) $sub_type->RangeSplit1,
            (int) $sub_type->RangeSplit2,
            (int) $sub_type->RangeSplit3
        ),
        'HeatGenerated' => $sub_type->HeatGenerated + $variation->HeatGenerated,
        'Damage' => $sub_type->Damage + $variation->Damage,
        // 'HeatDamage' => $sub_type->HeatDamage + $variation->HeatDamage,
        'ShotsWhenFired' => (int) $sub_type->ShotsWhenFired,
        'ProjectilesPerShot' => (int) $sub_type->ProjectilesPerShot,
        // 'Instability' => $sub_type->Instability + $variation->Instability,
        'Description' => (object) array(
            'Rarity' => 0,
            'Purchasable' => true,
        ),
        // 'InventorySize' => $sub_type->InventorySize + $variation->InventorySize,
        // 'Tonnage' => $sub_type->Tonnage + $variation->Tonnage,
        'ComponentTags' => (object) array(
            'items' => array(
                $componentTypeTag,
                $componentRangeTag,
            ),
        ),
    );

}

function get_variation() {

}

function get_variations() {
    global $wpdb;

    return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}btjg_weapon_variations;");

}

function get_sub_types() {
    global $wpdb;

    return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}btjg_weapon_sub_types;");
}