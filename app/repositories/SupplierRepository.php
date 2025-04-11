<?php
namespace App\Repositories;
use App\Models\Supplier;

class SupplierRepository
{
    public function getAllSuppliers($perPage = 10){
        return Supplier::paginate($perPage,['*'],'suppliers_page');
    }

}
