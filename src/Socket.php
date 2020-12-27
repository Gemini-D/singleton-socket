<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Gemini\SingletonSocket;

use Closure;
use Gemini\SingletonSocket\Exception\ConnectionException;
use Gemini\SingletonSocket\Exception\RuntimeException;
use Gemini\SingletonSocket\Exception\TimeoutException;
use Hyperf\Engine\Channel;

class Socket implements SocketInterface
{
    /**
     * @var mixed
     */
    protected $socket;

    /**
     * @var Channel
     */
    protected $channel;

    /**
     * @var Closure
     */
    protected $closure;

    /**
     * @var float
     */
    protected $waitTimeout = 0;

    public function __construct(Closure $closure, float $waitTimeout = 0)
    {
        $this->channel = new Channel(1);
        $this->closure = $closure;

        $this->channel->push($this->closure->__invoke());
    }

    public function call(Closure $closure)
    {
        try {
            $socket = $this->channel->pop($this->waitTimeout);
            if ($socket === false) {
                if ($this->channel->isTimeout()) {
                    throw new TimeoutException('Socket pop timeout.');
                }

                throw new RuntimeException('Socket pop failed.');
            }

            return $closure($socket);
        } catch (ConnectionException $exception) {
            return $closure($socket = $this->closure->__invoke());
        } finally {
            $socket && $this->channel->push($socket);
        }
    }
}
