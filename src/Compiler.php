<?php
namespace Thunder\Logeek;

class Compiler
    {
    public function compile(Board $board, $code)
        {
        $lines = explode("\n", trim($code));
        $contexts = array();
        $functionName = null;
        $functions = array();
        $forLoop = array();
        $levels = array();
        $forIterations = 0;
        $ifBranch = array();
        $elseBranch = array();
        $ifConditions = array();

        foreach($lines as $line)
            {
            if(!trim($line)) { continue; }
            $level = $this->getLineLevel($line);
            $this->debug('(Line: '.trim($line).') Level: '.$level);
            $tokens = explode(' ', trim($line));
            if(empty($contexts) || in_array($tokens[0], array('module', 'for', 'none')))
                {
                if('module' === $tokens[0])
                    {
                    $this->debug('Function: '.$tokens[1].' Level: '.$level);
                    array_unshift($contexts, 'module');
                    array_unshift($levels, $level);
                    $functionName = $tokens[1];
                    $functions[$functionName] = array();
                    }
                elseif('for' === $tokens[0])
                    {
                    $this->debug('Loop: for Level: '.$level);
                    array_unshift($contexts, 'for');
                    array_unshift($levels, $level);
                    $forLoop = array();
                    $forIterations = $tokens[1];
                    }
                elseif(in_array($tokens[0], array('if', 'else'), true))
                    {
                    $this->debug('If-else Level: '.$level);
                    array_unshift($contexts, 'if');
                    array_unshift($levels, $level);
                    $ifBranch = array();
                    $elseBranch = array();
                    $ifConditions = array('left' => $tokens[1], 'operator' => $tokens[2], 'right' => $tokens[3]);
                    }
                elseif('none' === $tokens[0])
                    {
                    $this->debug('none');
                    }
                }
            elseif('if' === $contexts[0] && !in_array($tokens[0], array('for')))
                {
                if($level == ($levels[0] + 1))
                    {
                    $this->debug('  Parse simple: '.trim($line));
                    $functions[$functionName] = array_merge($functions[$functionName], $this->parseSimple($board, $line));
                    }
                else
                    {
                    $this->debug('Leave function: '.$functionName);
                    array_shift($contexts);
                    array_shift($levels);
                    $functionName = null;
                    }
                }
            elseif('module' === $contexts[0] && !in_array($tokens[0], array('for')))
                {
                if($level == ($levels[0] + 1))
                    {
                    $this->debug('  Parse simple: '.trim($line));
                    $functions[$functionName] = array_merge($functions[$functionName], $this->parseSimple($board, $line));
                    }
                else
                    {
                    $this->debug('Leave function: '.$functionName);
                    array_shift($contexts);
                    array_shift($levels);
                    $functionName = null;
                    }
                }
            elseif('for' === $contexts[0])
                {
                if($level != ($levels[0] + 1))
                    {
                    array_shift($contexts);
                    $this->debug('  Leave loop: for Context: '.$contexts[0]);
                    array_shift($levels);
                    $functions[$functionName][] = array(
                        'action' => 'for',
                        'iterations' => $forIterations,
                        'program' => $forLoop,
                        );
                    }
                if('for' === $contexts[0])
                    {
                    $forLoop = array_merge($forLoop, $this->parseSimple($board, $line));
                    }
                else
                    {
                    $this->debug('For catch-line: '.$line);
                    $functions[$functionName] = array_merge($functions[$functionName], $this->parseSimple($board, $line));
                    }
                }
            }

        return $functions;
        }

    private function debug($message)
        {
        echo $message."\n";
        }

    private function getLineLevel($line)
        {
        return intval((strlen($line) - strlen(ltrim($line))) / 2);
        }

    private function parseSimple(Board $board, $line)
        {
        $tokens = explode(' ', trim($line));
        $number = 1;
        if(intval($tokens[0]))
            {
            $number = array_shift($tokens);
            }
        $action = array_shift($tokens);
        $args = $board->getAction($action)->getArguments();
        if(count($args) !== count($tokens))
            {
            throw new \RuntimeException(sprintf('Line [%s] mismatch: %s <> %s!', trim($line), json_encode($args), json_encode($tokens)));
            }
        $values = array_combine($args, $tokens);

        $ops = array();
        for($i = 0; $i < $number; $i++)
            {
            $ops[] = array_merge(array('action' => $action), $values);
            }

        return $ops;
        }
    }