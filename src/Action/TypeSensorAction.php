<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Board;

class TypeSensorAction implements ActionInterface
    {
    public function execute(Board $board, $alias, array $operation)
        {
        list($newX, $newY) = $board->getActorNextMove($alias);
        $type = $board->getField($newX, $newY);
        $board->setVariable($operation['variable'], $type);
        $board->debug(sprintf('Scan Field[%s,%s] Type[%s]', $newX, $newY, $type));
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
