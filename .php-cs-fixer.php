<?php

if (PHP_SAPI !== 'cli') {
    die('This script supports command line usage only. Please check your command.');
}

$header = <<<EOF
 This file is part of the Flowpack.OpenSearch package.

 (c) Contributors of the Flowpack Team - flowpack.org

 This package is Open Source Software. For the full copyright and license
 information, please view the LICENSE file which was distributed with this
 source code.
EOF;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules(
        [
            '@DoctrineAnnotation' => true,
            '@PSR12' => true,
            'array_syntax' => ['syntax' => 'short'],
            'blank_line_after_opening_tag' => true,
            'braces' => ['allow_single_line_closure' => true],
            'cast_spaces' => ['space' => 'none'],
            'compact_nullable_typehint' => true,
            'concat_space' => ['spacing' => 'one'],
            'declare_equal_normalize' => ['space' => 'none'],
            'dir_constant' => true,
            'function_typehint_space' => true,
            'general_phpdoc_annotation_remove' => ['annotations' => ['author']],
            'lowercase_cast' => true,
            'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
            'modernize_types_casting' => true,
            'native_function_casing' => true,
            'new_with_braces' => true,
            'no_alias_functions' => true,
            'no_blank_lines_after_phpdoc' => true,
            'no_empty_phpdoc' => true,
            'no_empty_statement' => true,
            'no_extra_blank_lines' => true,
            'no_leading_import_slash' => true,
            'no_leading_namespace_whitespace' => true,
            'no_null_property_initialization' => true,
            'no_short_bool_cast' => true,
            'no_singleline_whitespace_before_semicolons' => true,
            'no_superfluous_elseif' => true,
            'no_trailing_comma_in_singleline' => true,
            'no_unneeded_control_parentheses' => true,
            'no_unused_imports' => true,
            'no_useless_else' => true,
            'no_whitespace_in_blank_line' => true,
            'ordered_imports' => [
                'imports_order' => [
                    'class',
                    'function',
                    'const',
                ],
                'sort_algorithm' => 'alpha',
            ],
            'php_unit_construct' => ['assertions' => ['assertEquals', 'assertSame', 'assertNotEquals', 'assertNotSame']],
            'php_unit_mock_short_will_return' => true,
            'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],
            'phpdoc_no_access' => true,
            'phpdoc_no_empty_return' => true,
            'phpdoc_no_package' => true,
            'phpdoc_scalar' => true,
            'phpdoc_trim' => true,
            'phpdoc_types' => true,
            'phpdoc_types_order' => ['null_adjustment' => 'always_last', 'sort_algorithm' => 'none'],
            'return_type_declaration' => ['space_before' => 'none'],
            'single_line_comment_style' => ['comment_types' => ['hash']],
            'single_quote' => true,
            'single_trait_insert_per_statement' => true,
            'whitespace_after_comma_in_array' => true,
            'header_comment' => [
                'header' => $header,
                'comment_type' => 'comment',
                'location' => 'after_open',
                'separate' => 'both'
            ],
        ]
    )
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('Documentation')
            ->in(__DIR__)
    );
