<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Board;

final class WhileLoopAction implements ActionInterface
{
    private $iterations;

    public function __construct($iterations)
    {
        $this->iterations = $iterations;
    }

    public function execute(Board $board, $alias, array $operation)
    {
        $iteration = 0;
        while(true) {
            $left = (string)$board->getVariable($operation['left']);
            $board->debug(sprintf('While Evaluate %s %s %s', $left, $operation['operator'], $operation['right']));
            if($operation['operator'] === 'is' && $left === (string)$operation['right']) {
                $board->runActorProgram($alias, $operation['program']);
            } elseif($operation['operator'] === 'not' && $left !== (string)$operation['right']) {
                $board->runActorProgram($alias, $operation['program']);
            } else {
                $board->debug('While LoopEnd');
                break;
            }
            if($iteration > $this->iterations) {
                $board->debug('Iterations exceeded!');
                break;
            }
            $iteration++;
        }
    }

    public function getAlias()
    {
        return 'while';
    }

    public function getArguments()
    {
        return ['left', 'operator', 'right', 'program'];
    }
}
