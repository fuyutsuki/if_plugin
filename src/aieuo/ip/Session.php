<?php

namespace aieuo\ip;

use pocketmine\Player;

class Session {
    private static $sessions = [];

    /**
     * @param  Player $player
     * @return Session | null
     */
    public static function get(Player $player): Session {
        if(!isset(self::$sessions[$player->getName()])) return null;
        return self::$sessions[$player->getName()];
    }

    public static function register(Player $player) {
        self::$sessions[$player->getName()] = new Session();
    }

////////////////////////////////////////////////////////////////////////

    /** @var bool */
    private $valid = false;
    /** @var array */
    private $datas = [];

    public function setValid($valid = true, $deleteDatas = true): self {
        $this->valid = $valid;
        if(!$valid and $deleteDatas) $this->removeAllData();
        return $this;
    }

    public function getData($key, $default = null) {
        if(!isset($this->datas[$key])) return $default;
        return $this->datas[$key];
    }

    public function setData($key, $data): self {
        $this->datas[$key] = $data;
        return $this;
    }

    public function removeData($key) {
        unset($this->datas[$key]);
    }

    public function removeAllData() {
        $this->datas = [];
    }
}