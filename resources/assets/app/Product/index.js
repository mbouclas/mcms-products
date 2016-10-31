(function(){
    'use strict';

    angular.module('mcms.products.product', [
        'cfp.hotkeys'
    ])
        .run(run);

    run.$inject = ['mcms.widgetService'];

    function run(Widget) {
        Widget.registerWidget(Widget.newWidget({
            id : 'latestProducts',
            title : 'Latest products',
            template : '<latest-products-widget></latest-products-widget>',
            settings : {},
            order : 10
        }));

    }
})();

require('./routes');
require('./dataService');
require('./service');
require('./ProductHomeController');
require('./ProductController');
require('./productList.component');
require('./editProduct.component');
require('./Widgets/latestProducts.widget');
