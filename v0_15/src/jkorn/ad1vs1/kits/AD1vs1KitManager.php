<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\kits;


use jkorn\ad1vs1\AD1vs1Main;
use jkorn\ad1vs1\AD1vs1Util;
use jkorn\ad1vs1\kits\types\AK1vs1Kit;
use jkorn\ad1vs1\kits\types\Default1vs1Kit;
use jkorn\ad1vs1\kits\types\KKb1vs1Kit;
use kitkb\KitKb;
use kitkb\kits\Kit as KitKbKit;
use AdvancedKits\Kit as AKKit;
use AdvancedKits\Main as AdvancedKits;

class AD1vs1KitManager
{

    const DEFAULT_1VS1_KIT = "default";

    /** @var IDuelKit[] */
    private $kits;
    /** @var AD1vs1Main */
    private $main;

    /** @var array */
    private $duelKits;

    public function __construct(AD1vs1Main $main)
    {
        $this->kits = [];
        $this->duelKits = [];
        $this->main = $main;
        $this->loadKitInformation();
    }

    /**
     * Loads the default kit from the config.
     */
    private function loadKitInformation()
    {
        $contents = $this->main->getPluginData();
        if(isset($contents["default.info"]))
        {
            $defaultInfo = $contents["default.info"];
            $items = []; $armor = []; $effects = [];

            $contentItems = $defaultInfo["items"];
            foreach($contentItems as $slot => $itemData) {
                $item = AD1vs1Util::arrayToItem($itemData);
                if($item !== null) {
                    $items[$slot] = $item;
                }
            }

            $contentArmor = $defaultInfo["armor"];
            foreach($contentArmor as $armorType => $itemData) {
                $item = AD1vs1Util::arrayToItem($itemData);
                if($item !== null) {
                    $armor[$armorType] = $item;
                }
            }

            $contentEffects = $defaultInfo["effects"];
            foreach($contentEffects as $contentEffect => $effectData) {
                $effect = AD1vs1Util::arrayToEffect($effectData);
                if($effect !== null) {
                    $effects[] = $effect;
                }
            }

            $this->kits[self::DEFAULT_1VS1_KIT] = new Default1vs1Kit($items, $armor, $effects);
        }

        // Loads the corresponding duel kits.
        if(isset($contents["duel.kits"])) {

            $duelKits = $contents["duel.kits"];
            foreach($duelKits as $kitPlugin => $value) {

                if ($value !== null && $value !== false) {

                    if (is_array($value)) {
                        foreach ($value as $kit) {
                            $this->duelKits["{$kitPlugin}.{$kit}"] = true;
                        }
                    } elseif (is_bool($value)) {
                        $this->duelKits[$kitPlugin] = true;
                    }
                }
            }
        }

        $kitPlugin = AD1vs1Util::getKitPlugin();
        if($kitPlugin != null) {

            if($kitPlugin instanceof KitKb)
            {
                $kits = KitKb::getKitHandler()->getKits();
                foreach($kits as $kitName => $kit) {
                    if(!isset($this->kits[$localizedName = strtolower($kitName)])) {
                        if(!isset($this->duelKits["kitkb"], $this->duelKits["kitkb.{$localizedName}"])) {
                            continue;
                        }
                        $this->kits[$localizedName] = new KKb1vs1Kit($kit);
                    }
                }
            }
            elseif ($kitPlugin instanceof AdvancedKits)
            {
                $kits = $kitPlugin->kits;
                foreach($kits as $kitName => $kit) {
                    if(!isset($this->kits[$localizedName = strtolower($kitName)])) {

                        if(!isset($this->duelKits["advancedkits"], $this->duelKits["advancedkits.{$localizedName}"])) {
                            continue;
                        }

                        $this->kits[$localizedName] = new AK1vs1Kit($kit);
                    }
                }
            }
        }
    }

    /**
     * @param string $kitname
     * @return IDuelKit|mixed|null
     *
     * Gets the kit from the kit name.
     */
    public function getKit(string $kitname)
    {
        if(isset($this->kits[strtolower($kitname)])) {
            $kit = $this->kits[strtolower($kitname)];
            // TODO
        }

        return null;
    }


    /**
     * @param string $kitName
     * @return bool - Returns true if it was added/already exists.
     *
     * Puts the kit to the list.
     */
    public function putKit(string $kitName)
    {
        $plugin = AD1vs1Util::getKitPlugin();

        $kit = $this->getKit($kitName);
        if($kit !== null) {
            return true;
        }

        if($plugin !== null && $plugin->isEnabled())
        {
            if($plugin instanceof KitKb)
            {
                $kit = KitKb::getKitHandler()->getKit($kitName);
                if($kit instanceof KitKbKit) {

                    if(!isset($this->duelKits["kitkb"]) && !isset($this->duelKits["kitkb.{$kitName}"])) {
                        return false;
                    }

                    $duelKit = new KKb1vs1Kit($kit);
                    $this->kits[$duelKit->getLocalizedName()] = $duelKit;
                    return true;
                }
            }
            else if ($plugin instanceof AdvancedKits)
            {
                $kit = $plugin->getKit($kitName);
                if($kit instanceof AKKit) {

                    if(!isset($this->duelKits["advancedkits"]) && !isset($this->duelKits["advancedkits.{$kitName}"])) {
                        return false;
                    }

                    $duelKit = new AK1vs1Kit($kit);
                    $this->kits[$duelKit->getLocalizedName()] = $duelKit;
                    return true;
                }
            }
        }

        return false;
    }
}