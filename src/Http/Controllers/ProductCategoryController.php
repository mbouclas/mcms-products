<?php

namespace Mcms\Products\Http\Controllers;

use App\Http\Controllers\Controller;
use Config;
use Mcms\Core\Services\SettingsManager\SettingsManagerService;
use Mcms\Products\Services\ProductCategory\ProductCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use ItemConnector;


class ProductCategoryController extends Controller
{
    protected $category;

    public function __construct(ProductCategoryService $productCategory)
    {
        $this->category = $productCategory;
    }

    public function index()
    {
        $results = $this->category
            ->model
            ->defaultOrder()
            ->get()
            ->toTree();

        return $results;
    }

    public function tree()
    {
        return $this->category->htmlTree();
    }

    public function store(Request $request)
    {
        $data = $request->toArray();
        $data['user_id'] = \Auth::user()->id;
        $parentId = (!isset($data['id']) && isset($data['parent_id'])) ? $data['parent_id'] : null;
        return $this->category->store($data, $parentId);
    }


    public function update(Request $request, $id)
    {
        return $this->category->update($id, $request->toArray());
    }


    public function destroy($id)
    {
        $result = $this->category->destroy($id);
        return $this->index();
    }

    public function show($id)
    {
        $category = $this->category->model->with(['image', 'related'])->find($id);
        if ($category) {
            $category = $category->relatedItems();

            foreach ($category->related as $item) {
                $featured[] = $item;
            }

            $featured = [];

            $category->featured = $featured;
        }


        return [
            'item' => $category,
            'settings' => SettingsManagerService::get('productCategories'),
            'connectors' => ItemConnector::connectors(),
            'seoFields' => Config::get('seo')
        ];
    }

    /**
     * Rebuild the entire tree
     *
     * @param Request $request
     * @return mixed
     */
    public function rebuild(Request $request)
    {
        $this->category
            ->model
            ->rebuildTree($request->all());

        return $this->category
            ->model
            ->defaultOrder()
            ->get()
            ->toTree();
    }
}
