<?php
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Board;

class ForLoopAction implements ActionInterface
    {
    public function execute(Board $board, $alias, array $operation)
        {
        for($i = 0; $i < $operation['iterations']; $i++)
            {
            $board->runActorProgram($alias, $operation['program']);
            }
        }

    public function getAlias()
        {
        return 'for';
        }

    public function getArguments()
        {
        return array('iterations');
        }
    }
