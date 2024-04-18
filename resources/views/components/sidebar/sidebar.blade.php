<div
    class="vertical-menu rtl:right-0 fixed ltr:left-0 bottom-0 top-16 h-screen border-r bg-slate-50 border-gray-50 print:hidden dark:bg-zinc-800 dark:border-neutral-700 z-10">

    <div data-simplebar class="h-full">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu" id="side-menu">
                <x-sidebar.divider title="Menu" />
                <x-sidebar.first-single title="Dashboard" key="dashboard" icon="home"
                    url="{{ route('admin.dashboard') }}" />
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
