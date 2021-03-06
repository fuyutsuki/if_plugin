<?php

namespace aieuo\ip\utils;

class Language {

    static $messages = [];

    public function __construct($messages) {
        $this->setMessages($messages);
    }

    public function setMessages($messages) {
        self::$messages = $messages;
    }

    public static function get(string $key, array $replaces = []) {
        if(isset(self::$messages[$key])) {
            $message = self::$messages[$key];
            foreach($replaces as $cnt => $value) {
                $message = str_replace("{%".$cnt."}", $value, $message);
            }
            $message = str_replace("\\n", "\n", $message);
            return $message;
        }
        return $key;
    }
}