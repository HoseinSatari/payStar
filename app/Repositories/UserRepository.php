<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class UserRepository
{
    protected $model;

    public function __construct(User $model)
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
        $user = $this->findById($id);
        $user->update($data);
        return $user;
    }

    public function delete($id)
    {
        $user = $this->findById($id);
        $user->delete();
    }

    public function search($keyword)
    {
        return $this->model->where('name', 'like', "%$keyword%")
            ->orWhere('email', 'like', "%$keyword%")
            ->get();
    }

    public function getByRole($role)
    {
        return $this->model->where('role', $role)->get();
    }

    public function paginateCustom($perPage, $page, $filters = [])
    {
        $query = $this->model;


        foreach ($filters as $key => $value) {
            $query = $query->where($key, $value);
        }

        $total = $query->count();


        $paginator = new LengthAwarePaginator(
            $query->skip(($page - 1) * $perPage)->take($perPage)->get(),
            $total,
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return $paginator;
    }

    public function getByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function getActiveUsers()
    {
        return $this->model->where('is_active', true)->get();
    }

    public function getInactiveUsers()
    {
        return $this->model->where('is_active', false)->get();
    }

    public function getUsersWithOrders()
    {
        return $this->model->with('orders')->has('orders')->get();
    }

    public function getUsersWithoutOrders()
    {
        return $this->model->doesntHave('orders')->get();
    }


    public function getLatestUsers($limit = 5)
    {
        return $this->model->latest()->limit($limit)->get();
    }

    public function getOrders($withOrders = null)
    {
        $query = $this->model;

        if ($withOrders) {
            $query = $query->with('orders');
        }

        return $query->get()->pluck('orders')->flatten();
    }


}
