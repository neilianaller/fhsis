<?= $this->extend('template/admin_template') ?>

<?= $this->section('content') ?>

<div class="app-content mt-4">

    <div class="container-fluid">
        <div class="row mb-2">

            <div class="col-12 mb-2">

                <div class="card card-success card-outline">

                    <div class="card-body">

                        <div class="row">

                            <div class="col-2">

                                <div class="form-group">
                                    <label for="report_year" class="col-form-label">Year</label>
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

                            <div class="col-2">

                                <div class="form-group">
                                    <label for="report_quarter" class="col-form-label">Quarter</label>
                                    <select id="report_quarter" name="report_quarter" class="form-select btn btn-success">
                                        <option value="1">Quarter 1</option>
                                        <option value="2">Quarter 2</option>
                                        <option value="3">Quarter 3</option>
                                        <option value="4">Quarter 4</option>
                                    </select>

                                </div>

                            </div>

                            <div class="col-3">

                                <div class="form-group">
                                    <label for="barangay_code" class="col-form-label">barangay</label>
                                    <select id="barangay_code" name="barangay_code" class="form-select btn btn-primary dropdown-toggle">
                                        <?php foreach ($barangays as $barangay): ?>
                                            <option value="<?= $barangay['code']; ?>">
                                                <?= $barangay['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                </div>

                            </div>

                            <div class="col-5">

                                <label for="sectionSelect" class="col-form-label">select section</label>
                                <select class="form-select" id="sectionSelect" name="sectionSelect">
                                    <?php foreach ($sections as $section): ?>
                                        <option value="<?= $section['id']; ?>">
                                            <?= $section['code'] . '. ' . $section['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>

                    </div>

                    <div class="card-footer text-center">
                        <button type="button" class="btn btn-success" id="printBtn"><i class="bi bi-printer-fill me-2"></i>PRINT REPORT</button>
                    </div>

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