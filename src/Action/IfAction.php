<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Traits\ActionTrait;

class IfAction implements ActionInterface
    {
    use ActionTrait;

    public function execute($alias, array $operation)
        {
        switch($operation['condition'])
            {
            case 'variable-equal':
                {
                $this->board->getVariable($operation['variable']) === $operation['value']
                    ? $this->board->runActorProgram($alias, $operation['true'])
                    : $this->board->runActorProgram($alias, $operation['false']);
                break;
                }
            }
        }
    }
