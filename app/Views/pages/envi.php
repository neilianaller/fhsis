<?= $this->extend('template/admin_template') ?>

<?= $this->section('content') ?>
<div class="app-content-header">

</div>

<div class="app-content">

    <div class="container-fluid">

        <div class="row">

            <div class="col-12">
                <div class="info-box text-bg-success">
                    <span class="info-box-icon">
                        <i class="bi bi-tree-fill"></i>
                    </span>

                    <div class="info-box-content">
                        <span class="info-box-text fs-4 fw-bold">F. Environmental Health and Sanitation</span>
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

                            <!-- Water -->
                            <div class="col-4">

                                <div class="card">

                                    <div class="card-header text-center fw-bold">Water</div>

                                    <div class="card-body">

                                        <form class="needs-validation entriesForm" data-subsection="e1" novalidate>

                                            <select id="indicator_id" name="indicator_id" class="mb-2 form-select btn btn-success dropdown-toggle">
                                                <?php foreach ($e1Indicators as $e1indicator): ?>
                                                    <option value="<?= $e1indicator['id']; ?>">
                                                        <?= $e1indicator['code'] . ". " . $e1indicator['name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>

                                            <table class="table table-bordered text-center">
                                                <tbody>
                                                    <tr>
                                                        <td>Total</td>
                                                        <td><input type="number" class="form-control" data-subsection="e1"></td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
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

                            <!-- Sanitation -->
                            <div class="col-4">

                                <div class="card">

                                    <div class="card-header text-center fw-bold">Sanitation</div>

                                    <div class="card-body">

                                        <form class="needs-validation entriesForm" data-subsection="e2" novalidate>

                                            <select id="indicator_id" name="indicator_id" class="mb-2 form-select btn btn-success dropdown-toggle">
                                                <?php foreach ($e2Indicators as $e2indicator): ?>
                                                    <option value="<?= $e2indicator['id']; ?>">
                                                        <?= $e2indicator['code'] . ". " . $e2indicator['name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>

                                            <table class="table table-bordered text-center">
                                                <tbody>
                                                    <tr>
                                                        <td>Total</td>
                                                        <td><input type="number" class="form-control" data-subsection="e2"></td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
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

                            <!--  -->
                            <div class="col-4">

                                <div class="card">

                                    <div class="card-header text-center fw-bold">&nbsp;</div>

                                    <div class="card-body">

                                        <form class="needs-validation entriesForm" data-subsection="e3" novalidate>

                                            <select id="indicator_id" name="indicator_id" class="mb-2 form-select btn btn-success dropdown-toggle">
                                                <?php foreach ($e3Indicators as $e3indicator): ?>
                                                    <option value="<?= $e3indicator['id']; ?>">
                                                        <?= $e3indicator['code'] . ". " . $e3indicator['name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>

                                            <table class="table table-bordered text-center">
                                                <tbody>
                                                    <tr>
                                                        <td>Total</td>
                                                        <td><input type="number" class="form-control" data-subsection="e3"></td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
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
                url: "<?= base_url('get'); ?>/" + 'F',
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
                            const input = form.find(`input[data-subsection="${entry.subsection}"]`);
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
                const agegroup = input.data('agegroup') || null;
                const value = input.val() || 0;

                entries.push({
                    sex,
                    agegroup,
                    value
                });
            });

            if (this.checkValidity()) {

                $.ajax({
                    url: "<?= base_url('save'); ?>/" + 'F',
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