<?php

namespace aieuo\ip\processes;

use aieuo\ip\ifPlugin;
use aieuo\ip\variable\Variable;

use aieuo\ip\form\Form;
use aieuo\ip\form\Elements;

class AddVariable extends Process
{
	public $id = self::ADD_VARIABLE;

	public function __construct($player = null, $variable = null)
	{
		parent::__construct($player);
		$this->setValues($variable);
	}

	public function getName()
	{
		"変数を追加する";
	}

	public function getDescription()
	{
		"§7<name>§rという名前で§7<value>§rという値の変数を追加する";
	}

	public function getVariable() :?Variable
	{
		return $this->getValues();
	}

	public function setVariable(Variable $variable)
	{
		$this->setValues($variable);
	}

	public function parse(string $default)
	{
        $datas = explode(",", $content);
        if(!isset($datas[1])) return false;
        return new Variable($datas[0], $datas[1]);
	}

	public function excute()
	{
		$player = $this->getPlayer();
		$varibale = $this->getVariable();
		if($variable === false)
		{
			$player->sendMessage("§c[".$this->getName()."] 正しく入力できていません");
			return;
		}
        ifPlugin::getInstance()->getVariableHelper()->add($variable);
	}

	public function getEditForm(string $defaults = "", string $mes = "")
	{
		$var = $this->parse($defaults);
		if($var === false)
		{
			$name = $defaults;
			$value = "";
			$mes = "§c正しく入力できていません§f";
		}
		else
		{
			$name = $var->getName();
			$value = $var->getValue();
		}
		if($mes !== "") $mes = "\n".$mes;
        $data = [
            "type" => "custom_form",
            "title" => $this->getName(),
            "content" => [
                Elements::getLabel($this->getDescription().$mes),
                Elements::getInput("<name>\n変数の名前を入力してください", "例) aieuo", $name),
                Elements::getInput("<value>\n変数の値を入力してください", "例) 1000", $value),
                Elements::getToggle("削除する")
            ]
        ];
        $json = Form::encodeJson($data);
        return $json;
	}
}