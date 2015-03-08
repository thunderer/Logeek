# Logeek

[![Build Status](https://travis-ci.org/thunderer/Logeek.png?branch=master)](https://travis-ci.org/thunderer/Logeek)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/d64a8617-97e7-457f-ae3d-334310b8c4f5/mini.png)](https://insight.sensiolabs.com/projects/d64a8617-97e7-457f-ae3d-334310b8c4f5)
[![License](https://poser.pugx.org/thunderer/logeek/license.svg)](https://packagist.org/packages/thunderer/logeek)
[![Latest Stable Version](https://poser.pugx.org/thunderer/logeek/v/stable.svg)](https://packagist.org/packages/thunderer/logeek)
[![Dependency Status](https://www.versioneye.com/php/thunderer:logeek/badge.svg)](https://www.versioneye.com/php/thunderer:logeek)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thunderer/Logeek/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/thunderer/Logeek/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/thunderer/Logeek/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/thunderer/Logeek/?branch=master)
[![Code Climate](https://codeclimate.com/github/thunderer/Logeek/badges/gpa.svg)](https://codeclimate.com/github/thunderer/Logeek)

## Introduction

Logeek is a PHP engine for learning basic programming by simulating movement of element through a board. It's concepts are similar to [LEGO Mindstorms](http://www.lego.com/en-us/mindstorms/) or Android and iPhone games like [Robot School](https://play.google.com/store/apps/details?id=com.nextisgreat.robotschool).

## Requirements

Only PHP 5.3 (namespaces).

## Installation

This library is available on Packagist under alias `thunderer/logeek`.

## Usage

```php
// first create Board object with desired dimensions
$board = new Thunder\Logeek\Board(5, 5);
// then add field types and actions allowed on this board
$board->addFieldTypes(array('ground' => '.'));
$board->addActions(array(new MoveAction()));
// load board using field aliases
$board->loadFromString('...');
// add actors and exits
$board->addExit('exit', 2, 0);
$board->addActor('robot', 0, 0, 'right');
// add program for your actor, function "main" will be run
$compiler = new Compiler();
$functions = $compiler->compile($board, '
function main
  move 2
    ');
// run the simulation (print board size before and after)
echo $board->renderBoard();
$board->runActor('robot');
echo $board->renderBoard();
// check if everything is correct
assert(true === $board->isActorAtExit('robot', 'exit'));
```

Program syntax is a simple Python-like programming language with significant white-space (indentation of 2 spaces). Number as the first token in line means repeating that line that number of times. Currently implemented actions are as follows:

* Basic:

  * rotate left|right
  * move length|variable
  * pick up|down
  * open
  
* Loops:

  * for iterations
  * while variable is|not value
  
* Conditions:

  * if variable is|not value
  
* Structure:

  * function name
  
* Sensors:

  * sensor-type variable
  * sensor-distance variable
  * sensor-proximity variable

Sample simulations are implemented in `tests/LogeekTest.php`, it's easy to look, change and experiment with them.

## License

See LICENSE file in the root of this repository.
