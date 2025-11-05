<?= $this->extend('template/admin_template') ?>

<?= $this->section('content') ?>
<div class="app-content-header">

</div>

<div class="app-content">

    <div class="container-fluid">

        <div class="row">

            <div class="col-12">
                <div class="info-box text-bg-secondary">
                    <span class="info-box-icon">
                        <i class="bi bi-virus"></i>
                    </span>

                    <div class="info-box-content">
                        <span class="info-box-text fs-4 fw-bold">G. Infectious Disease Prevention and Control Services</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>

        </div>

        <div class="row mb-2">

            <div class="col-12 mb-2">

                <div class="card card-success card-outline">

                    <div class="card-body">

                        <div class="row">

                            <div class="col-2">

                                <div class="form-group">

                                    <select id="report_year" name="report_year" class="form-select btn btn-success dropdown-toggle">
                                        <?php
                                        $currentYear = date('Y');
                                        for ($year = $currentYear; $year >= 2025; $year--) {
                                            $selected = ($year == $currentYear) ? 'selected' : '';
                                            echo '<option value="' . $year . '" ' . $selected . '>' . $year . '</option>';
                                        }
                                        ?>
                                    </select>

                                </div>

                            </div>

                            <div class="col-3">

                                <div class="form-group">

                                    <select id="report_month" name="report_month" class="form-select btn btn-success dropdown-toggle">
                                        <option value="1">January</option>
                                        <option value="2">February</option>
                                        <option value="3">March</option>
                                        <option value="4">April</option>
                                        <option value="5">May</option>
                                        <option value="6">June</option>
                                        <option value="7">July</option>
                                        <option value="8">August</option>
                                        <option value="9">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>

                                    </select>

                                </div>

                            </div>

                            <div class="col-7">

                                <div class="form-group">

                                    <select id="barangay_code" name="barangay_code" class="form-select btn btn-primary dropdown-toggle">
                                        <?php foreach ($barangays as $barangay): ?>
                                            <option value="<?= $barangay['code']; ?>">
                                                <?= $barangay['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-12">

                <div class="card card-success card-outline">

                    <div class="card-body">

                        <div class="row mb-2">

                            <!-- A. Filariasis -->
                            <div class="col-4">

                                <div class="card">

                                    <div class="card-header text-center fw-bold">A. Filariasis</div>

                                    <div class="card-body">

                                        <form class="needs-validation entriesForm" data-subsection="id1" novalidate>

                                            <select id="indicator_id" name="indicator_id" class="mb-2 form-select btn btn-success dropdown-toggle">
                                                <?php foreach ($id1Indicators as $id1indicator): ?>
                                                    <option value="<?= $id1indicator['id']; ?>">
                                                        <?= $id1indicator['code'] . ". " . $id1indicator['name']; ?>
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
                                                        <td><input type="number" class="form-control" data-sex="male" data-subsection="id1"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Female</td>
                                                        <td><input type="number" class="form-control" data-sex="female" data-subsection="id1"></td>
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

                            <!-- B. Rabies -->
                            <div class="col-4">

                                <div class="card">

                                    <div class="card-header text-center fw-bold">B. Rabies</div>

                                    <div class="card-body">

                                        <form class="needs-validation entriesForm" data-subsection="id2" novalidate>

                                            <select id="indicator_id" name="indicator_id" class="mb-2 form-select btn btn-success dropdown-toggle">
                                                <?php foreach ($id2Indicators as $id2indicator): ?>
                                                    <option value="<?= $id2indicator['id']; ?>">
                                                        <?= $id2indicator['code'] . ". " . $id2indicator['name']; ?>
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
                                                        <td><input type="number" class="form-control" data-sex="male" data-subsection="id2"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Female</td>
                                                        <td><input type="number" class="form-control" data-sex="female" data-subsection="id2"></td>
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

                            <!-- C. Schistosomiasis -->
                            <div class="col-4">

                                <div class="card">

                                    <div class="card-header text-center fw-bold">C. Schistosomiasis</div>

                                    <div class="card-body">

                                        <form class="needs-validation entriesForm" data-subsection="id3" novalidate>

                                            <select id="indicator_id" name="indicator_id" class="mb-2 form-select btn btn-success dropdown-toggle">
                                                <?php foreach ($id3Indicators as $id3indicator): ?>
                                                    <option value="<?= $id3indicator['id']; ?>">
                                                        <?= $id3indicator['code'] . ". " . $id3indicator['name']; ?>
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
                                                        <td><input type="number" class="form-control" data-sex="male" data-subsection="id3"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Female</td>
                                                        <td><input type="number" class="form-control" data-sex="female" data-subsection="id3"></td>
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

                        <div class="row mb-2">

                            <!-- D. Soil-Transmitted Helminthiasis -->
                            <div class="col-4">

                                <div class="card">

                                    <div class="card-header text-center fw-bold">D. Soil-Transmitted Helminthiasis</div>

                                    <div class="card-body">

                                        <form class="needs-validation entriesForm" data-subsection="id4" novalidate>

                                            <select id="indicator_id" name="indicator_id" class="mb-2 form-select btn btn-success dropdown-toggle">
                                                <?php foreach ($id4Indicators as $id4indicator): ?>
                                                    <option value="<?= $id4indicator['id']; ?>">
                                                        <?= $id4indicator['code'] . ". " . $id4indicator['name']; ?>
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
                                                        <td><input type="number" class="form-control" data-sex="male" data-subsection="id4"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Female</td>
                                                        <td><input type="number" class="form-control" data-sex="female" data-subsection="id4"></td>
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

                            <!-- E. Leprosy -->
                            <div class="col-4">

                                <div class="card">

                                    <div class="card-header text-center fw-bold">E. Leprosy</div>

                                    <div class="card-body">

                                        <form class="needs-validation entriesForm" data-subsection="id5" novalidate>

                                            <select id="indicator_id" name="indicator_id" class="mb-2 form-select btn btn-success dropdown-toggle">
                                                <?php foreach ($id5Indicators as $id5indicator): ?>
                                                    <option value="<?= $id5indicator['id']; ?>">
                                                        <?= $id5indicator['code'] . ". " . $id5indicator['name']; ?>
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
                                                        <td><input type="number" class="form-control" data-sex="male" data-subsection="id5"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Female</td>
                                                        <td><input type="number" class="form-control" data-sex="female" data-subsection="id5"></td>
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

                            <!-- F. HIV-AIDS/STI -->
                            <div class="col-4">

                                <div class="card">

                                    <div class="card-header text-center fw-bold">F. HIV-AIDS/STI</div>

                                    <div class="card-body">

                                        <form class="needs-validation entriesForm" data-subsection="id6" novalidate>

                                            <select id="indicator_id" name="indicator_id" class="mb-2 form-select btn btn-success dropdown-toggle">
                                                <?php foreach ($id6Indicators as $id6indicator): ?>
                                                    <option value="<?= $id6indicator['id']; ?>">
                                                        <?= $id6indicator['code'] . ". " . $id6indicator['name']; ?>
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
                                                        <td><input type="number" class="form-control" data-sex="male" data-subsection="id6"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Female</td>
                                                        <td><input type="number" class="form-control" data-sex="female" data-subsection="id6"></td>
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
                url: "<?= base_url('getIDisease'); ?>",
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
                const input = $(this).find('input[type="number"]');
                const sex = input.data('sex');
                const value = input.val() || 0;

                entries.push({
                    sex,
                    value
                });
            });

            if (this.checkValidity()) {

                $.ajax({
                    url: "<?= base_url('saveIDisease'); ?>",
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
</script>
<?= $this->endSection() ?>