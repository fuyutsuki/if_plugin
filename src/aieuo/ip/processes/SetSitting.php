<?php

namespace aieuo\ip\processes;

use pocketmine\Server;
use pocketmine\entity\Entity;
use pocketmine\level\Position;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\network\mcpe\protocol\types\EntityLink;

use aieuo\ip\form\Form;
use aieuo\ip\form\Elements;

class SetSitting extends TypePosition {

    protected $id = self::SET_SITTING;
    protected $name = "座らせる";
    protected $description = "プレイヤーを§7<pos>§fに座らせる";

	public function getMessage() {
		$pos = $this->getPosition();
		if($pos === false) return false;
		return $pos->__toString()."で座る";
	}

	public function execute() {
		$player = $this->getPlayer();
		$pos = $this->getPosition();
		if(!($pos instanceof Position)) {
			$player->sendMessage("§c[".$this->getName()."] 正しく入力できていません");
			return;
		}
        $pk = new AddEntityPacket();
        $pk->entityRuntimeId = ++Entity::$entityCount;
        $pk->type = 84;
        $pk->position = $pos;
        $pk->links = [new EntityLink($pk->entityRuntimeId, $player->getId(), EntityLink::TYPE_RIDER)];
        $pk->metadata = [
			Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, 1 << Entity::DATA_FLAG_INVISIBLE]
		];
        $player->dataPacket($pk);
	}
}