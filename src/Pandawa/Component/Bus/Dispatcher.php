<?php
/**
 * This file is part of the Pandawa package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Pandawa\Component\Bus;

use Pandawa\Component\Message\QueueEnvelope;
use Illuminate\Bus\Dispatcher as LaravelDispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Dispatcher extends LaravelDispatcher
{
    /**
     * {@inheritdoc}
     */
    public function dispatchNow($message, $handler = null)
    {
        if ($message instanceof QueueEnvelope) {
            $message = $message->getCommand();
        }

        return parent::dispatchNow($message, $handler);
    }

    /**
     * {@inheritdoc}
     */
    protected function commandShouldBeQueued($message): bool
    {
        return $message instanceof QueueEnvelope || $message instanceof ShouldQueue;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandHandler($command): ?object
    {
        $handlerClass = $this->getHandlerClass($command);

        if (class_exists($handlerClass)) {
            return $this->container->make($handlerClass);
        }

        return parent::getCommandHandler($command);
    }

    /**
     * Get class handler.
     *
     * @param object $message
     *
     * @return string
     */
    private function getHandlerClass(object $message): string
    {
        $messageClass = get_class($message);

        return sprintf('%sHandler', $messageClass);
    }
}
