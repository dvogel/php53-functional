<?php

// User beware! PHP was not designed to be a functional language.
// Excessive use of these functions will likely steal an entire
// order of magnitude of performance from your code. Use these
// when it's just plain too ugly or painful not to.
//
// e.g.
//
// $rows = $stmt->fetchAll(FETCH_ASSOC);
// $unique_codes = array_map(partial(element, "code"), $rows);
//
// is just better than
//
// $rows = $stmt->fetchAll(FETCH_ASSOC);
// $codes = array();
// foreach ($rows as $row) {
//     $codes[] = $row["code"];
// }
// $unique_codes = array_unique($codes);

require_once("operators.php");

function flip ($f) {
	// This function expects a binary function. It returns a
	// new function that calls the given function with its 
	// arguments reversed.
	return function ($b, $a) use ($f) {
		return $f($a, $b);
	};
}

function exclusive () {
	// Returns it's left most argument that is not null.
	// If all arguments are null, it returns null.
	$args = func_get_args();
	foreach ($args as $a) {
		if (! is_null($a)) {
			return $a;
		}
	}
	return null;
}

function lazy_exclusive () {
	// Expects it's arguments to be callable. It returns
	// the result of calling it's left most argument that
	// is not null. If each argument returns null then
	// it returns null. It is lazy because it will only
	// evaluate as many of it's arguments as needed to
	// reach a non-null result.
	try {
		foreach (func_get_args() as $a) {
			if (is_callable($a) && (arg_count($a) == 0)) {
				$b = $a();
				if (! is_null($b)) {
					return $b;
				}
			} elseif (! is_null($a)) {
				return $a;
			}
		}

		return null;
	} catch (ReflectionMethod $refl_ex) {
		throw new Exception("Parameters to lazy_exclusive() should be nullary (taking 0 arguments).");
	}
}

function arg_count($f) {
	// This function is used to determine the number of arguments 
	// expected by the given function.

	try {
		if (is_array($f)) {
			$refl_method = new ReflectionMethod($f[0], $f[1]);
			return $refl_method->getNumberOfParameters();
		} else {
			$refl_func = new ReflectionFunction($f);
			return $refl_func->getNumberOfParameters();
		}
	} catch (ReflectionException $refl_ex) {
		throw new Exception("The argument of arg_count is not a function or a method.");
	}
}

function partial () {
	// The partial function expect the first argument to be callable.
	// All subsequent arguments should be acceptable as the arguments,
	// in order, of the first argument. It returns a closure that calls
	// the first argument with the given arguments.
	// e.g. function flip($f) { return function ($b, $a) { return $f($a, $b); }; }
	//      function element($a, $k) { return $a[$k]; }
	//      $id_vals = array_map(partial(flip(element), "id"), 
	//                           $pdo_stmt->fetchAll())
	$args = func_get_args();
	if (count($args) < 2) {
		throw new Exception("partial() expects at least two argument");
	}

	$f = array_shift($args);
	if (! is_callable($f)) {
		throw new Exception("The first argument to partial() must be callable.");
	}

	$f_arg_cnt = -1;
	try {
		if (is_array($f)) {
			// A class method
			$refl_method = new ReflectionMethod($f[0], $f[1]);
			$f_arg_cnt = $refl_method->getNumberOfParameters();
		} else {
			$refl_func = new ReflectionFunction($f);
			$f_arg_cnt = $refl_func->getNumberOfParameters();
		}
	} catch (ReflectionException $refl_ex) {
		throw new Exception("The first argument to partial() must be callable.");
	}

	$arg_cnt = count($args);
	if (($arg_cnt > $f_arg_cnt) and ($f_arg_cnt != 0)) {
		// This allows nullary functions because PHP's reflection
		// API claims that variable arity functions expect 0 arguments.
		throw new Exception("Too many arguments passed to partial().");
	}

	return function () use ($f, $args) {
		return call_user_func_array($f, array_merge($args, func_get_args()));
	};
}

function compose ($f, $g) {
	// The compose function takes two unary functions as arguments. 
	// It returns a new unary function that calls the first argument 
	// on the result of calling the second function.
	// i.e. compose(eq, inc) == function ($x) { return eq(inc($x)); }

	if (! is_callable($f)) {
		throw new Exception("The first argument to compose() is not callable.");
	}

	if (! is_callable($g)) {
		throw new Exception("The second argument to compose() is not callable.");
	}

	$f_arg_cnt = arg_count($f);
	if ($f_arg_cnt > 2) {
		throw new Exception("The first argument to compose expects too many arguments.");
	}

	$g_arg_cnt = arg_count($g);
	if ($g_arg_cnt > 2) {
		throw new Exception("The second argument to compose expects too many arguments.");
	}

	return function ($a) use ($f, $g) {
		return $f($g($a));
	};
}

function compose_many () {
	$args = func_get_args();
	return array_reduce($args, compose, identity);
}

function bifurcate ($xs, $f) {
	$left = array_filter($xs, $f);
	$right = array_filter($xs, partial(not, $f));
	return array($left, $right);
}

function mirror ($xs) {
	$ys = array();
	foreach ($xs as $x) {
		$ys[$x] = $x;
	}
	return $ys;
}

?>
