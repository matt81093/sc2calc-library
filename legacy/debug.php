<?php

Configure::debug 1;
/*
	echo var_dump($CommandCenter);
	echo json_last_error();
	echo json_last_error_msg();
*/


			/*
			$count = count($Product);
			for ($i = 0; $i < $count; ++$i){
				
			}
			foreach ($Product as $key => $value) {
				$key = $value;
			}
			*/
			
	public function DebugMe($arg = '') {
		throw_error(var_dump($arg));
	}
	
	public function DebugObject($arg = '') {
		throw_error(var_dump(get_object_vars($arg)));
	}
	
	/*
global $CommandCenter;
global $validProducts;
$size = count($validProducts);
for($n = 1; $n < $size; ++$n) {
	throw_error($n); 
	global ${$validProducts[$n]};
} */

//foreach ($validProducts as $productName => $Product) {
//	global ${$productName};
//}

?>