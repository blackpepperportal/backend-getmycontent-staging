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
                    <i class="ft-users"></i>
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
                    <i class="ft-user"></i>
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

            <li class="navigation-header">
                <span>{{tr('setting_management')}}</span>
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

            <li class="nav-item" id="settings">
                <a href="{{route('admin.settings')}}">
                    <i class="ft-globe"></i>
                    <span class="menu-title" data-i18n="">{{tr('settings')}}</span>
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