<?php
/**
 * Project: veemo.dev
 * User: ddedic
 * Email: dedic.d@gmail.com
 * Date: 25/04/15
 * Time: 15:48
 */

namespace Veemo\Core\Contracts\Modules;


interface ModulesInterface {

    public function install($slug);

    public function uninstall($slug);

    public function enable($slug);

    public function disable($slug);

    public function register($slug);

    public function registerCoreModules();

    public function registerAddonModules();

    public function registerModules();

} 