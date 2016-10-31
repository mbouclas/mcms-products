(function(){
    'use strict';

    angular.module('mcms.products.productCategory', [
        'ui.tree'
    ])
        .run(run);

    run.$inject = ['mcms.menuService'];

    function run(Menu) {

    }


})();

require('./routes');
require('./dataService');
require('./service');
require('./ProductCategoryHomeController');
require('./editProductCategory.component');