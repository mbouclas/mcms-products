<?php

namespace Mcms\Products\Presenters;
use App;
use Mcms\Core\Services\Presenter\Presenter;
use Mcms\Products\Models\Product;

/**
 * Works as $product->present()->methodName
 *
 * Class ProductPresenter
 * @package Mcms\Products\Presenters
 */
class ProductPresenter extends Presenter
{
    /**
     * @var string
     */
    protected $lang;

    /**
     * ProductPresenter constructor.
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->lang = App::getLocale();

        parent::__construct($product);
    }


}