<?php

namespace Mcms\Products\Models;

use Config;
use Mcms\Core\Models\Related as BaseRelated;


/**
 * Class Product
 * @package Mcms\Products\Models
 */
class Related extends BaseRelated
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
