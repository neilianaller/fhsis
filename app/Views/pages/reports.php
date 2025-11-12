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
                                        <option value="5">All Year</option>
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
                                        <option value="allbgy">All Barangays</option>
                                    </select>

                                </div>

                            </div>

                            <div class="col-5">

                                <label for="sectionSelect" class="col-form-label">select section</label>
                                <select class="form-select" id="sectionSelect" name="sectionSelect">
                                    <?php foreach ($sections as $section): ?>
                                        <option value="<?= $section['code']; ?>">
                                            <?= $section['code'] . '. ' . $section['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <option value="allsections">ALL SECTIONS</option>
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

        <div class="row mb-2">
            <table id="datatable" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th class="col-md-3">CREATED AT</th>
                        <th class="col-md-1">YEAR</th>
                        <th class="col-md-1">QUARTER</th>
                        <th class="col-md-3">BARANGAY</th>
                        <th class="col-md-1">SECTION</th>
                        <th class="col-md-3">FILE NAME</th>
                    </tr>
                </thead>
            </table>
        </div>

    </div>

</div>

<!-- LOADING SCREEN OVERLAY -->
<div id="loading-overlay">
    <div id="loading-container">
        <div class="loader"></div>
        <br />
        <span>Loading...</span>
    </div>
</div>





<?= $this->endSection() ?>

<?= $this->section('javascripts') ?>
<script>
    // POPULATE TABLE
    let table = $("#datatable").DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        paging: true,
        lengthChange: true,
        lengthMenu: [10, 20, 50],
        searching: true,
        autoWidth: false,
        order: [
            [6, 'desc']
        ],
        ajax: {
            url: '<?= base_url('reportslist') ?>',
            type: 'POST',
        },
        columns: [{
                data: 'created_at',
                render: function(data, type, row) {
                    if (!data) return '';
                    const date = new Date(data);
                    const options = {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: 'numeric',
                        minute: '2-digit',
                        hour12: true
                    };
                    const formattedDate = date.toLocaleString('en-US', options)
                        .replace(',', ',') // keep comma after day
                        .replace(' at', ' —'); // replace "at" with "—"
                    return formattedDate;
                }
            },
            {
                data: 'report_year',
            },
            {
                data: 'report_quarter',
            },
            {
                data: 'barangay',
            },
            {
                data: 'section',
            },
            {
                data: 'filepath',
            },
        ],
    });

    $(document).ready(function() {

        // 🔹 When user clicks PRINT REPORT
        $('#printBtn').on('click', function() {

            $("#loading-overlay").show();

            // Collect selected filter values
            const year = $('#report_year').val();
            const quarter = $('#report_quarter').val();
            const barangay = $('#barangay_code').val();
            const section = $('#sectionSelect').val();

            // Optional: quick validation
            if (!year || !quarter || !barangay || !section) {
                alert('Please complete all filters first.');
                return;
            }

            // 🔹 Determine AJAX URL based on section
            let ajaxUrl = '';
            switch (section) {
                case 'A': // Section A
                    ajaxUrl = "<?= base_url('generateFPReport') ?>";
                    break;
                case 'B': // Section B
                    ajaxUrl = "<?= base_url('generateMaternalReport') ?>";
                    break;
                case 'C': // Section B
                    ajaxUrl = "<?= base_url('generateChildReport') ?>";
                    break;
                case 'D': // Section B
                    ajaxUrl = "<?= base_url('generateOralReport') ?>";
                    break;
                case 'E': // Section B
                    ajaxUrl = "<?= base_url('generateNCDiseaseReport') ?>";
                    break;
                case 'F': // Section B
                    ajaxUrl = "<?= base_url('generateEnviReport') ?>";
                    break;
                case 'G': // Section B
                    ajaxUrl = "<?= base_url('generateIDiseaseReport') ?>";
                    break;
                case 'allsections': // Section B
                    ajaxUrl = "<?= base_url('generateAllReport') ?>";
                    break;
                    // Add more sections if needed
            }

            // 🔹 Send data via AJAX to your controller
            $.ajax({
                url: ajaxUrl,
                method: "POST",
                data: {
                    sectionSelect: $('#sectionSelect').val(),
                    report_year: $('#report_year').val(),
                    report_quarter: $('#report_quarter').val(),
                    barangay_code: $('#barangay_code').val()
                },
                beforeSend: function() {
                    $("#loading-overlay").show();
                },
                success: function(response) {
                    $("#loading-overlay").hide();
                    table.ajax.reload();

                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Report Generated!',
                            text: response.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 2000, // ⏱ auto close after 2 seconds
                            timerProgressBar: true
                        });
                    }
                },
                error: function(xhr) {
                    $("#loading-overlay").hide();

                    Swal.fire({
                        title: '🚫 Server Error',
                        text: xhr.responseText || 'An unexpected error occurred.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });

        });
    });

    // ✅ Make rows clickable for file download
    $('#datatable tbody').on('click', 'tr', function() {
        let data = table.row(this).data();
        if (data && data.id) {
            window.location.href = "<?= base_url('download') ?>/" + data.id;
        }
    });
</script>

<?= $this->endSection() ?>