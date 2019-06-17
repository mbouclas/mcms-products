<?php

namespace Mcms\Products\Services\Product;


use App;
use Config;
use Event;
use Mcms\Core\Helpers\Strings;
use Mcms\Core\Models\Image;
use Mcms\Core\Models\MenuItem;

use Mcms\Core\QueryFilters\Filterable;
use Mcms\Core\Services\DynamicTables\DynamicTablesService;
use Mcms\Core\Services\Image\GroupImagesByType;
use Mcms\Core\Traits\FixTags;
use Mcms\FrontEnd\Services\PermalinkArchive;
use Mcms\Products\Exceptions\InvalidProductFormatException;
use Mcms\Products\Models\Featured;
use Mcms\Products\Models\Product;
use Mcms\Products\Models\Related;
use Str;

/**
 * Class ProductService
 * @package Mcms\Products\Services\Product
 */
class ProductService
{
    use Filterable, FixTags;

    /**
     * @var Product
     */
    protected $product;
    /**
     * @var
     */
    public $model;

    protected $validator;

    protected $imageGrouping;

    /**
     * ProductService constructor.
     * @param Product $product
     */
    public function __construct()
    {
        $model = (Config::has('products.product')) ? Config::get('products.product') : Product::class;
        $this->product = $this->model = new $model;
        $this->validator = new ProductValidator();
        $this->imageGrouping = new GroupImagesByType();
    }

    /**
     * Filters the translations based on filters provided
     * Legend has it that it will filter properly role based queries.
     * So, if i am an admin, i should not be able to see the super users
     *
     * @param $filters
     */

    public function filter($filters, array $options = [])
    {
        $results = $this->product->filter($filters);
        $results = (array_key_exists('orderBy', $options)) ? $results->orderBy($options['orderBy']) : $results->orderBy('created_at', 'asc');
        $limit = ($filters->request->has('limit')) ? $filters->request->input('limit') : 10;
        $results = $results->paginate($limit);


        return $results;
    }

    /**
     * @param $id
     * @param array $product
     * @return array
     */
    public function update($id, array $product)
    {
        $Product = $this->product->find($id);
        //link has changed, write it out as a 301
        //create link
        $oldLink = $Product->generateSlug();
        $newLink = $Product->generateSlug($product);

        if ($oldLink != $newLink){
            //write 301

            PermalinkArchive::add($oldLink, $newLink);
        }
        $Product->update($product);
        //update relations
        $Product->categories()->sync($this->sortOutCategories($product['categories']));
        //sanitize the model
        $Product = $this->saveRelated($product, $Product);
        if (isset($product['dynamic_tables'])) {
            $dynamicTableService = new DynamicTablesService(new $this->model->dynamicTablesModel);
            $Product->dynamicTables()->sync($dynamicTableService->sync($product['dynamic_tables']));
        }
        $Product = $this->fixTags($product, $Product);
        if ($product['extra_fields']) {
            $Product->extraFieldValues()->sync($Product->sortOutExtraFields($product['extra_fields']));
        }

        //emit an event so that some other bit of the app might catch it
        event('menu.item.sync',$Product);
        event('product.updated',$Product);

        return $Product;
    }

    /**
     * Create a new product
     *
     * @param array $product
     * @return static
     */
    public function store(array $product)
    {
        try {
            $this->validator->validate($product);
        }
        catch (InvalidProductFormatException $e){
            return $e->getMessage();
        }

        $product['slug'] = $this->setSlug($product);

        $Product = $this->product->create($product);
        $Product->categories()->attach($this->sortOutCategories($product['categories']));
        if (isset($product['dynamic_tables'])){
            $dynamicTableService = new DynamicTablesService(new $this->model->dynamicTablesModel);
            $Product->dynamicTables()->attach($dynamicTableService->sync($product['dynamic_tables']));
        }

        $Product = $this->saveRelated($product, $Product);
        $Product = $this->fixTags($product, $Product);
        event('product.created',$Product);
        return $Product;
    }

    /**
     * Delete a product
     *
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        $item = $this->product->find($id);
        //delete images
        Image::where('model',get_class($this->model))->where('item_id', $id)->delete();
        //delete from menus
        MenuItem::where('model',get_class($this->model))->where('item_id', $id)->delete();
        //delete from featured
        Featured::where('model',get_class($this->model))->where('item_id', $id)->delete();
        //delete from related
        Related::where('model',get_class($this->model))->where('source_item_id', $id)->orWhere('item_id', $id)->delete();
        //emit an event so that some other bit of the app might catch it
        event('menu.item.destroy',$item);
        event('product.destroyed',$item);

        return $item->delete();
    }

    public function findOne($id, array $with = [], array $options = [
        'where' => []
    ])
    {

        $item = $this->model
            ->with($with);

        if (count($options['where']) > 0) {
            foreach ($options['where'] as $key => $value) {
                $item = $item->where($key, $value);
            }
        }

        $item = $item->find($id);

        if ($item){
            $item = $item->relatedItems();
            $item->related = collect($item->related);
        }

        if ($item && in_array('galleries', $with)){
            $item->images = $this->imageGrouping
                ->group($item->galleries, \Config::get('products.items.images.types'));
        }

        return $item;
    }

    /**
     * create an array of category ids with the extra value main
     *
     * @param $itemCategories
     * @return array
     */
    private function sortOutCategories($itemCategories){
        $categories = [];
        foreach ($itemCategories as $category){
            $main = (! isset($category['main']) || ! $category['main']) ? false : true;
            $categories[$category['id']] = ['main' => $main];
        }

        return $categories;
    }

    private function setSlug($item){
        if ( ! isset($item['slug']) || ! $item['slug']){
            return Str::slug($item['title'][App::getLocale()]);
        }

        return $item['slug'];
    }


    /**
     * @param array $product
     * @param Product $Product
     * @return Product
     */
    private function saveRelated(array $product, Product $Product)
    {
        if ( ! isset($product['related']) || ! is_array($product['related'])  ){
            return $Product;
        }

        foreach ($product['related'] as $index => $item) {
            $product['related'][$index]['dest_model'] = ( ! isset($item['dest_model']))
                ? $product['related'][$index]['dest_model'] = $item['model']
                : $product['related'][$index]['dest_model'] = $item['dest_model'];
            $product['related'][$index]['model'] = get_class($Product);
        }

        $Product->related = $Product->saveRelated($product['related']);

        return $Product;
    }

    public function buildPermalink(array $item)
    {
        $stringHelpers = new Strings();

        return $stringHelpers->vksprintf(Config::get('products.items.slug_pattern'), $item);
    }


}
