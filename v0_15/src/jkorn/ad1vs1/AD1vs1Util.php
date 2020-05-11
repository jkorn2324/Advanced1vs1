<?php

declare(strict_types=1);

namespace jkorn\ad1vs1;


use jkorn\ad1vs1\level\chunk\AD1vs1ChunkLoader;
use jkorn\ad1vs1\level\tasks\AsyncDeleteLevel;
use pocketmine\entity\Effect;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class AD1vs1Util
{

    /**
     * @param string $localizedName
     * @return bool
     *
     * Determines if the kit is designed for building (BuildUHC, etc...)
     */
    public static function isBuildingKit(string $localizedName)
    {
        return strpos(strtolower($localizedName), "build") !== false;
    }


    /**
     * @param string $localizedName
     * @return bool
     *
     * Determines if the localized name of the kit is a sumo kit.
     */
    public static function isSumoKit(string $localizedName)
    {
        return strpos(strtolower($localizedName), "sumo") !== false;
    }


    /**
     * @return Plugin|null
     *
     * Gets the main kit plugin that the plugin will be using.
     */
    public static function getKitPlugin()
    {
        $plugins = [
            "KitKB" => true,
            "AdvancedKits" => true
        ];

        $pluginManager = Server::getInstance()->getPluginManager();
        foreach($plugins as $pluginName => $output)
        {
            $plugin = $pluginManager->getPlugin($pluginName);
            if($plugin !== null && $plugin instanceof Plugin)
            {
                return $plugin;
            }
        }

        return null;
    }


    /**
     * @param Item $item
     * @return array
     *
     * Converts an item to a string.
     */
    public static function itemToArray(Item $item)
    {
        $inputArray = [
            "id" => $item->getId(),
            "meta" => $item->getDamage(),
            "count" => $item->getCount()
        ];

        if(count(($enchants = $item->getEnchantments())) > 0) {
            $enchantmentArray = [];
            foreach($enchants as $enchant) {
                $enchantmentArray[] = [
                    $enchant->getId(),
                    $enchant->getLevel()
                ];
            }
            $inputArray["enchantments"] = $enchantmentArray;
        }

        if($item->hasCustomName()) {
            $inputArray["customName"] = $item->getCustomName();
        }

        return $inputArray;
    }


    /**
     * @param array $data
     * @return Item|null
     *
     * Converts a string to an item based on data.
     */
    public static function arrayToItem(array $data)
    {
        if(!isset($data["id"], $data["meta"], $data["count"])) {
            return null;
        }

        $item = Item::get($data["id"], $data["meta"], $data["count"]);
        if(isset($data["enchantments"])) {
            $enchantments = $data["enchantments"];
            foreach($enchantments as $enchantmentData) {
                $item->addEnchantment(Enchantment::getEnchantment($enchantmentData["id"])->setLevel($enchantmentData["level"]));
            }
        }

        if(isset($data["customName"])) {
            $item->setCustomName($data["customName"]);
        }

        return $item;
    }

    /**
     * @param Effect $effect
     * @param int $durationMinutes
     * @return array
     *
     * Converts the effect to an array.
     */
    public static function effectToArray(Effect $effect, int $durationMinutes = 30) {

        return [
            "id" => $effect->getId(),
            "duration" => $durationMinutes * (60 * 20),
            "amplifier" => $effect->getAmplifier()
        ];
    }

    /**
     * @param array $effect
     * @return Effect|null
     */
    public static function arrayToEffect(array $effect)
    {
        if(!isset($effect["id"], $effect["duration"], $effect["amplifier"])) {
            return null;
        }

        return Effect::getEffect($effect["id"])->setDuration($effect["duration"])->setAmplifier($effect["amplifier"]);
    }

    /**
     * @param Level $level
     * @param int $chunkX
     * @param int $chunkZ
     * @param callable $callable
     *
     * Called when a chunk is generated, used to load the chunks.
     */
    public static function onChunkGenerated(Level $level, int $chunkX, int $chunkZ, callable $callable)
    {
        if($level->isChunkPopulated($chunkX, $chunkZ))
        {
            $callable();
            return;
        }

        $level->registerChunkLoader(new AD1vs1ChunkLoader($level, $chunkX, $chunkZ, $callable), $chunkX, $chunkZ, true);
    }

    /**
     * @return string
     *
     * Gets the prefix.
     */
    public static function getPrefix()
    {
        return TextFormat::BOLD . TextFormat::DARK_GRAY . "[" . TextFormat::GOLD . "Advanced1vs1" . TextFormat::DARK_GRAY . "]" . TextFormat::RESET;
    }

    /**
     * @return Position
     *
     * Gets the main spawn position.
     */
    public static function getSpawnPosition()
    {
        return Server::getInstance()->getDefaultLevel()->getSafeSpawn();
    }

    /**
     * @param $level
     *
     * Deletes the level & all its data.
     */
    public static function deleteLevel($level)
    {
        $server = Server::getInstance();
        if($level instanceof Level) {
            $server->unloadLevel($level);
            $path = $server->getDataPath() . "worlds/" . $level->getFolderName();
        } elseif (is_string($level)) {
            $path = $server->getDataPath() . "worlds/" . $level;
        }

        if(isset($path))
        {
            $server->getScheduler()->scheduleAsyncTask(new AsyncDeleteLevel($path));
        }
    }
}
