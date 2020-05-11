<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\kits\types;

use jkorn\ad1vs1\kits\IDuelKit;
use jkorn\ad1vs1\player\AD1vs1Player;
use AdvancedKits\Kit as AKKit;

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

    public function __construct(AKKit $kit)
    {
        $this->kit = $kit;
    }

    /**
     * @return string
     *
     * Gets the localized name of the kit.
     */
    public function getLocalizedName()
    {
        return strtolower($this->kit->getName());
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
}