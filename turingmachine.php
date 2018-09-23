#!/usr/bin/php
<?php
/**
 * PHP Turing Machine
 *
 * This is a PHP 7 CLI implementation of a Busy beaver class Turing machine.
 *
 * PHP version 7.2
 *
 * LICENSE:
 * MIT License
 *
 * Copyright (c) 2018 Joakim Winum Lien
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @author Joakim Winum Lien <joakim@winum.xyz>
 * @license https://opensource.org/licenses/mit-license.html MIT License
 * @version $Release: 1.0.0 $
 * @since File available since Release: 1.0.0
 */

/**
 * setup
 */

$timestampStart = time();

goto readStdin;
gotoReadStdin:
goto checkFlags;
gotoCheckFlags:
goto checkJsonFlag;
gotoCheckJsonFlag:
goto setDemo;
gotoSetDemo:

$stepCount = 0;
$stepJumpThreshold = 1*10**4; // 1*10^4
$stepJump = 5*10**4; // 5*10^4
$timestampStop = 0;
$initialTapeLength = 20;
$tapeLength = 1000;
$tape = array_fill(0, $initialTapeLength, 0); // initialize the tape
reset($tape);
$tmp = [];
$nextSate = "";
$gotoR = false;
$haltPrintTape = false;
$addNewLineOnTheEnd = true;
$showTape = false;

goto checkPrintTapeFlag;
gotoCheckPrintTapeFlag:

goto convertStatesArray;
gotoConvertStatesArray:

// set the first state
reset($states);
$state = key($states);

// start the Turing machine
goto machineLoop;

//------------------------------------------------------------------------------


/**
 * convert and error check the states array (this saves time)
 */

convertStatesArray:
$foundOneHalt = false;
foreach ($states as $input => $output) {
    if (strlen($input) !== 2) {
        echo "Error: Left side of the instructions block must have a length of 2.\n";
        goto quickExit;
    }

    if (strlen($output) !== 3) {
        echo "Error: Right side of the instructions block must have a length of 3.\n";
        goto quickExit;
    }

    $tmpRead = (string)substr($input, 0, 1);
    $stateIn = (string)substr($input, 1, 1);
    $tmpWrite = (int)substr($output, 0, 1);
    $tmpMove = (string)substr($output, 1, 1);
    $stateOut = (string)substr($output, 2, 1);

    if ($tmpRead !== "0" && $tmpRead !== "1") {
        echo "Error: Only use the numbers 0 and 1 for reading.\n";
        goto quickExit;
    }

    if ($tmpWrite !== 0 && $tmpWrite !== 1) {
        echo "Error: Only use the numbers 0 and 1 for writing.\n";
        goto quickExit;
    }

    if (is_string($stateIn) === false || is_string($stateOut) === false) {
        echo "Error: You have to provide a state letter.\n";
        goto quickExit;
    }

    $stateIn = strtoupper($stateIn);
    $tmpMove = strtoupper($tmpMove);
    $stateOut = strtoupper($stateOut);

    if ($tmpMove !== "L" && $tmpMove !== "R" && $tmpMove !== "N") {
        var_dump($tmpMove);
        echo "Error: Only use the the letters L, R or N for movements.\n";
        goto quickExit;
    }

    // check if there exist one and only one halt state
    if ($stateOut === "H") {
        if ($foundOneHalt === false) {
            $foundOneHalt = true;
        } else {
            $foundOneHalt = false;
        }
    }

    $syntaxCheck["input"][] = $stateIn;
    if ($stateOut !== "H") {
        $syntaxCheck["output"][] = $stateOut;
    }

    $tmp[$stateIn][$tmpRead] = array($tmpWrite, $tmpMove, $stateOut);
}

if ($foundOneHalt === false) {
    echo "Error: The instructions must contain one and only one halt state.\n";
    goto quickExit;
}

foreach ($tmp as $symbol) {
    if (isset($symbol[0]) === false || isset($symbol[1]) === false) {
        echo "Error: The instructions must come in symbol pairs for each state.\n";
        goto quickExit;
    }
}

// run syntax check on the instructions
$syntaxCheck["input"] = array_unique($syntaxCheck["input"]);
$syntaxCheck["output"] = array_unique($syntaxCheck["output"]);
foreach ($syntaxCheck["output"] as $rightBlock) {

    $lookUpStates = array_search($rightBlock, $syntaxCheck["input"]);

    if ($lookUpStates === false) {
        echo "Error: The state in the instruction block on the right side is referring to a state on left side that does not exist.\n";
        goto quickExit;
    }
}

// replace the old states array with the converted one
$states = $tmp;
unset($tmp);

goto gotoConvertStatesArray;

//------------------------------------------------------------------------------


/**
 * allow files to be piped in
 */

readStdin:
$fileHandler = fopen("php://stdin", "r");
$stdin = "";
$streamRead = array($fileHandler);
$null = null; // temporary variable due to a Zend Engine limitation
$tv_sec = 0; // timeout variable in seconds
$tv_usec = 0; // timeout variable in microseconds

// check the stdin for any content
if (stream_select($streamRead, $null, $null, $tv_sec, $tv_usec) === 1) {
    while (feof($fileHandler) === false) {
        // when content is found, read it all into the stdin variable
        $stdin .= fgets($fileHandler);
    }
}

fclose($fileHandler);
goto gotoReadStdin;

//------------------------------------------------------------------------------


/**
 * check arguments and store flags data
 */

checkFlags:
// flags that can be passed
$flags = array(
    "--json" => false,
    "--print-tape" => false
);

