<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Board;

class MoveAction implements ActionInterface
    {
    public function execute(Board $board, $alias, array $operation)
        {
        $length = $this->getMoveLength($board, $operation);
        for($i = 0; $i < $length; $i++)
            {
            $board->moveActor($alias);
            }
        }

    private function getMoveLength(Board $board, array $operation)
        {
        if(array_key_exists('length', $operation))
            {
            $length = $operation['length'];
            $board->debug('Move Length[%s]', $length);
            return $length;
            }
        if(array_key_exists('variable', $operation))
            {
            $length = $board->getVariable($operation['variable']);
            $board->debug('Move Variable[%s] Length[%s]', $operation['variable'], $length);
            return $length;
            }

        return null;
        }

    public function getAlias()
        {
        return 'move';
        }
    }
