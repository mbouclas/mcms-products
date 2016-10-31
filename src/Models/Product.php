<?php

namespace Mcms\Products\Models;
use Carbon\Carbon;
use Config;
use Conner\Tagging\Taggable;
use IdeaSeven\Core\Traits\ExtraFields;
use IdeaSeven\Core\Models\FileGallery;
use IdeaSeven\Core\Models\Image;
use IdeaSeven\Core\QueryFilters\Filterable;
use IdeaSeven\Core\Traits\CustomImageSize;
use IdeaSeven\Core\Traits\Presentable;
use IdeaSeven\Core\Traits\Relateable;
use IdeaSeven\Core\Traits\Userable;
use IdeaSeven\FrontEnd\Helpers\Sluggable;
use Mcms\Products\Models\Collections\ProductsCollection;
use Illuminate\Database\Eloquent\Model;
use Themsaid\Multilingual\Translatable;

/**
 * Class Product
 * @package Mcms\Products\Models
 */
class Product extends Model
{
    use Translatable, Filterable, Presentable, Taggable,
        Relateable, Sluggable, CustomImageSize, Userable, ExtraFields;

    /**
     * @var string
     */
    protected $table = 'products';
    /**
     * @var array
     */
    public $translatable = ['title', 'description', 'description_long'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description_long',
        'description',
        'slug',
        'thumb',
        'user_id',
        'settings',
        'active',
        'price',
        'published_at'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'published_at'];
    
    /**
     * @var array
     */
    public $casts = [
        'title' => 'array',
        'description' => 'array',
        'description_long' => 'array',
        'settings' => 'array',
        'thumb' => 'array',
        'price' => 'integer',
        'active' => 'boolean'
    ];

    /**
     * Set the presenter class. Will add extra view-model presenter methods
     * @var string
     */
    protected $presenter = 'Mcms\Products\Presenters\ProductPresenter';

    /**
     * Required to configure the images attached to this model
     *
     * @var
     */
    public $imageConfigurator = \Mcms\Products\Services\Product\ImageConfigurator::class;
    public $fileConfigurator = \Mcms\Products\Services\Product\FileConfigurator::class;

    protected $slugPattern = 'products.items.slug_pattern';
    protected $featuredModel;
    protected $relatedModel;
    protected $extraFieldModel;
    public $config;
    public $route;
    protected $defaultRoute = 'product';

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);

        $this->config = Config::get('products.items');
        $this->defaultRoute = (isset($this->config['route'])) ? $this->config['route'] : $this->defaultRoute;
        $this->slugPattern = Config::get($this->slugPattern);
        $this->featuredModel = (Config::has('products.featured')) ? Config::get('products.featured') : Featured::class;
        $this->relatedModel = (Config::has('products.related')) ? Config::get('products.related') : Related::class;
        $this->extraFieldModel = ExtraField::class;
        if (Config::has('products.items.images.imageConfigurator')){
            $this->imageConfigurator = Config::get('products.items.images.imageConfigurator');
        }

        if (Config::has('products.items.files.fileConfigurator')){
            $this->fileConfigurator = Config::get('products.items.files.fileConfigurator');
        }
    }

    private function assignMethod($class)
    {
        $child_class_functions = get_class_methods($class);

        foreach ($child_class_functions as $f){
//            $this->setAttribute($f, $c->$f);
        }
    }


    public function setPublishedAtAttribute($value)
    {
        if ( ! isset($value) || ! $value){
            $this->attributes['published_at'] = Carbon::now();
        }
        try {
            $this->attributes['published_at'] = Carbon::parse($value);
        }
        catch (\Exception $e){
            $this->attributes['published_at'] = Carbon::now();
        }
    }


    /**
     * Returns all the associated categories to this product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(ProductCategory::class, 'product_product_category', 'product_id', 'product_category_id')
            ->withPivot('main')
            ->withTimestamps();
    }

    /**
     * Returns the main category of this product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function mainCategory()
    {
        return $this->belongsToMany(ProductCategory::class)
            ->wherePivot('main', true);
    }

    /**
     * @return mixed
     */
    public function thumb()
    {
        return $this->hasOne(Image::class, 'item_id')->where('type', 'thumb');
    }

    /**
     * Grab all of the images with type image
     *
     * @return mixed
     */
    public function images()
    {
        return $this->hasMany(Image::class, 'item_id')
            ->where('type', 'images')
            ->orderBy('orderBy','ASC');
    }

    public function files()
    {
        return $this->hasMany(FileGallery::class, 'item_id')
            ->orderBy('orderBy','ASC');
    }


    /**
     * Use it with a closure for custom types
     *  ->with(['galleries' => function ($query) {
     * $query->where('type', 'myCustomType');
     * $query->orderBy('dueDate', 'asc');
     *
     * }])
     *
     * @return mixed
     */
    public function galleries()
    {
        return $this->hasMany(Image::class, 'item_id')
            ->where('type', '!=', 'thumb');
    }

    public function featured()
    {
        return $this->belongsToMany($this->featuredModel, $this->table, 'id', 'id');
    }

/*    public function related()
    {
        return $this->hasManyThrough(Product::class, Related::class ,'source_item_id', 'id', 'item_id')
            ->where('model', get_class($this))
            ->orderBy('orderBy','ASC');
    }*/

    /**
     * @return mixed
     */
    public function related()
    {
        return $this->hasMany($this->relatedModel, 'source_item_id')
            ->where('model', get_class($this))
            ->orderBy('orderBy','ASC');
    }

    public function extraFields()
    {
        return $this->hasMany(ExtraFieldValue::class, 'item_id')
            ->where('model', get_class($this));
    }

    public function newCollection(array $models = []){
        return new ProductsCollection($models);
    }
}
