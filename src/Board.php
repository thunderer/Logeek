<?php
namespace Thunder\Logeek;

class Board
    {
    private $width;
    private $height;
    private $fieldTypes = array();
    private $fields = array();
    private $actors = array();
    private $exits = array();
    private $functions = array();
    private $variables = array();
    private $actions = array();

    private static $moveMap = array(
        'left' => array(0, -1),
        'right' => array(0, 1),
        'up' => array(-1, 0),
        'down' => array(1, 0),
        );

    public function __construct($width, $height)
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
        foreach($actions as $action)
            {
            $this->addAction($action);
            }
        }

    public function addAction(ActionInterface $action)
        {
        $this->actions[$action->getAlias()] = $action;
        }

    public function addFieldTypes(array $types)
        {
        foreach($types as $alias => $symbol)
            {
            $this->addFieldType($alias, $symbol);
            }
        }

    public function addFieldType($alias, $symbol)
        {
        $this->fieldTypes[$alias] = $symbol;
        }

    public function loadFromString($string)
        {
        $types = array_flip($this->fieldTypes);
        $lines = explode("\n", $string);
        for($i = 0; $i < count($lines); $i++)
            {
            for($j = 0; $j < strlen($lines[0]); $j++)
                {
                $this->fields[$i][$j] = $types[$lines[$i][$j]];
                }
            }
        }

    public function addActor($alias, $x, $y, $direction, array $program)
        {
        $this->actors[$alias] = array(
            'x' => $x,
            'y' => $y,
            'direction' => $direction,
            'program' => $program,
            'pick' => null,
            );
        $this->debug('Actor Alias[%s] Direction[%s] Position[%s:%s]', $alias, $direction, $y, $x);
        }

    public function addExit($alias, $x, $y)
        {
        $this->exits[$alias] = array(
            'x' => $y,
            'y' => $x,
            );
        }

    public function addFunction($name, array $program)
        {
        $this->functions[$name] = $program;
        }

    public function isActorAtExit($actor, $exit)
        {
        $actor = $this->actors[$actor];
        $exit = $this->exits[$exit];

        return $actor['x'] === $exit['x'] && $actor['y'] === $exit['y'];
        }

    public function runActor($alias)
        {
        $actor = $this->actors[$alias];

        $this->runActorProgram($alias, $actor['program']);
        }

    public function runActorProgram($alias, array $program)
        {
        foreach($program as $operation)
            {
            $this->runActorOperation($alias, $operation);
            }
        }

    private function runActorOperation($alias, array $operation)
        {
        $class = $this->actions[$operation['action']];
        /** @var $action ActionInterface */
        $action = new $class($this);
        $action->execute($this, $alias, $operation);
        }

    public function getActorNextMove($alias)
        {
        list($x, $y) = $this->getActorPosition($alias);
        list($diffX, $diffY) = static::$moveMap[$this->getActorDirection($alias)];

        return array($x + $diffX, $y + $diffY);
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
        $moveMap = array(
            'left' => array(0, -1),
            'right' => array(0, 1),
            'up' => array(-1, 0),
            'down' => array(1, 0),
            );

        $actor = $this->actors[$alias];
        $x = $moveMap[$actor['direction']][0];
        $y = $moveMap[$actor['direction']][1];
        $newX = $actor['x'] + $x;
        $newY = $actor['y'] + $y;

        // echo $this->renderBoard();
        if('wall' === $this->fields[$newX][$newY])
            {
            throw new \RuntimeException(sprintf('Wall at [%s, %s]', $newX, $newY));
            }

        $actor['x'] = $newX;
        $actor['y'] = $newY;
        $this->actors[$alias] = $actor;
        $this->debug('Move [%s:%s] -> [%s:%s]', $actor['y'], $actor['x'], $newY, $newX);
        }

    public function debug()
        {
        /* $args = func_get_args();
        $message = array_shift($args);
        echo vsprintf($message, $args)."\n";
        echo $this->renderBoard(); */
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

    public function getActorPosition($alias)
        {
        return array($this->actors[$alias]['x'], $this->actors[$alias]['y']);
        }

    public function renderBoard()
        {
        $return = '';
        $return .= "\n";
        for($i = 0; $i < $this->height; $i++)
            {
            for($j = 0; $j < $this->width; $j++)
                {
                $symbol = $this->fieldTypes[$this->fields[$i][$j]];
                $isActor = array_reduce($this->actors, function($state, array $item) use($i, $j) {
                    $state += $item['x'] === $i && $item['y'] === $j;
                    return $state;
                    }, 0);
                $isExit = array_reduce($this->exits, function($state, array $item) use($i, $j) {
                    $state += $item['x'] === $i && $item['y'] === $j;
                    return $state;
                    }, 0);
                if($isExit) { $symbol = 'E'; }
                if($isActor) { $symbol = 'A'; }
                $return .= $symbol;
                }
            $return .= "\n";
            }

        return $return;
        }

    /* --- GETTERS --- */

    public function getWidth()
        {
        return $this->width;
        }

    public function getHeight()
        {
        return $this->height;
        }
    }
