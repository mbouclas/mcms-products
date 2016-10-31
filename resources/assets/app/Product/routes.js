(function() {
    'use strict';

    angular.module('mcms.products.product')
        .config(config);

    config.$inject = ['$routeProvider','PRODUCTS_CONFIG'];

    function config($routeProvider,Config) {

        $routeProvider
            .when('/products/content', {
                templateUrl:  Config.templatesDir + 'Product/index.html',
                controller: 'ProductHomeController',
                controllerAs: 'VM',
                reloadOnSearch : true,
                resolve: {
                    init : ["AuthService", '$q', 'ProductService', function (ACL, $q, Product) {
                        return (!ACL.role('admin')) ? $q.reject(403) : Product.init();
                    }]
                },
                name: 'products-home'
            })
            .when('/products/content/:id', {
                templateUrl:  Config.templatesDir + 'Product/editProduct.html',
                controller: 'ProductController',
                controllerAs: 'VM',
                reloadOnSearch : false,
                resolve: {
                    item : ["AuthService", '$q', 'ProductService', '$route', function (ACL, $q, Product, $route) {
                        return (!ACL.role('admin')) ? $q.reject(403) : Product.find($route.current.params.id);
                    }]
                },
                name: 'products-edit'
            });
    }

})();
