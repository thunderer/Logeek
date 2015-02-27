<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Board;

class TypeSensorAction implements ActionInterface
    {
    public function execute(Board $board, $alias, array $operation)
        {
        list($newX, $newY) = $board->getActorNextMove($alias);
        $board->setVariable($operation['variable'], $board->getField($newX, $newY));
        }

    public function getAlias()
        {
        return 'sensor-type';
        }

    public function getArguments()
        {
        return array('variable');
        }
    }
