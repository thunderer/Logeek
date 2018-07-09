<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Board;

final class DistanceSensorAction implements ActionInterface
{
    private static $moveMap = [
        'left' => [0, -1],
        'right' => [0, 1],
        'up' => [-1, 0],
        'down' => [1, 0],
    ];

    public function execute(Board $board, $alias, array $operation)
    {
        $distance = 0;
        list($x, $y) = $board->getActorPosition($alias);
        $direction = $board->getActorDirection($alias);
        list($diffX, $diffY) = static::$moveMap[$direction];
        while ('ground' === $board->getField($x + $diffX, $y + $diffY)) {
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

    public function getArguments()
    {
        return ['variable'];
    }
}
