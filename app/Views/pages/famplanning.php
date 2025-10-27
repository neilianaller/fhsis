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
                        <i class="fas fa-people-group"></i>
                    </span>

                    <div class="info-box-content">
                        <span class="info-box-text fs-4 fw-bold">A. Family Planning Services for Women of Reproductive Age — </span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>

        </div>

        <div class="row mb-2">

            <div class="col-12">

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

                            <div class="form-group">

                                <select id="indicator_id" name="indicator_id" class="form-select btn btn-success dropdown-toggle">
                                    <?php foreach ($fpIndicators as $indicator): ?>
                                        <option value="<?= $indicator['id']; ?>">
                                            <?= $indicator['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                            </div>

                        </div>

                        <div class="row mb-2">

                            <!-- CURRENT USERS (BEGINNING MONTH) -->
                            <div class="col-4">

                                <form class="card needs-validation entriesForm" novalidate>

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
                                                    <td><input type="number" required class="form-control" /></td>
                                                </tr>
                                                <tr>
                                                    <td>15-19</td>
                                                    <td><input type="number" required class="form-control" /></td>
                                                </tr>
                                                <tr>
                                                    <td>20-49</td>
                                                    <td><input type="number" required class="form-control" /></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <td class="fw-bold">TOTAL</td>
                                                <td><input type="number" required readonly class="form-control form-control-sm"></td>
                                            </tfoot>
                                        </table>

                                    </div>

                                    <div class="card-footer">

                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-save me-1"></i> Save
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

                                                <form class="card needs-validation entriesForm" novalidate>

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
                                                                <td><input type="number" required class="form-control" /></td>
                                                            </tr>
                                                            <tr>
                                                                <td>15-19</td>
                                                                <td><input type="number" required class="form-control" /></td>
                                                            </tr>
                                                            <tr>
                                                                <td>20-49</td>
                                                                <td><input type="number" required class="form-control" /></td>
                                                            </tr>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td class="fw-bold">TOTAL</td>
                                                                <td><input type="number" required readonly class="form-control form-control-sm"></td>
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

                                            <div class="col-6">

                                                <form class="card needs-validation entriesForm" novalidate>

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
                                                                <td><input type="number" required class="form-control" /></td>
                                                            </tr>
                                                            <tr>
                                                                <td>15-19</td>
                                                                <td><input type="number" required class="form-control" /></td>
                                                            </tr>
                                                            <tr>
                                                                <td>20-49</td>
                                                                <td><input type="number" required class="form-control" /></td>
                                                            </tr>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td class="fw-bold">TOTAL</td>
                                                                <td><input type="number" required readonly class="form-control form-control-sm"></td>
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

                        <div class="row mb-2">

                            <!-- DROP OUTS -->
                            <div class="col-4">

                                <div class="card">

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
                                                    <td><input type="number" required class="form-control" /></td>
                                                </tr>
                                                <tr>
                                                    <td>15-19</td>
                                                    <td><input type="number" required class="form-control" /></td>
                                                </tr>
                                                <tr>
                                                    <td>20-49</td>
                                                    <td><input type="number" required class="form-control" /></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td class="fw-bold">TOTAL</td>
                                                    <td><input type="number" required readonly class="form-control form-control-sm"></td>
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

                                    </div>
                                </div>

                            </div>

                            <!-- CURRENT USER (END OF THE MONTH) -->
                            <div class="col-4">

                                <div class="card">

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
                                                    <td><input type="number" required class="form-control" /></td>
                                                </tr>
                                                <tr>
                                                    <td>15-19</td>
                                                    <td><input type="number" required class="form-control" /></td>
                                                </tr>
                                                <tr>
                                                    <td>20-49</td>
                                                    <td><input type="number" required class="form-control" /></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td class="fw-bold">TOTAL</td>
                                                    <td><input type="number" required readonly class="form-control form-control-sm"></td>
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

                                    </div>
                                </div>

                            </div>

                            <!-- NEW ACCEPTORS (PRESENT MONTH) -->
                            <div class="col-4">

                                <div class="card">

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
                                                    <td><input type="number" required class="form-control" /></td>
                                                </tr>
                                                <tr>
                                                    <td>15-19</td>
                                                    <td><input type="number" required class="form-control" /></td>
                                                </tr>
                                                <tr>
                                                    <td>20-49</td>
                                                    <td><input type="number" required class="form-control" /></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td class="fw-bold">TOTAL</td>
                                                    <td><input type="number" required readonly class="form-control form-control-sm"></td>
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

                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="row">

            <div class="col-md-12">

                <!-- <table id="fpDatatable" class="table table-striped table-hover align-middle text-center">
                    <thead>
                        <tr>
                            <th rowspan="4">Modern FP Methods</th>
                            <th colspan="4" rowspan="2">Current Users (Beginning of the Month)</th>
                            <th colspan="8">Acceptors</th>
                            <th colspan="4" rowspan="2">Drop-outs (Present Month)</th>
                            <th colspan="4" rowspan="2">Current User (End of the Month)</th>
                            <th colspan="4" rowspan="2">New Acceptors (Present Month)</th>
                        </tr>
                        <tr>
                            <th colspan="4">New Acceptors (Previous Month)</th>
                            <th colspan="4">Other Acceptors (Present Month)</th>
                        </tr>
                        <tr>
                            <th colspan="3">Age Group</th>
                            <th rowspan="2" class="bg-secondary">TOTAL</th>
                            <th colspan="3">Age Group</th>
                            <th rowspan="2">TOTAL</th>
                            <th colspan="3">Age Group</th>
                            <th rowspan="2">TOTAL</th>
                            <th colspan="3">Age Group</th>
                            <th rowspan="2">TOTAL</th>
                            <th colspan="3">Age Group</th>
                            <th rowspan="2">TOTAL</th>
                            <th colspan="3">Age Group</th>
                            <th rowspan="2">TOTAL</th>
                        </tr>
                        <tr>
                            <th>10-14</th>
                            <th>15-19</th>
                            <th>20-49</th>
                            <th>10-14</th>
                            <th>15-19</th>
                            <th>20-49</th>
                            <th>10-14</th>
                            <th>15-19</th>
                            <th>20-49</th>
                            <th>10-14</th>
                            <th>15-19</th>
                            <th>20-49</th>
                            <th>10-14</th>
                            <th>15-19</th>
                            <th>20-49</th>
                            <th>10-14</th>
                            <th>15-19</th>
                            <th>20-49</th>
                        </tr>
                    <tbody>
                        <?php foreach ($fpIndicators as $fpIndicator): ?>
                            <tr>
                                <td><?= $fpIndicator['name'] ?></td>
                                <?php for ($i = 0; $i < 24; $i++): ?>
                                    <?php if (($i + 1) % 4 === 0): ?>
                                        <td><input type="number" required readonly class="form-control form-control-sm"></td>
                                    <?php else: ?>
                                        <td><input
                                                type="number"
                                                class="form-control"
                                                data-indicator-id="<?= $fpIndicator['id'] ?>"
                                                data-column="<?= $i ?>" /></td>
                                    <?php endif; ?>
                                <?php endfor; ?>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    </thead>
                </table> -->

            </div>

        </div>

    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('javascripts') ?>
<script>
    $(document).on("input", ".entry-value", function() {
        let row = $(this).closest("tr");
        row.find("td input[readonly]").each(function() {
            let sum = 0;
            row.find("td input:not([readonly])").each(function() {
                sum += Number($(this).val() || 0);
            });
            $(this).val(sum);
        });
    });

    $(document).ready(function() {
        $('.entriesForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const userType = form.find('.card-header').text().trim().replace(/\s+/g, '_').toLowerCase();
            const barangay = $('#barangay_code').val();
            const month = $('#report_month').val();
            const year = $('#report_year').val();

            const entries = [];
            form.find('tbody tr').each(function() {
                const agegroup = $(this).find('td:first').text().trim();
                const value = $(this).find('input[type="number"]').val() || 0;
                entries.push({
                    agegroup,
                    value
                });
            });

            $.ajax({
                url: "<?= base_url('save') ?>",
                type: "POST",
                dataType: "json",
                data: {
                    barangay_code: barangay,
                    report_month: month,
                    report_year: year,
                    user_type: userType,
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
        });
    });
</script>
<?= $this->endSection() ?>