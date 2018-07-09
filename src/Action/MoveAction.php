<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Board;

final class MoveAction implements ActionInterface
{
    public function execute(Board $board, $alias, array $operation)
    {
        $length = $this->getMoveLength($board, $operation);
        for($i = 0; $i < $length; $i++) {
            $board->moveActor($alias);
        }
    }

    private function getMoveLength(Board $board, array $operation)
    {
        if(is_numeric($operation['distance'])) {
            $length = $operation['distance'];
            $board->debug('Move Length[%s]', $length);
            return $length;
        } else {
            $length = $board->getVariable($operation['distance']);
            $board->debug('Move Variable[%s] Length[%s]', $operation['distance'], $length);
            return $length;
        }
    }

    public function getAlias()
    {
        return 'move';
    }

    public function getArguments()
    {
        return ['distance'];
    }
}
