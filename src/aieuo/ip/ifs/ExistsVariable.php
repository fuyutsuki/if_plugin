<?php

namespace aieuo\ip\ifs;

use aieuo\ip\ifPlugin;

use aieuo\ip\form\Form;
use aieui\ip\form\Elements;

class ExistsVariable extends IFs
{
	public $id = self::EXISTS_VARIABLE;
	private $variable;

	public function __construct($player = null, $variable = "")
	{
		parent::__construct($player);
		$this->variable = $variable;
	}

	public function getName() : string
	{
		return "変数が存在するか";
	}

	public function getDescription()
	{
		return "変数§7<variable>§fが存在するか";
	}

	public function getEditForm(string $defaults = "", string $mes = "")
	{
		if($mes !== "") $mes = "\n".$mes;
        $data = [
            "type" => "custom_form",
            "title" => $this->getName(),
            "content" => [
                Elements::getLabel($this->getDescription().$mes),
                Elements::getInput("<variable>\n変数の名前を入力してください", "例) aieuo", $defaults),
                Elements::getToggle("削除する")
            ]
        ];
        $json = Form::encodeJson($data);
        return $json;
	}

	public function getVariableName() : string
	{
		return $this->variable;
	}

	public function setVariableName(string $variable)
	{
		$this->variable = $variable;
	}

	public function check()
	{
		if(ifPlugin::getInstance()->getVariableHelper()->exists($this->getVariableName())) return self::MATCHED;
		return self::NOT_MATCHED;
	}
}