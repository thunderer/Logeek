# Logeek

[![Build Status](https://travis-ci.org/thunderer/SimilarWebApi.png?branch=master)](https://travis-ci.org/thunderer/SimilarWebApi)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/5b82d37f-c410-4fb7-982c-ad495f488526/mini.png)](https://insight.sensiolabs.com/projects/5b82d37f-c410-4fb7-982c-ad495f488526)
[![License](https://poser.pugx.org/thunderer/similarweb-api/license.svg)](https://packagist.org/packages/thunderer/similarweb-api)
[![Latest Stable Version](https://poser.pugx.org/thunderer/similarweb-api/v/stable.svg)](https://packagist.org/packages/thunderer/similarweb-api)
[![Dependency Status](https://www.versioneye.com/php/thunderer:similarweb-api/badge.svg)](https://www.versioneye.com/php/thunderer:similarweb-api)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thunderer/SimilarWebApi/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/thunderer/SimilarWebApi/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/thunderer/SimilarWebApi/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/thunderer/SimilarWebApi/?branch=master)
[![Code Climate](https://codeclimate.com/github/thunderer/SimilarWebApi/badges/gpa.svg)](https://codeclimate.com/github/thunderer/SimilarWebApi)

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
