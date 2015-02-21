<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Traits\ActionTrait;

class ForLoopAction implements ActionInterface
    {
    use ActionTrait;

    public function execute($alias, array $operation)
        {
        for($i = 0; $i < $operation['loops']; $i++)
            {
            $this->board->runActorProgram($alias, $operation['program']);
            }
        }
    }
