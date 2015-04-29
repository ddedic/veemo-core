<?php namespace Veemo\Core\Models;

use Veemo\Core\Model\Event\ModelsWereDeleted;
use Veemo\Core\Model\Event\ModelsWereUpdated;
use Veemo\Core\Model\Event\ModelWasCreated;
use Veemo\Core\Model\Event\ModelWasDeleted;
use Veemo\Core\Model\Event\ModelWasRestored;
use Veemo\Core\Model\Event\ModelWasSaved;
use Veemo\Core\Model\Event\ModelWasUpdated;
use Veemo\Core\Support\Observer;

/**
 * Class BaseEloquentObserver
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Model
 */
class BaseEloquentObserver extends Observer
{

    /**
     * Run after a record is created.
     *
     * @param $model
     */
    public function created($model)
    {
        $model->flushCache();

        $this->events->fire(new ModelWasCreated($model));
    }

    /**
     * Run after saving a record.
     *
     * @param $model
     */
    public function saved($model)
    {
        $model->flushCache();

        $this->events->fire(new ModelWasSaved($model));
    }

    /**
     * Run after a record has been updated.
     *
     * @param $model
     */
    public function updated($model)
    {
        $model->flushCache();

        $this->events->fire(new ModelWasUpdated($model));
    }

    /**
     * Run after multiple records have been updated.
     *
     * @param $model
     */
    public function updatedMultiple($model)
    {
        $model->flushCache();

        $this->events->fire(new ModelsWereUpdated($model));
    }

    /**
     * Run after a record has been deleted.
     *
     * @param $model
     */
    public function deleted($model)
    {
        $model->flushCache();

        $this->events->fire(new ModelWasDeleted($model));
    }

    /**
     * Run after multiple records have been deleted.
     *
     * @param $model
     */
    public function deletedMultiple($model)
    {
        $model->flushCache();

        $this->events->fire(new ModelsWereDeleted($model));
    }

    /**
     * Run after a record has been restored.
     *
     * @param $model
     */
    public function restored($model)
    {
        $model->flushCache();

        $this->events->fire(new ModelWasRestored($model));
    }
}
