<?php

namespace aieuo\ip\processes;

use pocketmine\event\entity\EntityDamageEvent;

use aieuo\ip\form\Form;
use aieuo\ip\form\Elements;

class Attack extends Process
{
	public $id = self::ATTACK;

	public function __construct($player = null, $health = null)
	{
		parent::__construct($player);
		$this->setValues($health);
	}

	public function getName()
	{
		return "ダメージを与える";
	}

	public function getDescription()
	{
		return "プレイヤーにダメージを§7<damage>§f与える";
	}

	public function getEditForm(string $defaults = "", string $mes = "")
	{
		$damage = $this->parse($defaults);
		if($damage === false)
		{
			$mes = "§c攻撃力は1以上にしてください§f";
			$damage = $defaults;
		}
		if($mes !== "") $mes = "\n".$mes;
        $data = [
            "type" => "custom_form",
            "title" => $this->getName(),
            "content" => [
                Elements::getLabel($this->getDescription().$mes),
                Elements::getInput("<damage>\n攻撃力を入力してください", "例) 5", $damage),
                Elements::getToggle("削除する")
            ]
        ];
        $json = Form::encodeJson($data);
        return $json;
	}

	public function parse(string $content)
	{
		$damage = (float)$content;
		if($damage <= 0) return false;
		return $damage;
	}

	public function getDamage() : ?float
	{
		return $this->getValues();
	}

	public function setDamage(float $damage)
	{
		$this->setValues($damage);
	}

	public function execute()
	{
		$player = $this->getPlayer();
		$damage = $this->getDamage();
		if($damage === false)
		{
			$player->sendMessage("§c[".$this->getName()."] 体力は1以上にしてください");
			return;
		}
		$event = new EntityDamageEvent($player, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $damage);
		$player->attack($event);
	}
}