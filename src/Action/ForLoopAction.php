<?php
declare(strict_types=1);
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Board;

final class ForLoopAction implements ActionInterface
{
    public function execute(Board $board, string $alias, array $operation)
    {
        for($i = 0; $i < $operation['iterations']; $i++) {
            $board->runActorProgram($alias, $operation['program']);
        }
    }

    public function getAlias(): string
    {
        return 'for';
    }

    public function getArguments(): array
    {
        return ['iterations'];
    }
}
