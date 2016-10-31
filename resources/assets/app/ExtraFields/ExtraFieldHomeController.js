(function() {
    'use strict';

    angular.module('mcms.products.extraFields')
        .controller('ExtraFieldHomeController',Controller);

    Controller.$inject = ['PRODUCTS_CONFIG'];

    function Controller(Config) {
        var vm = this;
        vm.Model = Config.productModel;
    }

})();
