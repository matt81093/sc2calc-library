<?php

class Helper {

	public function makeProduct($input) {
		return json_decode($input);
	}

	public function doesContain($haystack, $needle) {
		return strpos($haystack, $needle) !== false;
	}

	public function toType($type) {
		$type = explode(" | ", $type);
		$strings = count($type);
		$msg = null;
		for($count = 0; $count < $strings; ++$count) {
			$type[$count] = constant($type[$count]);
			$msg |= $type[$count];
		}
		return $msg;
	}

	public static function toRace($input) {
		switch ($input) {
			case $input === "Terran":
				return Terran;
			case $input === "Zerg":
				return Zerg;
			case $input === "Protoss":
				return Protoss;
			case $input === "Raynor":
				return Raynor;
			case $input === "Kerrigan":
				return Kerrigan;
			case $input === "Artanis":
				return Artanis;
			case $input === "Swann":
				return Swann;
			case $input === "Zagara":
				return Zagara;
			case $input === "Vorazun":
				return Vorazun;
			case $input === "Karax":
				return Karax;
			case $input === "Abathur":
				return Abathur;
			case $input === "Alarak":
				return Alarak;
			case $input === "Nova":
				return Nova;
			case $input === "Stukov":
				return Stukov;
			case $input === "Fenix":
				return Fenix;
			case $input === "Dehaka":
				return Dehaka;
			case $input === "Han&Horner":
				return Han&Horner;
			case $input === "Tycus":
				return Tycus;
			case $input === "Zeratul":
				return Zeratul;
			case $input === "Stetmann":
				return Stetmann;
		}
	}

	public static function sortToArray($input) {
		if($input == 0) {
			return array();
		}
		$input = explode(" | ", $input);
		$strings = count($type);
		$msg = null;
		for($count = 0; $count < $strings; ++$count) {
			$input[$count] = new $input[$count];
			$msg[] = $input[$count];
		}
		return $msg;
		//$Product::$all[$count][$name] === $json;
	}

	public static function processProductArray() {
		global $validTerran; //, $validZerg, $validProtoss;
		global $validProducts;
		$validProducts = $validTerran; //array_merge($validTerran, $validZerg, $validProtoss);
		foreach($validProducts as $newProductname => $newProduct) {
			global ${$newProductname};
			${$newProductname} = new Product(json_decode($newProduct));
		}

		echo ("Completed Processing Product Array!" . "\n");
	}

	public function isRace() {
		return $this->race & (Protoss | Terran | Zerg);
	}
};
?>
