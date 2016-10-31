(function() {
    'use strict';

    angular.module('mcms.products.productCategory')
        .config(config);

    config.$inject = ['$routeProvider','PRODUCTS_CONFIG'];

    function config($routeProvider,Config) {

        $routeProvider
            .when('/products/categories', {
                templateUrl:  Config.templatesDir + 'ProductCategory/index.html',
                controller: 'ProductCategoryHomeController',
                controllerAs: 'VM',
                reloadOnSearch : false,
                resolve: {
                    init : ["AuthService", '$q', 'ProductCategoryService', function (ACL, $q, Category) {
                        return (!ACL.role('admin')) ? $q.reject(403) : Category.get();
                    }]
                },
                name: 'products-categories'
            });
    }

})();
