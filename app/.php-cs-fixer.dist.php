<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

// Ensure the finder only searches existing directories
$finder = (new Finder())
    ->in([
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->append([
        __DIR__ . '/bin/console',
    ])
    ->exclude([
        'var',
        'vendor',
    ])
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setRules([
        // Use the official Symfony rulesets
        '@Symfony' => true,
        '@Symfony:risky' => true,

        // Use modern PHP 8 migration rules
        '@PHP82Migration' => true,
        '@PHP80Migration:risky' => true,

        // Strict types enforcement (Optional but highly recommended)
        'declare_strict_types' => true,

        // Custom overrides or fine-tuning
        'yoda_style' => false, // Set to true if you prefer Yoda-style conditions
        'concat_space' => ['spacing' => 'one'],
        'single_line_throw' => false,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setParallelConfig(ParallelConfigFactory::detect()); // Enables fast, multi-core analysis
