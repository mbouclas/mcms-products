(function() {
    'use strict';

    angular.module('mcms.products.extraFields')
        .controller('ProductsExtraFieldHomeController',Controller);

    Controller.$inject = ['PRODUCTS_CONFIG', 'LayoutManagerService'];

    function Controller(Config, LMS) {
        var vm = this;
        var layouts = [],
            allLayouts = LMS.layouts('products.items');

        for (var i in allLayouts){
            layouts.push({
                label : allLayouts[i].label,
                value : allLayouts[i].varName,
            });
        }

        vm.Model = Config.productModel;
        vm.additionalFields = [
            {
                varName : 'layoutId',
                label : 'Layout',
                type : 'selectMultiple',
                options : layouts
            }
        ];
    }
})();
