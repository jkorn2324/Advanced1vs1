<?php

declare(strict_types=1);

namespace jkorn\ad1vs1;


use jkorn\ad1vs1\commands\DuelCommand;
use jkorn\ad1vs1\duels\AD1vs1Manager;
use jkorn\ad1vs1\kits\AD1vs1KitManager;
use jkorn\ad1vs1\kits\types\Default1vs1Kit;
use jkorn\ad1vs1\level\AD1vs1GeneratorManager;
use jkorn\ad1vs1\level\generators\types\AD1vs1DefaultRed;
use jkorn\ad1vs1\level\generators\types\AD1vs1DefaultYellow;
use jkorn\ad1vs1\player\AD1vs1PlayerManager;
use pocketmine\plugin\PluginBase;

class AD1vs1Main extends PluginBase
{

    /** @var AD1vs1PlayerManager */
    private static $playerManager;
    /** @var AD1vs1GeneratorManager */
    private static $generatorManager;
    /** @var AD1vs1Manager */
    private static $duelsManager;
    /** @var AD1vs1KitManager */
    private static $kitsManager;

    /**
     * Called when the plugin is enabled.
     */
    public function onEnable()
    {
        $this->loadDataFolder();

        self::$playerManager = new AD1vs1PlayerManager($this);
        self::$generatorManager = new AD1vs1GeneratorManager($this);
        self::$kitsManager = new AD1vs1KitManager($this);
        self::$duelsManager = new AD1vs1Manager($this);

        $this->registerGenerators();
        $this->registerCommands();

        new AD1vs1Listener($this);
        new AD1vs1Task($this);

        $this->getLogger()->info(AD1vs1Util::getPrefix() . " The plugin is now enabled!");
    }


    /**
     * Called when the plugin is disabled.
     */
    public function onDisable()
    {
        $this->getLogger()->info(AD1vs1Util::getPrefix() . " The plugin is now disabled!");
    }

    /**
     * Loads the data folder.
     */
    private function loadDataFolder() {

        if(!is_dir($this->getDataFolder())) {
            mkdir($this->getDataFolder());
        }

        if(!file_exists($config = $this->getDataFolder() . "KitConfig.json"))  {

            $file = fopen($config, "w");
            fclose($file);

            file_put_contents($config, json_encode([
                "default.kit" => Default1vs1Kit::defaultInfo(),
                "duel.kits" => [
                    // Determines which kits in the plugin are for duels or not.
                    // true = all of them are duel kits
                    // false or null = none of them are duel kits
                    // array = list of kits that are only duel kits
                    "kitkb" => true,
                    "advancedkits" => true
                ]
            ]));
        }
    }

    /**
     * @return AD1vs1PlayerManager
     *
     * Gets the player manager.
     */
    public static function getPlayerManager()
    {
        return self::$playerManager;
    }

    /**
     * @return AD1vs1GeneratorManager
     *
     * Gets the generator manager.
     */
    public static function getGeneratorManager()
    {
        return self::$generatorManager;
    }

    /**
     * @return AD1vs1Manager
     *
     * Gets the 1vs1 manager.
     */
    public static function get1vs1Manager()
    {
        return self::$duelsManager;
    }

    /**
     * @return AD1vs1KitManager
     *
     * Gets the kit manager.
     */
    public static function getKitManager()
    {
        return self::$kitsManager;
    }

    /**
     * Registers the generators
     */
    private function registerGenerators() {

        self::$generatorManager->registerGenerator(
            "Default Red",
            AD1vs1GeneratorManager::DEFAULT_RED,
            AD1vs1DefaultRed::class
        );

        self::$generatorManager->registerGenerator(
            "Default Yellow",
            AD1vs1GeneratorManager::DEFAULT_YELLOW,
            AD1vs1DefaultYellow::class
        );
    }

    /**
     * Registers the commands.
     */
    private function registerCommands() {

        AD1vs1Util::registerCommand(new DuelCommand());
    }

    /**
     * @return array|mixed
     *
     * Gets the plugin data from the json config.
     */
    public function getPluginData()
    {
        if(file_exists($jsonData = $this->getDataFolder() . "KitConfig.json"))
        {
            return json_decode(file_get_contents($jsonData), true);
        }

        return [];
    }
}