<?php

namespace Mcms\Products\Menu;
use Config;
use Mcms\Products\Models\Filters\ProductFilters;
use Mcms\Products\Models\Product;
use Mcms\Products\Models\ProductCategory;
use Illuminate\Http\Request;
use Mcms\Core\Services\Menu\AdminInterfaceConnector;
use Illuminate\Support\Collection;


/**
 * Class ProductsInterfaceMenuConnector
 * @package Mcms\Products\Menu
 */
class ProductsInterfaceMenuConnector extends AdminInterfaceConnector
{
    /**
     * @var string
     */
    protected $moduleName = 'Products';
    /**
     * @var array
     */
    protected $sections = [];
    protected $order = 1;
    /**
     * @var Product
     */
    protected $product;
    /**
     * @var ProductCategory
     */
    protected $category;
    /**
     * @var
     */
    protected $filters;

    protected $type = 'generic';

    /**
     * ProductsInterfaceMenuConnector constructor.
     */
    public function __construct()
    {
        $this->product = new Product();
        $this->category = new ProductCategory();
        $this->sections = $this->getSections();

        parent::__construct($this->product);

        return $this;
    }

    /**
     * Setup the sections needed for the admin interface to render the menu selection
     *
     * @return array
     */
    private function getSections(){
        //extract it to a config file maybe
        $categoryFilterValues = $this->filterCategories(new Request, []);


        return [
            [
                'name' => 'Items',
                'filterService' => 'Mcms\Products\Menu\ProductsInterfaceMenuConnector',
                'filterMethod' => 'filterItems',
                'settings' => [
                    'perProduct' => 10,
                    'preload' => true,
                    'filter' => true
                ],
                'filters' => [
                    ['key'=>'id', 'label'=> '#ID', 'default' => true],
                    ['key'=>'category_id', 'label'=> 'Category', 'type' => 'select', 'values' => $categoryFilterValues['data']],
                    ['key'=>'title', 'label'=> 'Title'],
                    ['key'=>'description', 'label'=> 'Description'],
                    ['key'=>'description_long', 'label'=> 'Long Description'],
                ],
                'titleField' => 'title',
                'slug_pattern' => Config::get('products.items.slug_pattern')
            ],
            [
                'name' => 'Categories',
                'filterService' => 'Mcms\Products\Menu\ProductsInterfaceMenuConnector',
                'filterMethod' => 'filterCategories',
                'filters' => [
                    ['key'=>'id', 'label'=> '#ID'],
                    ['key'=>'title', 'label'=> 'title', 'default' => true],

                ],
                'settings' => [
                    'perProduct' => 10,
                    'preload' => true,//load it on click, no filtering by hand
                    'filter' => false//Do not display filters on admin
                ],
                'titleField' => 'title',
                'slug_pattern' => Config::get('products.categories.slug_pattern')
            ]
        ];
    }

    /**
     * Provide the menu connector with products results. Query string filters apply here
     *
     * @param Request $request
     * @param $section
     * @return array
     */
    public function filterItems(Request $request, $section){
        $results = $this->product->limit($section['settings']['perProduct'])->filter(new ProductFilters($request))->get();
        if (count($results) == 0){
            return ['data' => []];
        }

        //now formulate the results
        $toReturn = [];

        foreach ($results as $result){

            $toReturn[] = [
                'item_id' => $result->id,
                'title' => $result->title,
                'module' => $this->moduleName,
                'model' => get_class($result),
                'section' => $section
            ];
        }

        $results = $results->toArray();
        $results['data'] = $toReturn;


        return ['data' => $toReturn];
    }

    /**
     * Provide the menu connector with category results.
     * Make sure to send a traversed flat tree for the user to easily select it
     *
     * @param Request $request
     * @param $section
     * @return array
     */
    public function filterCategories(Request $request, $section){

        //traverse the tree
        $results = $this->category->get()->toTree();
        if (count($results) == 0){
            return ['data' => []];
        }

        $leafs = new Collection();

        $traverse = function ($categories, $prefix = '-') use (&$traverse, $leafs, $section) {
            foreach ($categories as $category) {
                $space = '';
                for ($i=0; strlen($prefix) > $i;$i++){
                    $space .= '&nbsp;&nbsp;';
                }

                $leafs->push([
                    'item_id' => $category->id,
                    'title' => $space. ' ' . $prefix.' '.$category->title,
                    'module' => $this->moduleName,
                    'model' => get_class($category),
                    'section' => $section
                ]);

                $traverse($category->children, $prefix.'-');
            }

            return $leafs;
        };

        $tree = $traverse($results);

        return ['data' => $tree];
    }
}