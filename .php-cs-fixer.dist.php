<?php

declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

return
    (new PhpCsFixer\Config())
        ->setFinder(
            PhpCsFixer\Finder::create()
                ->in([
                    __DIR__.'/src',
                    __DIR__.'/tests',
                ])
                ->append([
                    __FILE__,
                ])
        )
        ->setRules([
            '@PHP71Migration' => true,
            '@PHP71Migration:risky' => true,
            '@PhpCsFixer' => true,
            '@PhpCsFixer:risky' => true,
            '@PHPUnit60Migration:risky' => true,
            'blank_line_before_statement' => [
                'statements' => [
                    'case',
                    'continue',
                    'declare',
                    'default',
                    'return',
                    'throw',
                    'try',
                ],
            ],
            'date_time_immutable' => true,
            'final_class' => true,
            'fopen_flags' => ['b_mode' => true],
            'no_superfluous_phpdoc_tags' => [
                'allow_mixed' => true,
                'remove_inheritdoc' => true,
            ],
            'ordered_class_elements' => [
                'order' => [
                    'use_trait',
                    'constant_public',
                    'constant_protected',
                    'constant_private',
                    'property_public_static',
                    'property_protected_static',
                    'property_private_static',
                    'property_public',
                    'property_protected',
                    'property_private',
                    'construct',
                    'destruct',
                    'phpunit',
                    'method_public_static',
                    'method_protected_static',
                    'method_private_static',
                    'magic',
                    'method_public',
                    'method_protected',
                    'method_private',
                ],
            ],
            'ordered_imports' => ['imports_order' => ['class', 'function', 'const']],
            'php_unit_size_class' => true,
            'php_unit_strict' => false,
            'php_unit_test_case_static_method_calls' => ['call_type' => 'this'],
            'phpdoc_add_missing_param_annotation' => false,
            'phpdoc_to_comment' => false,
            'phpdoc_types_order' => ['null_adjustment' => 'always_last'],
            'phpdoc_var_without_name' => false,
            'random_api_migration' => false,
            'return_assignment' => false,
            'single_line_comment_style' => ['comment_types' => ['hash']],
            'static_lambda' => true,
        ])
    ;
