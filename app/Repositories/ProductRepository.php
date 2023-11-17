<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\Log;


class ProductRepository
{
    protected $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }
    public function __get($property)
    {
        return $this->model->{$property};
    }

    public function __set($property, $value)
    {
        $this->model->{$property} = $value;
    }
    public function save()
    {
        $this->model->save();
    }
    public function getAll()
    {
        return $this->model->all();
    }

    public function findById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $product = $this->findById($id);
        $product->update($data);
        return $product;
    }

    public function delete($id)
    {
        $product = $this->findById($id);
        $product->delete();
    }

    public function getByTitle($title)
    {
        return $this->model->where('title', $title)->get();
    }

    public function getByPriceRange($minPrice, $maxPrice)
    {
        return $this->model->whereBetween('price', [$minPrice, $maxPrice])->get();
    }


}
