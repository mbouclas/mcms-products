<?php

Route::group(['prefix' => 'admin/api'], function () {
    Route::group(['middleware' =>['level:2']], function($router)
    {
        $router->get('product/preview/{id}', 'Mcms\Products\Http\Controllers\ProductController@preview');
        $router->resource('product' ,'Mcms\Products\Http\Controllers\ProductController');
        $router->put('productCategory/rebuild','Mcms\Products\Http\Controllers\ProductCategoryController@rebuild');
        $router->get('productCategory/tree','Mcms\Products\Http\Controllers\ProductCategoryController@tree');
        $router->resource('productCategory' ,'Mcms\Products\Http\Controllers\ProductCategoryController');
    });

});