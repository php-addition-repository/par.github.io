<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules(
        [
            '@PHP84Migration' => true,
            '@PHPUnit100Migration:risky' => true,
            '@Symfony' => true,
            'blank_line_before_statement' => [
                'statements' => [
                    'continue',
                    'declare',
                    'return',
                    'throw',
                    'try',
                ],
            ],
            'class_attributes_separation' => [
                'elements' => [
                    'const' => 'only_if_meta',
                    'method' => 'one',
                    'property' => 'only_if_meta',
                    'trait_import' => 'none',
                    'case' => 'none',
                ],
            ],
            'concat_space' => [
                'spacing' => 'one',
            ],
            'function_declaration' => [
                'closure_function_spacing' => 'none',
                'closure_fn_spacing' => 'none',
            ],
            'general_phpdoc_tag_rename' => [
                'replacements' => [
                    'template-implements' => 'implements',
                    'template-mixin' => 'mixin',
                    'template-extends' => 'extends',
                    'inheritDocs' => 'inheritDoc',
                ],
            ],
            'global_namespace_import' => [
                'import_classes' => true,
            ],
            'method_argument_space' => [
                'on_multiline' => 'ensure_fully_multiline',
            ],
            'phpdoc_align' => [
                'align' => 'left',
            ],
            'phpdoc_order' => [
                'order' => [
                    'deprecated',
                    'internal',
                    'see',
                    'template',
                    'extends',
                    'implements',
                    'mixin',
                    'var',
                    'param',
                    'return',
                    'throws',
                ],
            ],
            'phpdoc_separation' => [
                'groups' => [
                    ['Annotation', 'NamedArgumentConstructor', 'Target'],
                    ['author', 'copyright', 'license'],
                    ['category', 'package', 'subpackage'],
                    ['property', 'property-read', 'property-write'],
                    ['deprecated', 'link', 'see', 'since'],
                    ['mixin', 'extends', 'implements'],
                ],
            ],
            'php_unit_internal_class' => true,
            'php_unit_test_case_static_method_calls' => [
                'call_type' => 'self',
            ],
            'single_line_throw' => false,
        ]
    )
    ->setFinder($finder);
