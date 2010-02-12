<?php

function identity ($x) {
	return $x;
}

function not ($a) {
	return ! $a;
}

function eq ($a, $b) {
	return $a == $b;
}

function ne ($a, $b) {
	return $a != $b;
}

function lt ($a, $b) {
	return $a < $b;
}

function le ($a, $b) {
	return $a <= $b;
}

function gt ($a, $b) {
	return $a > $b;
}

function ge ($a, $b) {
	return $a >= $b;
}

function odd ($a) {
	return ($a % 2 == 1);
}

function even ($a) {
	return not(odd($a));
}

function inc ($a) {
	return $a++;
}

function dec ($a) {
	return $a--;
}

function add ($a, $b) {
	return $a + $b;
}

function sub ($a, $b) {
	return $a - $b;
}

function mul ($a, $b) {
	return $a * $b;
}

function div ($a, $b) {
	return $a / $b;
}

function mod ($a, $b) {
	return $a % $b;
}

function concat ($a, $b) {
	return $a . $b;
}

function wrap ($left, $right, $s) {
	return $left . $a . $right;
}

function element ($k, $a) {
	return $a[$k];
}

function accessor ($key) {
	return partial(element, $key);
}

function lookup_in ($a) {
	return partial(flip(element), $a);
}

function first ($a) { return element(0, $a); }

function second ($a) { return element(1, $a); }

function any () {
	foreach (func_get_args() as $a) {
		if ($a === true) {
			return true;
		}
	}
	return false;
}

function all () {
	foreach (func_get_args() as $a) {
		if ($a === false) {
			return false;
		}
	}
	return true;
}

?>
