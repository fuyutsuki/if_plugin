<?php

namespace aieuo\ip\processes;

use aieuo\ip\ifPlugin;

use aieuo\ip\form\Form;
use aieuo\ip\form\Elements;

class TakeMoney extends TypeMoney {

    protected $id = self::TAKEMONEY;
    protected $name = "所持金を減らす";
    protected $description = "所持金を§7<amount>§f減らす";

	public function getMessage() {
		return "所持金を".$this->getAmount()."減らす";
	}

	public function execute() {
		$player = $this->getPlayer();
        $economy = ifPlugin::getInstance()->getEconomy();
        if($economy === null) {
            $player->sendMessage("§c経済システムプラグインが見つかりません");
            return;
        }
        $economy->takeMoney($player->getName(), $this->getAmount());
	}
}