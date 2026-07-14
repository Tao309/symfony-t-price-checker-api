<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->notPath([
        'config/bundles.php',
        'config/reference.php',
    ])
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
//        '@PSR12' => true,

//        'line_length_fixer' => [
//            'max_line_length' => 120,
//            'break_long_statements' => true,
//            'inline_short_lines' => true,
//        ],


//        'array_syntax' => ['syntax' => 'short'],
//        'concat_space' => ['spacing' => 'one'],
//        'increment_style' => ['style' => 'post'],
//        'no_extra_blank_lines' => ['tokens' => [
//            'extra',
//            'parenthesis_brace_block',
//            'square_brace_block',
//            'throw',
//            'use',
//        ]],
//        'no_superfluous_phpdoc_tags' => false,
//        'phpdoc_align' => false,
//        'phpdoc_annotation_without_dot' => false,
//        'trailing_comma_in_multiline_array' => false,
//        'yoda_style' => false
    ])
    ->setFinder($finder)
;
