<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\kits\types;


use jkorn\ad1vs1\kits\IDuelKit;
use jkorn\ad1vs1\player\AD1vs1Player;
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

    public function __construct(KitKbKit $kit)
    {
        $this->kit = $kit;
    }

    /**
     * Gets the localized name of the 1vs1 kit.
     * @return string
     */
    public function getLocalizedName()
    {
        return strtolower($this->kit->getName());
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
}