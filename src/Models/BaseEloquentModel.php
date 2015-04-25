<?php namespace Veemo\Core\Models;

/**
 * Project: veemo.dev
 * User: ddedic
 * Email: dedic.d@gmail.com
 * Date: 24/04/15
 * Time: 21:02
 */


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesCommands;


class BaseEloquentModel extends Model implements Arrayable {

    use DispatchesCommands;


} 