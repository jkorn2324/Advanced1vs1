<?php

declare(strict_types=1);

namespace jkorn\ad1vs1;


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

    /**
     * Called when the plugin is enabled.
     */
    public function onEnable()
    {
        $this->loadDataFolder();

        self::$playerManager = new AD1vs1PlayerManager($this);
        self::$generatorManager = new AD1vs1GeneratorManager($this);

        $this->registerGenerator();

        $this->getLogger()->info("Advanced1vs1 is enabled!");
    }


    /**
     * Called when the plugin is disabled.
     */
    public function onDisable()
    {
        $this->getLogger()->info("Advanced1vs1 is disabled!");
    }

    /**
     * Loads the data folder.
     */
    private function loadDataFolder() {

        if(!is_dir($this->getDataFolder())) {
            mkdir($this->getDataFolder());
        }
    }

    /**
     * @return AD1vs1PlayerManager
     *
     * Gets the player manager.
     */
    public static function getPlayerManager() {
        return self::$playerManager;
    }

    /**
     * @return AD1vs1GeneratorManager
     *
     * Gets the generator manager.
     */
    public static function getGeneratorManager() {
        return self::$generatorManager;
    }

    /**
     * Registers the generators
     */
    private function registerGenerator() {

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
}