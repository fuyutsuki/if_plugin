<?php

namespace aieuo\ip\processes;

use aieuo\ip\ifPlugin;

use aieuo\ip\form\Form;
use aieuo\ip\form\Elements;

class AddMoney extends TypeMoney {

	protected $id = self::ADDMONEY;
    protected $name = "所持金を増やす";
    protected $description = "所持金を§7<amount>§f増やす";

	public function getMessage() {
		return "所持金を".$this->getAmount()."増やす";
	}

	public function execute() {
		$player = $this->getPlayer();
        $economy = ifPlugin::getInstance()->getEconomy();
        if($economy === null) {
            $player->sendMessage("§c経済システムプラグインが見つかりません");
            return;
        }
        $economy->addMoney($player->getName(), $this->getAmount());
	}
}