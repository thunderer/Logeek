<?php
declare(strict_types=1);
namespace Thunder\Logeek;

interface ActionInterface
{
    public function execute(Board $board, string $alias, array $operation);

    public function getAlias(): string;

    public function getArguments(): array;
}
