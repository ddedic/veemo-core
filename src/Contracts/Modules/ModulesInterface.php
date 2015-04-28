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

    public function install($slug, $enable);

    public function uninstall($slug);

    public function enable($slug);

    public function disable($slug);

    public function register($module);

    public function registerModules();

    public function getManager();

} 