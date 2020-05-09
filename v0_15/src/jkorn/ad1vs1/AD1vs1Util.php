<?php

declare(strict_types=1);

namespace jkorn\ad1vs1;


use pocketmine\plugin\Plugin;
use pocketmine\Server;

class AD1vs1Util
{

    /**
     * @param $kit
     * @return bool
     *
     * Determines if the kit is designed for building (BuildUHC, etc...)
     */
    public static function isEditingKit($kit) {
        // TODO
        return false;
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
}
