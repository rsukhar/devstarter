<?php

class Inflector extends Kohana_Inflector {


	public static function ru_plural_form($n, $forms)
	{
		$n = intval($n);
		return ($n % 10 == 1 && $n % 100 != 11 ? $forms[0] : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? $forms[1] : $forms[2]));
	}

}