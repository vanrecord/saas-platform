<?php

namespace Rinvex\Repository\Repositories;

use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

abstract class EloquentRepository
{
    protected ?Container $container = null;
    protected ?string $model = null;
    protected ?string $repositoryId = null;

    public function setContainer(Container $container)
    {
        $this->container = $container;
        return $this;
    }

    public function getContainer(?string $service = null)
    {
        if ($service && $this->container) {
            return $this->container->make($service);
        }
        return $this->container;
    }

    public function setModel(string $model)
    {
        $this->model = $model;
        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function createModel(): Model
    {
        $class = $this->model;
        return new $class;
    }

    public function setRepositoryId(string $id)
    {
        $this->repositoryId = $id;
        return $this;
    }

    public function getRepositoryId(): ?string
    {
        return $this->repositoryId;
    }

    public function query(): Builder
    {
        return $this->createModel()->newQuery();
    }

    public function find($id, array $columns = ['*'])
    {
        if ($id instanceof Model) {
            return $id;
        }
        return $this->query()->find($id, $columns);
    }

    public function findAll(array $columns = ['*'])
    {
        return $this->query()->get($columns);
    }

    public function all(array $columns = ['*'])
    {
        return $this->findAll($columns);
    }

    public function create(array $attributes = [])
    {
        return $this->query()->create($attributes);
    }

    public function update($id, array $attributes = [])
    {
        $entity = $id instanceof Model ? $id : $this->find($id);
        if ($entity) {
            $entity->update($attributes);
        }
        return $entity;
    }

    public function delete($id)
    {
        $entity = $id instanceof Model ? $id : $this->find($id);
        return $entity ? $entity->delete() : false;
    }

    public function paginate($perPage = null, array $columns = ['*'])
    {
        return $this->query()->paginate($perPage, $columns);
    }

    public function with($relations)
    {
        return $this->query()->with(is_string($relations) ? func_get_args() : $relations);
    }

    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        return $this->query()->where(...func_get_args());
    }

    public function __call($method, $parameters)
    {
        return $this->query()->$method(...$parameters);
    }
}

