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
                <div class="info-box bg-primary text-white">
                    <span class="info-box-icon">
                        <i class="bi bi-people-fill"></i>
                    </span>

                    <div class="info-box-content">
                        <span class="info-box-text fs-4 fw-bold">A. Family Planning Services for Women of Reproductive Age</span>
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

                            <div class="form-group">

                                <select id="indicator_id" name="indicator_id" class="form-select btn btn-success dropdown-toggle">
                                    <?php foreach ($fpIndicators as $indicator): ?>
                                        <option value="<?= $indicator['id']; ?>">
                                            <?= $indicator['code'] . ". " . $indicator['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                            </div>

                        </div>

                        <div class="row mb-2">

                            <!-- CURRENT USERS (BEGINNING MONTH) -->
                            <div class="col-4">

                                <form class="card needs-validation entriesForm" data-user-type="current_user_beginning" novalidate>

                                    <div class="card-header text-center fw-bold">
                                        Current Users (Beginning of the Month)
                                    </div>

                                    <div class="card-body">

                                        <table class="table table-bordered text-center">
                                            <thead>
                                                <td>Age Group</td>
                                                <td></td>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>10-14</td>
                                                    <td><input type="number" class="form-control" data-agegroup="10-14" data-user-type="current_user_beginning"></td>
                                                </tr>
                                                <tr>
                                                    <td>15-19</td>
                                                    <td><input type="number" class="form-control" data-agegroup="15-19" data-user-type="current_user_beginning"></td>
                                                </tr>
                                                <tr>
                                                    <td>20-49</td>
                                                    <td><input type="number" class="form-control" data-agegroup="20-49" data-user-type="current_user_beginning"></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <td class="fw-bold">TOTAL</td>
                                                <td><input type="number" required readonly class="form-control fw-bold"></td>
                                            </tfoot>
                                        </table>

                                    </div>

                                    <div class="card-footer">

                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="bi bi-save me-1"></i> Save
                                        </button>

                                    </div>

                                </form>

                            </div>

                            <!-- ACCEPTORS -->
                            <div class="col-8">

                                <div class="card">

                                    <div class="card-header text-center fw-bold">
                                        Acceptors
                                    </div>

                                    <div class="card-body">

                                        <div class="row">

                                            <div class="col-6">

                                                <form class="card needs-validation entriesForm" data-user-type="new_acceptor_previous" novalidate>

                                                    <table class="table table-bordered text-center">
                                                        <thead>
                                                            <td class="fw-bold" colspan="2">New Acceptors (Previous Month)</td>
                                                        </thead>
                                                        <thead>
                                                            <td>Age Group</td>
                                                            <td></td>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>10-14</td>
                                                                <td><input type="number" class="form-control" data-agegroup="10-14" data-user-type="new_acceptor_previous"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>15-19</td>
                                                                <td><input type="number" class="form-control" data-agegroup="15-19" data-user-type="new_acceptor_previous"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>20-49</td>
                                                                <td><input type="number" class="form-control" data-agegroup="20-49" data-user-type="new_acceptor_previous"></td>
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

                                            <div class="col-6">

                                                <form class="card needs-validation entriesForm" data-user-type="other_acceptor_present" novalidate>

                                                    <table class="table table-bordered text-center">
                                                        <thead>
                                                            <td class="fw-bold" colspan="2">Other Acceptors (Present Month)</td>
                                                        </thead>
                                                        <thead>
                                                            <td>Age Group</td>
                                                            <td></td>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>10-14</td>
                                                                <td><input type="number" class="form-control" data-agegroup="10-14" data-user-type="other_acceptor_present"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>15-19</td>
                                                                <td><input type="number" class="form-control" data-agegroup="15-19" data-user-type="other_acceptor_present"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>20-49</td>
                                                                <td><input type="number" class="form-control" data-agegroup="20-49" data-user-type="other_acceptor_present"></td>
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

                        <div class="row mb-2">

                            <!-- DROP OUTS -->
                            <div class="col-4">

                                <div class="card">

                                    <form class="card needs-validation entriesForm" data-user-type="drop_outs" novalidate>

                                        <div class="card-header text-center fw-bold">
                                            Drop-outs (Present Month)
                                        </div>

                                        <div class="card-body">

                                            <table class="table table-bordered text-center">
                                                <thead>
                                                    <td>Age Group</td>
                                                    <td></td>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>10-14</td>
                                                        <td><input type="number" class="form-control" data-agegroup="10-14" data-user-type="drop_outs"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>15-19</td>
                                                        <td><input type="number" class="form-control" data-agegroup="15-19" data-user-type="drop_outs"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>20-49</td>
                                                        <td><input type="number" class="form-control" data-agegroup="20-49" data-user-type="drop_outs"></td>
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

                                        </div>

                                    </form>

                                </div>

                            </div>

                            <!-- CURRENT USER (END OF THE MONTH) -->
                            <!-- <div class="col-4">

                                <div class="card">

                                    <form class="card needs-validation entriesForm" data-user-type="current_user_end" novalidate>

                                        <div class="card-header text-center fw-bold">
                                            Current User (End of the Month)
                                        </div>

                                        <div class="card-body">

                                            <table class="table table-bordered text-center">
                                                <thead>
                                                    <td>Age Group</td>
                                                    <td></td>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>10-14</td>
                                                        <td><input type="number" class="form-control" data-agegroup="10-14" data-user-type="current_user_end"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>15-19</td>
                                                        <td><input type="number" class="form-control" data-agegroup="15-19" data-user-type="current_user_end"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>20-49</td>
                                                        <td><input type="number" class="form-control" data-agegroup="20-49" data-user-type="current_user_end"></td>
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

                                        </div>

                                    </form>

                                </div>

                            </div> -->

                            <!-- NEW ACCEPTORS (PRESENT MONTH) -->
                            <div class="col-4">

                                <div class="card">

                                    <form class="card needs-validation entriesForm" data-user-type="new_acceptor_present" novalidate>


                                        <div class="card-header text-center fw-bold">
                                            New Acceptors (End of the Month)
                                        </div>

                                        <div class="card-body">

                                            <table class="table table-bordered text-center">
                                                <thead>
                                                    <td>Age Group</td>
                                                    <td></td>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>10-14</td>
                                                        <td><input type="number" class="form-control" data-agegroup="10-14" data-user-type="new_acceptor_present"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>15-19</td>
                                                        <td><input type="number" class="form-control" data-agegroup="15-19" data-user-type="new_acceptor_present"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>20-49</td>
                                                        <td><input type="number" class="form-control" data-agegroup="20-49" data-user-type="new_acceptor_present"></td>
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

                                        </div>

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

<?= $this->endSection() ?>

<?= $this->section('javascripts') ?>
<script>
    $(document).ready(function() {

        // Load entries when page loads or when dropdowns change
        function loadEntries() {
            const barangay = $('#barangay_code').val();
            const month = $('#report_month').val();
            const year = $('#report_year').val();
            const indicator_id = $('#indicator_id').val();

            $.ajax({
                url: "<?= base_url('get'); ?>/A",
                method: "GET",
                data: {
                    barangay_code: barangay,
                    report_month: month,
                    report_year: year,
                    indicator_id: indicator_id
                },
                success: function(response) {
                    if (response.status === 'success') {
                        const entries = response.data;

                        // Clear all current inputs first
                        $('table input[type="number"]').val('');

                        // Populate values
                        entries.forEach(entry => {
                            // Find a matching input field
                            // Example mapping logic:
                            // You can refine this by adding data attributes like data-agegroup or data-user_type
                            const table = $(`form[data-user-type="${entry.user_type}"]`);
                            const input = table.find(`input[data-agegroup="${entry.agegroup}"]`);

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

        // Trigger load on page ready and on filter change
        loadEntries();
        $('#barangay_code, #report_month, #report_year, #indicator_id').change(loadEntries);

        $('.entriesForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const userType = form.data('user-type'); // 👈 gets the data-user-type value
            const barangay = $('#barangay_code').val();
            const month = $('#report_month').val();
            const year = $('#report_year').val();
            const indicatorId = $('#indicator_id').val();

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
                    url: "<?= base_url('save') ?>/" + 'A',
                    type: "POST",
                    dataType: "json",
                    data: {
                        barangay_code: barangay,
                        report_month: month,
                        report_year: year,
                        user_type: userType,
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
                            loadEntries();
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