<?php
namespace Thunder\Logeek\Traits;

use Thunder\Logeek\Board;

trait ActionTrait
    {
    /**
     * @var Board
     */
    private $board;

    public function __construct(Board $board)
        {
        $this->board = $board;
        }
    }
