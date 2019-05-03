<?php

namespace aieuo\ip;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

use aieuo\ip\economy\Economy;

class Main extends PluginBase implements Listener {
    /** @var string */
    public $version;

    /** @var float */
    private $wait = 0;
    /** @var CommandManager */
    private $command;
    /** @var BlockManager */
    private $block;
    /** @var EventManager */
    private $event;
    /** @var ChainManager */
    private $chain;

    private static $instance;

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);

        if(!file_exists($this->getDataFolder()."exports")) @mkdir($this->getDataFolder()."exports", 0721, true);
        if(!file_exists($this->getDataFolder()."imports")) @mkdir($this->getDataFolder()."imports", 0721, true);
        $this->config = new Config($this->getDataFolder()."config.yml", Config::YAML, [
            "wait" => 0,
            "save_time" => 10*20*60
        ]);
        $this->wait = (float)$this->config->get("wait");

        (new Economy($this))->loadPlugin();

        $this->version = $this->getDescription()->getVersion();

        self::$instance = $this;
    }

    public static function getInstance() {
        return $this->getInstance();
    }
}