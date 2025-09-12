<?= $this->extend('template/admin_template') ?>

<?= $this->section('content') ?>
<div class="app-content-header">

    <div class="container-fluid">

    </div>

</div>

<div class="app-content">

    <div class="container-fluid">

        <div class="col-md-12">
            <table id="datatable" class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
            </table>

        </div>

    </div>

</div>

<!-- MODAL VIEW SECTION -->
<div class="modal fade" data-bs-backdrop="static" id="viewSectionModal" aria-labelledby="editModalLabel" aria-hidden="true" tabindex="-1">

    <div class="modal-dialog modal-dialog-scrollable" style="max-width:90vw;">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title text-bold"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

            <div class="modal-body">

                <div class="row">

                    <div class="col-md-12">

                        <div id="sectionsContainer"></div>

                    </div>


                </div>

            </div>

        </div>

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
        lengthChange: false,
        searching: false,
        ordering: true,
        autoWidth: false,
        ajax: {
            url: "<?= base_url('sectionsList') ?>",
            type: "POST",
        },
        columns: [{
                data: "code",
                orderable: false,
            },
            {
                data: "name",
                orderable: false,
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function() {
                    return `
                <button type="button" class="btn btn-sm btn-primary" id="viewBtn">
                      <i class="bi bi-folder2-open"></i>
                  </button>
                `;
                },
                createdCell: function(td) {
                    td.classList.add("text-center");
                }
            }
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search..."
        }
    });

    // VIEW BUTTON
    $(document).on("click", "#viewBtn", function() {
        let row = $(this).parents("tr")[0];
        let id = table.row(row).data().id;

        // Fetch section details
        $.ajax({
            url: "<?= base_url('sections'); ?>/" + id,
            type: "GET",
            success: function(section) {
                $("#viewSectionModal").modal("show");
                $(".modal-title").text(section.code + ". " + section.name);

                let html = "";

                section.subsections.forEach(sub => {
                    // Subsection card
                    html += `
                <div class="card card-success card-outline mb-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">${sub.code}. ${sub.name}</h6>
                    </div>
                    <div class="card-body">
            `;

                    // If has categories
                    if (sub.categories && sub.categories.length > 0) {
                        sub.categories.forEach(cat => {
                            html += `
                        <div class="card card-primary card-outline mb-2">
                            <div class="card-header py-2">
                                <strong>${cat.code}. ${cat.name}</strong>
                            </div>
                            <div class="card-body p-2">
                    `;

                            if (cat.indicators && cat.indicators.length > 0) {
                                cat.indicators.forEach(ind => {
                                    html += `
                                <form class="updateIndicatorForm row mb-2 needs-validation" novalidate>
                                    <input type="hidden" name="id" value="${ind.id}" />
                                    <div class="col-md-2">
                                        <input type="text" class="form-control form-control-sm" name="code" value="${ind.code}" required />
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control form-control-sm" name="name" value="${ind.name}" required />
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-save me-1"></i>Save
                                        </button>
                                    </div>
                                </form>
                            `;
                                });
                            }

                            html += `</div></div>`; // close cat card
                        });
                    }

                    // If no categories → render indicators directly under subsection
                    if (sub.indicators && sub.indicators.length > 0) {
                        sub.indicators.forEach(ind => {
                            html += `
                        <form class="updateIndicatorForm row mb-2 needs-validation" novalidate>
                            <input type="hidden" name="id" value="${ind.id}" />
                            <div class="col-md-2">
                                <input type="text" class="form-control form-control-sm" name="code" value="${ind.code}" required />
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control form-control-sm" name="name" value="${ind.name}" required />
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="fas fa-save me-1"></i>Save
                                </button>
                            </div>
                        </form>
                    `;
                        });
                    }

                    html += `</div></div>`; // close subsection card
                });

                $("#sectionsContainer").html(html);
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

    function clearform() {
        $("#id").val('');
        $("#name").val('');
        $("#newSectionForm #code").val('');
    }

    // CLOSE MODAL
    $(document).on("click", '.btn-close', function() {
        clearform();
    })

    // NEW INDICATOR FORM
    $(document).on('submit', '.newSectionForm', function(e) {
        e.preventDefault();

        let formdata = $(this).serializeArray().reduce(function(obj, item) {
            obj[item.name] = item.value;
            return obj;
        }, {});
        // alert(JSON.stringify(formdata));
        let jsondata = JSON.stringify(formdata);

        if (this.checkValidity()) {

            //create
            $.ajax({
                url: "<?= base_url('indicator') ?>",
                type: "POST",
                data: jsondata,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'SUCCESS',
                        text: response.message,
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: true
                    });

                    let ind = response.data; // assuming backend returns the new indicator

                    let newRow = `
        <form class="needs-validation row m-1 updateIndicatorForm" novalidate>
            <input type="hidden" name="id" value="${ind.id}" required readonly/>
            <input type="hidden" name="section_code" value="${ind.section_code}" required readonly/>
            <div class="col-md-1">
                <input type="text" name="code" class="form-control" value="${ind.code}" required />
            </div>
            <div class="col-md-9">
                <input type="text" name="name" class="form-control" value="${ind.name}" required />
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-success">
                    <i class="fas fa-save me-2"></i>Save
                </button>
            </div>
        </form>
    `;

                    // Insert before the newSectionForm
                    $(".newSectionForm").before(newRow);

                    // Clear the add form
                    $(".newSectionForm")[0].reset();

                },
                error: function(response) {
                    let parseresponse = JSON.parse(response.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'FAILED',
                        text: response.message,
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: true
                    });
                },
            });
        }
    });

    // UPDATE INDICATOR FORM
    $(document).on('submit', '.updateIndicatorForm', function(e) {
        e.preventDefault();

        let formdata = $(this).serializeArray().reduce(function(obj, item) {
            obj[item.name] = item.value;
            return obj;
        }, {});
        // alert(JSON.stringify(formdata));
        let jsondata = JSON.stringify(formdata);

        if (this.checkValidity()) {

            //create
            $.ajax({
                url: "<?= base_url('indicator') ?>/" + formdata.id,
                type: "PUT",
                data: jsondata,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'SUCCESS',
                        text: response.message,
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: true
                    });
                },
                error: function(response) {
                    let parseresponse = JSON.parse(response.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'FAILED',
                        text: response.message,
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: true
                    });
                },
            });
        }
    });
</script>

<?= $this->endSection() ?>