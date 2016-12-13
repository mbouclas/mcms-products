<?php

namespace Mcms\Products\Models;

use Config;
use Mcms\Core\Models\ExtraFieldValue as BaseExtraFieldValue;


/**
 * Class Product
 * @package Mcms\Products\Models
 */
class ExtraFieldValue extends BaseExtraFieldValue
{
    protected $productsModel;

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->productsModel = (Config::has('products.product')) ? Config::get('products.product') : Product::class;
    }

    public function field()
    {
        return $this->BelongsTo(ExtraField::class, 'extra_field_id');
    }

}
