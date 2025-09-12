<?= $this->extend('template/admin_template') ?>

<?= $this->section('content') ?>
<div class="app-content-header">

    <div class="container-fluid">

    </div>

</div>

<div class="app-content">

    <div class="container-fluid">

        <div class="row mb-2">

            <div class="col-12">

                <div class="card card-success card-outline">

                    <div class="card-body">

                        <div class="row">

                            <div class="col-2">

                                <div class="form-group">

                                    <select class="form-select btn btn-success dropdown-toggle">
                                        <?php
                                        $currentYear = date('Y');
                                        for ($year = $currentYear; $year >= 2020; $year--) {
                                            $selected = ($year == $currentYear) ? 'selected' : '';
                                            echo '<option value="' . $year . '" ' . $selected . '>' . $year . '</option>';
                                        }
                                        ?>
                                    </select>

                                </div>

                            </div>

                            <div class="col-3">

                                <div class="form-group">

                                    <select class="form-select btn btn-success dropdown-toggle">
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

                            <div class="col-4">

                                <div class="form-group">

                                    <select class="form-select btn btn-primary dropdown-toggle">
                                        <?php foreach ($barangays as $barangay): ?>
                                            <option value="<?= $barangay['code']; ?>">
                                                <?= $barangay['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                </div>

                            </div>

                            <div class="col-3">

                                <div class="form-group">

                                    <button type="button" class="btn btn-warning"><i class="bi bi-arrow-clockwise me-2"></i>Load Results</button>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="row">

            <div class="col-md-12">

                <div class="col-md-12">

                    <table id="fpDatatable" class="table table-striped table-hover align-middle text-center">
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
                                            <td><input type="number" readonly class="form-control form-control-sm"></td>
                                        <?php else: ?>
                                            <td><input type="number" class="form-control form-control-sm"></td>
                                        <?php endif; ?>
                                    <?php endfor; ?>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        </thead>
                    </table>

                </div>

            </div>


        </div>

    </div>

</div>







<?= $this->endSection() ?>

<?= $this->section('javascripts') ?>
<script>

</script>
<?= $this->endSection() ?>