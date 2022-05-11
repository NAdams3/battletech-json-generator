<?php

class WeaponVariation extends Weapon {

    // how to handle this better???
    // const TABLE_PREFIX = 'btjg_';
    public static $table_name;

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

        parent::__construct(
            $HeatGenerated,
            $Damage,
            $HeatDamage,
            $AccuracyModifier,
            $CriticalChanceMultiplier,
            $RefireModifier,
            $ShotsWhenFired,
            $ProjectilesPerShot,
            $AttackRecoil,
            $Instability,
            $Cost,
            $Rarity,
            $BattleValue,
            $InventorySize,
            $Tonnage
        );
    }

    public static function init( $table_prefix ) {
        global $wpdb;

        //set table_prefix;
        // $weapon = new Weapon();
        $this->table_name = `{$wpdb->prefix}{$table_prefix}weapon_variations`;

        // create variation table
        $wpdb->query("CREATE TABLE IF NOT EXISTS {$this->table_name} (
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

    public static function from_json( string $json ): WeaponVariation {
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
            $variation->HeatGenerated,
            $variation->Damage,
            $variation->HeatDamage,
            $variation->AccuracyModifier,
            $variation->CriticalChanceMultiplier,
            $variation->RefireModifier,
            $variation->ShotsWhenFired,
            $variation->ProjectilesPerShot,
            $variation->AttackRecoil,
            $variation->Instability,
            $variation->Description->Cost,
            $variation->Description->Rarity,
            $variation->BattleValue,
            $variation->InventorySize,
            $variation->Tonnage
        );
    }

    public function save() {

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

    }

    public function update() {
        return;
    }

}
