<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('public/dist/assets/img/lguseal.png') ?>">


    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <!--end::Accessibility Meta Tags-->

    <!--begin::Primary Meta Tags-->
    <meta name="title" content="AdminLTE v4 | Dashboard" />
    <meta name="author" content="ColorlibHQ" />
    <meta
        name="description"
        content="Field Health Services Information System (FHSIS) — LGU Lantapan" />
    <meta
        name="keywords"
        content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard, accessible admin panel, WCAG compliant" />
    <!--end::Primary Meta Tags-->

    <!--begin::Accessibility Features-->
    <!-- Skip links will be dynamically added by accessibility.js -->
    <meta name="supported-color-schemes" content="light dark" />
    <link rel="preload" href="<?= base_url() ?>/public/dist/css/adminlte.css" as="style" />
    <!--end::Accessibility Features-->

    <!--begin::Fonts-->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
        crossorigin="anonymous"
        media="print"
        onload="this.media='all'" />
    <!--end::Fonts-->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <!--end::Third Party Plugin(Bootstrap Icons)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="<?= base_url() ?>/public/dist/css/adminlte.css" />
    <!--end::Required Plugin(AdminLTE)-->

    <!-- apexcharts -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0="
        crossorigin="anonymous" />

    <!-- jsvectormap -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
        integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4="
        crossorigin="anonymous" />

    <!--MY CSS-->
    <link rel="stylesheet" href="<?= base_url() ?>/public/dist/css/mycss.css" />
    <!--end:: MY CSS-->

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="<?= base_url('public/dist/plugins/sweetalert2/sweetalert2.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/dist/plugins/sweetalert2/sweetalert2.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/dist/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/dist/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') ?>">
    <!-- SweetAlert2 -->

    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('public/dist/plugins/fontawesome-free-7.0.1-web/css/all.min.css') ?>">
    <!-- Font Awesome -->

    <!-- Datatables -->
    <link href="<?= base_url('public/dist/plugins/datatables/datatables.min.css') ?>" rel="stylesheet">
    <!-- Datatables -->

    <script data-cfasync="false" nonce="cda52b14-b0cb-4a77-bb1f-0f7cf2c810a6">
        try {
            (function(w, d) {
                ! function(j, k, l, m) {
                    if (j.zaraz) console.error("zaraz is loaded twice");
                    else {
                        j[l] = j[l] || {};
                        j[l].executed = [];
                        j.zaraz = {
                            deferred: [],
                            listeners: []
                        };
                        j.zaraz._v = "5870";
                        j.zaraz._n = "cda52b14-b0cb-4a77-bb1f-0f7cf2c810a6";
                        j.zaraz.q = [];
                        j.zaraz._f = function(n) {
                            return async function() {
                                var o = Array.prototype.slice.call(arguments);
                                j.zaraz.q.push({
                                    m: n,
                                    a: o
                                })
                            }
                        };
                        for (const p of ["track", "set", "debug"]) j.zaraz[p] = j.zaraz._f(p);
                        j.zaraz.init = () => {
                            var q = k.getElementsByTagName(m)[0],
                                r = k.createElement(m),
                                s = k.getElementsByTagName("title")[0];
                            s && (j[l].t = k.getElementsByTagName("title")[0].text);
                            j[l].x = Math.random();
                            j[l].w = j.screen.width;
                            j[l].h = j.screen.height;
                            j[l].j = j.innerHeight;
                            j[l].e = j.innerWidth;
                            j[l].l = j.location.href;
                            j[l].r = k.referrer;
                            j[l].k = j.screen.colorDepth;
                            j[l].n = k.characterSet;
                            j[l].o = (new Date).getTimezoneOffset();
                            if (j.dataLayer)
                                for (const t of Object.entries(Object.entries(dataLayer).reduce((u, v) => ({
                                        ...u[1],
                                        ...v[1]
                                    }), {}))) zaraz.set(t[0], t[1], {
                                    scope: "page"
                                });
                            j[l].q = [];
                            for (; j.zaraz.q.length;) {
                                const w = j.zaraz.q.shift();
                                j[l].q.push(w)
                            }
                            r.defer = !0;
                            for (const x of [localStorage, sessionStorage]) Object.keys(x || {}).filter(z => z.startsWith("_zaraz_")).forEach(y => {
                                try {
                                    j[l]["z_" + y.slice(7)] = JSON.parse(x.getItem(y))
                                } catch {
                                    j[l]["z_" + y.slice(7)] = x.getItem(y)
                                }
                            });
                            r.referrerPolicy = "origin";
                            r.src = "/cdn-cgi/zaraz/s.js?z=" + btoa(encodeURIComponent(JSON.stringify(j[l])));
                            q.parentNode.insertBefore(r, q)
                        };
                        ["complete", "interactive"].includes(k.readyState) ? zaraz.init() : j.addEventListener("DOMContentLoaded", zaraz.init)
                    }
                }(w, d, "zarazData", "script");
                window.zaraz._p = async bs => new Promise(bt => {
                    if (bs) {
                        bs.e && bs.e.forEach(bu => {
                            try {
                                const bv = d.querySelector("script[nonce]"),
                                    bw = bv?.nonce || bv?.getAttribute("nonce"),
                                    bx = d.createElement("script");
                                bw && (bx.nonce = bw);
                                bx.innerHTML = bu;
                                bx.onload = () => {
                                    d.head.removeChild(bx)
                                };
                                d.head.appendChild(bx)
                            } catch (by) {
                                console.error(`Error executing script: ${bu}\n`, by)
                            }
                        });
                        Promise.allSettled((bs.f || []).map(bz => fetch(bz[0], bz[1])))
                    }
                    bt()
                });
                zaraz._p({
                    "e": ["(function(w,d){})(window,document)"]
                });
            })(window, document)
        } catch (e) {
            throw fetch("/cdn-cgi/zaraz/t"), e;
        };
    </script>


    <?php

    function getCurrentPageName($withIcon = false)
    {
        $uri = uri_string();
        switch ($uri) {
            
            case 'dashboard':
                return $withIcon
                    ? '<i class="bi bi-table me-2"></i>Dashboard'
                    : 'Dashboard';

            case 'sections':
                return $withIcon
                    ? '<i class="bi bi-puzzle-fill me-2"></i>Sections'
                    : 'Sections';

            case 'famplanning':
                return $withIcon
                    ? '<i class="nav-icon bi bi-database-add me-2"></i> ADD ENTRY'
                    : 'ENTRY — A. Family Planning Services for Women of Reproductive Age';

            case 'maternal':
                return $withIcon
                    ? '<i class="nav-icon bi bi-database-add me-2"></i> ADD ENTRY'
                    : 'ENTRY — B. Maternal Care and Services';

            case 'child':
                return $withIcon
                    ? '<i class="nav-icon bi bi-database-add me-2"></i> ADD ENTRY'
                    : 'ENTRY — C. Child Care and Services';

            case 'oral':
                return $withIcon
                    ? '<i class="nav-icon bi bi-database-add me-2"></i> ADD ENTRY'
                    : 'ENTRY — D. Oral Health Care Services';

            case 'ncdisease':
                return $withIcon
                    ? '<i class="nav-icon bi bi-database-add me-2"></i> ADD ENTRY'
                    : 'ENTRY — E. Non-Communicable Diseases';

            case 'envi':
                return $withIcon
                    ? '<i class="nav-icon bi bi-database-add me-2"></i> ADD ENTRY'
                    : 'ENTRY — F. Environmental Health and Sanitation';

            case 'idisease':
                return $withIcon
                    ? '<i class="nav-icon bi bi-database-add me-2"></i> ADD ENTRY'
                    : 'ENTRY — G. Infectious Disease Prevention and Control Services';

            case 'reports':
                return $withIcon
                    ? '<i class="nav-icon bi bi-printer-fill me-2"></i> GENERATE REPORT'
                    : 'GENERATE REPORT';

            default:
                return $withIcon
                    ? '<i class="bi bi-file-earmark me-2"></i>' . ucfirst($uri)
                    : ucfirst($uri);
        }
    }

    ?>

    <title><?= getCurrentPageName(false) ?> — FHSIS</title>

</head>