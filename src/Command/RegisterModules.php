<?php namespace Veemo\Core\Command;

use Illuminate\Contracts\Bus\SelfHandling;


class RegisterModules implements SelfHandling
{

    /**
     * Handle the command.
     */
    public function handle()
    {
        $modules = app('veemo.modules');

        $modules->registerModules();
    }
}
