<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Board;

final class IfAction implements ActionInterface
{
    public function execute(Board $board, $alias, array $operation)
    {
        $board->getVariable($operation['left']) === $operation['right']
            ? $board->runActorProgram($alias, $operation['true'])
            : $board->runActorProgram($alias, $operation['false']);
    }

    public function getAlias()
    {
        return 'if';
    }

    public function getArguments()
    {
        return ['left', 'operand', 'right', 'true', 'false'];
    }
}
