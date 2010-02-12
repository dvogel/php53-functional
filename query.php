<?php

function group_by ($xs, $f) {
	$ys = array();
	foreach ($xs as $x) {
		$k = call_user_func($f, $x);
		$ys[$k][] = $x;
	}
	return $ys;
}

function rekey ($xs, $f) {
	$ys = array();
	foreach ($xs as $x) {
		$ys[$f($x)] = $x;
	}
	return $ys;
}

function no_nulls ($xs) {
	return array_filter($xs, compose(not, is_null));
}

?>
