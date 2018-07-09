<?php
declare(strict_types=1);
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Board;

final class FunctionAction implements ActionInterface
{
    public function execute(Board $board, string $alias, array $operation)
    {
        $board->runActorProgram($alias, $board->getFunction($operation['name']));
    }

    public function getAlias(): string
    {
        return 'function';
    }

    public function getArguments(): array
    {
        return ['name'];
    }
}
