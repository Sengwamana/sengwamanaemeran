<?php

namespace Botble\Translation\Tokens;

interface TokenProviderInterface
{
    public function generateToken(string $source, string $target, string $text): string;
}
