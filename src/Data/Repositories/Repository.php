<?php

/**
 * Part of the Tracker package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Tracker
 * @version    1.0.0
 * @author     Antonio Carlos Ribeiro @ PragmaRX
 * @license    BSD License (3-clause)
 * @copyright  (c) 2013, PragmaRX
 * @link       http://pragmarx.com
 */

namespace PragmaRX\Tracker\Data\Repositories;

abstract class Repository implements RepositoryInterface {

	protected $builder;

	protected $model;

	protected $result;

	public function __construct($model)
	{
		$this->model = $model;
	}

	public function getBuilder()
	{
		if ( ! $this->builder)
		{
			$this->builder = $this->model->newQuery();	
		}

		return $this->builder;
	}

	public function where($key, $operation, $value = null)
	{
		$this->builder = $this->getBuilder()->where($key, $operation, $value = null);

		return $this;
	}

	public function first()
	{
		$this->result = $this->getBuilder()->first();

		return $this->result ? $this : null;
	}

	public function find($id)
	{
		$this->result = $this->model->find($id);

		return $this->result ? $this : null;
	}

	public function create($attributes)
	{
		$this->model = $this->model->create($attributes);

		return $this;
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
        $model = $this->model->newQuery();

        $keys = $keys ?: array_keys($attributes);

        foreach($keys as $key)
        {
	        $model = $model->where($key, $attributes[$key]);
        }

        if (! $model = $model->first())
        {
            $model = $this->model->create($attributes);

	        $created = true;
        }

	    $this->model = $model;

        return $this->model->id;
    }

}
