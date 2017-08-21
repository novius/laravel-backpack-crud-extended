<?php

// Inspired from Laravel php_cs, converted to php-cs-fixer 2, and simplified.
//  - https://github.com/laravel/framework/blob/5.4/.php_cs
//  - http://cs.sensiolabs.org/
//  - https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/UPGRADE.md
// Removed: not_operator_with_successor_space, phpdoc_no_package, phpdoc_summary, unary_operator_spaces

$excludes = [
    'bootstrap/cache',
    'node_modules',
    'resources/lang',
    'storage',
    'vendor',
];

$rules = [
    '@PSR2' => true,
    'blank_line_after_opening_tag' => true,
    'braces' => true,
    'concat_space' => ['spacing' => 'none'],
    'no_multiline_whitespace_around_double_arrow' => true,
    'no_empty_statement' => true,
    'simplified_null_return' => true,
    'encoding' => true,
    'single_blank_line_at_eof' => true,
    'no_extra_consecutive_blank_lines' => true,
    'no_spaces_after_function_name' => true,
    'function_declaration' => true,
    'include' => true,
    'indentation_type' => true,
    'no_alias_functions' => true,
    'blank_line_after_namespace' => true,
    'line_ending' => true,
    'no_trailing_comma_in_list_call' => true,
    'lowercase_constants' => true,
    'lowercase_keywords' => true,
    'method_argument_space' => true,
    'trailing_comma_in_multiline_array' => true,
    'no_multiline_whitespace_before_semicolons' => true,
    'single_import_per_statement' => true,
    'no_leading_namespace_whitespace' => true,
    'no_blank_lines_after_class_opening' => true,
    'no_blank_lines_after_phpdoc' => true,
    'object_operator_without_whitespace' => true,
    'binary_operator_spaces' => true,
    'no_spaces_inside_parenthesis' => true,
    'phpdoc_indent' => true,
    'phpdoc_inline_tag' => true,
    'phpdoc_no_access' => true,
    'phpdoc_scalar' => true,
    'phpdoc_to_comment' => true,
    'phpdoc_trim' => true,
    'phpdoc_no_alias_tag' => true,
    'phpdoc_var_without_name' => true,
    'no_leading_import_slash' => true,
    'no_extra_consecutive_blank_lines' => true,
    'blank_line_before_return' => true,
    'self_accessor' => true,
    'array_syntax' => ['syntax' => 'short'],
    'no_short_echo_tag' => true,
    'full_opening_tag' => true,
    'no_trailing_comma_in_singleline_array' => true,
    'single_blank_line_before_namespace' => true,
    'single_line_after_imports' => true,
    'single_quote' => true,
    'no_singleline_whitespace_before_semicolons' => true,
    'cast_spaces' => true,
    'standardize_not_equals' => true,
    'ternary_operator_spaces' => true,
    'no_trailing_whitespace' => true,
    'trim_array_spaces' => true,
    'binary_operator_spaces' => ['align_equals' => false],
    'no_unused_imports' => true,
    'visibility_required' => true,
    'no_whitespace_in_blank_line' => true,
];

$finder = PhpCsFixer\Finder::create()
    ->name('*.php')
    ->exclude($excludes)
    ->notPath('server.php')
    ->ignoreDotFiles(true)
    ->in(__DIR__);

return PhpCsFixer\Config::create()
    ->setRules($rules)
    ->setFinder($finder)
    ->setUsingCache(true);
