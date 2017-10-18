<?php

namespace Mcms\Products\Http\Controllers;

use App\Http\Controllers\Controller;
use Config;
use Mcms\Core\ExtraFields\ExtraFields;
use Mcms\Core\Models\Filters\ExtraFieldFilters;
use Mcms\Core\Services\DynamicTables\DynamicTablesService;
use Mcms\Core\Services\SettingsManager\SettingsManagerService;
use Mcms\Products\Models\Filters\ProductFilters;
use Mcms\Products\Models\Product;
use Mcms\Products\Models\ProductCategory;
use Mcms\Products\Services\Product\ProductService;
use Illuminate\Http\Request;
use ItemConnector;

class ProductController extends Controller
{
    protected $product;
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(ProductFilters $filters, Request $request)
    {
/*        $category = ProductCategory::find(4);
        $category->children()->create([
            'title' => ['en'=>'Jobs'],
            'slug' => str_slug('Jobs'),
            'description' => ['en'=>''],
            'user_id' => \Auth::user()->id,
            'orderBy' => 0,
            'active' => true
        ]);*/
/*        ProductCategory::create([
            'title' => ['en'=>'Properties'],
            'slug' => str_slug('Properties'),
            'description' => ['en'=>''],
            'user_id' => \Auth::user()->id,
            'orderBy' => 0,
            'active' => true
        ]);*/

/*        $product = Product::create([
            'title' => ['en'=>'The team'],
            'slug' => str_slug('The team'),
            'description' => ['en'=>'sdfgs sgsg sdgsdg'],
            'description_long' => ['en'=>'24rt243 tgf42 g432'],
            'user_id' => \Auth::user()->id,
            'active' => true
        ]);
        
        $category = ProductCategory::find(4);

        $product->categories()->attach([$category->id, 5]);*/

//        return Product::with('categories')->find(129);

/*        \DB::listen(function($sql) {
            var_dump($sql->sql);
            var_dump($sql->bindings);
        });*/

//        return Product::limit(10)->filter($filters)->get();
//        return ProductCategory::with('products')->find(4);


//        return $productService->filter($filters);


/*        $product = $productService->store([
            'title' => 'a new product',
            'slug' => str_slug('a new product'),
            'active' => true,
            'user_id' => 2,
            'categories' => [
                ['id'=>3],
                ['id'=>4,'main'=>true]
            ]
        ]);*/


/*        $product = Product::with('categories')->find(109);
        $update = $product->toArray();
        $update['categories'] = [
            ['id'=>4],
            ['id'=>5,'main'=>true]
        ];
        $product = $productService->update($product->id, $update);*/

        \DB::listen(function ($query) {
//            print_r($query->sql);
//            print_r($query->bindings);
            // $query->time
        });
        $limit = ($request->has('limit')) ? (int) $request->input('limit') : 10;

        if (! $request->has('orderBy')) {
            $request->merge(['orderBy' => 'created_at']);
        }

        return $this->productService->model->with(['categories','images'])
            ->filter($filters)
            ->paginate($limit);
    }

    public function store(Request $request)
    {
        $data = $request->toArray();
        $data['user_id'] = \Auth::user()->id;
        return $this->productService->store($data);
    }


    public function update(Request $request, $id)
    {
        return $this->productService->update($id, $request->toArray());
    }


    public function destroy($id)
    {
        $result = $this->productService->destroy($id);
        return ['success' => $result];
    }

    public function show($id, ExtraFieldFilters $filters)
    {
        $imageCategories = Config::get('products.items.images.types');
        $extraFieldService = new ExtraFields();
        \DB::listen(function ($query) {
//            print_r($query->sql);
//            print_r($query->bindings);
            // $query->time
        });
        $filters->request->merge(['model' => str_replace('\\','\\\\',get_class($this->productService->model))]);
        $dynamicTableService = new DynamicTablesService(new $this->productService->model->dynamicTablesModel);

        return [
            'item' => $this->productService->findOne($id, ['related', 'categories', 'dynamicTables',
                'galleries','tagged','files', 'extraFields', 'extraFields.field']),
            'imageCategories' => $imageCategories,
            'extraFields' => $extraFieldService->model->filter($filters)->get(),
            'imageCopies' => Config::get('products.items.images'),
            'config' => array_merge(Config::get('products.items'), Config::get('products.money')),
            'tags' => $this->productService->model->existingTags(),
            'dynamicTables' => $dynamicTableService->all(),
            'settings' => SettingsManagerService::get('products'),
            'connectors' => ItemConnector::connectors(),
            'seoFields' => Config::get('seo')
        ];
    }

    public function preview($id)
    {
        $item = Product::find($id);
        return response(['url' => $item->createUrl()]);
    }
}
