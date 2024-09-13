<?php

namespace App\Http\Controllers;

use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{

    private $invoiceService;
    private $filterInputs;
    private $sortGrades;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
        $this->filterInputs = ['per-page', 'status', 'keyword', 'from-date', 'to-date'];
        $this->sortGrades = ['order-by', 'sort'];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only($this->filterInputs);
        $sorting = $request->only($this->sortGrades);

        $invoices = $this->invoiceService->getPaginateInvoices($filters, $sorting);

        return view('invoices.index', compact('invoices') );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("invoices.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
