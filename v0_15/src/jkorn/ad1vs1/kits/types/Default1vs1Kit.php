<?php


namespace jkorn\ad1vs1\kits\types;


use jkorn\ad1vs1\AD1vs1Util;
use jkorn\ad1vs1\kits\AD1vs1KitManager;
use jkorn\ad1vs1\kits\IDuelKit;
use jkorn\ad1vs1\player\AD1vs1Player;
use pocketmine\entity\Effect;
use pocketmine\item\Item;

class Default1vs1Kit implements IDuelKit
{

    /** @var Item[] */
    private $items;
    /** @var Item[] */
    private $armor;
    /** @var Effect[] */
    private $effects;

    public function __construct(array $items, array $armor, array $effects)
    {
        $this->items = $items;
        $this->armor = $armor;
        $this->effects = $effects;
    }


    /**
     * @return string
     *
     * Gets the localized name of the kit.
     */
    public function getLocalizedName()
    {
        return AD1vs1KitManager::DEFAULT_1VS1_KIT;
    }

    /**
     * @param AD1vs1Player $player
     *
     * Sends the kit to the 1vs1 player.
     */
    public function sendTo(AD1vs1Player $player)
    {
        // TODO: Implement sendTo() method.
    }

    /**
     * @return array
     *
     * Exports the default 1vs1 kit to a json.
     */
    public static function defaultInfo()
    {
        return [
            "items" => [
                0 => AD1vs1Util::itemToArray(Item::get(Item::IRON_SWORD)),
                1 => AD1vs1Util::itemToArray(Item::get(Item::STEAK, 0, 64))
            ],
            "armor" => [
                "helmet" => AD1vs1Util::itemToArray(Item::get(Item::DIAMOND_HELMET)),
                "chestplate" => AD1vs1Util::itemToArray(Item::get(Item::DIAMOND_CHESTPLATE)),
                "leggings" => AD1vs1Util::itemToArray(Item::get(Item::DIAMOND_LEGGINGS)),
                "boots" => AD1vs1Util::itemToArray(Item::get(Item::DIAMOND_BOOTS))
            ],
            "effects" => []
        ];
    }
}