<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Traits\ActionTrait;

class PickAction implements ActionInterface
    {
    use ActionTrait;

    private static $moveMap = array(
        'left' => array(0, -1),
        'right' => array(0, 1),
        'up' => array(-1, 0),
        'down' => array(1, 0),
        );

    public function execute($alias, array $operation)
        {
        $direction = $this->board->getActorDirection($alias);
        list($x, $y) = $this->board->getActorPosition($alias);
        $newX = $x + static::$moveMap[$direction][0];
        $newY = $y + static::$moveMap[$direction][1];

        if('up' === $operation['direction'])
            {
            $this->board->setField($newX, $newY, 'ground');
            $this->board->setActorPick($alias, 'brick');
            }
        elseif('down' === $operation['direction'])
            {
            $this->board->setField($newX, $newY, 'brick');
            $this->board->setActorPick($alias, null);
            }
        }
    }
