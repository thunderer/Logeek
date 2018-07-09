<?php
declare(strict_types=1);
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Board;

final class PickAction implements ActionInterface
{
    private static $moveMap = [
        'left' => [0, -1],
        'right' => [0, 1],
        'up' => [-1, 0],
        'down' => [1, 0],
    ];

    public function execute(Board $board, string $alias, array $operation)
    {
        $direction = $board->getActorDirection($alias);
        list($x, $y) = $board->getActorPosition($alias);
        $newX = $x + static::$moveMap[$direction][0];
        $newY = $y + static::$moveMap[$direction][1];

        if('up' === $operation['direction']) {
            $board->debug('Pick[up]');
            $board->setField($newX, $newY, 'ground');
            $board->setActorPick($alias, 'brick');
        } elseif('down' === $operation['direction']) {
            $board->debug('Pick[down]');
            $board->setField($newX, $newY, 'brick');
            $board->setActorPick($alias, null);
        }
    }

    public function getAlias(): string
    {
        return 'pick';
    }

    public function getArguments(): array
    {
        return ['direction'];
    }
}
