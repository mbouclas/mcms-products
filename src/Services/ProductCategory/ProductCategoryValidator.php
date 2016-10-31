<?php
/**
 * Created by PhpStorm.
 * User: mbouc
 * Date: 13-Jun-16
 * Time: 12:24 PM
 */

namespace Mcms\Products\Services\ProductCategory;

use Mcms\Products\Exceptions\InvalidProductCategoryFormatException;
use Validator;

class ProductValidator
{
    public function validate(array $item)
    {
        $check = Validator::make($item, [
            'title' => 'required',
            'user_id' => 'required',
            'active' => 'required',
        ]);

        if ($check->fails()) {
            throw new InvalidProductCategoryFormatException($check->errors());
        }

        return true;
    }
}