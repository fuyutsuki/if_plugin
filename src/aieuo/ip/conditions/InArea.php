<?php

namespace aieuo\ip\conditions;

use pocketmine\math\Vector3;

use aieuo\ip\form\Form;
use aieuo\ip\form\Elements;

class InArea extends Condition {

	protected $id = self::IN_AREA;
    protected $name = "指定した範囲内にいたら";
    protected $description = "プレイヤーが§7<x|y|z>§r軸が§7<min>§r～§7<max>§rの範囲にいたら";

	public function __construct($player = null, $area = false) {
		parent::__construct($player);
		$this->setValues($area);
	}

	public function getMessage() {
		$areas = $this->getArea();
		if($areas === false) return false;
		$mes = [];
		foreach ($areas as $axis => $area) {
			$mes[] = $axis."軸が".$area[0]."~".$area[1]."の範囲";
		}
		return implode(",", $mes)."にいるなら";
	}

	public function getArea() {
		return $this->getValues();
	}

	public function setArea(Array $area) {
		$this->setValues($area);
	}

	public function parse(string $areas) {
        if(!preg_match("/([xyz]\(-?[0-9]+\.?[0-9]*,-?[0-9]+\.?[0-9]*\))+/", $areas, $matches)) return false;
        array_shift($matches);
        $areas = [];
        foreach ($matches as $match) {
            if(!preg_match("/([xyz])\((-?[0-9]+\.?[0-9]*),(-?[0-9]+\.?[0-9]*)\)/", $match, $matches1)) continue;
            $min = min((float)$matches1[2], (float)$matches1[3]);
            $max = max((float)$matches1[2], (float)$matches1[3]);
            $areas[$matches1[1]] = [$min, $max];
        }
        return $areas;
	}

	public function check() {
		$player = $this->getPlayer();
		$areas = $this->getArea();
		if($areas === false) {
			$player->sendMessage("§c[指定した範囲内にいたら] 正しく入力できていません§f");
			return self::ERROR;
		}
        foreach ($areas as $axis => $area) {
            if($player->$axis < $area[0] and $player->$axis > $area[1]) return self::NOT_MATCHED;
        }
        return self::MATCHED;
	}

	public function getEditForm(string $default = "", string $mes = "") {
		$areas = $this->parse($default);
		if($areas === false) {
			$areas = ["x" => $default, "y" => "", "z" => ""];
			if($default !== "") $mes .= "§c正しく入力できていません§f";
		}

		$content = [Elements::getLabel($this->getDescription().(empty($mes) ? "" : "\n".$mes))];
		foreach (["x", "y", "z"] as $axis) {
			if(!isset($areas[$axis])) $areas[$axis] = "";
			if(is_array(($areas[$axis]))) $areas[$axis] = implode(",", $areas[$axis]);
			$content[] = Elements::getInput("\n§7<".$axis.">§f ".$axis."軸の範囲を入力してください (指定しない場合は空白で)", "例) 0,100", $areas[$axis]);
		}
		$content[] = Elements::getToggle("削除する");
		$content[] = Elements::getToggle("キャンセル");
        $data = [
            "type" => "custom_form",
            "title" => $this->getName(),
            "content" => $content
        ];
        $json = Form::encodeJson($data);
        return $json;
	}

    public function parseFormData(array $datas) {
    	$status = true;
    	$area_str = $datas[1] !== "" ? "x(".$datas[1].")" : "";
    	$area_str .= $datas[2] !== "" ? "y(".$datas[2].")" : "";
    	$area_str .= $datas[3] !== "" ? "z(".$datas[3].")" : "";
    	if($datas[1] === "" and $datas[2] === "" and $datas[3] === "") {
    		$status = null;
    	} else {
	    	$areas = $this->parse($area_str);
	    	if($areas == false) $status = false;
	    }
    	return ["status" => $status, "contents" => $area_str, "delete" => $datas[4], "cancel" => $datas[5]];
    }
}