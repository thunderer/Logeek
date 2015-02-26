<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Board;

class DistanceSensorAction implements ActionInterface
    {
    private static $moveMap = array(
        'left' => array(0, -1),
        'right' => array(0, 1),
        'up' => array(-1, 0),
        'down' => array(1, 0),
        );

    public function execute(Board $board, $alias, array $operation)
        {
        $distance = 0;
        list($x, $y) = $board->getActorPosition($alias);
        $direction = $board->getActorDirection($alias);
        $diffX = static::$moveMap[$direction][0];
        $diffY = static::$moveMap[$direction][1];
        while('ground' === $board->getField($x + $diffX, $y + $diffY))
            {
            $board->debug('Scan Field[%s:%s] Field[%s:%s] Type[%s]',
                $y, $x, $y + $diffY, $x + $diffX,
                $board->getField($x + $diffX, $y + $diffY));
            $x += $diffX;
            $y += $diffY;
            $distance++;
            }
        $board->setVariable($operation['variable'], $distance);
        $board->debug('Scan Distance[%s] Variable[%s]', $distance, $operation['variable']);
        }

    public function getAlias()
        {
        return 'sensor-distance';
        }
    }
