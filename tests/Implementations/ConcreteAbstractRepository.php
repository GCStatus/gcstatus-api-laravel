<?php

namespace Tests\Implementations;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\AbstractRepository;
use App\Contracts\Repositories\AbstractRepositoryInterface;

class ConcreteAbstractRepository extends AbstractRepository implements AbstractRepositoryInterface
{
    /**
     * The related model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected Model $model;

    /**
     * Create a new class instance.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get the model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function model(): Model
    {
        return $this->model;
    }
}
