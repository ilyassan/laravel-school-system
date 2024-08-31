<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Invoice;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Repositories\AbstractRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class InvoiceRepository extends AbstractRepository
{
    public function model(): string
    {
        return Invoice::class;
    }

    public function getPaginate(array $filters = [], array $sorting = []): LengthAwarePaginator
    {
        $query = $this->getFilteredQuery($filters);

        $perPage = (int) Arr::get($filters, 'per-page', 10);

        [ $orderByColumn, $sortDirection ] = $this->sortGradesQuery($sorting);

        $colums = Arr::get($filters, 'colums', ['*']);

        return $query->orderBy($orderByColumn, $sortDirection)->paginate($perPage, $colums);
    }

    public function getFilteredQuery(array $filters): Builder
    {
        $query = $this->model::query();

        if ($isPayed = Arr::get($filters, 'status')) {
            $isPayed = $isPayed == -1 ? 0 : 1;
            $query->where($this->model::PAYED_COLUMN, $isPayed);
        }
        
        if ($keyword = Arr::get($filters, 'keyword')) {
            $keyword = '%'. $keyword .'%';
            $query
                ->where($this->model::TITLE_COLUMN, 'like', $keyword)
                ->orWhere($this->model::PRICE_EXCL_TAX_COLUMN, '=', $keyword)
                ->orWhere($this->model::TAX_RATIO_COLUMN, '=', $keyword)
                ->orWhere(DB::raw($this->model::PRICE_EXCL_TAX_COLUMN . ' * (1 + ' . $this->model::TAX_RATIO_COLUMN . ')'), '=', $keyword)
                ->orWhere($this->model::QUANTITY_COLUMN, '=', $keyword)
                ->orWhere(DB::raw($this->model::PRICE_EXCL_TAX_COLUMN . ' * ' .$this->model::QUANTITY_COLUMN), '=', $keyword)
                ->orWhere(DB::raw($this->model::PRICE_EXCL_TAX_COLUMN . ' * (1 + ' . $this->model::TAX_RATIO_COLUMN . ')' . ' * ' .$this->model::QUANTITY_COLUMN), '=', $keyword);
        }
        if ($fromDate = Arr::get($filters, 'from-date')) {
            $query->whereDate('created_at', '>=', Carbon::parse($fromDate)->format('Y-m-d'));
        }

        if ($toDate = Arr::get($filters, 'to-date')) {
            $query->whereDate('created_at', '<=', Carbon::parse($toDate)->format('Y-m-d'));
        }

        return $query;
    }

    public function sortGradesQuery($sorting = [])
    {
        $orderBy = Arr::get($sorting, 'order-by');

        $sortDirection = Arr::get($sorting, 'sort');
        if (!$sortDirection) {
            $sortDirection = 'desc';
        }

        $orderQuery = null;

        switch ($orderBy) {
            case 'title':
                $orderQuery = $this->model::TITLE_COLUMN;
                break;
            case 'price_excl_tax':
                $orderQuery = $this->model::PRICE_EXCL_TAX_COLUMN;
                break;
            case 'price_incl_tax':
                $orderQuery = DB::raw($this->model::PRICE_EXCL_TAX_COLUMN . ' * (1 + ' . $this->model::TAX_RATIO_COLUMN . ')');
                break;
            case 'quantity':
                $orderQuery = $this->model::QUANTITY_COLUMN;
                break;
            case 'total_incl_tax':
                $orderQuery = DB::raw($this->model::PRICE_EXCL_TAX_COLUMN . ' * (1 + ' . $this->model::TAX_RATIO_COLUMN . ')' . ' * ' .$this->model::QUANTITY_COLUMN);
                break;
            case 'status':
                $orderQuery = $this->model::PAYED_COLUMN;
                break;
            default:
                $orderQuery = $this->model::CREATED_AT;
                break;
        }

        return [$orderQuery, $sortDirection];
    }
}