<?php
namespace Thunder\Logeek;

interface ActionInterface
    {
    public function __construct(Board $board);

    public function execute($alias, array $operation);
    }
