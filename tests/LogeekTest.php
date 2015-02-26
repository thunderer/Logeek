<?php
namespace Thunder\Logeek\Tests;

use Thunder\Logeek\Action\DistanceSensorAction;
use Thunder\Logeek\Action\ForLoopAction;
use Thunder\Logeek\Action\FunctionAction;
use Thunder\Logeek\Action\IfAction;
use Thunder\Logeek\Action\MoveAction;
use Thunder\Logeek\Action\OpenAction;
use Thunder\Logeek\Action\PickAction;
use Thunder\Logeek\Action\RotateAction;
use Thunder\Logeek\Action\TypeSensorAction;
use Thunder\Logeek\Board;

class LogeekTest extends \PHPUnit_Framework_TestCase
    {
    public function testBoardMoves()
        {
        $board = new Board(7, 5);
        $board->addFieldTypes(array(
            'wall' => '#',
            'ground' => '.',
            'brick' => 'B',
            ));
        $board->addActions(array(
            new MoveAction(),
            new RotateAction(),
            new PickAction(),
            new FunctionAction(),
            ));
        $board->loadFromString(trim(''
            .'#######'."\n"
            .'#.#B.B#'."\n"
            .'#.#.#.#'."\n"
            .'#B..#.#'."\n"
            .'#######'."\n"
            ));
        $board->addExit('exit', 5, 3);
        $board->addFunction('pick-rotate', array(
            array('action' => 'pick', 'direction' => 'up'),
            array('action' => 'move', 'length' => 1),
            array('action' => 'rotate', 'direction' => 'right'),
            array('action' => 'rotate', 'direction' => 'right'),
            array('action' => 'pick', 'direction' => 'down'),
            array('action' => 'rotate', 'direction' => 'right'),
            array('action' => 'rotate', 'direction' => 'right'),
            ));
        $board->addActor('bot', 1, 1, 'up', array(
            array('action' => 'rotate', 'direction' => 'right'),
            array('action' => 'rotate', 'direction' => 'right'),
            array('action' => 'move', 'length' => 1),
            array('action' => 'function', 'name' => 'pick-rotate'),
            array('action' => 'rotate', 'direction' => 'left'),
            array('action' => 'move', 'length' => 2),
            array('action' => 'rotate', 'direction' => 'left'),
            array('action' => 'move', 'length' => 1),
            array('action' => 'function', 'name' => 'pick-rotate'),
            array('action' => 'rotate', 'direction' => 'right'),
            array('action' => 'move', 'length' => 1),
            array('action' => 'function', 'name' => 'pick-rotate'),
            array('action' => 'rotate', 'direction' => 'right'),
            array('action' => 'move', 'length' => 2),
            ));
        $board->runActor('bot');

        $this->assertEquals(7, $board->getWidth());
        $this->assertEquals(5, $board->getHeight());
        $this->assertTrue($board->isActorAtExit('bot', 'exit'));
        }

    public function testBoardSensors()
        {
        $board = new Board(7, 6);
        $board->addFieldTypes(array(
            'wall' => '#',
            'ground' => '.',
            'door' => 'D',
            ));
        $board->addActions(array(
            new MoveAction(),
            new RotateAction(),
            new PickAction(),
            new FunctionAction(),
            new ForLoopAction(),
            new DistanceSensorAction(),
            new OpenAction(),
            ));
        $board->loadFromString(trim(''
            .'#######'."\n"
            .'#...D.#'."\n"
            .'#D###.#'."\n"
            .'#.###.#'."\n"
            .'#.D.#.#'."\n"
            .'#######'."\n"
            ));
        $board->addExit('exit', 3, 4);
        $board->addFunction('move-rotate-open', array(
            array('action' => 'sensor-distance', 'variable' => 'len0'),
            array('action' => 'move', 'variable' => 'len0'),
            array('action' => 'rotate', 'direction' => 'left'),
            array('action' => 'open'),
            ));
        $board->addActor('bot', 4, 5, 'up', array(
            array('action' => 'for', 'loops' => 3, 'program' => array(
                array('action' => 'function', 'name' => 'move-rotate-open')
                )),
            array('action' => 'move', 'length' => 2),
            ));
        $board->runActor('bot');

        $this->assertEquals(7, $board->getWidth());
        $this->assertEquals(6, $board->getHeight());
        $this->assertTrue($board->isActorAtExit('bot', 'exit'));
        }

    public function testBoardOperators()
        {
        $board = new Board(7, 7);
        $board->addFieldTypes(array(
            'wall' => '#',
            'ground' => '.',
            'red' => 'R',
            'green' => 'G',
            ));
        $board->addActions(array(
            new MoveAction(),
            new RotateAction(),
            new DistanceSensorAction(),
            new TypeSensorAction(),
            new IfAction(),
            ));
        $board->loadFromString(trim(''
            .'#######'."\n"
            .'###R###'."\n"
            .'#....G#'."\n"
            .'###.###'."\n"
            .'###.###'."\n"
            .'###.###'."\n"
            .'#######'."\n"
            ));
        $board->addExit('exit', 1, 2);
        $board->addActor('bot', 5, 3, 'up', array(
            array('action' => 'sensor-distance', 'variable' => 'len0'),
            array('action' => 'move', 'variable' => 'len0'),
            array('action' => 'sensor-type', 'variable' => 'type0'),
            array('action' => 'if', 'condition' => 'variable-equal', 'variable' => 'type0', 'value' => 'red',
                'true' => array(
                    array('action' => 'rotate', 'direction' => 'right'),
                    array('action' => 'sensor-distance', 'variable' => 'len0'),
                    array('action' => 'move', 'variable' => 'len0'),
                    array('action' => 'sensor-type', 'variable' => 'type0'),
                    array('action' => 'if', 'condition' => 'variable-equal', 'variable' => 'type0', 'value' => 'green',
                        'true' => array(
                            array('action' => 'rotate', 'direction' => 'right'),
                            array('action' => 'rotate', 'direction' => 'right'),
                            array('action' => 'sensor-distance', 'variable' => 'len0'),
                            array('action' => 'move', 'variable' => 'len0'),
                            ),
                        'false' => array(),
                        ),
                    ),
                'false' => array()
                ),
            ));
        $board->runActor('bot');

        $this->assertEquals(7, $board->getWidth());
        $this->assertEquals(7, $board->getHeight());
        $this->assertTrue($board->isActorAtExit('bot', 'exit'));
        }
    }