// check each flag from argv if they are set
foreach ($flags as $flag => $flagValue) {
    if ($flagValue === false) {
        unset($lookForFlag);
        $lookForFlag = array_search($flag, $argv);
        if ($lookForFlag !== false) {
            // flag found
            $flags[$flag] = true;
        }
    }
}
goto gotoCheckFlags;

//------------------------------------------------------------------------------


/**
 * check for the json flag
 */

checkJsonFlag:
if ($flags["--json"] === true) {
    $inputArray = json_decode($stdin, true);

    // set states to input array content
    if (isset($inputArray) === true) {
        $states = $inputArray;
    }
}
goto gotoCheckJsonFlag;

//------------------------------------------------------------------------------


/**
 * check for the print tape flag
 */

checkPrintTapeFlag:
if ($flags["--print-tape"] === true) {
    // display the tape for each step
    $showTape = true;
}
goto gotoCheckPrintTapeFlag;

//------------------------------------------------------------------------------


/**
 * run the demo instructions if no instructions have been piped in and the json flag is not set
 */

setDemo:
if (isset($states) === false) {
    $demoFileContent = file_get_contents("examples/busybeaver-4-state-2-symbol.json", FILE_USE_INCLUDE_PATH);
    $states = json_decode($demoFileContent, true);

    if (isset($states) === false) {
        echo "Error\n";
        goto quickExit;
    }
}
goto gotoSetDemo;

//------------------------------------------------------------------------------


/**
 * adds more tape onto the tape, because it is suppose to be infinite
 */

addMoreTapeL:
$gotoR = false;
goto addMoreTape;

addMoreTapeR:
$gotoR = true;
goto addMoreTape;

addMoreTape:

$moreTape = array_fill(0, $tapeLength, 0);

if ($gotoR === true) {
    if ($showTape === false) echo "Out of tape: adding more (right).\n";
    $tape = array_merge($tape, $moreTape);
    $currentTapePosition = count($tape)-1-$tapeLength;
} else {
    if ($showTape === false) echo "Out of tape: adding more (left).\n";
    $currentTapePosition = key($tape) + $tapeLength;
    $tape = array_merge($moreTape, $tape);
}

// move the internal pointer to the correct place
reset($tape);
resetHeadPosition:
next($tape);
$tapeKey = key($tape);
if ($tapeKey < $currentTapePosition) goto resetHeadPosition;

// check if the internal pointer is set to the correct position
$tapeKey = key($tape);
if ($tapeKey !== $currentTapePosition) {
    echo "Error\n";
    goto gotoExit;
}

// return back
if ($gotoR === true) {
    goto gotoAddMoreTapeR;
} else {
    goto gotoAddMoreTapeL;
}

//------------------------------------------------------------------------------


/**
 * halt the Turing machine
 */

HALT:
if($showTape === false) {
    goto printTapeHalt;
}
gotoPrintTapeHalt:
$zeroes = count(array_keys($tape, 0));
$ones = count(array_keys($tape, 1));
$total = count($tape);
echo "HALT\n";
echo "Ones (Score): ".$ones."\n";
echo "Zeroes: ".$zeroes."\n";
echo "Tape length: ".$total."\n";
echo "Halt sequence: ".$stepCount."\n";
goto gotoExit;

//------------------------------------------------------------------------------


/**
 * print the tape
 */

printTapeHalt:
$haltPrintTape = true;
goto printTape;

printTapeMachineLoop:
$haltPrintTape = false;
goto printTape;

printTape:

$string = "";

foreach ($tape as $element) {
    $string .= $element;
}

if ($addNewLineOnTheEnd === true) {
    $string .= "\n";
}

echo $string;

// return back
if ($haltPrintTape === true) {
    goto gotoPrintTapeHalt;
} else {
    goto gotoPrintTapeMachineLoop;
}

//------------------------------------------------------------------------------


/**
 * the Turing machine loop
 */

machineLoop:
// read the tape at the current head position
$read = current($tape);

$print = "Sequence: ".$stepCount.", head read: ".$read.", current state: ".$state.", next state: ";

if (isset($nextState) === false) {
    $print .= "NULL\n";
} else {
    $state = $nextState;
    $print .= $state."\n";
}

if ($showTape === true) {
    goto printTapeMachineLoop;
} else {
    $modulus = $stepCount % $stepJump;
    if ($stepCount < $stepJumpThreshold || $modulus === 0) {
        echo $print;
    }
}
gotoPrintTapeMachineLoop:

if ($state === "H") {
    goto HALT;
}

$write = $states[$state][$read][0];
$move = $states[$state][$read][1];
$nextState = $states[$state][$read][2];

// write symbol to tape at current head position
$key = key($tape);
$tape[$key] = $write;

// tape movements
if ($move === "L") {
    // move tape to the left
    gotoAddMoreTapeL:
    $response = prev($tape);

    if (is_numeric($response) === false) {
        // end of tape, should not happen in a real Turing machine
        goto addMoreTapeL;
    }
} else if ($move === "R") {
    // move tape to the right
    gotoAddMoreTapeR:
    $response = next($tape);

    if (is_numeric($response) === false) {
        // end of tape, should not happen in a real Turing machine
        goto addMoreTapeR;
    }
} else if ($move === "N") {
    // do nothing
}

$stepCount += 1;
goto machineLoop;

//------------------------------------------------------------------------------


/**
 * exit and statistics printing
 */

gotoExit:
$timestampStop = time();
$totalTime = $timestampStop - $timestampStart;
echo "Time used in seconds: ".$totalTime."\n";
echo "Timestamp start: ".$timestampStart."\n";
echo "Timestamp stop: ".$timestampStop."\n";
quickExit:
exit();
