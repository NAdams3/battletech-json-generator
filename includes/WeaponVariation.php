<?php

namespace BTJG;

class WeaponVariation {

    // how to handle this better???
    // const TABLE_PREFIX = 'btjg_';
    public static $table_name;
    public static $plugin_path;

    public $Id;
    public $WeaponSubType;
    public $Manufacturer;
    public $UIName;
    public $Name;
    public $BonusValueA;
    public $BonusValueB;
    public $ComponentTags1;
    public $ComponentTags2;
    public $ComponentTags3;

    public function __construct(
        string $Id,
        string $WeaponSubType,
        ?string $Manufacturer = "",
        ?string $UIName = "",
        ?string $Name = "",
        ?string $BonusValueA = "",
        ?string $BonusValueB = "",
        ?string $ComponentTags1 = "",
        ?string $ComponentTags2 = "",
        ?string $ComponentTags3 = "",
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
        $this->Id = $Id;
        $this->WeaponSubType = $WeaponSubType;
        $this->Manufacturer = $Manufacturer;
        $this->UIName = $UIName;
        $this->Name = $Name;
        $this->BonusValueA = $BonusValueA;
        $this->BonusValueB = $BonusValueB;
        $this->ComponentTags1 = $ComponentTags1;
        $this->ComponentTags2 = $ComponentTags2;
        $this->ComponentTags3 = $ComponentTags3;
        // $this->HeatGenerated = $HeatGenerated;
        // $this->Damage = $Damage;
        // $this->HeatDamage = $HeatDamage;
        // $this->AccuracyModifier = $AccuracyModifier;
        // $this->CriticalChanceMultiplier = $CriticalChanceMultiplier;
        // $this->RefireModifier = $RefireModifier;
        // $this->ShotsWhenFired = $ShotsWhenFired;
        // $this->ProjectilesPerShot = $ProjectilesPerShot;
        // $this->AttackRecoil = $AttackRecoil;
        // $this->Instability = $Instability;
        // $this->Cost = $Cost;
        // $this->Rarity = $Rarity;
        // $this->BattleValue = $BattleValue;
        // $this->InventorySize = $InventorySize;
        // $this->Tonnage = $Tonnage;

        // parent::__construct(
        //     $HeatGenerated,
        //     $Damage,
        //     $HeatDamage,
        //     $AccuracyModifier,
        //     $CriticalChanceMultiplier,
        //     $RefireModifier,
        //     $ShotsWhenFired,
        //     $ProjectilesPerShot,
        //     $AttackRecoil,
        //     $Instability,
        //     $Cost,
        //     $Rarity,
        //     $BattleValue,
        //     $InventorySize,
        //     $Tonnage
        // );
    }

    public static function init( $plugin_path, $table_prefix ) {
        global $wpdb;

        //set table_prefix;
        self::$table_name = "{$wpdb->prefix}{$table_prefix}weapon_variations";
        WeaponVariation::$plugin_path = $plugin_path;

        // create variation table
        $wpdb->query("CREATE TABLE IF NOT EXISTS " . WeaponVariation::$table_name . " (
            Id VARCHAR(255),
            WeaponSubType VARCHAR(255),
            HeatGenerated INT,
            Damage INT,
            HeatDamage INT,
            AccuracyModifier INT,
            CriticalChanceMultiplier INT,
            RefireModifier INT,
            ShotsWhenFired INT,
            ProjectilesPerShot INT,
            AttackRecoil INT,
            Instability INT,
            Cost INT,
            Rarity INT,
            Manufacturer VARCHAR(255),
            UIName VARCHAR(255),
            Name VARCHAR(255),
            BonusValueA VARCHAR(255),
            BonusValueB VARCHAR(255),
            BattleValue INT,
            InventorySize INT,
            Tonnage DECIMAL(10, 2),
            ComponentTags1 VARCHAR(255),
            ComponentTags2 VARCHAR(255),
            ComponentTags3 VARCHAR(255),
            PRIMARY KEY(Id)
        );");
    }

    public static function deactivate() {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS " . WeaponVariation::$table_name . ";");
    }

    public static function from_json( string $json, $sub_type ): WeaponVariation {
        $variation = json_decode($json);
        return new WeaponVariation(
            $variation->Description->Id,
            $variation->WeaponSubType,
            $variation->Description->Manufacturer,
            $variation->Description->UIName,
            $variation->Description->Name,
            $variation->BonusValueA,
            $variation->BonusValueB,
            $variation->ComponentTags->items[0],
            $variation->ComponentTags->items[1],
            $variation->ComponentTags->items[2],
            $variation->HeatGenerated - $sub_type->HeatGenerated,
            $variation->Damage - $sub_type->Damage,
            $variation->HeatDamage - $sub_type->HeatDamage,
            $variation->AccuracyModifier - $sub_type->AccuracyModifier,
            $variation->CriticalChanceMultiplier - $sub_type->CriticalChanceMultiplier,
            $variation->RefireModifier - $sub_type->RefireModifier,
            $variation->ShotsWhenFired - $sub_type->ShotsWhenFired,
            $variation->ProjectilesPerShot - $sub_type->ProjectilesPerShot,
            $variation->AttackRecoil - $sub_type->AttackRecoil,
            $variation->Instability - $sub_type->Instability,
            $variation->Description->Cost - $sub_type->Cost,
            $variation->Description->Rarity - $sub_type->Rarity,
            $variation->BattleValue - $sub_Type->BattleValue,
            $variation->InventorySize - $sub_type->InventorySize,
            $variation->Tonnage - $sub_type->Tonnage
        );
    }

    public static function get_all() {
        global $wpdb;

        return $wpdb->get_results("SELECT * FROM {$this->table_name};");
    }

    public function save() {

        $sub_type = new WeaponSubType($this->WeaponSubType);
        if( !$sub_type->exists() ) {
            return false;
        }

        if( $this->exists() ) {
            return $this-update();
        }

        return $this->insert();

    }

    public function exists() {

        $variation = $wpdb->get_row("SELECT * FROM {$this->table_name} WHERE Id = '{$this->Id}';");
        if( !empty($variation) ) {
            return true;
        }

        return false;

    }

    public function insert() {
        global $wpdb;

        $wpdb->query("INSERT INTO {$this->table_name} VALUES (
            '{$this->Id}',
            '{$this->WeaponSubType}',
            {$this->HeatGenerated},
            {$this->Damage},
            {$this->HeatDamage},
            {$this->AccuracyModifier},
            {$this->CriticalChanceMultiplier},
            {$this->RefireModifier},
            {$this->ShotsWhenFired},
            {$this->ProjectilesPerShot},
            {$this->AttackRecoil},
            {$this->Instability},
            {$this->Cost},
            {$this->Rarity},
            '{$this->Manufacturer}',
            '{$this->UIName}',
            '{$this->Name}',
            '{$this->BonusValueA}',
            '{$this->BonusValueB}',
            {$this->BattleValue},
            {$this->InventorySize},
            {$this->Tonnage},
            '{$this->ComponentTag1}',
            '{$this->ComponentTag2}',
            '{$this->ComponentTag3}');");
    }

    public function update() {
        return;
    }

}
