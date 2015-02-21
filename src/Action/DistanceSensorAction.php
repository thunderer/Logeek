<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Traits\ActionTrait;

class DistanceSensorAction implements ActionInterface
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
        $distance = 0;
        list($x, $y) = $this->board->getActorPosition($alias);
        $direction = $this->board->getActorDirection($alias);
        $diffX = static::$moveMap[$direction][0];
        $diffY = static::$moveMap[$direction][1];
        while('ground' === $this->board->getField($x + $diffX, $y + $diffY))
            {
            $this->board->debug('Scan Field[%s:%s] Field[%s:%s] Type[%s]',
                $y, $x, $y + $diffY, $x + $diffX,
                $this->board->getField($x + $diffX, $y + $diffY));
            $x += $diffX;
            $y += $diffY;
            $distance++;
            }
        $this->board->setVariable($operation['variable'], $distance);
        $this->board->debug('Scan Distance[%s] Variable[%s]', $distance, $operation['variable']);
        }
    }
