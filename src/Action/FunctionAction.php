<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Board;

final class FunctionAction implements ActionInterface
{
    public function execute(Board $board, $alias, array $operation)
    {
        $board->runActorProgram($alias, $board->getFunction($operation['name']));
    }

    public function getAlias()
    {
        return 'function';
    }

    public function getArguments()
    {
        return ['name'];
    }
}
