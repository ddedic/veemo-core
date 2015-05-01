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
use Veemo\Core\Cache\CacheCollection;

class BaseEloquentModel extends Model implements Arrayable {

    use DispatchesCommands;


    /**
     * The number of minutes to cache query results.
     *
     * @var null
     */
    protected $cacheMinutes = false;



    /**
     * Observable model events.
     *
     * @var array
     */
    protected $observables = [
        'updatingMultiple',
        'updatedMultiple',
        'deletingMultiple',
        'deletedMultiple'
    ];


    /**
     * Boot the model.
     */
    protected static function boot()
    {
        self::observe(app(substr(__CLASS__, 0, -5) . 'Observer'));

        parent::boot();
    }


    /**
     * Set the cache minutes.
     *
     * @param  $cacheMinutes
     * @return $this
     */
    public function setCacheMinutes($cacheMinutes)
    {
        $this->cacheMinutes = $cacheMinutes;

        return $this;
    }

    /**
     * Get the cache minutes.
     *
     * @return int|mixed
     */
    public function getCacheMinutes()
    {
        return $this->cacheMinutes;
    }

    /**
     * Get cache collection key.
     *
     * @return string
     */
    public function getCacheCollectionKey()
    {
        return get_called_class();
    }

    /**
     * Flush the model's cache.
     *
     * @return $this
     */
    public function flushCache()
    {
        (new CacheCollection())->setKey($this->getCacheCollectionKey())->flush();

        return $this;
    }

    /**
     * Fire a model event.
     *
     * @param $event
     * @return mixed
     */
    public function fireEvent($event)
    {
        return $this->fireModelEvent($event);
    }

    /**
     * Get a new query builder for the model's table.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery()
    {
        $builder = new EloquentQueryBuilder($this->newBaseQueryBuilder());

        // Once we have the query builders, we will set the model instances so the
        // builder can easily access any information it may need from the model
        // while it is constructing and executing various queries against it.
        $builder->setModel($this)->with($this->with);

        return $this->applyGlobalScopes($builder);
    }




} 