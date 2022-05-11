<?php

class WeaponSubType extends Weapon {

    // how to handle this better???
    // const TABLE_PREFIX = 'btjg_';
    public static $table_name;

    public $Category string;
    public $Type string;
    public $Name string;
    public $MinRange int;
    public $MaxRange int;
    public $RangeSplit1 int;
    public $RangeSplit2 int;
    public $RangeSplit3 int;
    public $ammoCategoryID string;
    public $StartingAmmoCapacity int;
    public $OverheatedDamageMultiplier int;
    public $EvasiveDamageMultiplier int;
    public $EvasivePipsIgnored int;
    public $DamageVariance int;
    public $AOECapable boolean;
    public $IndirectFireCapable boolean;
    public $WeaponEffectID string;
    public $Purchasable boolean;
    public $Model string;
    public $Details string;
    public $Icon string;
    public $ComponentType string;
    public $ComponentSubType string;
    public $PrefabIdentifier string;
    public $AllowedLocations string;
    public $DisallowedLocations string;
    public $CriticalComponent boolean;
    public $tagSetSourceFile string;

    public function __construct(
        string $Name,
        ?string $Category = "",
        ?string $Type = "",
        ?int $MinRange = 0,
        ?int $MaxRange = 0,
        ?int $RangeSplit1 = 0,
        ?int $RangeSplit2 = 0,
        ?int $RangeSplit3 = 0,
        ?string $ammoCategoryID = "",
        ?int $StartingAmmoCapacity = 0,
        ?int $OverheatedDamageMultiplier = 0,
        ?int $EvasiveDamageMultiplier = 0,
        ?int $EvasivePipsIgnored = 0,
        ?int $DamageVariance = 0,
        ?boolean $AOECapable = false,
        ?boolean $IndirectFireCapable = false,
        ?string $WeaponEffectID = "",
        ?boolean $Purchasable = false,
        ?string $Model = "",
        ?string $Details = "",
        ?string $Icon = "",
        ?string $ComponentType = "",
        ?string $ComponentSubType = "",
        ?string $PrefabIdentifier = "",
        ?string $AllowedLocations = "",
        ?string $DisallowedLocations = "",
        ?boolean $CriticalComponent = false,
        ?string $tagSetSourceFile = "",
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
        $this->WeaponSubType = $WeaponSubType;
        $this->Category = $Category;
        $this->Type = $Type;
        $this->MinRange = $MinRange;
        $this->MaxRange = $MaxRange;
        $this->RangeSplit1 = $RangeSplit1;
        $this->RangeSplit2 = $RangeSplit2;
        $this->RangeSplit3 = $RangeSplit3;
        $this->ammoCategoryID = $ammoCategoryID;
        $this->StartingAmmoCapacity = $StartingAmmoCapacity;
        $this->OverheatedDamageMultiplier = $OverheatedDamageMultiplier;
        $this->EvasiveDamageMultiplier = $EvasiveDamageMultiplier;
        $this->EvasivePipsIgnored = $EvasivePipsIgnored;
        $this->DamageVariance = $DamageVariance;
        $this->AOECapable = $AOECapable;
        $this->IndirectFireCapable = $IndirectFireCapable;
        $this->WeaponEffectID = $WeaponEffectID;
        $this->Purchasable = $Purchasable;
        $this->Model = $Model;
        $this->Details = $Details;
        $this->Icon = $Icon;
        $this->ComponentType = $ComponentType;
        $this->ComponentSubType = $ComponentSubType;
        $this->PrefabIdentifier = $PrefabIdentifier;
        $this->AllowedLocations = $AllowedLocations;
        $this->DisallowedLocations = $DisallowedLocations;
        $this->CriticalComponent = $CriticalComponent;
        $this->tagSetSourceFile = $tagSetSourceFile;
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
        $this->table_name = `{$wpdb->prefix}{$table_prefix}weapon_sub_types`;

        // create sub_type table
        $wpdb->query("CREATE TABLE IF NOT EXISTS {$this->table_name} (
            Name VARCHAR(255), /*WeaponSubType VARCHAR(255),*/
            Category VARCHAR(255),
            Type VARCHAR(255),
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
            Tonnage DECIMAL(10, 2),
            AllowedLocations VARCHAR(255),
            DisallowedLocations VARCHAR(255),
            CriticalComponent BOOLEAN,
            -- statusEffects1 VARCHAR(255), not gonna bother with status effects for now
            tagSetSourceFile VARCHAR(255),
            PRIMARY KEY(Name)
        );");
    }

