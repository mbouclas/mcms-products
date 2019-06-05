<?php

namespace Mcms\Products\Models;

use Config;
use Mcms\Core\Models\Related as BaseRelated;


/**
 * Class Product
 * @package Mcms\Products\Models
 */
class CategoryRelated extends BaseRelated
{

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    public function item()
    {
        return $this->BelongsTo(ProductCategory::class, 'item_id');
    }

}
