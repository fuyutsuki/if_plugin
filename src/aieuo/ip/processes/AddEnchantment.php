<?php

namespace aieuo\ip\processes;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;

use aieuo\ip\form\Form;
use aieuo\ip\form\Elements;

class AddEnchantment extends Process
{
	public $id = self::ADD_ENCHANTMENT;

	public function __construct($player = null, $ehcnant = null)
	{
		parent::__construct($player);
		$this->setValues($ehcnant);
	}

	public function getName()
	{
		return "手に持ってるアイテムにエンチャントを追加する";
	}

	public function getDescription()
	{
		return "手に持ってるアイテムにidが§7<id>§fで強さが§7<power>§rのエンチャントを追加する";
	}

	public function getEditForm(string $defaults = "", string $mes = "")
	{
		$enchant = $this->parse($defaults);
		$id = $defaults;
		$power = "";
		if($enchant instanceof EnchantmentInstance)
		{
			$id = $enchant->getId();
			$power = $enchant->getLevel();
			$mes = "§cエンチャントが見つかりません§f";
		}
		if($mes !== "") $mes = "\n".$mes;
        $data = [
            "type" => "custom_form",
            "title" => $this->getName(),
            "content" => [
                Elements::getLabel($this->getDescription().$mes),
                Elements::getInput("<id>\nエンチャントの名前かidを入力してください", "例) 1", $id),
                Elements::getInput("<power>\nアイテムの数を入力してください", "例) 5", $name),
                Elements::getToggle("削除する")
            ]
        ];
        $json = Form::encodeJson($data);
        return $json;
	}

	public function parse(string $content)
	{
        $args = explode(",", $content);
        if(!isset($args[1]) or (int)$args[1] <= 0) $args[1] = 1;
        if(is_numeric($args[0]))
        {
            $enchantment = Enchantment::getEnchantment((int)$args[0]);
        }
        else
        {
            $enchantment = Enchantment::getEnchantmentByName($args[0]);
        }
        if(!($enchantment instanceof Enchantment)) return false;
        return new EnchantmentInstance($enchantment, (int)$args[1]);
	}

	public function getEnchantment() : ?EnchantmentInstance
	{
		return $this->getValues();
	}

	public function setEnchantment(EnchantmentInstance $enchant)
	{
		$this->setValues($enchant);
	}

	public function execute()
	{
		$player = $this->getPlayer();
		$enchant = $this->getEnchantment();
		if(!($enchant instanceof EnchantmentInstance))
		{
			$player->sendMessage("§c[".$this->getName()."] 正しく入力できていません");
			return;
		}
		$item = $player->getInventory()->getItemInHand();
        $item->addEnchantment($enchant);
		$player->getInventory()->setItemInHand($item);
	}
}