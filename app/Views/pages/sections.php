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

    <div class="modal-dialog modal-xl">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title text-bold"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

            <div class="modal-body">

                <div class="row">

                    <div class="col-md-12">

                        <div id="indicatorsContainer"></div>

                        <div id="newindicatorContainer"></div>

                        <!-- 
                        <form id="newIndicatorForm" class="needs-validation row m-1" novalidate>

                            <input type="text" id="id" name="id" class="form-control" value="" readonly />
                            <input type="text" id="section_code_new" name="section_code" class="form-control" value="" required readonly />

                            <div class="col-md-1">

                                <input
                                    type="text"
                                    class="form-control"
                                    id="code"
                                    name="code"
                                    required />
                                <div class="valid-feedback">Looks good!</div>

                            </div>

                            <div class="col-md-9">

                                <input
                                    type="text"
                                    class="form-control"
                                    id="name"
                                    name="name"
                                    required />
                                <div class="valid-feedback">Looks good!</div>

                            </div>

                            <div class="col-md-2">

                                <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-view-list me-2"></i>Add</button>

                            </div>


                        </form> -->

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
                $("#viewSectionModal").modal('show');
                $(".modal-title").text(section.code + ". " + section.name);
                // $("#section_code_new").val(section.code);

                // Now fetch indicators of this section
                $.ajax({
                    url: "<?= base_url('indicators'); ?>/" + section.code,
                    type: "GET",
                    success: function(indicators) {
                        let html = "";

                        indicators.forEach(ind => {
                            html += `
                            <form id="updateIndicatorForm" class="needs-validation row m-1 updateIndicatorForm" novalidate>
                                <input type="hidden" id="id" name="id" class="form-control" value="${ind.id}" required readonly/>
                                <input type="hidden" id="section_code" name="section_code" class="form-control" value="${ind.section_code}" required readonly/>

                                <div class="col-md-1">
                                    <input type="text" id="code" name="code" class="form-control" value="${ind.code}" required />
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="name" name="name" class="form-control" value="${ind.name}" required />
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-save me-2"></i>Save
                                    </button>
                                </div>
                            </form>
                        `;
                        });


                        // Add empty form for "new" indicator
                        html += `
                        <form class="newIndicatorForm needs-validation row m-1" novalidate>
                        <input type="hidden" id="id" name="id" class="form-control" value="" readonly />
                            <input type="hidden" id="section_code_new" name="section_code" class="form-control" value="${section.code}" required readonly />
                            <div class="col-md-1">
                                <input type="text" id="code" name="code" class="form-control" placeholder="Code" required />
                            </div>
                            <div class="col-md-9">
                                <input type="text" id="name" name="name" class="form-control" placeholder="Name" required />
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="bi bi-view-list me-2"></i>Add
                                </button>
                            </div>
                        </form>
                    `;

                        $("#indicatorsContainer").html(html);

                        $("#indicatorsContainer").html(html);
                    }
                });
            }
        });
    });

    // NEW INDICATOR FORM
    $(document).on('submit', '.newIndicatorForm', function(e) {
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
                    // ✅ Append new row dynamically without reload
                    let newRow = `
                        <form class="needs-validation row m-1 updateIndicatorForm" novalidate>
                            <div class="row m-1">
                                <div class="col-md-1">
                                    <input type="text" class="form-control" name="code" value="${response.data.code}" required />
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="name" value="${response.data.name}" required />
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="id" value="${response.data.id}" />
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-save me-2"></i>Save
                                    </button>
                                </div>
                            </div>
                        </form>
                    `;
                    $("#newindicatorContainer").prepend(newRow); // add before the "Add New" form

                    // ✅ Clear form after adding
                    $('#newIndicatorForm')[0].reset();

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
        $("#newIndicatorForm #code").val('');
    }

    // CLOSE MODAL
    $(document).on("click", '.btn-close', function() {
        clearform();
    })
</script>

<?= $this->endSection() ?>