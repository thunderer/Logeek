<?php
namespace Thunder\Logeek;

interface ActionInterface
{
    /**
     * @param Board $board
     * @param $alias
     * @param array $operation
     *
     * @return void
     */
    public function execute(Board $board, $alias, array $operation);

    /**
     * @return string
     */
    public function getAlias();

    /**
     * @return array
     */
    public function getArguments();
}
