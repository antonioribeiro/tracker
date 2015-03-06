<?php

namespace PragmaRX\Tracker\Data\Repositories;

abstract class Repository implements RepositoryInterface {

	protected $builder;

	protected $model;

	protected $result;

	protected $connection;

	protected $className;

	public function __construct($model)
	{
		$this->model = $model;

		$this->className = get_class($model);

		$this->connection = $this->getModel()->getConnectionName();
	}

	public function where($key, $operation, $value = null)
	{
		$this->builder = $this->builder ?: $this->newQuery();

		$this->builder = $this->builder->where($key, $operation, $value = null);

		return $this;
	}

	public function first()
	{
		$this->result = $this->builder->first();

		return $this->result ? $this : null;
	}

	public function find($id)
	{
		$this->result = $this->newQuery()->find($id);

		if ($this->result)
		{
			$this->model = $this->result;
		}

		return $this->result;
	}

	public function create($attributes)
	{
		$model = $this->newModel();

		foreach ($attributes as $attribute => $value)
		{
			if (in_array($attribute, $model->getFillable()))
			{
				$model->{$attribute} = $value;
			}
		}

		$model->save();

		return $model;
	}

	public function getId()
	{
		return $this->getAttribute('id');
	}

	public function getAttribute($attribute)
	{
		return $this->result ? $this->result->{$attribute} : null;
	}

	public function setAttribute($attribute, $value)
	{
		return $this->result->{$attribute} = $value;
	}

	public function save()
	{
		return $this->result->save();
	}

    public function findOrCreate($attributes, $keys = null, &$created = false)
    {
        $model = $this->newQuery();

        $keys = $keys ?: array_keys($attributes);

        foreach ($keys as $key)
        {
	        $model = $model->where($key, $attributes[$key]);
        }

        if ( ! $model = $model->first())
        {
            $model = $this->create($attributes);

	        $created = true;
        }

	    $this->model = $model;

        return  $model->id;
    }

    public function getModel()
    {
    	if ($this->model instanceof Illuminate\Database\Eloquent\Builder)
    	{
    		$this->model = new $this->className;
    	}

	    if ($this->connection)
	    {
		    $this->model->setConnection($this->connection);
	    }

    	return $this->model;
    }

	public function newModel()
	{
		$this->model = new $this->className;

		return $this->getModel();
	}

	public function newQuery()
	{
		$this->builder = new $this->className;

		if ($this->connection)
		{
			$this->builder = $this->builder->on($this->connection);
		}

		return $this->builder->newQuery();
	}

}
