<?= $this->extend('template/admin_template') ?>

<?= $this->section('content') ?>
<div class="app-content-header">

    <div class="container-fluid">

    </div>

</div>

<div class="app-content">

    <div class="container-fluid">

        <div class="row">

            <div class="col-12">
                <div class="info-box bg-warning text-white">
                    <span class="info-box-icon">
                        <i class="bi bi-person-arms-up"></i>
                    </span>

                    <div class="info-box-content">
                        <span class="info-box-text fs-4 fw-bold">C. Child Care and Services</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>

        </div>

        <div class="row mb-2">

            <?php include 'form1.php' ?>

            <div class="col-12">

                <div class="card card-success card-outline">

                    <div class="card-body">

                        <div class="row mb-2">

                            <div class="col-12">

                                <!-- C.1 IMMUNIZATION -->
                                <div class="card card-success card-outline">

                                    <div class="card-header fw-bold text-center">C.1 IMMUNIZATION</div>

                                    <div class="card-body">

                                        <div class="row mb-2">

                                            <!-- A.1 IMMUNIZATION SERVICES -->
                                            <div class="col-3">

                                                <div class="card">

                                                    <div class="card-header text-center fw-bold">A.1 Immunization Services</div>

                                                    <div class="card-body">

                                                        <form class="needs-validation entriesForm" data-subsection="ca1" novalidate>

                                                            <select id="indicator_id" name="indicator_id" class="mb-2 form-select btn btn-success dropdown-toggle">
                                                                <?php foreach ($ca1Indicators as $ca1indicator): ?>
                                                                    <option value="<?= $ca1indicator['id']; ?>">
                                                                        <?= $ca1indicator['code'] . ". " . $ca1indicator['name']; ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>

                                                            <table class="table table-bordered text-center">
                                                                <thead>
                                                                    <td>Sex</td>
                                                                    <td></td>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>Male</td>
                                                                        <td><input type="number" class="form-control" data-sex="male" data-subsection="ca1"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Female</td>
                                                                        <td><input type="number" class="form-control" data-sex="female" data-subsection="ca1"></td>
                                                                    </tr>
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr>
                                                                        <td class="fw-bold">TOTAL</td>
                                                                        <td><input type="number" required readonly class="form-control fw-bold"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="2">
                                                                            <button type="submit" class="btn btn-success w-100">
                                                                                <i class="bi bi-save me-1"></i> Save
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>

                                                        </form>

                                                    </div>

                                                </div>

                                            </div>

                                            <!-- A.2 IMMUNIZATION SERVICES 0-12 MONTHS OLD -->
                                            <div class="col-3">

                                                <div class="card">

                                                    <div class="card-header text-center fw-bold">A.2 Immunization Services (0-12 months old)</div>

                                                    <div class="card-body">

                                                        <form class="needs-validation entriesForm" data-subsection="ca2" novalidate>

                                                            <select id="indicator_id" name="indicator_id" class="mb-2 form-select btn btn-success dropdown-toggle">
                                                                <?php foreach ($ca2Indicators as $ca2indicator): ?>
                                                                    <option value="<?= $ca2indicator['id']; ?>">
                                                                        <?= $ca2indicator['code'] . ". " . $ca2indicator['name']; ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>

                                                            <table class="table table-bordered text-center">
                                                                <thead>
                                                                    <td>Sex</td>
                                                                    <td></td>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>Male</td>
                                                                        <td><input type="number" class="form-control" data-sex="male" data-subsection="ca2"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Female</td>
                                                                        <td><input type="number" class="form-control" data-sex="female" data-subsection="ca2"></td>
                                                                    </tr>
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr>
                                                                        <td class="fw-bold">TOTAL</td>
                                                                        <td><input type="number" required readonly class="form-control fw-bold"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="2">
                                                                            <button type="submit" class="btn btn-success w-100">
                                                                                <i class="bi bi-save me-1"></i> Save
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>

                                                        </form>

                                                    </div>

                                                </div>

                                            </div>

                                            <!-- A.3 IMMUNIZATION SERVICES 13-23 MONTHS OLD -->
                                            <div class="col-3">

                                                <div class="card">

                                                    <div class="card-header text-center fw-bold">A.3 Immunization Services (13-23 months old)</div>

                                                    <div class="card-body">

                                                        <form class="needs-validation entriesForm" data-subsection="ca3" novalidate>

                                                            <select id="indicator_id" name="indicator_id" class="mb-2 form-select btn btn-success dropdown-toggle">
                                                                <?php foreach ($ca3Indicators as $ca3indicator): ?>
                                                                    <option value="<?= $ca3indicator['id']; ?>">
                                                                        <?= $ca3indicator['code'] . ". " . $ca3indicator['name']; ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>

                                                            <table class="table table-bordered text-center">
                                                                <thead>
                                                                    <td>Sex</td>
                                                                    <td></td>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>Male</td>
                                                                        <td><input type="number" class="form-control" data-sex="male" data-subsection="ca3"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Female</td>
                                                                        <td><input type="number" class="form-control" data-sex="female" data-subsection="ca3"></td>
                                                                    </tr>
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr>
                                                                        <td class="fw-bold">TOTAL</td>
                                                                        <td><input type="number" required readonly class="form-control fw-bold"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="2">
                                                                            <button type="submit" class="btn btn-success w-100">
                                                                                <i class="bi bi-save me-1"></i> Save
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>

                                                        </form>

                                                    </div>

                                                </div>

                                            </div>

                                            <!-- A.4 SCHOOL BASED IMMUNIZATION -->
                                            <div class="col-3">

                                                <div class="card">

                                                    <div class="card-header text-center fw-bold">A.4 School Based Immunization</div>

                                                    <div class="card-body">

                                                        <form class="needs-validation entriesForm" data-subsection="ca4" novalidate>

                                                            <select id="indicator_id" name="indicator_id" class="mb-2 form-select btn btn-success dropdown-toggle">
                                                                <?php foreach ($ca4Indicators as $ca4indicator): ?>
                                                                    <option value="<?= $ca4indicator['id']; ?>">
                                                                        <?= $ca4indicator['code'] . ". " . $ca4indicator['name']; ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>

                                                            <table class="table table-bordered text-center">
                                                                <thead>
                                                                    <td>Sex</td>
                                                                    <td></td>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>Male</td>
                                                                        <td><input type="number" class="form-control" data-sex="male" data-subsection="ca4"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Female</td>
                                                                        <td><input type="number" class="form-control" data-sex="female" data-subsection="ca4"></td>
                                                                    </tr>
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr>
                                                                        <td class="fw-bold">TOTAL</td>
                                                                        <td><input type="number" required readonly class="form-control fw-bold"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="2">
                                                                            <button type="submit" class="btn btn-success w-100">
                                                                                <i class="bi bi-save me-1"></i> Save
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>

                                                        </form>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="row mb-2">

                            <!-- C.2 NUTRITION -->
                            <div class="col-6">

                                <div class="card card-success card-outline">

                                    <div class="card-header text-center fw-bold">NUTRITION</div>

                                    <div class="card-body">

                                        <form class="needs-validation entriesForm" data-subsection="cb" novalidate>

                                            <select id="indicator_id" name="indicator_id" class="mb-2 form-select btn btn-success dropdown-toggle">
                                                <?php foreach ($cbIndicators as $cbindicator): ?>
                                                    <option value="<?= $cbindicator['id']; ?>">
                                                        <?= $cbindicator['code'] . ". " . $cbindicator['name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>

                                            <table class="table table-bordered text-center">
                                                <thead>
                                                    <td>Sex</td>
                                                    <td></td>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Male</td>
                                                        <td><input type="number" class="form-control" data-sex="male" data-subsection="cb"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Female</td>
                                                        <td><input type="number" class="form-control" data-sex="female" data-subsection="cb"></td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td class="fw-bold">TOTAL</td>
                                                        <td><input type="number" required readonly class="form-control fw-bold"></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2">
                                                            <button type="submit" class="btn btn-success w-100">
                                                                <i class="bi bi-save me-1"></i> Save
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>

                                        </form>

                                    </div>

                                </div>

                            </div>

                            <!-- C.2 MANAGEMENT OF SICK -->
                            <div class="col-6">

                                <div class="card card-success card-outline">

                                    <div class="card-header text-center fw-bold">MANAGEMENT OF SICK</div>

                                    <div class="card-body">

                                        <form class="needs-validation entriesForm" data-subsection="cc" novalidate>

                                            <select id="indicator_id" name="indicator_id" class="mb-2 form-select btn btn-success dropdown-toggle">
                                                <?php foreach ($ccIndicators as $ccindicator): ?>
                                                    <option value="<?= $ccindicator['id']; ?>">
                                                        <?= $ccindicator['code'] . ". " . $ccindicator['name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>

                                            <table class="table table-bordered text-center">
                                                <thead>
                                                    <td>Sex</td>
                                                    <td></td>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Male</td>
                                                        <td><input type="number" class="form-control" data-sex="male" data-subsection="cc"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Female</td>
                                                        <td><input type="number" class="form-control" data-sex="female" data-subsection="cc"></td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td class="fw-bold">TOTAL</td>
                                                        <td><input type="number" required readonly class="form-control fw-bold"></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2">
                                                            <button type="submit" class="btn btn-success w-100">
                                                                <i class="fas fa-save me-1"></i> Save
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>

                                        </form>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('javascripts') ?>
<script>
    $(document).ready(function() {

        // Load entries function
        function loadEntries(form) {
            console.log('Entries lock and loaded!');
            const barangay = $('#barangay_code').val();
            const month = $('#report_month').val();
            const year = $('#report_year').val();
            const indicator_id = form.find('select[name="indicator_id"]').val();
            const subsection = form.data('subsection'); // scoped!

            $.ajax({
                url: "<?= base_url('get'); ?>/C",
                method: "GET",
                data: {
                    barangay_code: barangay,
                    report_month: month,
                    report_year: year,
                    indicator_id: indicator_id,
                    subsection: subsection
                },
                success: function(response) {
                    if (response.status === 'success') {
                        const entries = response.data;
                        form.find('table input[type="number"]').val(''); // clear current form only

                        entries.forEach(entry => {
                            const input = form.find(`input[data-sex="${entry.sex}"][data-subsection="${entry.subsection}"]`);
                            if (input.length) {
                                input.val(entry.value);
                                input.trigger('input'); // recalc total
                            }
                        });
                    } else {
                        console.warn(response.message);
                    }
                },
                error: function(xhr) {
                    console.error("Error loading entries", xhr.responseText);
                }
            });
        }

        // Load entries for each form on page ready
        $('.entriesForm').each(function() {
            loadEntries($(this));
        });

        // Reload entries when filters change
        $('#barangay_code, #report_month, #report_year').change(function() {
            $('.entriesForm').each(function() {
                loadEntries($(this));
            });
        });

        // Also reload when indicator in a form changes
        $('.entriesForm select[name="indicator_id"]').change(function() {
            const form = $(this).closest('form');
            loadEntries(form);
        });

        // Handle submit
        $('.entriesForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const subsection = form.data('subsection'); // 👈 gets the data-subsection value
            const barangay = $('#barangay_code').val();
            const month = $('#report_month').val();
            const year = $('#report_year').val();
            const indicatorId = form.find('select[name="indicator_id"]').val();

            const entries = [];
            form.find('tbody tr').each(function() {
                const sex = $(this).find('td:first').text().trim().toLowerCase();
                const value = $(this).find('input[type="number"]').val() || 0;
                entries.push({
                    sex,
                    value
                });
            });

            if (this.checkValidity()) {

                $.ajax({
                    url: "<?= base_url('save'); ?>/C",
                    type: "POST",
                    dataType: "json",
                    data: {
                        barangay_code: barangay,
                        report_month: month,
                        report_year: year,
                        subsection: subsection,
                        indicatorId: indicatorId,
                        entries: entries
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Saved!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            loadEntries(form);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Request Failed',
                            text: error
                        });
                    }
                });
            }
        });


        'use strict';
        let form = $(".needs-validation");
        form.each(function() {
            $(this).on('submit', function(e) {
                if (this.checkValidity() === false) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                $(this).addClass('was-validated');
            });
        });


        // When user types in any number input inside a table
        $(document).on('input', 'table input[type="number"]:not([readonly])', function() {
            const table = $(this).closest('table');
            let total = 0;

            // Sum all number inputs in the tbody
            table.find('tbody input[type="number"]').each(function() {
                const val = parseFloat($(this).val()) || 0;
                total += val;
            });

            // Update the readonly TOTAL field in the same table
            table.find('tfoot input[readonly]').val(total);
        });

    });

    $(document).ready(function() {
        $('.entriesForm[data-subsection="ca4"]').each(function() {
            let form = $(this);

            form.find('#indicator_id').on('change', function() {
                let selectedId = parseInt($(this).val());
                const hideMaleFor = [103, 104];

                // Get the male row (parent <tr>)
                let maleRow = form.find('input[data-sex="male"]').closest('tr');

                if (hideMaleFor.includes(selectedId)) {
                    maleRow.hide();
                } else {
                    maleRow.show();
                }
            }).trigger('change'); // run once on load
        });
    });
</script>
<?= $this->endSection() ?>