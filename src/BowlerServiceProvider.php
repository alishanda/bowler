<?php

namespace Vinelab\Bowler;

use Illuminate\Support\ServiceProvider;
use Vinelab\Bowler\Console\Commands\QueueCommand;
use Vinelab\Bowler\Console\Commands\ConsumeCommand;
use Vinelab\Bowler\Console\Commands\SubscriberCommand;
use Vinelab\Bowler\Console\Commands\ConsumerHealthCheckCommand;

/**
 * @author Ali Issa <ali@vinelab.com>
 * @author Kinane Domloje <kinane@vinelab.com>
 */
class BowlerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton('vinelab.bowler.registrator', function ($app) {
            return new RegisterQueues();
        });

        // Bind connection to env configuration
        $rbmqHost = config('queue.connections.rabbitmq.host');
        $rbmqPort = config('queue.connections.rabbitmq.port');
        $rbmqUsername = config('queue.connections.rabbitmq.username');
        $rbmqPassword = config('queue.connections.rabbitmq.password');
        $this->app->bind(Connection::class, function () use ($rbmqHost, $rbmqPort, $rbmqUsername, $rbmqPassword) {
            return new Connection($rbmqHost, $rbmqPort, $rbmqUsername, $rbmqPassword);
        });

        $this->app->bind(
            \Vinelab\Bowler\Contracts\BowlerExceptionHandler::class,
            $this->app->getNamespace().\Exceptions\Handler::class
        );

        //register command
        $commands = [
            QueueCommand::class,
            ConsumeCommand::class,
            SubscriberCommand::class,
            ConsumerHealthCheckCommand::class,
        ];
        $this->commands($commands);
    }
}
