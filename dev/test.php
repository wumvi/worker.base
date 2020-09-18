<?php

include __DIR__ . '/../vendor/autoload.php';

class Test extends \Wumvi\Worker\Worker
{
    public function action(): void
    {
        echo 1, PHP_EOL;
    }

    public function beforeAction(): void
    {
        echo 'before', PHP_EOL;
    }

    public function gotSignal(int $signo): void
    {
        echo 'gotSignal', PHP_EOL;
    }
}

$test = new Test();
$test->run(2, 5, SIGTERM);
