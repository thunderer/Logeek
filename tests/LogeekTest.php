<?php
namespace Thunder\Logeek\Tests;

use PHPUnit\Framework\TestCase;
use Thunder\Logeek\Action\DistanceSensorAction;
use Thunder\Logeek\Action\ForLoopAction;
use Thunder\Logeek\Action\FunctionAction;
use Thunder\Logeek\Action\IfAction;
use Thunder\Logeek\Action\MoveAction;
use Thunder\Logeek\Action\OpenAction;
use Thunder\Logeek\Action\PickAction;
use Thunder\Logeek\Action\RotateAction;
use Thunder\Logeek\Action\TypeSensorAction;
use Thunder\Logeek\Action\WhileLoopAction;
use Thunder\Logeek\Board;
use Thunder\Logeek\Compiler;

final class LogeekTest extends TestCase
{
    public function testBoardMoves()
    {
        $board = new Board(7, 5);
        $board->addFieldTypes([
            'wall' => '#',
            'ground' => '.',
            'brick' => 'B',
        ]);
        $board->addActions([
            new MoveAction(),
            new RotateAction(),
            new PickAction(),
            new FunctionAction(),
        ]);
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
function pick-rotate
  pick up
  move 1
  2 rotate right
  pick down
  2 rotate right

function main
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
        $board->addFieldTypes([
            'wall' => '#',
            'ground' => '.',
            'door' => 'D',
        ]);
        $board->addActions([
            new MoveAction(),
            new RotateAction(),
            new PickAction(),
            new FunctionAction(),
            new ForLoopAction(),
            new DistanceSensorAction(),
            new OpenAction(),
        ]);
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
function move-rotate-open
  sensor-distance len0
  move len0
  rotate left
  open

function main
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
        $board->addFieldTypes([
            'wall' => '#',
            'ground' => '.',
            'red' => 'R',
            'green' => 'G',
        ]);
        $board->addActions([
            new MoveAction(),
            new RotateAction(),
            new DistanceSensorAction(),
            new TypeSensorAction(),
            new IfAction(),
        ]);
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
function main
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

    public function testBoardLoop()
    {
        $board = new Board(6, 5);
        $board->addFieldTypes([
            'wall' => '#',
            'ground' => '.',
            'red' => 'R',
            'green' => 'G',
        ]);
        $board->addActions([
            new MoveAction(),
            new RotateAction(),
            new DistanceSensorAction(),
            new TypeSensorAction(),
            new IfAction(),
            new WhileLoopAction(20),
        ]);
        $board->loadFromString(trim('
            ######
            #....#
            #.##.#
            #..#.#
            ######
            '));
        $board->addExit('exit', 2, 3);

        $compiler = new Compiler();
        $functions = $compiler->compile($board, '
function main
  sensor-type type0
  while type0 not exit
    sensor-distance len0
    while len0 not 0
      move 1
      sensor-distance len0
    rotate left
  rotate left
  move 1
            ');

        $board->addFunctions($functions);
        $board->addActor('bot', 3, 3, 'up');
        $board->runActor('bot');

        $this->assertEquals(6, $board->getWidth());
        $this->assertEquals(5, $board->getHeight());
        $this->assertTrue($board->isActorAtExit('bot', 'exit'));
    }
}
