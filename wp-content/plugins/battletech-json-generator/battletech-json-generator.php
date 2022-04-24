<?php
/*
Plugin Name: BattleTech JSON Generator
Plugin URI: www.bjg.com
Description: Should make JSON for BattleTech game when finished.
Version: 0.0.1
Author: Nick Adams
Author URI: www.bjg.com
License: GPLv2 or later
Text Domain: battletech-json-generator
*/

function btjg_activate () {
    global $wpdb;

    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}btjg_weapon_sub_types (
        Category VARCHAR(255),
        Type VARCHAR(255),
        Name VARCHAR(255), /*WeaponSubType VARCHAR(255),*/
        MinRange INT,
        MaxRange INT,
        RangeSplit1 INT,
        RangeSplit2 INT,
        RangeSplit3 INT,
        ammoCategoryID VARCHAR(255),
        StartingAmmoCapacity INT,
        HeatGenerated INT,
        Damage INT,
        OverheatedDamageMultiplier INT,
        EvasiveDamageMultiplier INT,
        EvasivePipsIgnored INT,
        DamageVariance INT,
        HeatDamage INT,
        AccuracyModifier INT,
        CriticalChanceMultiplier INT,
        AOECapable BOOLEAN,
        IndirectFireCapable BOOLEAN,
        RefireModifier INT,
        ShotsWhenFired INT,
        ProjectilesPerShot INT,
        AttackRecoil INT,
        Instability INT,
        WeaponEffectID VARCHAR(255),
        Cost INT,
        Rarity INT,
        Purchasable BOOLEAN,
        Model VARCHAR(255),
        Details LONGTEXT,
        Icon VARCHAR(255),
        ComponentType VARCHAR(255),
        ComponentSubType VARCHAR(255),
        PrefabIdentifier VARCHAR(255),
        BattleValue INT,
        InventorySize INT,
        Tonnage DECIMAL(10, 1),
        AllowedLocations VARCHAR(255),
        DisallowedLocations VARCHAR(255),
        CriticalComponent BOOLEAN,
        statusEffects1 VARCHAR(255),
        tagSetSourceFile VARCHAR(255)
    );");
    
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}btjg_weapon_variations (
        WeaponSubType VARCHAR(255),
        HeatGenerated INT,
        Damage INT,
        HeatDamage INT,
        AccuracyModifier INT,
        CriticalChanceMultiplier INT,RefireModifier INT,
        ShotsWhenFired INT,
        ProjectilesPerShot INT,
        AttackRecoil INT,
        Instability INT,
        Cost INT,
        Rarity INT,
        Manufacturer VARCHAR(255),
        UIName VARCHAR(255),
        Id VARCHAR(255),
        Name VARCHAR(255),
        BonusValueA VARCHAR(255),
        BonusValueB VARCHAR(255),
        BattleValue INT,
        InventorySize INT,
        Tonnage DECIMAL(10, 1),
        ComponentTags1 VARCHAR(255),
        ComponentTags2 VARCHAR(255),
        ComponentTags3 VARCHAR(255)
    );");
}

register_activation_hook(__FILE__, "btjg_activate");

function save_weapon_json( $variation ) {
    global $wpdb;

    $dbVariation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}btjg_weapon_variations WHERE Id = '{$variation->Description->Id}';");
    if( !empty($dbVariation) ) {
        print("variation exists");
        return;
    }
    insert_weapon_json($variation);
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

function save_sub_type( $sub_type ) {
    global $wpdb;
    
    $db_sub_type = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}btjg_weapon_sub_types WHERE Name = '{$sub_type->WeaponSubType}';");
    if( !empty($db_sub_type) ) {
        print("sub type exists");
        return;
    }
    insert_sub_type($sub_type);
}

function insert_sub_type( $sub_type ) {
    global $wpdb;

    print_r($sub_type);

    $description = $sub_type->Description;
    $isAOECapable = $sub_type->AOECapable ?: 0;
    $isIndirectFireCapable = $sub_type->IndirectFireCapable ?: 0;
    $isPurchasable = $description->Purchasable ?: 0;
    $isCriticalComponent = $sub_type->CriticalComponent ?: 0;

    $wpdb->query("INSERT INTO {$wpdb->prefix}btjg_weapon_sub_types VALUES (" .
    "'{$sub_type->Category}'," .
    "'{$sub_type->Type}'," .
    "'{$sub_type->WeaponSubType}'," .
    "{$sub_type->MinRange}," . 
    "{$sub_type->MaxRange}," . 
    "{$sub_type->RangeSplit[0]}," . 
    "{$sub_type->RangeSplit[1]}," . 
    "{$sub_type->RangeSplit[2]}," . 
    "'{$sub_type->ammoCategoryID}'," . 
    "{$sub_type->StartingAmmoCapacity}," . 
    "{$sub_type->HeatGenerated}," . 
    "{$sub_type->Damage}," . 
    "{$sub_type->OverheatedDamageMultiplier}," . 
    "{$sub_type->EvasiveDamageMultiplier}," . 
    "{$sub_type->EvasivePipsIgnored}," . 
    "{$sub_type->DamageVariance}," . 
    "{$sub_type->HeatDamage}," . 
    "{$sub_type->AccuracyModifier}," . 
    "{$sub_type->CriticalChanceMultiplier}," . 
    "{$isAOECapable}," . 
    "{$isIndirectFireCapable}," . 
    "{$sub_type->RefireModifier}," . 
    "{$sub_type->ShotsWhenFired}," .
    "{$sub_type->ProjectilesPerShot}," . 
    "{$sub_type->AttackRecoil}," . 
    "{$sub_type->Instability}," . 
    "'{$sub_type->WeaponEffectID}'," . 
    "{$sub_type->Description->Cost}," . 
    "{$sub_type->Description->Rarity}," . 
    "{$isPurchasable}," . 
    "'{$sub_type->Description->Model}'," . 
    "'{$sub_type->Description->Details}'," . 
    "'{$sub_type->Description->Icon}',"  . 
    "'{$sub_type->ComponentType}'," . 
    "'{$sub_type->ComponentSubType}'," . 
    "'{$sub_type->PrefabIdentifier}'," . 
    "{$sub_type->BattleValue}," . 
    "{$sub_type->InventorySize}," . 
    "{$sub_type->Tonnage}," . 
    "'{$sub_type->AllowedLocations}'," . 
    "'{$sub_type->DisallowedLocations}'," . 
    "{$isCriticalComponent}," . 
    "'{$sub_type->statusEffects[0]}'," . 
    "'{$sub_type->ComponentTags->tagSetSourceFile}')");
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
