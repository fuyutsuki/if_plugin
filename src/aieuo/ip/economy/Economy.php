<?php

namespace aieuo\economy;

class Economy {

    private static $economy = null;

    public function __construct($owner) {
        $this->owner = $owner;
    }

    public function loadPlugin() {
        $pluginManager = $this->owner->getServer()->getPluginManager();
        if(($plugin = $pluginManager->getPlugin("EconomyAPI")) !== null) {
            self::$economy = new EconomyAPILoader($plugin);
            $this->owner->getLogger()->info("EconomyAPIを見つけました。");
        } elseif(($plugin = $pluginManager->getPlugin("MoneySystem")) !== null) {
            self::$economy = new MoneySystemLoader($plugin);
            $this->owner->getLogger()->info("MoneySystemを見つけました。");
        } elseif(($plugin = $pluginManager->getPlugin("PocketMoney")) !== null) {
            self::$economy = new PocketMoneyLoader($plugin);
            $this->owner->getLogger()->info("PocketMoneyを見つけました。");
        } else {
            $this->owner->getLogger()->warning("経済システムプラグインが見つかりませんでした。");
        }
    }

    public static function isPluginLoaded() {
        return self::$economy !== null;
    }

    public static function getPlugin() {
        return self::$economy;
    }
}