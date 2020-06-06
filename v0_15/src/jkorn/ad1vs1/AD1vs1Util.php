<?php

declare(strict_types=1);

namespace jkorn\ad1vs1;


use jkorn\ad1vs1\level\chunk\AD1vs1ChunkLoader;
use jkorn\ad1vs1\level\tasks\AsyncDeleteLevel;
use pocketmine\command\Command;
use pocketmine\entity\Effect;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class AD1vs1Util
{

    const CEILING_LOW = 6;

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

    /**
     * @param Vector3 $position
     * @return string
     *
     * Localizes the position so it can be put in an array.
     */
    public static function localizePosition(Vector3 $position)
    {
        return "{$position->x}:{$position->y}:{$position->z}";
    }

    /**
     * @param Command $command
     *
     * Registers the command to the map.
     */
    public static function registerCommand(Command $command)
    {
        Server::getInstance()->getCommandMap()->register($command->getName(), $command);
    }

    /**
     * @param AD1vs1Main $main
     * @return array
     *
     * Gets the levels from the folder.
     */
    public static function getLevelsFromFolder(AD1vs1Main $main)
    {
        $dataFolder = $main->getDataFolder();

        $worlds = substr($dataFolder, 0, strpos($dataFolder, '/plugins')) . '/worlds';

        if(!is_dir($worlds)) {
            return [];
        }

        return array_filter(scandir($worlds), function(string $world) {
            return is_dir($world);
        });
    }

    /**
     * @param Vector3 $vec1
     * @param Vector3 $vec2
     *
     * @return Vector3
     */
    public static function getMinimumVector(Vector3 $vec1, Vector3 $vec2)
    {
        $minX = $vec1->x;
        if($minX > $vec2->x)
        {
            $minX = $vec2->x;
        }

        $minY = $vec1->y;
        if($minY > $vec2->y)
        {
            $minY = $vec2->y;
        }

        $minZ = $vec1->z;
        if($minZ > $vec2->z)
        {
            $minZ = $vec2->z;
        }

        return new Vector3($minX, $minY, $minZ);
    }


    /**
     * @param Vector3 $vec1
     * @param Vector3 $vec2
     *
     * @return Vector3
     *
     * Gets the maximum vector based on the two vectors.
     */
    public static function getMaximumVector(Vector3 $vec1, Vector3 $vec2)
    {
        $minX = $vec1->x;
        if($minX < $vec2->x)
        {
            $minX = $vec2->x;
        }

        $minY = $vec1->y;
        if($minY < $vec2->y)
        {
            $minY = $vec2->y;
        }

        $minZ = $vec1->z;
        if($minZ < $vec2->z)
        {
            $minZ = $vec2->z;
        }

        return new Vector3($minX, $minY, $minZ);
    }

    /**
     * @param Vector3 $pos
     *
     * @return array
     *
     * Converts a vector3 to an array.
     */
    public static function vec3ToArr(Vector3 $pos)
    {
        return [
            "x" => $pos->x,
            "y" => $pos->y,
            "z" => $pos->z
        ];
    }

    /**
     * @param $data
     * @return Vector3|null
     *
     * Converts an array to a vector3.
     */
    public static function arrToVec3($data)
    {
        if(is_array($data) && isset($data["x"], $data["y"], $data["z"]))
        {
            return new Vector3(
                $data["x"],
                $data["y"],
                $data["z"]
            );
        }

        return null;
    }

    /**
     * @param $level1
     * @param $level2
     *
     * @return bool
     *
     * Determines whether the levels are the same.
     */
    public static function areLevelsEqual($level1, $level2)
    {
        if($level1 instanceof Level && $level2 instanceof Level)
        {
            return $level1->getName() === $level2->getName();
        }

        return false;
    }

    /**
     * @param $object
     * @return array
     *
     * Gets the variables in a class.
     */
    public static function getVariablesIn($object)
    {
        try
        {
            $class = new \ReflectionClass($object);
            return $class->getDefaultProperties();

        }catch(\Exception $e)
        {
            return [];
        }
    }
}
