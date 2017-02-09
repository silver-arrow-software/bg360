<?php return [
    'sas' => [
        'short_name' => 'SAS',
        'name' => 'Silver Arrow Software',
    ],
    'plugin' => [
        'name' => 'sasERP',
        'description' => 'Enterprise resource planning for business.',
        'profile' => 'Profile',
        'profiles' => 'Profiles',
        'company' => 'Company',
        'companies' => 'Companies',
        'place' => 'Place',
        'places' => 'Places',
        'post' => 'Post',
        'posts' => 'Posts',
        'product' => 'Product',
        'products' => 'Products',
        'order' => 'Order',
        'orders' => 'Orders',
        'accounts' => 'Accounts',
        'account' => 'Account',
        'feedbacks' => 'Feedbacks',
        'feedback' => 'Feedback',
    ],
    'profile' => [
        'name' => 'Profile name',
        'mobile' => 'Mobile',
        'phone' => 'Telephone',
        'address' => 'Address',
        'street' => 'Street',
        'district' => 'District',
        'city' => 'City',
        'country' => 'Country',
    ],
    'common' => [
        'id' => 'ID',
        'name' => 'Name',
        'general' => 'General',
        'description' => 'Description',
        'excerpt' => 'Short description',
        'notes' => 'Notes',
        'title' => 'Title',
        'type' => 'Type',
        'tag' => 'Tag',
        'tags' => 'Tags',
        'code_id' => 'Code',
        'empty' => 'Empty',
        'created_at' => 'Created at',
        'updated_at' => 'Updated at',
        'deleted_at' => 'Deleted at',
        'featured' => 'Featured',
        'published' => 'Published',
        'cancel' => 'Cancel',
        'save' => 'Save',
        'save_close' => 'Save and Close',
        'back' => 'Back',
        'create' => 'Create',
        'create_close' => 'Create and Close',
        'delete' => 'Delete',
        'remove' => 'Remove',
        'published_at' => 'Published At',
        'images' => 'Images',
        'price' => 'Price',
        'quantity' => 'Quantity',
        'active' => 'Active',
        'belongs_to' => 'Belongs to',
        'owner' => 'Owner',
    ],
    'permission' => [
        'manage_profiles' => 'Manage Profiles',
        'manage_places' => 'Manage Places',
        'perm_create_place' => 'Create Place',
        'perm_edit_place' => 'Edit Place',
        'perm_delete_place' => 'Delete Place',
        'manage_products' => 'Manage Products',
        'perm_create_product' => 'Create Product',
        'perm_edit_product' => 'Edit Product',
        'perm_delete_product' => 'Delete Product',
        'manage_feedback' => 'Manage Feedbacks',
        'config_feedback' => 'Manage Feedback Channels',
    ],
    'message' => [
        'create_success' => 'Successfully create item(s).',
        'delete_success' => 'Successfully delete item(s).',
        'hide_success' => 'Successfully hide item(s).',
        'show_success' => 'Successfully show item(s).',
        'error' => 'An unknown error has occured.'
    ],
    'settings' => [
        'menu_label' => 'Shop',
        'menu_description' => 'Configuration of ERP plugin',
        'product_display_page' => 'Product page',
        'product_display_page_description' => 'Name of the product display page file.',
        'category_page' => 'Category page',
        'category_page_description' => 'Name of the category page file for the category links.',
        'cart_page' => 'Cart page',
        'cart_page_description' => 'Name of the cart page file.',
        'checkout_page' => 'Checkout page',
        'checkout_page_description' => 'Name of the checkout page file.',
        'success_page' => 'Success page',
        'success_page_description' => 'Name of the success page file.',
        'order_display_page' => 'Order page',
        'order_display_page_description' => 'Name of the order page file.',
        'section_currency' => 'Currency',
        'section_currency_description' => 'Please select the currency in which the prices will be calculated.',
        'section_pages' => 'Pages',
        'section_pages_description' => 'Please select the pages. This properties are used by the component\'s partials.',
        'section_vat' => 'VAT',
        'vat_state' => 'Check if you want to apply the VAT tax on the total amount in the checkout process.',
        'vat_value' => 'VAT value',
        'vat_value_description' => 'Please enter VAT value.',
        'section_redirect' => 'Redirect user after adding an item to the shopping cart',
        'redirect_user_after_add_to_cart' => 'Add to cart redirect',
        'redirect_user_after_add_to_cart_description' => 'Enter the page you wish to redirect the customer to when an item is added to the cart, or nothing for no redirect.',
        'currency' => 'Currency',
        'currency_description' => 'Please choose the currency.',
        'tab_main' => 'Main',
        'tab_checkout' => 'Checkout',
        'section_messages' => 'Email messages',
        'section_messages_description' => 'Here you can customize the mails sent to the site administrator and customer, after an order is placed.',
        'admin_emails' => 'Administrator emails',
        'admin_emails_description' => 'After each placed order, an email with the order details will be sent to all the addresses from the list above. Please add one email address per line.',
        'send_user_message' => 'Send an email to the customer after an order is placed',
        'currency_format' => 'Currency Format',
        'currency_format_description' => 'Decimal, Decimal Symbol, Thousand Symbol',
        'currency_format_default' => '0, \',\', \'.\'',
    ],
    'company' => [
        'name' => 'Name',
        'name_en' => 'English name',
        'tax_id' => 'Tax number',
        'type_1' => 'Company',
        'type_2' => 'Shop',
        'type_3' => 'Club',
    ],
    'place' => [
        'name' => 'Place name',
        'component_desc' => 'Display a single place.',
        'logo' => 'Logo',
    ],
    'places' => [
        'component_name' => 'Places',
        'component_desc' => 'Display a list of places.',
    ],
    'product' => [
        'component_name' => 'Product',
        'component_desc' => 'Display a single product.',
        'title' => 'Product name',
        'slug' => 'Product slug',
        'slug_description' => 'Look up the product using the supplied slug value.',
        'title_placeholder' => 'New product title',
        'slug_placeholder' => 'new-product-slug',
        'tab_edit' => 'General',
        'tab_attributes' => 'Attributes',
        'tab_inventory' => 'Inventory',
        'item_list_title' => 'Item',
        'default_boardgame_desc' => 'Tên tiếng Việt:<br/>Tác giả:<br/>Số người chơi: 2 - 4<br/>Thời gian chơi: 30 phút<br/>Phù hợp với 10 tuổi trở lên',
    ],
    'products' => [
        'menu_label' => 'Products',
        'name' => 'Products',
        'description' => 'Display a list of products on the page.',
        'pagination' => 'Page number',
        'pagination_description' => 'This value is used to determine what page the user is on.',
        'filter' => 'Category filter',
        'filter_description' => 'Enter a category slug or URL parameter to filter the products by. Leave empty to show all products.',
        'products_per_page' => 'Products per page',
        'products_per_page_validation' => 'Invalid format of the products per page value',
        'no_products' => 'No products message',
        'no_products_description' => 'Message to display in the product list in case if there are no products.',
        'order' => 'Product order',
        'order_description' => 'Attribute on which the products should be ordered',
        'chart_published' => 'Published',
        'chart_drafts' => 'Drafts',
        'chart_total' => 'Total',
        'title' => 'Products',
        'creating' => 'Creating Product...',
        'return' => 'Return to products list',
        'saving' => 'Saving Product...',
        'deleting' => 'Deleting Product...',
        'deleting_confirm' => 'Do you really want to delete this product?',
        'new_product' => 'New Product',
        'delete_confirm' => 'Are you sure?',
        'show_confirm' => 'Are you sure?',
        'hide_confirm' => 'Are you sure?',
        'show_selected' => 'Show selected',
        'hide_selected' => 'Hide selected',
        'list_title' => 'Manage Products',
        'product' => 'Product',
        'edit_product' => 'Edit Product',
        'create_product' => 'Create Product',
        'import_bggeek' => 'Import from BGG',
    ],
    'attribute' => [
        'name' => 'Name',
        'value' => 'Value',
    ],
    'attributes' => [
        'list_title' => 'Attribute',
    ],
    'order' => [
        'menu_label' => 'Orders',
        'name' => 'Order',
        'description' => 'Display a single order.',
        'order_id' => 'Order id',
        'order_id_description' => 'Look up the order using the supplied id value.',
    ],
    'orders' => [
        'menu_label' => 'Orders',
        'list_title' => 'Manage Orders',
        'name' => 'Orders',
        'description' => 'Displays a list of orders on the page.',
        'no_orders' => 'No orders message',
        'no_orders_description' => 'Message to display in the order list in case if there are no orders.',
        'user' => 'User',
        'email' => 'E-mail',
        'items' => 'Items',
        'billing_info' => 'Billing info',
        'shipping_info' => 'Shipping info',
        'delete_confirm' => 'Are you sure?',
        'created_at' => 'Date Created',
        'vat' => 'VAT',
        'total' => 'Total',
        'currency' => 'Currency',
    ],
    'cart' => [
        'name' => 'Cart',
        'description' => 'Show the contents of and process the user\'s cart.',
        'no_products' => 'No products message',
        'no_products_description' => 'Message to display in the product list in case if there are no products.',
        '' => '',
    ],
    'checkout' => [
        'name' => 'Checkout',
        'description' => 'Displays billing and shipping form on the page.',
    ],
    'account' => [
        'name' => 'Account Name',
    ],
    'feedback' => [
        'channel' => [
            'one' => 'Feedback Channel',
            'many' => 'Feedback Channels',

            'action' => [
                'create' => 'Create feedback channel',
                'update' => 'Edit feedback channel',
                'preview' => 'Preview feedback channel',
                'creating' => 'Creating feedback channel...',
                'saving' => 'Saving feedback channel...',
                'delete_confirm' => 'Do you really want to delete this feedback channel?',
                'deleting' => 'Deleting feedback channel...'
            ],

            'name' => 'Name',
            'code' => 'Code',
            'method' => 'Method',
            'prevent_save_database' => 'Do not save feedbacks in a database',
            'no_action_warning' => 'Warning! This configuration have no action: you will not recive any feedback messages'
        ],

        'one' => 'Feedback',
        'many' => 'Feedbacks',

        'action' => [
            'archive' => 'Archive',
            'preview' => 'Preview feedback'
        ],

        'method' => [
            'email' => [
                'destination' => 'Email destination',
                'destination_comment' => 'An address where to send the feedback. Use comma (,) to add more than 1 address. Leave it blank to use the admin\'s address',
                'subject' => 'Subject',
                'template' => 'Template',
                'template_comment' => 'The variables available here are these on the form'
            ],
            'group' => [
                'channels_comment' => 'Select one or more channels'
            ]
        ],

        'name' => 'Name',
        'email' => 'Email',
        'message' => 'Message',
        'created_at' => 'Created at',

        'mail_template' => [
            'description' => 'The template is used to send messages from the feedback form'
        ],

        'navigation' => [
            'menu' => [
                'side' => [
                    'feedbacks' => 'Feedbacks',
                    'archived' => 'Feedbacks Archived'
                ],
                'settings' => [
                    'channels' => [
                        'description' => 'Manage feedback channels'
                    ]
                ]
            ],
            'channels' => [
                'list_title' => 'Feedback Channels list',
                'return_to_list' => 'Return to feedback channels list'
            ],
            'feedbacks' => [
                'list_title' => 'Feedbacks list',
                'archived_title' => 'Archived feedbacks',
                'return_to_list' => 'Return to feedbacks list'
            ]
        ],

        'component' => [
            'feedback' => [
                'name' => 'Feedback',
                'description' => 'Adds a feedback form to your page',

                'channelCode' => [
                    'description' => 'Select the feedback channel you want to use with this form'
                ],
                'successMessage' => [
                    'title' => 'Custom success message',
                    'description' => 'You can specify a custom message that will be shown to a user after successful form submit'
                ],
                'redirectTo' => [
                    'title' => 'Redirect to',
                    'description' => 'You can choose a page to redirect to after successful form submit'
                ]
            ],

            'onSend' => [
                'success' => 'Thank you for your message!',
                'error' => [
                    'email' => [
                        'email' => 'Invalid email address, please provide a valid email'
                    ]
                ]
            ]
        ],
    ],
];
