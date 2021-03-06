<?php

namespace aieuo\ip\processes;

use aieuo\ip\ifPlugin;
use aieuo\ip\task\KickTask;

use aieuo\ip\form\Form;
use aieuo\ip\form\Elements;

class Kick extends Process {

	protected $id = self::KICK;
    protected $name = "キックする";
    protected $description = "プレイヤーを§7<reason>§fでキックする";

	public function getMessage() {
		$reason = $this->getReason();
		return "プレイヤーを".$reason."でキックする";
	}

	public function getReason() {
		return $this->getValues();
	}

	public function setReason(string $reason) {
		$this->setValues($reason);
	}

	public function execute() {
		$player = $this->getPlayer();
		$reason = $this->getReason();
        ifPlugin::getInstance()->getScheduler()->scheduleDelayedTask(new KickTask($player, $reason), 5);
	}

	public function getEditForm(string $default = "", string $mes = "") {
        $data = [
            "type" => "custom_form",
            "title" => $this->getName(),
            "content" => [
                Elements::getLabel($this->getDescription().(empty($mes) ? "" : "\n".$mes)),
                Elements::getInput("\n§7<reason>§f 理由を入力してください", "例) 悪いことをしたから", $default),
                Elements::getToggle("削除する"),
                Elements::getToggle("キャンセル")
            ]
        ];
        $json = Form::encodeJson($data);
        return $json;
	}

    public function parseFormData(array $datas) {
    	$status = true;
    	if($datas[1] === "") $status = null;
    	return ["status" => $status, "contents" => $datas[1], "delete" => $datas[2], "cancel" => $datas[3]];
    }
}