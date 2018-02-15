<?php

$finder = PhpCsFixer\Finder::create()
	->exclude('vendor')
	->exclude('node_modules')
	->in(__DIR__)
;

return PhpCsFixer\Config::create()
	->setRules([
		'@PSR2' => true,
		'array_syntax' => ['syntax' => 'short'],
		'blank_line_before_statement' => true,
		'braces' => ['position_after_functions_and_oop_constructs' => 'same'],
		'trailing_comma_in_multiline_array' => false
	])
	->setFinder($finder)
;
