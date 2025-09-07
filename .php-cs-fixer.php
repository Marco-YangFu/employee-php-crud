<?php
return (new PhpCsFixer\Config())
    ->setRules([
        'single_quote' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'blank_line_before_statement' => [     // 見やすい空行
            'statements' => ['return']
        ],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
            ->exclude(['vendor'])
    );
