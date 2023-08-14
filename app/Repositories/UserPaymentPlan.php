<?php

namespace App\Repositories;

use App\Models\Accounting\PaymentPlan;
use App\Models\Accounting\UserPaymentPlan as SomePaymentPlan;

class UserPaymentPlan
{
    public function create($data)
    {
        return SomePaymentPlan::create($data);
    }

    public function getAll($order = 'name')
    {
        return PaymentPlan::get();
    }
    public function update($id, $data)
    {
        return UserPaymentPlan::find($id)->update($data);
    }

    public function find($id)
    {
        return UserPaymentPlan::find($id);
    }
}