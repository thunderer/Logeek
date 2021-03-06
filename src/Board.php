<?php
declare(strict_types=1);
namespace Thunder\Logeek;

final class Board
{
    private $width;
    private $height;
    private $fieldTypes = [];
    private $fields = [];
    private $actors = [];
    private $exits = [];
    private $functions = [];
    private $variables = [];
    private $actions = [];

    private static $moveMap = [
        'left' => [0, -1],
        'right' => [0, 1],
        'up' => [-1, 0],
        'down' => [1, 0],
    ];

    public function __construct(int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    /* --- METHODS --- */

    /**
     * @param ActionInterface[] $actions
     */
    public function addActions(array $actions)
    {
        foreach($actions as $action) {
            $this->addAction($action);
        }
    }

    public function addAction(ActionInterface $action)
    {
        $this->actions[$action->getAlias()] = $action;
    }

    public function getAction(string $alias): ActionInterface
    {
        if(!$this->hasAction($alias)) {
            throw new \RuntimeException(sprintf('Action %s does not exist!', $alias));
        }

        return $this->actions[$alias];
    }

    public function addFieldTypes(array $types)
    {
        foreach($types as $alias => $symbol) {
            $this->addFieldType($alias, $symbol);
        }
    }

    public function hasAction($alias): bool
    {
        return array_key_exists($alias, $this->actions);
    }

    public function addFieldType($alias, $symbol)
    {
        $this->fieldTypes[$alias] = $symbol;
    }

    public function loadFromString($string)
    {
        $types = array_flip($this->fieldTypes);
        $lines = explode("\n", trim($string));
        $height = \count($lines);
        $width = \strlen(trim($lines[0]));
        for($i = 0; $i < $height; $i++) {
            $line = trim($lines[$i]);
            for($j = 0; $j < $width; $j++) {
                $this->fields[$i][$j] = $types[$line[$j]];
            }
        }
    }

    public function addActor($alias, $x, $y, $direction)
    {
        $this->actors[$alias] = [
            'x' => $x,
            'y' => $y,
            'direction' => $direction,
            'pick' => null,
        ];
        $this->debug('Actor Alias[%s] Direction[%s] Position[%s:%s]', $alias, $direction, $y, $x);
    }

    public function addExit($alias, $x, $y)
    {
        $this->exits[$alias] = [
            'x' => $y,
            'y' => $x,
        ];
    }

    public function addFunctions(array $functions)
    {
        foreach($functions as $name => $program) {
            $this->functions[$name] = $program;
        }
    }

    public function addFunction($name, array $program)
    {
        $this->functions[$name] = $program;
    }

    public function isActorAtExit($actor, $exit): bool
    {
        $actor = $this->actors[$actor];
        $exit = $this->exits[$exit];

        return $actor['x'] === $exit['x'] && $actor['y'] === $exit['y'];
    }

    public function runActor($alias)
    {
        $this->runActorProgram($alias, $this->functions['main']);
    }

    public function runActorProgram($alias, array $program)
    {
        foreach($program as $operation) {
            $this->runActorOperation($alias, $operation);
        }
    }

    private function runActorOperation($alias, array $operation)
    {
        $this->getAction($operation['action'])->execute($this, $alias, $operation);
    }

    public function getActorNextMove($alias): array
    {
        list($x, $y) = $this->getActorPosition($alias);
        list($diffX, $diffY) = static::$moveMap[$this->getActorDirection($alias)];

        return [$x + $diffX, $y + $diffY];
    }

    public function setVariable($name, $value)
    {
        $this->variables[$name] = $value;
    }

    public function getVariable($name)
    {
        return $this->variables[$name];
    }

    public function getFunction($name)
    {
        return $this->functions[$name];
    }

    public function moveActor($alias)
    {
        $moveMap = [
            'left' => [0, -1],
            'right' => [0, 1],
            'up' => [-1, 0],
            'down' => [1, 0],
        ];

        $actor = $this->actors[$alias];
        list($x, $y) = $moveMap[$actor['direction']];
        $newX = $actor['x'] + $x;
        $newY = $actor['y'] + $y;

        $this->debug('Move [%s:%s] -> [%s:%s]', $actor['y'], $actor['x'], $newY, $newX);
        if('wall' === $this->fields[$newX][$newY]) {
            throw new \RuntimeException(sprintf('Wall at [%s, %s]', $newX, $newY));
        }

        $actor['x'] = $newX;
        $actor['y'] = $newY;
        $this->actors[$alias] = $actor;
    }

    public function debug(...$args)
    {
    }

    public function setField($x, $y, $field)
    {
        $this->fields[$x][$y] = $field;
    }

    public function getField($x, $y)
    {
        return $this->fields[$x][$y];
    }

    public function getActorDirection($alias)
    {
        return $this->actors[$alias]['direction'];
    }

    public function setActorDirection($alias, $direction)
    {
        $this->actors[$alias]['direction'] = $direction;
    }

    public function getActorPick($alias)
    {
        return $this->actors[$alias]['pick'];
    }

    public function setActorPick($alias, $pick)
    {
        $this->actors[$alias]['pick'] = $pick;
    }

    public function getActorPosition($alias): array
    {
        return [$this->actors[$alias]['x'], $this->actors[$alias]['y']];
    }

    public function renderBoard(): string
    {
        $return = '';
        $return .= "\n";
        for($i = 0; $i < $this->height; $i++) {
            for($j = 0; $j < $this->width; $j++) {
                $symbol = $this->fieldTypes[$this->fields[$i][$j]];
                $isActor = array_reduce($this->actors, function($state, array $item) use ($i, $j) {
                    $state += $item['x'] === $i && $item['y'] === $j;
                    return $state;
                }, 0);
                $isExit = array_reduce($this->exits, function($state, array $item) use ($i, $j) {
                    $state += $item['x'] === $i && $item['y'] === $j;
                    return $state;
                }, 0);
                if($isExit) {
                    $symbol = 'E';
                }
                if($isActor) {
                    $symbol = 'A';
                }
                $return .= $symbol;
            }
            $return .= "\n";
        }

        return $return;
    }

    /* --- GETTERS --- */

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }
}
