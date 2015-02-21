<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Traits\ActionTrait;

class FunctionAction implements ActionInterface
    {
    use ActionTrait;

    public function execute($alias, array $operation)
        {
        $this->board->runActorProgram($alias, $this->board->getFunction($operation['name']));
        }
    }
