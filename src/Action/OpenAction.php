<?php
declare(strict_types=1);
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Board;

final class OpenAction implements ActionInterface
{
    public function execute(Board $board, string $alias, array $operation)
    {
        list($newX, $newY) = $board->getActorNextMove($alias);
        if('door' === $board->getField($newX, $newY)) {
            $board->setField($newX, $newY, 'ground');
            $board->debug('Door Open[%s:%s]', $newY, $newX);
            return;
        }
        $board->debug('Door Open[%s:%s] Failed', $newY, $newX);
    }

    public function getAlias(): string
    {
        return 'open';
    }

    public function getArguments(): array
    {
        return [];
    }
}