    public static function from_json( string $json ): WeaponSubType {
        $sub_type = json_decode($json);
        return new WeaponSubType(
            $sub_type->WeaponSubType,
            $sub_type->Category,
            $sub_type->Type,
            $sub_type->MinRange,
            $sub_type->MaxRange,
            $sub_type->RangeSplit[0],
            $sub_type->RangeSplit[1],
            $sub_type->RangeSplit[2],
            $sub_type->ammoCategoryID,
            $sub_type->StartingAmmoCapacity,
            $sub_type->OverheatedDamageMultiplier,
            $sub_type->EvasiveDamageMultiplier,
            $sub_type->EvasivePipsIgnored,
            $sub_type->DamageVariance,
            $sub_type->AOECapable,
            $sub_type->IndirectFireCapable,
            $sub_type->WeaponEffectID,
            $sub_type->Description->Purchasable,
            $sub_type->Description->Model,
            str_replace("\"", "'", $sub_type->Description->Details),
            $sub_type->Description->Icon,
            $sub_type->ComponentType,
            $sub_type->ComponentSubType,
            $sub_type->PrefabIdentifier,
            $sub_type->AllowedLocations,
            $sub_type->DisallowedLocations,
            $sub_type->CriticalComponent,
            $sub_type->ComponentTags->tagSetSourceFile,
            $sub_type->HeatGenerated,
            $sub_type->Damage,
            $sub_type->HeatDamage,
            $sub_type->AccuracyModifier,
            $sub_type->CriticalChanceMultiplier,
            $sub_type->RefireModifier,
            $sub_type->ShotsWhenFired,
            $sub_type->ProjectilesPerShot,
            $sub_type->AttackRecoil,
            $sub_type->Instability,
            $sub_type->Description->Cost,
            $sub_type->Description->Rarity,
            $sub_type->BattleValue,
            $sub_type->InventorySize,
            $sub_type->Tonnage
        );
    }


    public function save() {

        if( $this->exists() ) {
            return $this-update();
        }

        return $this->insert();

    }

    public function exists() {

        $sub_type = $wpdb->get_row("SELECT * FROM {$this->table_name} WHERE Name = '{$this->Name}';");
        if( !empty($sub_type) ) {
            return true;
        }

        return false;

    }

    public function insert() {
        global $wpdb;
    
        return $wpdb->query("INSERT INTO {$this->table_name} VALUES (
            '{$this->Name}',
            '{$this->Category}',
            '{$this->Type}',
            {$this->MinRange},
            {$this->MaxRange},
            {$this->RangeSplit1},
            {$this->RangeSplit2},
            {$this->RangeSplit3},
            '{$this->ammoCategoryID}',
            {$this->StartingAmmoCapacity},
            {$this->HeatGenerated},
            {$this->Damage},
            {$this->OverheatedDamageMultiplier},
            {$this->EvasiveDamageMultiplier},
            {$this->EvasivePipsIgnored},
            {$this->DamageVariance},
            {$this->HeatDamage},
            {$this->AccuracyModifier},
            {$this->CriticalChanceMultiplier},
            {$this->AOECapable},
            {$this->IndirectFireCapable},
            {$this->RefireModifier},
            {$this->ShotsWhenFired},
            {$this->ProjectilesPerShot},
            {$this->AttackRecoil},
            {$this->Instability},
            '{$this->WeaponEffectID}',
            {$this->Cost},
            {$this->Rarity},
            {$this->Purchasable},
            '{$this->Model}',
            \"{$this->Details}\",
            '{$this->Icon}',
            '{$this->ComponentType}',
            '{$this->ComponentSubType}',
            '{$this->PrefabIdentifier}',
            {$this->BattleValue},
            {$this->InventorySize},
            {$this->Tonnage},
            '{$this->AllowedLocations}',
            '{$this->DisallowedLocations}',
            {$this->CriticalComponent}, 
            '{$this->tagSetSourceFile}')");
    }

    public function update() {
        return;
    }


}
