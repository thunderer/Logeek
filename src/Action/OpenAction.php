<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Board;

class OpenAction implements ActionInterface
    {
    public function execute(Board $board, $alias, array $operation)
        {
        list($newX, $newY) = $board->getActorNextMove($alias);
        if('door' === $board->getField($newX, $newY))
            {
            $board->setField($newX, $newY, 'ground');
            $board->debug('Door Open[%s:%s]', $newY, $newX);
            return;
            }
        $board->debug('Door Open[%s:%s] Failed', $newY, $newX);
        }

    public function getAlias()
        {
        return 'open';
        }

    public function getArguments()
        {
        return array();
        }
    }
