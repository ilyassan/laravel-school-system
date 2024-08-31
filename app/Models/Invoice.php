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
        'price_excl_tax',
        'quantity',
        'tax_ratio',
        'payed',
    ];

    public const TITLE_COLUMN = 'title';
    public const DESCRIPTION_COLUMN = 'description';
    public const PRICE_EXCL_TAX_COLUMN = 'price_excl_tax';
    public const QUANTITY_COLUMN = 'quantity';
    public const TAX_RATIO_COLUMN = 'tax_ratio';
    public const PAYED_COLUMN = 'payed';
    public const PAYED_STATUS = 'Payed';
    public const UNPAYED_STATUS = 'Unpayed';

    public function getTitle(): string
    {
        return $this->getAttributeValue(self::TITLE_COLUMN);
    }
    public function getDescription(): string
    {
        return $this->getAttributeValue(self::DESCRIPTION_COLUMN);
    }
    public function getStatus(): string
    {
        return $this->getAttributeValue(self::PAYED_COLUMN) == 1 ? self::PAYED_STATUS: self::UNPAYED_STATUS;
    }    
    public function isPayed(): string
    {
        return $this->getStatus() == self::PAYED_STATUS;
    }
    public function getQuantity(): int
    {
        return $this->getAttributeValue(self::QUANTITY_COLUMN);
    }
    public function getPriceExclTax(): string
    {
        return number_format($this->getAttributeValue(self::PRICE_EXCL_TAX_COLUMN), 2);
    }
    public function getPriceInclTax(): string
    {
        return number_format($this->getAttributeValue(self::PRICE_EXCL_TAX_COLUMN) * ( 1 + $this->getAttributeValue(self::TAX_RATIO_COLUMN)) , 2);
    }
    public function getTaxRatio(): string
    {
        return $this->getAttributeValue(self::TAX_RATIO_COLUMN) * 100 . '%';
    }
    public function getTotalExclTax(): string
    {
        return number_format($this->getAttributeValue(self::PRICE_EXCL_TAX_COLUMN) * $this->getQuantity(), 2);
    }
    public function getTotalInclTax(): string
    {
        return number_format($this->getAttributeValue(self::PRICE_EXCL_TAX_COLUMN) * $this->getQuantity() * ( 1 + $this->getAttributeValue(self::TAX_RATIO_COLUMN)), 2);
    }
    public function getCreatedAtFormated(): string
    {
        return $this->getAttributeValue(self::CREATED_AT)->format('m/d/Y');
    }
}
