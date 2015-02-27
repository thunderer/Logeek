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
use Thunder\Logeek\Compiler;

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
        $board->loadFromString('
            #######
            #.#B.B#
            #.#.#.#
            #B..#.#
            #######
            ');
        $board->addExit('exit', 5, 3);

        $compiler = new Compiler();
        $functions = $compiler->compile($board, '
module pick-rotate
  pick up
  move 1
  2 rotate right
  pick down
  2 rotate right

module main
  2 rotate right
  move 1
  function pick-rotate
  rotate left
  move 2
  rotate left
  move 1
  function pick-rotate
  rotate right
  move 1
  function pick-rotate
  rotate right
  move 2
            ');

        $board->addFunctions($functions);
        $board->addActor('bot', 1, 1, 'up');
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
        $board->loadFromString(trim('
            #######
            #...D.#
            #D###.#
            #.###.#
            #.D.#.#
            #######
            '));
        $board->addExit('exit', 3, 4);

        $compiler = new Compiler();
        $functions = $compiler->compile($board, '
module move-rotate-open
  sensor-distance len0
  move len0
  rotate left
  open

module main
  for 3
    function move-rotate-open
  move 2
            ');

        $board->addFunctions($functions);
        $board->addActor('bot', 4, 5, 'up');
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
        $board->loadFromString(trim('
            #######
            ###R###
            #....G#
            ###.###
            ###.###
            ###.###
            #######
            '));
        $board->addExit('exit', 1, 2);

        $compiler = new Compiler();
        $functions = $compiler->compile($board, '
module main
  sensor-distance len0
  move len0
  sensor-type type0
  if type0 is red
    rotate right
    sensor-distance len0
    move len0
    sensor-type type0
    if type0 is green
      2 rotate right
      sensor-distance len0
      move len0
    else
      none
  else
    none
            ');

        $board->addFunctions($functions);
        $board->addActor('bot', 5, 3, 'up');
        $board->runActor('bot');

        $this->assertEquals(7, $board->getWidth());
        $this->assertEquals(7, $board->getHeight());
        $this->assertTrue($board->isActorAtExit('bot', 'exit'));
        }
    }
