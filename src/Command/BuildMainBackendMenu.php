<?php namespace Veemo\Core\Command;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Auth\Guard;

use Illuminate\Contracts\Foundation\Application;
use Menu;

class BuildMainBackendMenu implements SelfHandling
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
    public function handle(Guard $auth, Application $app)
    {
        \Debugbar::startMeasure('buildBackendMenu', 'Building Backend Main Menu');

        $modules = app('veemo.modules')->getManager()->installed()->enabled()->getModules();
        $backendMenu = Menu::make('backend_main', function(){});


            foreach ($modules as $module) {
                $moduleMenu = isset($module['config']['backend']['menu']) ? $module['config']['backend']['menu'] : null;

                if ($moduleMenu && is_array($moduleMenu) && (count($moduleMenu) > 0) && $this->evaluateMenuArray($moduleMenu)) {
                    foreach ($moduleMenu as $menuItemSlug => $menuItem) {

                        // @TODO here we should check if there already exist slug item, then append to it instead create new menu item

                        $backendMenu->add($menuItem['text'], $menuItem['link'])
                            ->icon($menuItem['icon'])
                            ->data('order', $menuItem['order'])
                            ->data('permissions', $menuItem['permissions']);

                        // @TODO maybe recursive function for unlimited nested submenus

                        $moduleSubMenu = isset($menuItem['children']) ? $menuItem['children'] : null;
                        if ($moduleSubMenu && is_array($moduleSubMenu) && (count($moduleSubMenu) > 0) && $this->evaluateMenuArray($moduleSubMenu)) {
                            foreach ($moduleSubMenu as $subMenuItemSlug => $subMenuItem) {

                                // @TODO again, slug camel_case, bullshit. what if translatable??

                                $subSlug = camel_case($menuItem['text']);
                                $backendMenu->$subSlug->add($subMenuItem['text'], $subMenuItem['link'])
                                    ->icon($subMenuItem['icon'])
                                    ->data('order', $subMenuItem['order'])
                                    ->data('permissions', $subMenuItem['permissions']);
                            }
                        }

                    }
                }


            }

            // SORT
            $backendMenu->sortBy('order', 'asc');

            //dd($app);
            //dd(app('events'));


            // FILTER
            //$backendMenu->filter(function($item) use ($auth) {
            //    return ($auth->user()->can($item->data('permissions')) ?: false);
            //});



        \Debugbar::stopMeasure('buildBackendMenu');

    }


    protected function evaluateMenuArray($menu)
    {

        $required = ['link', 'text', 'icon', 'order', 'permissions'];

        if (is_array($menu)) {

            foreach($menu as $menu_slug => $menu_item)
            {
                foreach ($required as $requiredField) {
                    if (is_array($menu_item)) {
                        if (!array_key_exists($requiredField, $menu_item)) {
                            return false;
                        }
                    }
                }
            }

            return true;
        }

        return false;
    }



}
