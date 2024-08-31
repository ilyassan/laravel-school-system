<?php

namespace App\Repositories;

use App\Models\Invoice;

class InvoiceRepository extends AbstractRepository
{
    public function model(): string
    {
        return Invoice::class;
    }
}