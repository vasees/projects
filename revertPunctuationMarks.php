<?php

function revertPunctuationMarks($str){
	
	$pattern = "~[^ёЁа-яА-Яa-zA-Z0-9]~u";
	$j = 0;
	
	preg_match_all($pattern, $str, $result);
	$reverse = array_reverse($result[0]);
	
	for ($i = 0; $i < strlen($str); $i++) {
		if ($str[$i] == $result[0][$j]) {
			$str[$i] = $reverse[$j];
			$j++;
		}
	}
	echo $str;
}