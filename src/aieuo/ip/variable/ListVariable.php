<?php

namespace aieuo\ip\variable;

class ListVariable extends Variable {

	public $type = Variable::LIST;

	public function Addition(Variable $var, string $name = "result") {
		if($var->getType() !== Variable::LIST) {
			return new StringVariable("ERROR", "リストにリスト以外を足すことはできません");
		}
		$result = array_merge($this->getValue(), $var->getValue());
		return new ListVariable($name, $result);
	}

	public function Subtraction(Variable $var, string $name = "result") {
		if($var->getType() !== Variable::LIST) {
			return new StringVariable("ERROR", "リストからリスト以外を引くことはできません");
		}
		$result = array_diff($this->getValue(), $var->getValue());
		$result = array_values($result);
		return new ListVariable($name, $result);
	}

	public function Multiplication(Variable $var, string $name = "result") {
		if($var->getType() !== Variable::NUMBER) {
			return new StringVariable("ERROR", "リスト数字以外をかける事はできません");
		}
		$result = [];
		$max = (int)$var->getValue();
		for($i=0; $i<$max; $i ++) {
			$result = array_merge($result, $this->getValue());
		}
		return new ListVariable($name, $result);
	}

	public function Division(Variable $var, string $name = "result") {
		return new StringVariable("ERROR", "リストは割り算できません");
	}

	public function Modulo(Variable $var, string $name = "result") {
		return new StringVariable("ERROR", "リストは割り算できません");
	}

	public function getValueFromIndex($index) {
		if(!isset($this->value[$index])) return null;
		return $this->value[$index];
	}

	public function getCount() {
		return count($this->value);
	}

	public function toStringVariable() {
		$variable = new StringVariable($this->getName(), implode(", ", $this->getValue()));
		return $variable;
	}
}