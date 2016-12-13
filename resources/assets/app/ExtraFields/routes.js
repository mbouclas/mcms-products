(function() {
    'use strict';

    angular.module('mcms.products.extraFields')
        .config(config);

    config.$inject = ['$routeProvider','PRODUCTS_CONFIG'];

    function config($routeProvider,Config) {

        $routeProvider
            .when('/products/extraFields', {
                templateUrl:  Config.templatesDir + 'ExtraFields/index.html',
                controller: 'ProductsExtraFieldHomeController',
                controllerAs: 'VM',
                reloadOnSearch : true,
                resolve: {
                    init : ["AuthService", '$q', function (ACL, $q) {
                        return (!ACL.role('admin')) ? $q.reject(403) : $q.resolve();
                    }]
                },
                name: 'products-extra-fields-home'
            });
    }

})();
