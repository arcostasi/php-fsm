<?php

namespace Arcostasi\PhpStateMachine;

// Load dependencies through composer
require './vendor/autoload.php';

use Arcostasi\PhpStateMachine\Stubs\EventEnum;
use Arcostasi\PhpStateMachine\Stubs\StateEnum;
use jagarsoft\StateMachine\StateMachine;

/* This sets the maximum time in seconds a script is
   allowed to run before it is terminated by the parser.
ini_set('max_execution_time', 60); */

$log = function (StateMachine $sm) {
    // Print the info state to standard output
    echo "My current state is {$sm->getCurrentState()}";
    echo " on event {$sm->getCurrentEvent()}";
    echo " and {$sm->getNextState()} will be the next state" . PHP_EOL;

    $data = $sm->getTransitionData(['DATA']);

    // Print current runtime
    echo mb_convert_case($sm->getCurrentState(), MB_CASE_TITLE, 'UTF-8') .
        " timer elapsed: {$data['DATA']['pause']} seconds" . PHP_EOL . PHP_EOL;
};

$pause = function (StateMachine $sm) {
    $data = $sm->getTransitionData(['DATA']);
    Sleep($data['DATA']['pause']);
};

/* Create a new state machine */
$sm = (new StateMachine())
    // Add the states
    ->addState(StateEnum::GREEN)
    ->addState(StateEnum::YELLOW)
    ->addState(StateEnum::RED)
    // Add the transition state GREEN to YELLOW
    ->addTransition(StateEnum::GREEN, EventEnum::CHANGE, StateEnum::YELLOW,
                            [
                                StateMachine::$EXEC_ACTION => $pause,
                                StateMachine::$EXEC_AFTER => $log,
                                'DATA' => ['pause' => 6]
                            ] )
    // Add the transition state YELLOW to RED
    ->addTransition(StateEnum::YELLOW, EventEnum::CHANGE, StateEnum::RED,
                            [
                                StateMachine::$EXEC_ACTION => $pause,
                                StateMachine::$EXEC_AFTER => $log,
                                'DATA' => ['pause' => 3]
                            ] )
    // Add the transition state RED to GREEN
    ->addTransition(StateEnum::RED, EventEnum::CHANGE, StateEnum::GREEN,
                            [
                                StateMachine::$EXEC_ACTION => $pause,
                                StateMachine::$EXEC_AFTER => $log,
                                'DATA' => ['pause' => 5]
                            ] );

/*
  Runs forever until user exits routine.
 */
// Print outgoing message
echo "[ Press <CTRL+C> to exit the loop ]" . PHP_EOL . PHP_EOL;

while (true) {
   // Just send CHANGE event
   $sm->fireEvent(EventEnum::CHANGE);
}
