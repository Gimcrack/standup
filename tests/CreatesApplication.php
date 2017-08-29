<?php

namespace Tests;

use Event;
use Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        Event::fake();
        Notification::fake();

        return $app;
    }
}
