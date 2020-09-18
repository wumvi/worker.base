<?php
declare(ticks=1, strict_types=1);

namespace Wumvi\Worker;

abstract class Worker
{
    public const INFINITE = -1;
    public const NO_SIGNAL = -1;
    private bool $isStop = false;
    private int $signal = self::NO_SIGNAL;

    /**
     * @return void
     *
     * @throws WorkerStopException
     */
    public abstract function action(): void;

    private function sigHandler(int $signo)
    {
        if ($signo === $this->signal) {
            $this->gotSignal($signo);
            $this->stop();
        }
    }

    protected function gotSignal(int $signo): void
    {

    }

    protected function beforeAction(): void
    {

    }

    protected function afterAction(): void
    {

    }

    public function run(int $sleepInSecond, int $loopCount = self::INFINITE, $signal = self::NO_SIGNAL)
    {
        if ($this->signal === self::NO_SIGNAL && $signal !== self::NO_SIGNAL) {
            pcntl_signal($signal, [$this, 'sigHandler']);
            $this->signal = $signal;
        }
        $loopCountCurrent = $loopCount;
        $this->beforeAction();
        while (true) {
            if ($this->isStop || $loopCountCurrent === 0) {
                break;
            }

            try {
                $this->action();
                usleep($sleepInSecond * 1000000);
            } catch (WorkerStopException $ex) {
                $this->stop();
                break;
            }

            $loopCountCurrent -= 1;
        }
        $this->afterAction();
    }

    public function stop()
    {
        $this->isStop = true;
    }
}
