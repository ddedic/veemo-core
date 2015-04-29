<?php namespace Veemo\Core\Command;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Routing\Redirector;


class InitializeApplication implements SelfHandling
{

    /**
     * Keep installed status around.
     *
     * @var bool
     */
    protected $installed = null;


    /**
     * Handle the command.
     *
     */
    public function handle()
    {

        $modules = app('veemo.modules');

        if (!$this->isInstalled() && app('request')->segment(1) !== 'installer') {

            $r = $modules->register('installer');


            app('router')->any(
                '{url?}',
                function (Redirector $redirector) {
                    return $redirector->to('installer');
                }
            )->where(['url' => '[-a-z0-9/]+']);

            return;
        }


        // SETUP Application

    }




    /**
     * Is the application installed?
     *
     * @return bool
     */
    public function isInstalled()
    {
        if (is_null($this->installed)) {
            $this->installed = (file_exists(base_path('.env')) && env('INSTALLED'));
        }

        return $this->installed;
    }


}
