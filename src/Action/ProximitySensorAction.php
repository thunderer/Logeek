<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Board;

class ProximitySensorAction implements ActionInterface
    {
    public function execute(Board $board, $alias, array $operation)
        {
        list($newX, $newY) = $board->getActorNextMove($alias);
        $board->setVariable($operation['variable'], $board->getField($newX, $newY) === 'ground');
        }

    public function getAlias()
        {
        return 'sensor-proximity';
        }

    public function getArguments()
        {
        return array('variable');
        }
    }
