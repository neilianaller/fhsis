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
                <div class="info-box bg-success text-white">
                    <span class="info-box-icon">
                        <i class="fas fa-person-pregnant"></i>
                    </span>

                    <div class="info-box-content">
                        <span class="info-box-text fs-4 fw-bold">B. Maternal Care and Services</span>
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

                            <div class="col-6">

                                <!-- B.1 PRENATAL CARE SERVICES -->
                                <div class="card card-success card-outline">

                                    <div class="card-header fw-bold text-center">B.1 PRENATAL SERVICES</div>

                                    <div class="card-body">

                                        <form class="needs-validation entriesForm" data-subsection="b1" novalidate>

                                            <select id="indicator_id" name="indicator_id" class="mb-2 form-select btn btn-success dropdown-toggle">
                                                <?php foreach ($b1Indicators as $b1indicator): ?>
                                                    <option value="<?= $b1indicator['id']; ?>">
                                                        <?= $b1indicator['code'] . ". " . $b1indicator['name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>

                                            <table class="table table-bordered text-center">
                                                <thead>
                                                    <td>Age Group</td>
                                                    <td></td>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>10-14</td>
                                                        <td><input type="number" class="form-control" data-agegroup="10-14" data-subsection="b1"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>15-19</td>
                                                        <td><input type="number" class="form-control" data-agegroup="15-19" data-subsection="b1"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>20-49</td>
                                                        <td><input type="number" class="form-control" data-agegroup="20-49" data-subsection="b1"></td>
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

                            <div class="col-6">

                                <!-- B.2 INTRAPARTUM AND NEWBORN CARE -->
                                <div class="card card-success card-outline">

                                    <div class="card-header fw-bold text-center">B.2 INTRAPARTUM AND NEWBORN CARE</div>

                                    <div class="card-body">

                                        <form class="needs-validation entriesForm" data-subsection="b2" novalidate>

                                            <select id="indicator_id" name="indicator_id" class="mb-2 form-select btn btn-success dropdown-toggle">
                                                <?php foreach ($b2Indicators as $b2indicator): ?>
                                                    <option value="<?= $b2indicator['id']; ?>">
                                                        <?= $b2indicator['code'] . ". " . $b2indicator['name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>

                                            <table class="table table-bordered text-center">
                                                <thead>
                                                    <td>Age Group</td>
                                                    <td></td>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>10-14</td>
                                                        <td><input type="number" class="form-control" data-agegroup="10-14" data-subsection="b2"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>15-19</td>
                                                        <td><input type="number" class="form-control" data-agegroup="15-19" data-subsection="b2"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>20-49</td>
                                                        <td><input type="number" class="form-control" data-agegroup="20-49" data-subsection="b2"></td>
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

                        <div class="row">

                            <div class="col-6">

                                <!-- B.3 POSTPARTUM CARE -->
                                <div class="card card-success card-outline">

                                    <div class="card-header fw-bold text-center">B.3 POSTPARTUM CARE</div>

                                    <div class="card-body">

                                        <form class="needs-validation entriesForm" data-subsection="b3" novalidate>

                                            <select id="indicator_id" name="indicator_id" class="mb-2 form-select btn btn-success dropdown-toggle">
                                                <?php foreach ($b3Indicators as $b3indicator): ?>
                                                    <option value="<?= $b3indicator['id']; ?>">
                                                        <?= $b3indicator['code'] . ". " . $b3indicator['name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>

                                            <table class="table table-bordered text-center">
                                                <thead>
                                                    <td>Age Group</td>
                                                    <td></td>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>10-14</td>
                                                        <td><input type="number" class="form-control" data-agegroup="10-14" data-subsection="b3"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>15-19</td>
                                                        <td><input type="number" class="form-control" data-agegroup="15-19" data-subsection="b3"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>20-49</td>
                                                        <td><input type="number" class="form-control" data-agegroup="20-49" data-subsection="b3"></td>
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
            const barangay = $('#barangay_code').val();
            const month = $('#report_month').val();
            const year = $('#report_year').val();
            const indicator_id = form.find('select[name="indicator_id"]').val();
            const subsection = form.data('subsection'); // scoped!

            if (!barangay || !month || !year || !indicator_id) return;

            $.ajax({
                url: "<?= base_url('get'); ?>/" + 'B',
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
                            const input = form.find(`input[data-agegroup="${entry.agegroup}"][data-subsection="${entry.subsection}"]`);
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
                const agegroup = $(this).find('td:first').text().trim();
                const value = $(this).find('input[type="number"]').val() || 0;
                entries.push({
                    agegroup,
                    value
                });
            });

            if (this.checkValidity()) {

                $.ajax({
                    url: "<?= base_url('save'); ?>/" + 'B',
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
    });

    // VALIDATION
    $(document).ready(function() {
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
    });

    $(document).ready(function() {

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