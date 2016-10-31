<?php
/**
 * Created by PhpStorm.
 * User: mbouc
 * Date: 13-Jun-16
 * Time: 12:24 PM
 */

namespace Mcms\Products\Services\Product;

use Mcms\Products\Exceptions\InvalidProductFormatException;
use Validator;

class ProductValidator
{
    public function validate(array $item)
    {
        $check = Validator::make($item, [
            'title' => 'required',
            'user_id' => 'required',
            'active' => 'required',
            'categories' => 'required|array',
        ]);

        if ($check->fails()) {
            throw new InvalidProductFormatException($check->errors());
        }

        return true;
    }
}