<?php

$finder = PhpCsFixer\Finder::create()
	->exclude('vendor')
	->exclude('node_modules')
	->in(__DIR__)
;

return PhpCsFixer\Config::create()
	->setRules([
		'@PSR2' => true,
		'@PHP71Migration:risky' => true,
		'array_syntax' => ['syntax' => 'short'],
		'blank_line_after_opening_tag' => true,
		'blank_line_before_statement' => true,
		'braces' => [
			'position_after_functions_and_oop_constructs' => 'next'
		],
		'cast_spaces' => [
			'space' => 'none'
		],
		'combine_consecutive_issets' => true,
		'combine_consecutive_unsets' => true,
		'compact_nullable_typehint' => true,
		'declare_strict_types' => false,
		'include' => true,
		'is_null' => true,
		'line_ending' => true,
		'list_syntax' => [
			'syntax' => 'short'
		],
		'mb_str_functions' => true,
		'method_argument_space' => [
			'ensure_fully_multiline' => true,
			'keep_multiple_spaces_after_comma' => false
		],
		'method_separation' => false,
		'no_alias_functions' => true,
		'no_empty_comment' => true,
		'no_empty_phpdoc' => true,
		'no_empty_statement' => true,
		'no_leading_namespace_whitespace' => true,
		'no_mixed_echo_print' => ['use' => 'echo'],
		'no_multiline_whitespace_around_double_arrow' => true,
		'no_multiline_whitespace_before_semicolons' => true,
		'no_php4_constructor' => true,
		'no_singleline_whitespace_before_semicolons' => true,
		'no_trailing_comma_in_list_call' => true,
		'no_trailing_comma_in_singleline_array' => true,
		'no_unneeded_control_parentheses' => [
			'statements' => ['break', 'clone', 'continue', 'echo_print', 'return', 'switch_case', 'yield']
		],
		'no_unneeded_curly_braces' => true,
		'no_whitespace_before_comma_in_array' => true,
		'no_whitespace_in_blank_line' => true,
		'trailing_comma_in_multiline_array' => false,
		'standardize_not_equals' => true
	])
	->setFinder($finder)
;
