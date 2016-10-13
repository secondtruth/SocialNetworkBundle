<?php

$header = <<<'EOF'
This file is part of KibokoSocialNetworkBundle.

(c) Grégory Planchat <gregory@kiboko.fr>
    
Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
EOF;

Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader($header);

return Symfony\CS\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers(array(
        'combine_consecutive_unsets',
        'header_comment',
        'short_array_syntax',
        'no_useless_else',
        'no_useless_return',
        'ordered_use',
        'php_unit_construct',
        'php_unit_strict',
        'strict',
        'strict_param',
        'phpdoc_params',
        'phpdoc_scalar',
        'phpdoc_separation',
        'phpdoc_order',
    ))
    ->finder(
        Symfony\CS\Finder::create()
            ->in(__DIR__ . '/src')
    )
;
