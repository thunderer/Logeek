<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Board;

class PickAction implements ActionInterface
    {
    private static $moveMap = array(
        'left' => array(0, -1),
        'right' => array(0, 1),
        'up' => array(-1, 0),
        'down' => array(1, 0),
        );

    public function execute(Board $board, $alias, array $operation)
        {
        $direction = $board->getActorDirection($alias);
        list($x, $y) = $board->getActorPosition($alias);
        $newX = $x + static::$moveMap[$direction][0];
        $newY = $y + static::$moveMap[$direction][1];

        if('up' === $operation['direction'])
            {
            $board->setField($newX, $newY, 'ground');
            $board->setActorPick($alias, 'brick');
            }
        elseif('down' === $operation['direction'])
            {
            $board->setField($newX, $newY, 'brick');
            $board->setActorPick($alias, null);
            }
        }

    public function getAlias()
        {
        return 'pick';
        }
    }
