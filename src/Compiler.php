<?php
declare(strict_types=1);
namespace Thunder\Logeek;

final class Compiler
{
    public function compile(Board $board, $code): array
    {
        $lines = explode("\n", trim($code));
        $tree = $this->buildTree($lines);
        $functions = [];
        foreach($tree as $fn) {
            $tokens = explode(' ', $fn['line']);
            if(!$tokens || !$fn['line']) {
                continue;
            }
            $functions[$tokens[1]] = $this->compileTree($board, $fn['sub']);
        }

        return $functions;
    }

    private function compileTree(Board $board, array $tree): array
    {
        $code = [];
        while(true) {
            if(!$tree) {
                break;
            }
            $line = array_shift($tree);
            $tokens = explode(' ', $line['line']);
            if(!$tokens || !$line['line']) {
                continue;
            }
            switch($tokens[0]) {
                case 'for': {
                    $code[] = [
                        'action' => 'for',
                        'iterations' => $tokens[1],
                        'program' => $this->compileTree($board, $line['sub']),
                    ];
                    break;
                }
                case 'none': { break; }
                case 'if': {
                    $else = array_shift($tree);
                    $code[] = [
                        'action' => 'if',
                        'left' => $tokens[1],
                        'operator' => $tokens[2],
                        'right' => $tokens[3],
                        'true' => $this->compileTree($board, $line['sub']),
                        'false' => $this->compileTree($board, $else['sub']),
                    ];
                    break;
                }
                case 'while': {
                    $code[] = [
                        'action' => 'while',
                        'left' => $tokens[1],
                        'operator' => $tokens[2],
                        'right' => $tokens[3],
                        'program' => $this->compileTree($board, $line['sub']),
                    ];
                    break;
                }
                default: {
                    $code = array_merge($code, $this->compileSimple($board, $tokens));
                }
            }
        }

        return $code;
    }

    private function compileSimple(Board $board, array $tokens): array
    {
        $action = array_shift($tokens);
        $number = 1;
        if(is_numeric($action)) {
            $number = $action;
            $action = array_shift($tokens);
        }

        $code = [];
        for($i = 0; $i < $number; $i++) {
            $args = $board->getAction($action)->getArguments();
            if(\count($args) !== \count($tokens)) {
                throw new \RuntimeException(sprintf('Action args %s does not match tokens %s!', json_encode($args), json_encode($tokens)));
            }
            $code[] = array_merge(['action' => $action], array_combine($args, $tokens));
        }

        return $code;
    }

    private function buildTree(array $lines): array
    {
        $tree = [];
        while($lines) {
            $line = array_shift($lines);
            $level = $this->getLineLevel($line);
            $subLines = [];
            while($lines && $this->getLineLevel($lines[0]) > $level) {
                $subLines[] = array_shift($lines);
            }
            $tree[] = [
                'line' => trim($line),
                'level' => $level,
                'sub' => $this->buildTree($subLines),
            ];
        }

        return $tree;
    }

    private function getLineLevel($line): int
    {
        return (int)((\strlen($line) - \strlen(ltrim($line))) / 2);
    }
}
