<?php

namespace Mcms\Products\Models;


use Config;
use Mcms\Core\Models\DynamicTable as BaseDynamicTable;
use Mcms\FrontEnd\Helpers\Sluggable;

class DynamicTable extends BaseDynamicTable
{
    use Sluggable;

    public $itemModel;

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->itemModel = (Config::has('products.product')) ? Config::get('products.product') : Product::class;
    }

    public function products()
    {
        return $this->belongsToMany($this->itemModel);
    }


}
