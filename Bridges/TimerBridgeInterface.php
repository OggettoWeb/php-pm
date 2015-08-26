<?php

namespace PHPPM\Bridges;

interface TimerBridgeInterface
{
    public function timer(\PHPPM\ProcessSlave $process);
}
