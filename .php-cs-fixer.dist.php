<?php

$finder = (new PhpCsFixer\Finder())
    ->in(['src', 'tests'])
    ->notPath([
        'Kernel.php',
        'bootstrap.php',
    ])
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'declare_strict_types' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'length'],
        'strict_param' => true,
        'single_quote' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arrays', 'arguments', 'parameters']],
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
;