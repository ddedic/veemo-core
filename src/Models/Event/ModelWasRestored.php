<?php namespace Veemo\Core\Model\Event;

use Veemo\Core\Model\BaseEloquentModel;

/**
 * Class ModelWasRestored
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Veemo\Core\Model\Event
 */
class ModelWasRestored
{

    /**
     * The model object.
     *
     * @var mixed
     */
    protected $model;

    /**
     * Create a new ModelWasRestored instance.
     *
     * @param $model
     */
    function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Get the model object.
     *
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }
}
