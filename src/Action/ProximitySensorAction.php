<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Traits\ActionTrait;

class ProximitySensorAction implements ActionInterface
    {
    use ActionTrait;

    public function execute($alias, array $operation)
        {
        list($newX, $newY) = $this->board->getActorNextMove($alias);
        $this->board->setVariable($operation['variable'], $this->board->getField($newX, $newY) === 'ground');
        }
    }
