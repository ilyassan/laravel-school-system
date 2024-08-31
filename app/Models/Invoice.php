<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price',
        'quantity',
        'payed',
    ];

    public const TITLE_COLUMN = 'title';
    public const DESCRIPTION_COLUMN = 'description';
    public const PRICE_COLUMN = 'price';
    public const QUANTITY_COLUMN = 'quantity';
    public const PAYED_COLUMN = 'payed';
}
