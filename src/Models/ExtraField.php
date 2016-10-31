<?php

namespace Mcms\Products\Models;

use Config;
use IdeaSeven\Core\Models\ExtraField as BaseExtraField;


/**
 * Class Product
 * @package Mcms\Products\Models
 */
class ExtraField extends BaseExtraField
{

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
    protected $productsModel;

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->productsModel = (Config::has('products.product')) ? Config::get('products.product') : Product::class;
    }

    public function item()
    {
        return $this->BelongsTo($this->productsModel, 'item_id');
    }

}
