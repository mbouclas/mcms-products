<?php

namespace Mcms\Products\Presenters;
use App;
use Mcms\Core\Services\Presenter\Presenter;
use Mcms\Products\Models\ProductCategory;

/**
 * Works as $category->present()->methodName
 *
 * Class ProductCategoryPresenter
 * @package Mcms\Products\Presenters
 */
class ProductCategoryPresenter extends Presenter
{
    /**
     * @var string
     */
    protected $lang;

    /**
     * ProductPresenter constructor.
     * @param ProductCategory $productCategory
     */
    public function __construct(ProductCategory $productCategory)
    {
        $this->lang = App::getLocale();

        parent::__construct($productCategory);
    }


}