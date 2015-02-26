<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Board;

class IfAction implements ActionInterface
    {
    public function execute(Board $board, $alias, array $operation)
        {
        switch($operation['condition'])
            {
            case 'variable-equal':
                {
                $board->getVariable($operation['variable']) === $operation['value']
                    ? $board->runActorProgram($alias, $operation['true'])
                    : $board->runActorProgram($alias, $operation['false']);
                break;
                }
            }
        }

    public function getAlias()
        {
        return 'if';
        }
    }
