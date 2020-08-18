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

            <li class="nav-item" id="stardoms">
                <a href="{{route('admin.stardoms.index')}}">
                    <i class="ft-star"></i>
                    <span class="menu-title" data-i18n="">{{tr('stardoms')}}</span>
                </a>
                <ul class="menu-content">
                    <li id="stardoms-create">
                        <a class="menu-item" href="{{route('admin.stardoms.create')}}">
                            {{tr('add_stardom')}}
                        </a>
                    </li>
                    <li id="stardoms-view">
                        <a class="menu-item" href="{{route('admin.stardoms.index')}}">
                            {{tr('view_stardoms')}}
                        </a>
                    </li>
                    <li id="stardoms-documents">
                        <a class="menu-item" href="{{route('admin.stardoms.documents.index')}}">
                            {{tr('stardom_documents')}}
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
                <a href="{{route('admin.posts.index',['scheduled' => 'scheduled_posts'])}}">
                    <i class="fa fa-clock-o"></i>
                    <span class="menu-title" data-i18n="">Scheduled Posts</span>
                </a>
            
            </li>

            <li class="nav-item" id="post_albums">
                <a href="{{route('admin.stardom_products.index')}}">
                    <i class="fa fa-clone"></i>
                    <span class="menu-title" data-i18n="">{{tr('post_albums')}}</span>
                </a>
                <ul class="menu-content">
                    <li id="post_albums-view">
                        <a class="menu-item" href="{{route('admin.post_albums.index')}}">
                            {{tr('view_post_albums')}}
                        </a>
                    </li>
                </ul>            
            
            </li>

            <!-- posts_management end -->

            <!-- products_management start -->

            <li class="navigation-header">
                <span>{{tr('products_management')}}</span>
            </li>

            <li class="nav-item" id="stardom_products">
                <a href="{{route('admin.stardom_products.index')}}">
                    <i class="ft-box"></i>
                    <span class="menu-title" data-i18n="">{{tr('stardom_products')}}</span>
                </a>
                <ul class="menu-content">
                    <li id="stardom_products-create">
                        <a class="menu-item" href="{{route('admin.stardom_products.create')}}">
                            {{tr('add_stardom_product')}}
                        </a>
                    </li>
                    <li id="stardom_products-view">
                        <a class="menu-item" href="{{route('admin.stardom_products.index')}}">
                            {{tr('view_stardom_products')}}
                        </a>
                    </li>
                </ul>            
            
            </li>

            <li class="nav-item" id="stardom_orders">
                <a href="{{route('admin.stardom_products.index')}}">
                    <i class="ft-book"></i>
                    <span class="menu-title" data-i18n="">Orders</span>
                </a>
                <ul class="menu-content">
                    <li id="stardom_products-create">
                        <a class="menu-item" href="{{route('admin.stardom_products.create')}}">
                            {{tr('add_stardom_product')}}
                        </a>
                    </li>
                    <li id="stardom_products-view">
                        <a class="menu-item" href="{{route('admin.stardom_products.index')}}">
                            {{tr('view_stardom_products')}}
                        </a>
                    </li>
                </ul>            
            
            </li>

            <li class="nav-item" id="inventory">
                <a href="{{route('admin.settings')}}">
                    <i class="fa fa-shopping-bag"></i>
                    <span class="menu-title" data-i18n="">{{tr('inventory')}}</span>
                </a>
            
            </li>

            <!--  products_management end -->

            <li class="navigation-header">
                <span>{{tr('revenue_management')}}</span>
            </li>

            <li class="nav-item" id="settings">
                <a href="{{route('admin.settings')}}">
                    <i class="ft-globe"></i>
                    <span class="menu-title" data-i18n="">Dashboard</span>
                </a>
            </li>

            <li class="nav-item" id="payments">
                <a href="{{route('admin.documents.index')}}">
                    <i class="ft-file"></i>
                    <span class="menu-title" data-i18n="">Payments</span>
                </a>
                <ul class="menu-content">
                    <li id="post-payments">
                        <a class="menu-item" href="{{route('admin.post.payments')}}">
                           {{tr('post_payments')}}
                        </a>
                    </li>
                </ul>            
            
            </li>

            <li class="nav-item" id="payments">
                <a href="{{route('admin.documents.index')}}">
                    <i class="ft-file"></i>
                    <span class="menu-title" data-i18n="">Redeems</span>
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

            <li class="nav-item" id="payments">
                <a href="{{route('admin.documents.index')}}">
                    <i class="ft-file"></i>
                    <span class="menu-title" data-i18n="">Subscriptions</span>
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

            <!-- lookups_management start -->

            <li class="navigation-header">
                <span>{{tr('lookups_management')}}</span>
            </li>

            <li class="nav-item" id="documents">
                <a href="{{route('admin.documents.index')}}">
                    <i class="ft-file"></i>
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
                    <i class="ft-user"></i>
                    <span class="menu-title" data-i18n="">{{tr('static_pages')}}</span>
                </a>
                <ul class="menu-content">
                    <li id="static_pages-create">
                        <a class="menu-item" href="{{route('admin.static_pages.create')}}">
                            {{tr('add_user')}}
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

            <li class="nav-item" id="settings">
                <a href="{{route('admin.settings')}}">
                    <i class="fa fa-user"></i>
                    <span class="menu-title" data-i18n="">{{tr('account')}}</span>
                </a>
            </li>
        
            <li class="nav-item">
                <a data-toggle="modal" data-target="#logoutModel" href="{{route('admin.logout')}}">
                    <i class="ft-power"></i>
                    <span class="menu-title" data-i18n="">{{tr('logout')}}</span>
                </a>
            </li>

        </ul>
    </div>
</div>