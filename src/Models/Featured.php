<?php

namespace Mcms\Products\Models;

use Config;
use IdeaSeven\Core\Models\Featured as BaseFeatured;


/**
 * Class Product
 * @package Mcms\Products\Models
 */
class Featured extends BaseFeatured
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
