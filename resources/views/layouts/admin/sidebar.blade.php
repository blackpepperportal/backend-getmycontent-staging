<div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">

    <div class="main-menu-content">

        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">

            <li class="nav-item" id="dashboard">
                <a href="{{route('admin.dashboard')}}">
                    <i class="fa fa-dashboard"></i>
                    <span class="menu-title" data-i18n="">{{tr('dashboard')}}</span>
                </a>
            </li>

            <li class="navigation-header">
                <span>{{tr('account_management')}}</span>
            </li>
            
            <li class="nav-item" id="users">
                <a href="{{route('admin.users.index')}}">
                    <i class="ft-user"></i>
                    <span class="menu-title" data-i18n="">{{tr('users')}}</span>
                </a>
                <ul class="menu-content">
                    <li id="users-create">
                        <a class="menu-item" href="{{route('admin.users.create')}}">
                            {{tr('add_user')}}
                        </a>
                    </li>
                    <li id="users-view">
                        <a class="menu-item" href="{{route('admin.users.index')}}">
                            {{tr('view_users')}}
                        </a>
                    </li>
                </ul>            
            
            </li>

            <li class="nav-item" id="content_creators">
                <a href="{{route('admin.content_creators.index')}}">
                    <i class="ft-star"></i>
                    <span class="menu-title" data-i18n="">{{tr('content_creators')}}</span>
                </a>
                <ul class="menu-content">
                    <li id="content_creators-create">
                        <a class="menu-item" href="{{route('admin.content_creators.create')}}">
                            {{tr('add_content_creator')}}
                        </a>
                    </li>
                    <li id="content_creators-view">
                        <a class="menu-item" href="{{route('admin.content_creators.index')}}">
                            {{tr('view_content_creators')}}
                        </a>
                    </li>
                    <li id="content_creators-unverified">
                        <a class="menu-item" href="{{route('admin.content_creators.index',['unverified' => YES])}}">
                            {{tr('unverified_content_creators')}}
                        </a>
                    </li>
                    <li id="content_creators-documents">
                        <a class="menu-item" href="{{route('admin.content_creators.documents.index')}}">
                            {{tr('content_creator_documents')}}
                        </a>
                    </li>
                </ul>            
           
            </li>

            <!-- posts_management start -->

            <li class="navigation-header">
                <span>{{tr('posts_management')}}</span>
            </li>

            <li class="nav-item" id="posts">
                <a href="{{route('admin.posts.index')}}">
                    <i class="fa fa-image"></i>
                    <span class="menu-title" data-i18n="">{{tr('posts')}}</span>
                </a>
                <ul class="menu-content">
                    <li id="posts-view">
                        <a class="menu-item" href="{{route('admin.posts.index')}}">
                            {{tr('view_posts')}}
                        </a>
                    </li>
                </ul>            
            
            </li>

            <li class="nav-item" id="scheduled-posts">
                <a href="{{route('admin.posts.index',['scheduled' => YES])}}">
                    <i class="fa fa-clock-o"></i>
                    <span class="menu-title" data-i18n="">{{tr('scheduled_posts')}}</span>
                </a>
            
            </li>

            <li class="nav-item" id="post_albums">
                <a href="{{route('admin.post_albums.index')}}">
                    <i class="fa fa-clone"></i>
                    <span class="menu-title" data-i18n="">{{tr('post_albums')}}</span>
                </a>
            
            </li>

            <!-- posts_management end -->

            <!-- products_management start -->

            <li class="navigation-header">
                <span>{{tr('products_management')}}</span>
            </li>

            <li class="nav-item" id="user_products">
                <a href="{{route('admin.user_products.index')}}">
                    <i class="ft-box"></i>
                    <span class="menu-title" data-i18n="">{{tr('user_products')}}</span>
                </a>
                <ul class="menu-content">
                    <li id="user_products-create">
                        <a class="menu-item" href="{{route('admin.user_products.create')}}">
                            {{tr('add_user_product')}}
                        </a>
                    </li>
                    
                    <li id="user_products-view">
                        <a class="menu-item" href="{{route('admin.user_products.index')}}">
                            {{tr('view_user_products')}}
                        </a>
                    </li>
                </ul>            
            
            </li>

            <li class="nav-item" id="orders">

                <a href="{{route('admin.orders.index')}}">
                    <i class="fa fa-shopping-basket" aria-hidden="true"></i>
                    <span class="menu-title" data-i18n="">{{tr('orders')}}</span>
                </a>

                <ul class="menu-content">
                    <li id="orders-new">
                        <a class="menu-item" href="{{route('admin.orders.index',['new_orders' => YES])}}">
                            {{tr('new_orders')}}
                        </a>
                    </li>
                    <li id="orders-view">
                        <a class="menu-item" href="{{route('admin.orders.index')}}">
                            {{tr('view_orders')}}
                        </a>
                    </li>
                </ul>            
            
            </li>

            <li class="nav-item" id="product-inventories">

                <a href="{{route('admin.product_inventories.index')}}">
                    <i class="fa fa-shopping-bag"></i>
                    <span class="menu-title" data-i18n="">{{tr('product_inventories')}}</span>
                </a>
            
            </li>

            <li class="nav-item" id="delivery-address">

                <a href="{{route('admin.delivery_address.index')}}">
                    <i class="fa fa-truck"></i>
                    <span class="menu-title" data-i18n="">{{tr('delivery_address')}}</span>
                </a>
            
            </li>

            <!--  products_management end -->

            <li class="navigation-header">
                <span>{{tr('revenue_management')}}</span>
            </li>

            <li class="nav-item" id="revenue-dashboard">
                <a href="{{route('admin.revenues.dashboard')}}">
                    <i class="ft-globe"></i>
                    <span class="menu-title" data-i18n="">{{tr('revenue_dashboard')}}</span>
                </a>
            </li>

            <li class="nav-item" id="payments">
                <a href="{{route('admin.documents.index')}}">
                    <i class="fa fa-money"></i>
                    <span class="menu-title" data-i18n="">{{tr('payments')}}</span>
                </a>
                <ul class="menu-content">
                    <li id="post-payments">
                        <a class="menu-item" href="{{route('admin.post.payments')}}">
                           {{tr('post_payments')}}
                        </a>
                    </li>

                     <li id="order-payments">
                        <a class="menu-item" href="{{route('admin.order.payments')}}">
                           {{tr('order_payments')}}
                        </a>
                    </li>
                </ul>            
            </li>

            <li class="nav-item" id="user_wallets">
                <a href="{{route('admin.user_wallets.index')}}">
                     <i class="fa fa-shopping-bag"></i>
                    <span class="menu-title" data-i18n="">{{tr('user_wallets')}}</span>
                </a>
            </li>

            <li class="nav-item" id="content_creator-withdrawals">
                <a href="{{route('admin.user_withdrawals')}}">
                    <i class="fa fa-location-arrow" aria-hidden="true"></i>

                    <span class="menu-title" data-i18n="">{{tr('user_withdrawals')}}</span>
                </a>
            </li>

            <li class="nav-item" id="subscriptions">

                <a href="{{route('admin.subscriptions.index')}}">
                    <i class="fa fa-diamond" aria-hidden="true"></i>
                    <span class="menu-title" data-i18n="">{{tr('subscriptions')}}</span>
                </a>

                <ul class="menu-content">
                    <li id="subscriptions-create">
                        <a class="menu-item" href="{{route('admin.subscriptions.create')}}">
                            {{tr('add_subscription')}}
                        </a>
                    </li>
                    <li id="subscriptions-view">
                        <a class="menu-item" href="{{route('admin.subscriptions.index')}}">
                            {{tr('view_subscriptions')}}
                        </a>
                    </li>
                </ul>            
            
            </li>

            <!-- lookups_management start -->

            <li class="navigation-header">
                <span>{{tr('lookups_management')}}</span>
            </li>

            <li class="nav-item" id="documents">
                <a href="{{route('admin.documents.index')}}">
                    <i class="fa fa-file-text-o" aria-hidden="true"></i>
                    <span class="menu-title" data-i18n="">{{tr('documents')}}</span>
                </a>
                <ul class="menu-content">
                    <li id="documents-create">
                        <a class="menu-item" href="{{route('admin.documents.create')}}">
                            {{tr('add_document')}}
                        </a>
                    </li>
                    <li id="documents-view">
                        <a class="menu-item" href="{{route('admin.documents.index')}}">
                            {{tr('view_documents')}}
                        </a>
                    </li>
                </ul>            
            
            </li>

            <li class="nav-item" id="static_pages">
                <a href="{{route('admin.static_pages.index')}}">
                    <i class="ft-file"></i>
                    <span class="menu-title" data-i18n="">{{tr('static_pages')}}</span>
                </a>
                <ul class="menu-content">
                    <li id="static_pages-create">
                        <a class="menu-item" href="{{route('admin.static_pages.create')}}">
                            {{tr('add_static_page')}}
                        </a>
                    </li>
                    <li id="static_pages-view">
                        <a class="menu-item" href="{{route('admin.static_pages.index')}}">
                            {{tr('view_static_pages')}}
                        </a>
                    </li>
                </ul>            
            
            </li>

            <!-- lookups_management end -->

            <li class="navigation-header">
                <span>{{tr('setting_management')}}</span>
            </li>

            <li class="nav-item" id="settings">
                <a href="{{route('admin.settings')}}">
                    <i class="ft-globe"></i>
                    <span class="menu-title" data-i18n="">{{tr('settings')}}</span>
                </a>
            </li>

            <li class="nav-item" id="profile">
                <a href="{{route('admin.profile')}}">
                    <i class="fa fa-user"></i>
                    <span class="menu-title" data-i18n="">{{tr('account')}}</span>
                </a>
            </li>
        
            <li class="nav-item">
                <a data-toggle="modal" data-target="#logoutModel" href="{{route('admin.logout')}}" onclick="return confirm('Are You sure?')">
                    <i class="ft-power"></i>
                    <span class="menu-title" data-i18n="">{{tr('logout')}}</span>
                </a>
            </li>

        </ul>
    </div>
</div>
