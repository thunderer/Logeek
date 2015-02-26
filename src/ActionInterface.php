<?php
namespace Thunder\Logeek;

interface ActionInterface
    {
    public function execute(Board $board, $alias, array $operation);

    public function getAlias();
    }
