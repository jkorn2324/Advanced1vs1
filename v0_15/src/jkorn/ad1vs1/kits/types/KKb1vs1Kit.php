<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\kits\types;


use jkorn\ad1vs1\AD1vs1Util;
use jkorn\ad1vs1\kits\IDuelKit;
use jkorn\ad1vs1\player\AD1vs1Player;
use kitkb\KitKb;
use kitkb\kits\Kit as KitKbKit;

/**
 * Class KKb1vs1Kit
 * @package jkorn\ad1vs1\kits\types
 *
 * The corresponding kit from KitKb.
 */
class KKb1vs1Kit implements IDuelKit
{

    /** @var KitKbKit */
    private $kit;
    /** @var string */
    private $localized;
    /** @var string */
    private $name;

    public function __construct(KitKbKit $kit)
    {
        $this->kit = $kit;
        $this->name = $kit->getName();
        $this->localized = strtolower($kit->getName());
    }

    /**
     * Gets the localized name of the 1vs1 kit.
     * @return string
     */
    public function getLocalizedName()
    {
        return $this->localized;
    }

    /**
     * @param AD1vs1Player $player
     *
     * Sends the kit to the 1vs1 player.
     */
    public function sendTo(AD1vs1Player $player)
    {
        if(!$player->isOnline()) {
            return;
        }

        $this->kit->giveTo($player->getPlayer(), false);
    }

    /**
     * @return bool
     *
     * Determines if the kit is valid or not.
     */
    public function isValid()
    {
        $plugin = AD1vs1Util::getKitPlugin();
        if($plugin instanceof KitKb && $plugin->isEnabled()) {
            if(KitKb::getKitHandler()->isKit($this->name))
            {
                $this->kit = KitKb::getKitHandler()->getKit($this->name);
                return true;
            }
        }
        return false;
    }
}