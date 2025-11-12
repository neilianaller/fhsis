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