<nav class="app-header navbar navbar-expand bg-body">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Start Navbar Links-->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list me-2"></i>
                    <span id="toggleSpan"></span>
                </a>
            </li>


            <li class="nav-item">
                <span class="nav-link text-bold">
                    <?= getCurrentPageName(true) ?>
                </span>
            </li>
        </ul>
        <!--end::Start Navbar Links-->

        <!--begin::End Navbar Links-->
        <ul class="navbar-nav ms-auto">

            <!--begin::Fullscreen Toggle-->
            <li class="nav-item">
                <a class="nav-link" href="#" id="logoutLink">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout</a>
            </li>
            <!--end::Fullscreen Toggle-->

        </ul>
        <!--end::End Navbar Links-->
    </div>
    <!--end::Container-->
</nav>
<!--end::Header-->

<!--begin::Sidebar-->
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <!--begin::Brand Link-->
        <span class="brand-link">
            <!--begin::Brand Image-->
            <img
                src="public/dist/assets/img/lguseal.png"
                alt="AdminLTE Logo"
                class="brand-image opacity-75 shadow" />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light">FHSIS</span>
            <!--end::Brand Text-->
        </span>
        <!--end::Brand Link-->
    </div>
    <!--end::Sidebar Brand-->
    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul
                class="nav sidebar-menu flex-column"
                data-lte-toggle="treeview"
                role="navigation"
                aria-label="Main navigation"
                data-accordion="false"
                id="navigation">

                <!-- <li class="nav-item">
                    <a href="<?= base_url('dashboard') ?>" class="nav-link <?= (uri_string() == 'dashboard') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-table"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li> -->

                <li class="nav-item">
                    <a href="<?= base_url('reports') ?>" class="nav-link <?= (uri_string() == 'reports') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-printer-fill"></i>
                        <p>
                            Generate Report
                        </p>
                    </a>
                </li>

                <?php
                $uri = uri_string();
                $addEntryPages = ['famplanning', 'maternal', 'child', 'oral', 'ncdisease', 'envi', 'idisease'];

                $isAddEntryActive = in_array($uri, $addEntryPages);
                ?>

                <li class="nav-item menu-open">
                    
                    <a href="#" class="nav-link parent">
                        <i class="nav-icon bi bi-database-add"></i>
                        <p>
                            Add Entry
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>

                    <ul class="ps-3 nav nav-treeview">

                        <li class="nav-item">
                            <a href="<?= base_url('famplanning') ?>" class="nav-link <?= (uri_string() == 'famplanning') ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-people-fill"></i>
                                <p>
                                    A. Family Planning
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?= base_url('maternal') ?>" class="nav-link <?= (uri_string() == 'maternal') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-person-pregnant"></i>
                                <p>
                                    B. Maternal
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?= base_url('child') ?>" class="nav-link <?= (uri_string() == 'child') ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-person-arms-up"></i>
                                <p>
                                    C. Child
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?= base_url('oral') ?>" class="nav-link <?= (uri_string() == 'oral') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-tooth"></i>
                                <p>
                                    D. Oral
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?= base_url('ncdisease') ?>" class="nav-link <?= (uri_string() == 'ncdisease') ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-virus2"></i>
                                <p>
                                    E. Non-Commun Diseases
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?= base_url('envi') ?>" class="nav-link <?= (uri_string() == 'envi') ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-tree-fill"></i>
                                <p>
                                    F. Environmental
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?= base_url('idisease') ?>" class="nav-link <?= (uri_string() == 'idisease') ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-virus"></i>
                                <p>
                                    G. Infectious Diseases
                                </p>
                            </a>
                        </li>

                    </ul>

                </li>

                <?php if (auth()->loggedIn() && (auth()->user()->inGroup('admin'))) { ?>

                    <li class="nav-item">
                        <a href="<?= base_url('sections') ?>" class="nav-link <?= (uri_string() == 'sections') ? 'active' : '' ?>">
                            <i class="nav-icon bi bi-puzzle-fill"></i>
                            <p>
                                Sections
                            </p>
                        </a>
                    </li>

                <?php } ?>

            </ul>
            <!--end::Sidebar Menu-->
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>

<script>
    // LOGOUT
    document.getElementById('logoutLink').addEventListener('click', function(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "You will be logged out!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Proceed'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= base_url('logout') ?>';
            }
        })
    });
</script>