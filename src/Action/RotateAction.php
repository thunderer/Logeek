<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Traits\ActionTrait;

class RotateAction implements ActionInterface
    {
    use ActionTrait;

    private static $rotateMap = array(
        'left' => array(
            'up' => 'left',
            'down' => 'right',
            'right' => 'up',
            'left' => 'down',
            ),
        'right' => array(
            'up' => 'right',
            'down' => 'left',
            'right' => 'down',
            'left' => 'up',
            ),
        );

    public function execute($alias, array $operation)
        {
        $direction = $this->board->getActorDirection($alias);
        $newDirection = static::$rotateMap[$operation['direction']][$direction];
        $this->board->debug('Rotate[%s] From[%s] To[%s]', $operation['direction'], $direction, $newDirection);
        $this->board->setActorDirection($alias, $newDirection);
        }
    }
