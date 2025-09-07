<?php
return (new PhpCsFixer\Config())
    ->setRules([
        'single_quote' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'no_trailing_whitespace' => true, // 行末の空白削除
        'indentation_type' => true,       // インデント揃える
        'blank_line_before_statement' => [     // 見やすい空行
            'statements' => ['return']
        ],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
            ->exclude(['vendor'])
    );
