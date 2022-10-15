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

 /* Place this before next state you want to calculate time. */
$GLOBALS['start'] = 0;

/* Create event list in an array */
$eventList = [
   EventEnum::EVENT_1,
   EventEnum::EVENT_2,
   EventEnum::EVENT_3
];

/* State machine closure */
$ction = function (StateMachine $sm) {
    // Print the info state to standard output
    echo "My current state is {$sm->getCurrentState()}";
    echo " on event {$sm->getCurrentEvent()}";
    echo " and {$sm->getNextState()} will be the next state" . PHP_EOL;

    // Switch sleep for n seconds
    switch ($sm->getCurrentEvent()) {
        case EventEnum::EVENT_1: Sleep(6); // 6 seconds
            break;
        case EventEnum::EVENT_2: Sleep(3); // 3 seconds
            break;
        case EventEnum::EVENT_3: Sleep(5); // 5 seconds
            break;
    }

    /* Represents the current time in seconds since the
       Unix epoch accurate to the nearest microsecond. */
    $timeElapsedSecs = intval(microtime(true) - $GLOBALS['start']);

    // Print current runtime
    echo mb_convert_case($sm->getCurrentState(), MB_CASE_TITLE, 'UTF-8') .
        " timer elapsed: {$timeElapsedSecs} seconds" . PHP_EOL . PHP_EOL;
};

/* Create a new state machine */
$sm = (new StateMachine())
    // Add the states
    ->addState(StateEnum::STATE_1)
    ->addState(StateEnum::STATE_2)
    ->addState(StateEnum::STATE_3)
    // Add the transition state 1 to 2
    ->addTransition(StateEnum::STATE_1, EventEnum::EVENT_1,
                    StateEnum::STATE_2, $ction)
    // Add the transition state 2 to 3
    ->addTransition(StateEnum::STATE_2, EventEnum::EVENT_2,
                    StateEnum::STATE_3, $ction)
    // Add the transition state 3 to 1
    ->addTransition(StateEnum::STATE_3, EventEnum::EVENT_3,
                    StateEnum::STATE_1, $ction);

/*
  Runs forever until user exits routine.
 */
while (1) {
    // Scroll through the event list
    for ($next = 0; $next < sizeof($eventList); $next++) {
        /* Place this before next state you want to calculate time. */
        $GLOBALS['start'] = microtime(true);
        // Get the next event and trigger it
        $sm->fireEvent($eventList[$next]);
    }
    // Print outgoing message
    echo "[ Press <CTRL+C> to exit the loop ]" . PHP_EOL . PHP_EOL;
}
