<?php
namespace App\Repositories;
use App\Models\Supplier;

class SupplierRepository
{
    public function getAllSuppliers(){
        return Supplier::all();
    }
}
