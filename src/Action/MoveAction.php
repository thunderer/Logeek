<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Traits\ActionTrait;

class MoveAction implements ActionInterface
    {
    use ActionTrait;

    public function execute($alias, array $operation)
        {
        $length = $this->getMoveLength($operation);
        for($i = 0; $i < $length; $i++)
            {
            $this->board->moveActor($alias);
            }
        }

    private function getMoveLength(array $operation)
        {
        if(array_key_exists('length', $operation))
            {
            $length = $operation['length'];
            $this->board->debug('Move Length[%s]', $length);
            return $length;
            }
        if(array_key_exists('variable', $operation))
            {
            $length = $this->board->getVariable($operation['variable']);
            $this->board->debug('Move Variable[%s] Length[%s]', $operation['variable'], $length);
            return $length;
            }

        return null;
        }
    }
