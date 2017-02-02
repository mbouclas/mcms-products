(function () {
    angular.module('mcms.products.product')
        .directive('productList', Directive);

    Directive.$inject = ['PRODUCTS_CONFIG', '$timeout'];
    Controller.$inject = ['$scope', 'ProductService', '$mdBottomSheet', 'LangService',
        '$mdSidenav', 'BottomSheet', 'Dialog', '$filter', '$location', 'core.services', '$rootScope'];

    function Directive(Config, $timeout) {

        return {
            templateUrl: Config.templatesDir + "Product/Components/productList.component.html",
            controller: Controller,
            controllerAs: 'VM',
            require: ['productList'],
            scope: {
                options: '=?options',
                items: '=items',
                categories: '=?categories',
                onSave: '&?onSave'
            },
            restrict: 'E',
            link: function (scope, element, attrs, controllers) {
                var defaults = {
                    limit : 10
                };
                var watcher = scope.$watch('items', function (val) {
                   if (!val){
                       return;
                   }
                    controllers[0].setUp(scope.items, scope.categories);
                    watcher();
                });

                scope.options = (!scope.options) ? defaults : angular.extend(defaults, scope.options);
            }
        };
    }

    function Controller($scope, ProductService, $mdBottomSheet, Lang, $mdSidenav, BottomSheet, Dialog,
                        $filter, $location, Helpers, $rootScope) {
        var vm = this,
            Filters = {
                title: null,
                description: null,
                description_long: null,
                active: null,
                userId: null,
                dateStart: null,
                dateEnd: null,
                category_ids : [],
                dateMode: 'created_at',
                orderBy : 'created_at',
                way : 'DESC',
                product: 1,
                limit : $scope.options.limit || 10
            };
        vm.Items = [];
        vm.Categories = [];
        vm.Pagination = {};

        vm.boolValues = [
            {
                label: 'Don\'t care',
                value: null
            },
            {
                label: 'Yes',
                value: true
            },
            {
                label: 'No',
                value: false
            }
        ];
        resetFilters();
        vm.Lang = Lang;
        vm.defaultLang = Lang.defaultLang();

        this.setUp = function (items, categories) {
            vm.Pagination = items;
            vm.Items = items.data;
            vm.Categories = categories;
            console.log(vm.Pagination);
        };

        vm.sort = function (sort, direction) {
            vm.filters.orderBy = sort;
            vm.filters.way = direction || null;
            filter();
        };

        function filter() {
            vm.Loading = true;
            vm.Items = [];
            return ProductService.get(vm.filters)
                .then(function (res) {
                    vm.Loading = false;
                    vm.Pagination = res;
                    vm.Items = res.data;
                    $rootScope.$broadcast('scroll.to.top');
                });
        }

        vm.changePage = function (page, limit) {
            vm.filters.page = page;
            filter();
        };

        vm.applyFilters = function () {
            filter();
        };

        vm.listItemClick = function($index) {
            $mdBottomSheet.hide(clickedItem);
        };

        vm.toggleFilters = function () {
            $mdSidenav('filters').toggle();
        };

        vm.edit = function (item) {
            var id = (item) ? item.id : 'new';
            $location.path($filter('reverseUrl')('products-edit',{id : id}).replace('#',''));
        };

        vm.quickEdit = function (item) {
            if (!item || !item.id){
                item = ProductService.newProduct();
            }


            Dialog.show({
                title : (!item.id) ? 'Create item' : 'Edit #' + item.id,
                contents : '<edit-product item="VM.Item.id" on-save="VM.onSave(item, isNew)"></edit-product>',
                locals : {
                    Item :item,
                    onSave : vm.onSave
                }
            });
        };

        vm.enableItem = function (item) {
            item.active = true;

            ProductService.save(item)
                .then(function () {
                    Helpers.toast('Saved!');
                });
        };

        vm.disableItem = function (item) {
            item.active = false;

            ProductService.save(item)
                .then(function () {
                    Helpers.toast('Saved!');
                });
        };

        vm.delete = function (item) {
            Helpers.confirmDialog({}, {})
                .then(function () {
                    ProductService.destroy(item)
                        .then(function () {
                            filter();
                            Helpers.toast('Saved!');
                        });
                });
        };

        vm.showActions = function (ev, item) {
            var toggle = (item.active) ?
            { name: 'Disable', icon: 'block', fn : vm.disableItem } :
            { name: 'Enable', icon: 'done', fn : vm.enableItem };

            BottomSheet.show({
                item : item,
                title : 'Edit ' + item.title[vm.defaultLang]
            },[
                { name: 'Edit', icon: 'edit', fn : vm.edit },
                { name: 'Quick Edit', icon: 'edit', fn : vm.quickEdit },
                toggle,
                { name: 'Delete', icon: 'delete', fn : vm.delete },
            ]);
        };



        function resetFilters() {
            vm.filters = angular.copy(Filters);
        }
    }
})();
