<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;

class PaymentRepository
{
    protected $model;

    public function __construct(Payment $model)
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

    public function left_over_time_reserved()
    {
        if ($this->model->reserved_transaction) {
            $reservedTime = Carbon::parse($this->model->reserved_transaction);
            $currentTime = now()->setTimezone('Asia/Tehran');

            return $currentTime->diffInMinutes($reservedTime);
        }

        return null;
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
        $this->model = $this->model->create($data);
        return $this->model;
    }

    public function update( array $data)
    {
        if ($this->model) {
            $this->model->update($data);
            return $this->model;
        }

        return null;
    }

    public function delete($id)
    {
        $payment = $this->findById($id);

        if ($payment) {
            $payment->delete();
            return true;
        }

        return false;
    }

    public function user()
    {
        return $this->model->belongsTo(User::class);
    }

    public function order()
    {
        return $this->model->belongsTo(Order::class);
    }


}

