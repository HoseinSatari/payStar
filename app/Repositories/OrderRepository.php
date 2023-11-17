<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderRepository
{
    protected $model;

    public function __construct(Order $model)
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

    public function find($id)
    {
        $this->model = $this->model->find($id);
        return $this->model;
    }

    public function findOrFail($id)
    {
        $this->model = $this->model->findOrFail($id);
        return $this->model;
    }

    public function create(array $data)
    {
        $randomCode = Str::random(8);
        while ($this->model->where('order_code', $randomCode)->exists()) {
            $randomCode = Str::random(8);
        }
        $data['order_code'] = $randomCode;
        $this->model = $this->model->create($data);
        return $this->model;
    }

    public function update(array $data)
    {
        if ($this->model) {
            $this->model->update($data);
            return $this->model;
        }

        return null;
    }

    public function delete($id)
    {
        if ($this->model) {
            $this->model->delete();
            return true;
        }

        return false;
    }

    public function user()
    {
        return $this->model->user;
    }

    public function products()
    {
        return $this->model->products()->get();
    }



    public function getLastTransaction()
    {
        return $this->model->payments()->latest('created_at')->first();
    }

    public function transactions(array $conditions = [], array $orWhereConditions = [])
    {
        $query = $this->model->payments();

        foreach ($conditions as $field => $value) {
            $query->where($field, $value);
        }

        if (!empty($orWhereConditions)) {
            $query->orWhere(function ($query) use ($orWhereConditions) {
                foreach ($orWhereConditions as $field => $value) {
                    $query->orWhere($field, $value);
                }
            });
        }

        return $query;
    }

    public function totalPrice()
    {
        $totalPrice = 0;
        foreach ($this->products() as $product) {
            $totalPrice += $product->pivot->amount * $product->pivot->quantity;
        }
        return $totalPrice;
    }

    public function attachProducts(array $products)
    {
        if (empty($products)) {
            Log::error('error attaching products array for order');
            throw new \InvalidArgumentException('No products to attach.');
        }
        return $this->model->products()->attach($products);
    }
}
