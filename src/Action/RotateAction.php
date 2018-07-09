<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Board;

final class RotateAction implements ActionInterface
{
    private static $rotateMap = [
        'left' => [
            'up' => 'left',
            'down' => 'right',
            'right' => 'up',
            'left' => 'down',
        ],
        'right' => [
            'up' => 'right',
            'down' => 'left',
            'right' => 'down',
            'left' => 'up',
        ],
    ];

    public function execute(Board $board, $alias, array $operation)
    {
        $direction = $board->getActorDirection($alias);
        $newDirection = static::$rotateMap[$operation['direction']][$direction];
        $board->debug('Rotate[%s] From[%s] To[%s]', $operation['direction'], $direction, $newDirection);
        $board->setActorDirection($alias, $newDirection);
    }

    public function getAlias()
    {
        return 'rotate';
    }

    public function getArguments()
    {
        return ['direction'];
    }
}
