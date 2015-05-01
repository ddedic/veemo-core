<?php namespace Veemo\Core\Command;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Routing\Redirector;
use Illuminate\Filesystem\Filesystem;


class CheckInstaller implements SelfHandling
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
    public function handle(Filesystem $files, Repository $config)
    {
        $modules = app('veemo.modules');



        // Check if we are already installed, otherwise redirect to installer
        if (!$this->isInstalled()) {

            if($modules->getManager()->exist('installer')) {
                if (!$modules->getManager()->isInstalled('installer')) {

                    // Install and enable Installer module
                    $modules->install('installer', true);

                } else {
                    // Enable installer module
                    $modules->enable('installer');
                }

            } else {
                dd('Installer module doesn\'t exist!');
            }


            // Redirect every other than installer url to installer module
            if (app('request')->segment(1) !== 'installer')
            {

                app('router')->any(
                    '{url?}',
                    function (Redirector $redirector) {
                        return $redirector->to('installer');
                    }
                )->where(['url' => '[-a-z0-9/]+']);

                return;

            }

        } else {

            // It is installed, remove/disable Installer module for security reasons
            $this->disableInstallerModule($files, $config);
        }


    }


    public function disableInstallerModule(Filesystem $files, Repository $config)
    {
        $modules = app('veemo.modules');

        if ($installer = $modules->getManager()->info('installer')) {

            // rename Installer module config file to something else, so it wont be registered anymore
            $configFilename = $config->get('veemo.modules.moduleConfigFilename');

            if ($files->exists($installer['path'] . '/' . $configFilename))
            {
                // Rename Installer module config file
                $files->move($installer['path'] . '/' . $configFilename, $installer['path'] . '/_disabled_' . $configFilename . '_disabled.bak');

                // Uninstall and remove from db
                $modules->uninstall('installer', true);

            }

        }
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
