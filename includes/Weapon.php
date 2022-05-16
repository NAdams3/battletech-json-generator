<?php
namespace BTJG;

class Weapon {

    public static $import_path;
    public static $export_path;

    public $Id;
    public $WeaponSubType;
    public $HeatGenerated;
    public $Damage;
    public $HeatDamage;
    public $AccuracyModifier;
    public $CriticalChanceMultiplier;
    public $RefireModifier;
    public $ShotsWhenFired;
    public $ProjectilesPerShot;
    public $AttackRecoil;
    public $Instability;
    public $Cost;
    public $Rarity;
    public $BattleValue;
    public $InventorySize;
    public $Tonnage;
    

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
        WeaponSubType::init($plugin_path, $table_prefix);
        WeaponVariation::init($plugin_path, $table_prefix);

        Weapon::$import_path = "{$plugin_path}/original-json/weapon";
        Weapon::$export_path = "{$plugin_path}/json-output/weapon";
    }

    public static function deactivate() {
        WeaponSubType::deactivate();
        WeaponVariation::deactivate();
    }

    public static function from_json( $json ) {
        $obj = json_decode($json);
        $weapon = new Weapon();
        $weapon->Id = $obj->Description->Id;
        $weapon->WeaponSubType = $obj->WeaponSubType;
        return $weapon;
    }

    public static function import() {

        $json_files = new DirectoryIterator(Weapon::$import_path);
        
        foreach( $json_files as $json_file ) {
            if($json_file->isDot()) {
                continue;
            }
            $file_path = `{Weapon::$import_path}/{$json_file->getFilename()}`;
            $file_contents = file_get_contents($file_path);

            $weapon = Weapon::from_json( $file_contents );
            $sub_type = new \stdClass;
            if( !str_contains( $weapon->Id, "STOCK") ) {
                $sub_type = WeaponSubType::from_json( $file_contents );
            } else {
                $sub_type = WeaponSubType::get_by_name( $weapon->WeaponSubType );
            }

            if( !$empty($sub_type) ) {
                $sub_type->save();

                $variation = WeaponVariation::from_json( $file_contents, $sub_type );
                $variation->save();

            }
    
        }
    
    }

    public static function export() {
        $variations = WeaponVariation::get_all();
        $sub_types = WeaponSubType::get_all();

        foreach( $variations as $variation ) {
            // find appropriate sub_type
            $sub_type_index = array_search($variation->WeaponSubType, array_column($sub_types, 'Name'));

            // format weapon
            $weapon = Weapon::make( $variation, $sub_types[$sub_type_index] );

            //make file and push contents
            file_put_contents(`{Weapon::$export_path}/{$weapon->Id}.json`, json_encode($weapon));
        }
    }

    public static function make( $variation, $sub_type ) {

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

}
    




