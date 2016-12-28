(function () {
    'use strict';

    angular.module('mcms.products.product')
        .service('ProductService',Service);

    Service.$inject = ['ProductDataService', 'LangService', 'lodashFactory', 'mediaFileService',
        '$q', 'ProductCategoryService', 'ItemSelectorService', 'mcms.settingsManagerService',
        'SeoService', 'TagsService', '$location', 'PRODUCTS_CONFIG', 'core.services', 'ExtraFieldService'];

    function Service(DS, Lang, lo, MediaFiles, $q, ProductCategoryService, ItemSelector,
                     SM, SEO, Tags, $location, Config, Helpers, ExtraFieldService) {
        var _this = this,
            Filters = {},
            ExtraFields = [],
            Products = [],
            ImageSettings = {},
            ImageCopies = [];

        this.get = get;
        this.init = init;
        this.find = find;
        this.newProduct = newProduct;
        this.save = save;
        this.destroy = destroy;
        this.availableFilters = availableFilters;
        this.previewUrl = previewUrl;
        this.extraFields = extraFields;
        this.formatProductAccessor = formatProductAccessor;
        this.formatProductMutator = formatProductMutator;
        this.imageSettings = imageSettings;

        function init(filters) {

            Filters = Helpers.parseLocation(availableFilters(), $location.search());
            if (lo.isObject(filters)){
                Filters = angular.extend(filters, Filters);
            }

            var tasks = [
                get(Filters),
                categories()
            ];

            return $q.all(tasks);
        }

        function get(filters) {
            return DS.index(filters)
                .then(function (response) {
                    Products = response;
                    return Products;
                });
        }

        function categories() {
            return ProductCategoryService.tree();
        }

        function find(id) {
            return DS.show(id)
                .then(function (response) {

                    ItemSelector.register(response.connectors);
                    MediaFiles.setImageCategories(response.imageCategories);
                    SM.addSettingsItem(response.settings);
                    if (typeof response.config == 'undefined' || typeof response.config.previewController == 'undefined'){
                        Config.previewUrl = null;
                    }
                    imageSettings().set(response.imageCopies);
                    SEO.init(response.seoFields);
                    Tags.set(response.tags);
                    ExtraFields = ExtraFieldService.convertFieldsFromMysql(response.extraFields);
                    return formatProductAccessor(response.item) || newProduct();
                });
        }

        function newProduct() {
            return {
                title : Lang.langFields(),
                slug : '',
                description : Lang.langFields(),
                description_long : Lang.langFields(),
                active : false,
                price : 0,
                categories : [],
                extraFields : [],
                tagged : [],
                related : [],
                files : [],
                settings : {
                    seo : {}
                },
                id : null
            };
        }

        function save(item) {
            var toSave = angular.copy(item);
            toSave = formatProductMutator(toSave);
            if (!item.id){
                return DS.store(toSave);
            }

            return DS.update(toSave);
        }

        function destroy(item) {
            return DS.destroy(item.id);
        }

        function availableFilters(reset) {
            if (!lo.isEmpty(Filters) && !reset){
                return Filters;
            }

            return {
                id : null,
                title: null,
                description: null,
                description_long: null,
                active: null,
                userId: null,
                dateStart: null,
                dateEnd: null,
                category_id: null,
                category_ids : [],
                dateMode: 'created_at',
                orderBy : 'created_at',
                way : 'DESC',
                product: 1,
                limit :  10
            };
        }

        function extraFields() {
            return ExtraFields;
        }

        function previewUrl(id) {
            return DS.previewUrl(id);
        }

        function formatProductAccessor(item) {
            if (lo.isNull(item)){
                return item;
            }
            
            var precision = 2;

            if (lo.isObject(item.price)){
                precision = item.price.currency[Object.keys(item.price.currency)[0]].precision || 2;
                item.price = parseFloat(item.price.amount/100).toFixed(precision);
            } else if (lo.isNumber(item.price)) {
                item.price = parseFloat(item.price/100).toFixed(precision);
            }

            return item;
        }

        function formatProductMutator(item) {
            item.price = parseInt(item.price*100);

            return item;
        }

        function imageSettings() {
            return {
                set : function(val){
                    ImageSettings = val;
                    lo.forEach(val.copies, function (copy, key) {
                        copy.key = key;
                        ImageCopies.push(copy);
                    });
                },
                recommendedSizeLabel : function(){
                    return ImageSettings.recommendedSize || null;
                },
                adminCopy : function () {
                    var copy = lo.find(ImageCopies, {useOnAdmin : true});
                    return (copy) ? copy.key : 'thumb';
                }
            };
        }
    }
})();
