(function () {
    'use strict';

    angular.module('mcms.products.dynamicTables')
        .config(config);

    config.$inject = ['$routeProvider', 'PRODUCTS_CONFIG'];

    function config($routeProvider, Config) {
        $routeProvider
            .when('/products/dynamicTables', {
                templateUrl: Config.templatesDir + 'DynamicTables/index.html',
                controller: 'DynamicTablesHomeController',
                controllerAs: 'VM',
                reloadOnSearch: true,
                resolve: {
                    init: ["AuthService", '$q', function (ACL, $q) {
                        return (!ACL.role('admin')) ? $q.reject(403) : $q.resolve();
                    }]
                },
                name: 'dynamic-tables-home'
            })
            .when('/products/dynamicTables/:id', {
                templateUrl: Config.templatesDir + 'DynamicTables/index.html',
                controller: 'DynamicTablesHomeController',
                controllerAs: 'VM',
                reloadOnSearch: true,
                resolve: {
                    init: ["AuthService", '$q', 'DynamicTableService', '$route', function (ACL, $q, DynamicTableService, $route) {
                        return (!ACL.role('admin')) ? $q.reject(403) : DynamicTableService.get($route.current.params.id);
                    }]
                },
                name: 'dynamic-table-items'
            })
            .when('/products/dynamicTables/item/:id', {
                templateUrl: Config.templatesDir + 'DynamicTables/index.html',
                controller: 'DynamicTablesHomeController',
                controllerAs: 'VM',
                reloadOnSearch: true,
                resolve: {
                    init: ["AuthService", '$q', 'DynamicTableService', '$route', function (ACL, $q, DynamicTableService, $route) {
                        return (!ACL.role('admin')) ? $q.reject(403) : DynamicTableService.find($route.current.params.id);
                    }]
                },
                name: 'dynamic-table-item-edit'
            });
    }
})();
