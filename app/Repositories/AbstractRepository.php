<?php

namespace App\Repositories;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository{

    protected $model;
    abstract public function model();

    public function __construct()
    {
        $this->makeModel();
    }

    public function getModel(){
        return $this->model;
    }

    public function makeModel(): void
    {
        $model = app()->make($this->model());

        if(!$model instanceof Model){
            throw new \Exception("This is not an instance of a Model");
        }

        $this->model = $model;
    }

}