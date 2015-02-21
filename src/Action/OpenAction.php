<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Traits\ActionTrait;

class OpenAction implements ActionInterface
    {
    use ActionTrait;

    public function execute($alias, array $operation)
        {
        list($newX, $newY) = $this->board->getActorNextMove($alias);
        if('door' === $this->board->getField($newX, $newY))
            {
            $this->board->setField($newX, $newY, 'ground');
            $this->board->debug('Door Open[%s:%s]', $newY, $newX);
            return;
            }
        $this->board->debug('Door Open[%s:%s] Failed', $newY, $newX);
        }
    }
