<?php
declare(strict_types=1);
namespace Thunder\Logeek\Action;

use Thunder\Logeek\ActionInterface;
use Thunder\Logeek\Board;

final class TypeSensorAction implements ActionInterface
{
    public function execute(Board $board, string $alias, array $operation)
    {
        list($newX, $newY) = $board->getActorNextMove($alias);
        $type = $board->getField($newX, $newY);
        $board->setVariable($operation['variable'], $type);
        $board->debug(sprintf('Scan Field[%s,%s] Type[%s]', $newX, $newY, $type));
    }

    public function getAlias(): string
    {
        return 'sensor-type';
    }

    public function getArguments(): array
    {
        return ['variable'];
    }
}
