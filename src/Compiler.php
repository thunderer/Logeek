<?php
namespace Thunder\Logeek;

class Compiler
    {
    public function compile(Board $board, $code)
        {
        $lines = explode("\n", trim($code));
        $tree = $this->buildTree($lines);
        $functions = array();
        foreach($tree as $fn)
            {
            $tokens = explode(' ', $fn['line']);
            if(!$fn['line'] || !$tokens) { continue; }
            $functions[$tokens[1]] = $this->compileTree($board, $fn['sub']);
            }

        return $functions;
        }

    private function compileTree(Board $board, array $tree)
        {
        $code = array();
        while(true)
            {
            if(!$tree) { break; }
            $line = array_shift($tree);
            $tokens = explode(' ', $line['line']);
            if(!$line['line'] || !$tokens) { continue; }
            switch($tokens[0])
                {
                case 'for':
                    {
                    $code[] = array(
                        'action' => 'for',
                        'iterations' => $tokens[1],
                        'program' => $this->compileTree($board, $line['sub']),
                        );
                    break;
                    }
                case 'none':
                    {
                    break;
                    }
                case 'if':
                    {
                    $else = array_shift($tree);
                    $code[] = array(
                        'action' => 'if',
                        'left' => $tokens[1],
                        'operator' => $tokens[2],
                        'right' => $tokens[3],
                        'true' => $this->compileTree($board, $line['sub']),
                        'false' => $this->compileTree($board, $else['sub']),
                        );
                    break;
                    }
                default:
                    {
                    $code = array_merge($code, $this->compileSimple($board, $tokens));
                    }
                }
            }

        return $code;
        }

    private function compileSimple(Board $board, array $tokens)
        {
        $action = array_shift($tokens);
        $number = 1;
        if(is_numeric($action))
            {
            $number = $action;
            $action = array_shift($tokens);
            }

        $code = [];
        for($i = 0; $i < $number; $i++)
            {
            $code[] = array_merge(array('action' => $action), array_combine($board->getAction($action)->getArguments(), $tokens));
            }

        return $code;
        }

    private function buildTree(array $lines)
        {
        $tree = array();
        while($lines)
            {
            $line = array_shift($lines);
            $level = $this->getLineLevel($line);
            $subLines = array();
            while($lines && $this->getLineLevel($lines[0]) > $level)
                {
                $subLines[] = array_shift($lines);
                }
            $tree[] = array(
                'line' => trim($line),
                'level' => $level,
                'sub' => $this->buildTree($subLines, $level + 1),
                );
            }

        return $tree;
        }

    private function getLineLevel($line)
        {
        return intval((strlen($line) - strlen(ltrim($line))) / 2);
        }
    }
