(function(){
    'use strict';

    angular.module('mcms.products.product')
        .directive('latestProductsWidget', Component);

    Component.$inject = ['PRODUCTS_CONFIG', 'ProductService'];

    function Component(Config, Product){

        return {
            templateUrl: Config.templatesDir + "Product/Widgets/latestProducts.widget.html",
            restrict : 'E',
            scope : {
                options : '=?options'
            },
            link : function(scope, element, attrs, controllers){
                scope.Options = {limit : 5};
                if (typeof scope.options != 'undefined'){
                    scope.Options = angular.extend(scope.Options, scope.options);
                }

                Product.init({limit : scope.Options.limit}).then(function (res) {
                    scope.Categories = res[1];
                    scope.Items = res[0];

                });
            }
        };
    }
})();