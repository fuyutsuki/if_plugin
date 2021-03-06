<?php

namespace aieuo\ip\form;

use aieuo\ip\ifAPI;
use aieuo\ip\ifPlugin;

use aieuo\ip\Session;
use aieuo\ip\form\BlockForm;
use aieuo\ip\form\Form;
use aieuo\ip\utils\Messages;

class ChainIfForm {
    public function getSelectActionForm(){
        $data = [
            "type" => "form",
            "title" => "chain > 操作選択",
            "content" => "§7ボタンを押してください",
            "buttons" => [
                Elements::getButton("追加"),
                Elements::getButton("編集"),
                Elements::getButton("削除"),
                Elements::getButton("一覧"),
                Elements::getButton("キャンセル"),
                Elements::getButton("ひとつ前の画面に戻る")
            ]
        ];
        $json = Form::encodeJson($data);
        return $json;
    }

    public function onSelectAction($player, $data) {
        if($data === null) return;
        $session = Session::get($player);
        switch ($data) {
            case 0:
                $session->setData("action", "add");
                $form = $this->getAddChainIfForm();
                Form::sendForm($player, $form, $this, "onAddChainIf");
                break;
            case 1:
                $session->setData("action", "edit");
                $form = $this->getEditChainIfForm();
                Form::sendForm($player, $form, $this, "onEditChainIf");
                break;
            case 2:
                $session->setData("action", "del");
                $form = $this->getEditChainIfForm();
                Form::sendForm($player, $form, $this, "onEditChainIf");
                break;
            case 3:
                $form = $this->getChainIfListForm();
                Form::sendForm($player, $form, $this, "onChainIfList");
                break;
            case 4:
                $session->setValid(false);
                $player->sendMessage("キャンセルしました");
                return;
            case 5:
                $form = (new Form())->getSelectIfTypeForm();
                Form::sendForm($player, $form, new Form(), "onSelectIfType");
                return;
        }
        $session->setIfType(Session::CHAIN);
        $session->setValid();
    }

    public function getAddChainIfForm($mes = "") {
        $data = [
            "type" => "custom_form",
            "title" => "chain > 追加",
            "content" => [
                Elements::getInput(($mes !== "" ? $mes."\n" : "")."連携時に使う名前を入力してください", ""),
                Elements::getToggle("キャンセル")
            ]
        ];
        $json = Form::encodeJson($data);
        return $json;
    }

    public function onAddChainIf($player, $data) {
        $session = Session::get($player);
        if($data === null) {
            $session->setValid(false, false);
            return;
        }
        if($data[1]) {
            $form = $this->getSelectActionForm();
            Form::sendForm($player, $form, $this, "onSelectAction");
            return;
        }
        if($data[0] === ""){
            $form = $this->getAddChainIfForm("§c必要事項を入力してください§f");
            Form::sendForm($player, $form, $this, "onAddChainIf");
            $player->sendMessage("必要事項を入力してください");
            return;
        }
        $manager = ifPlugin::getInstance()->getChainManager();
        if($manager->isAdded($data[0])) {
            $form = $this->getAddChainIfForm("§cその名前は既に使用されています§f");
            Form::sendForm($player, $form, $this, "onAddChainIf");
            $player->sendMessage("その名前は既に使用されています");
            return;
        }
        $session->setData("if_key", $data[0]);
        $datas = $manager->repairIF([]);
        $manager->set($data[0], $datas);
        $mes = Messages::createMessage($datas["if"], $datas["match"], $datas["else"]);
        $form = (new Form)->getEditIfForm($mes);
        Form::sendForm($player, $form, new Form(), "onEditIf");
    }

    public function getEditChainIfForm($mes = "") {
        $data = [
            "type" => "custom_form",
            "title" => "chain > 編集",
            "content" => [
                Elements::getInput(($mes !== "" ? $mes."\n" : "")."編集する名前を入力してください", ""),
                Elements::getToggle("キャンセル")
            ]
        ];
        $json = Form::encodeJson($data);
        return $json;
    }

    public function onEditChainIf($player, $data) {
        $session = Session::get($player);
        if($data === null) {
            $session->setValid(false, false);
            return;
        }
        if($data[1]) {
            $form = $this->getSelectActionForm();
            Form::sendForm($player, $form, $this, "onSelectAction");
            return;
        }
        if($data[0] === ""){
            $form = $this->getAddChainIfForm("§c必要事項を入力してください§f");
            Form::sendForm($player, $form, $this, "onAddChainIf");
            $player->sendMessage("必要事項を入力してください");
            return;
        }
        $manager = ifPlugin::getInstance()->getChainManager();
        if(!$manager->isAdded($data[0])) {
            $form = $this->getAddChainIfForm("§cその名前の物は存在しません§f");
            Form::sendForm($player, $form, $this, "onAddChainIf");
            $player->sendMessage("その名前の物は存在しません");
            return;
        }
        $session->setData("if_key", $data[0]);
        $action = $session->getData("action");
        if($action === "edit") {
            $datas = $manager->repairIF([]);
            $mes = Messages::createMessage($datas["if"], $datas["match"], $datas["else"]);
            $form = (new Form)->getEditIfForm($mes);
            Form::sendForm($player, $form, new Form(), "onEditIf");
        } elseif($action === "del") {
            $form = (new Form())->getConfirmDeleteForm();
            Form::sendForm($player, $form, new Form(), "onDeleteIf");
        }
    }

    public function getChainIfListForm() {
        $datas = ifPlugin::getInstance()->getChainManager()->getAll();
        $buttons = [Elements::getButton("<ひとつ前のページに戻る>")];
        foreach ($datas as $name => $data) {
            $buttons[] = Elements::getButton($name);
        }
        $data = [
            "type" => "form",
            "title" => "編集",
            "content" => "§7ボタンを押してください",
            "buttons" => $buttons
        ];
        $json = Form::encodeJson($data);
        return $json;
    }

    public function onChainIfList($player, $data) {
        $session = Session::get($player);
        if($data === null) {
            $session->setValid(false, false);
            return;
        }
        if($data === 0) {
            $form = $this->getSelectActionForm();
            Form::sendForm($player, $form, $this, "onSelectAction");
            return;
        }
        $manager = ifPlugin::getInstance()->getChainManager();
        $ifs = array_slice($manager->getAll(), $data-1, 1, true);
        $key = key($ifs);
        $datas = current($ifs);
        $session->setData("if_key", $key);
        $mes = Messages::createMessage($datas["if"], $datas["match"], $datas["else"]);
        $form = (new Form)->getEditIfForm($mes);
        Form::sendForm($player, $form, new Form(), "onEditIf");
    }
}