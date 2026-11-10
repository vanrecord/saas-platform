<?php

namespace App\Repositories;

interface SupplierRepositoryInterface
{
    public function getDataTable();

    public function columnsDataTable($table);

    public function find($id, array $columns = ['*']);

    public function findWithAddress($id);

    public function create(array $attributes = []);

    public function update($id, array $attributes = []);

    public function delete($id);

    public function getProvinces();

    public function getDistricts($provinceId);

    public function getCommunes($districtId);

    public function getVillages($communeId);

    public function saveAddress($supplier, array $addressData);
}
