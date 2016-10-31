(function () {
    'use strict';

    angular.module('mcms.products', [
        'mcms.mediaFiles',
        'mcms.fileGallery',
        'mcms.extraFields',
        'mcms.products.product',
        'mcms.products.productCategory',
        'mcms.products.extraFields',
        'ngFileUpload'
    ])

        .run(run);

    run.$inject = ['mcms.menuService'];

    function run(Menu) {

        Menu.addMenu(Menu.newItem({
            id: 'products',
            title: 'Products',
            permalink: '',
            icon: 'shopping_cart',
            order: 1,
            acl: {
                type: 'level',
                permission: 2
            }
        }));

        var productsMenu = Menu.find('products');

        productsMenu.addChildren([
            Menu.newItem({
                id: 'products-manager',
                title: 'Catalogue',
                permalink: '/products/content',
                icon: 'content_copy',
                order : 2
            }),
            Menu.newItem({
                id: 'products-extra-fields',
                title: 'Extra Fields',
                permalink: '/products/extraFields',
                icon: 'note_add',
                order : 3
            })
        ]);

        productsMenu.addChildren([
            Menu.newItem({
                id: 'productsCategories-manager',
                title: 'Categories',
                permalink: '/products/categories',
                icon: 'view_list',
                order : 1
            })
        ]);
    }

})();

require('./config');
require('./Product');
require('./ProductCategory');
require('./ExtraFields');
