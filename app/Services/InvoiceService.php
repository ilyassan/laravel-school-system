<?php

namespace App\Services;

use App\Repositories\InvoiceRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InvoiceService
{
    protected $invoiceRepository;

    public function __construct(InvoiceRepository $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    public function getPaginateInvoices($filters, $sorting): LengthAwarePaginator
    {

        return $this->invoiceRepository->getPaginate($filters, $sorting);
    }

}
