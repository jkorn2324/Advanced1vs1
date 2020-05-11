<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\kits\types;

use jkorn\ad1vs1\AD1vs1Util;
use jkorn\ad1vs1\kits\IDuelKit;
use jkorn\ad1vs1\player\AD1vs1Player;
use AdvancedKits\Kit as AKKit;
use AdvancedKits\Main as AdvancedKits;

/**
 * Class AK1vs1Kit
 * @package jkorn\ad1vs1\kits\types
 *
 * The corresponding kit that belongs to advancedkits.
 */
class AK1vs1Kit implements IDuelKit
{

    /** @var AKKit $kit */
    private $kit;
    /** @var string */
    private $localizedName;

    public function __construct(AKKit $kit)
    {
        $this->kit = $kit;
        $this->localizedName = strtolower($kit->getName());
    }

    /**
     * @return string
     *
     * Gets the localized name of the kit.
     */
    public function getLocalizedName()
    {
        return $this->localizedName;
    }

    /**
     * @param AD1vs1Player $player
     *
     * Sends the kit to the player.
     */
    public function sendTo(AD1vs1Player $player)
    {
        if(!$player->isOnline()) {
            return;
        }

        $this->kit->addTo($player->getPlayer());
    }

    /**
     * @return bool
     *
     * Determines whether the kit is valid or not.
     */
    public function isValid()
    {
        $plugin = AD1vs1Util::getKitPlugin();
        if($plugin instanceof AdvancedKits && $plugin->isEnabled())
        {
            $outputKit = $plugin->getKit($this->localizedName);
            if($outputKit instanceof AKKit)
            {
                $this->kit = $outputKit;
                return true;
            }
        }
        return false;
    }
}