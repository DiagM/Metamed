/**
 * Page User List
 */

'use strict';

// Datatable (jquery)
$(function () {
  // Variable declaration for table
  var dt_user_table = $('.datatable-project'),
    select2 = $('.select2'),

    offCanvasForm = $('#offcanvasAddHoliday');


  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  // Users datatable
if (dt_user_table.length) {
  var dt_user = dt_user_table.DataTable({
      processing: true,
      serverSide: true,
      ajax: {
          url: baseUrl + 'holidays',
      },
      columns: [
        { data: '' }, // Add your first column if needed
        { data: 'id' },
        { data: 'reason' },
        { data: 'date_start' },
        { data: 'date_end' },
        { data: 'status' },
        { data: 'doctor_name' },
        { data: 'department_name' },
        { data: 'action' }
      ],
      columnDefs: [
          {
              className: 'control',
              searchable: false,
              orderable: false,
              responsivePriority: 2,
              targets: 0,
              render: function(data, type, full, meta) {
                  return '';
              }
          },
          {
              searchable: false,
              orderable: false,
              targets: 1,
              render: function(data, type, full, meta) {
                  return `<span>${full.fake_id}</span>`;
              }
          },
          {
              targets: 2,
              responsivePriority: 4,
              render: function(data, type, full, meta) {
                  var $reason = full['reason'];
                  return '<span class="user-reason">' + $reason + '</span>';

              }
          },
          {
              targets: 3,
              render: function(data, type, full, meta) {
                  var $date_start = full['date_start'];
                  return '<span class="user-date_start">' + $date_start + '</span>';
              }
          },
          {
              targets: 4,
              render: function(data, type, full, meta) {
                  var $date_end = full['date_end'];
                  return '<span class="user-date_end">' + $date_end + '</span>';
              }
          },
          {
            targets: 5,
            render: function(data, type, full, meta) {
                return '<span class="doctor-name">' + full['status'] + '</span>';
            }
        },
          {
              targets: 6,
              render: function(data, type, full, meta) {
                  return '<span class="doctor-name">' + full['doctor_name'] + '</span>';
              }
          },
          {
            targets: 7,
            render: function(data, type, full, meta) {
                return '<span class="department-name">' + full['department_name'] + '</span>';
            }
        },
          {
              targets: -1,
              title: 'Actions',
              searchable: false,
              orderable: false,
              render: function(data, type, full, meta) {
                  return (
                      '<div class="d-inline-block text-nowrap">' +
                      `<button class="btn btn-sm btn-icon edit-record" data-id="${full['id']}" data-reason="${full['reason']}" data-start="${full['date_start']}" data-end="${full['date_end']}" data-status="${full['status']}" data-doctor-name="${full['doctor_name']}" data-department-name="${full['department_name']}" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEditHoliday"><i class="ti ti-edit"></i></button>` +
                      `<button class="btn btn-sm btn-icon delete-record" data-id="${full['id']}"><i class="ti ti-trash"></i></button>` +

                      '</div>'
                  );
              }
          }
      ],
      order: [[2, 'desc']],
      dom:
          '<"row mx-2"' +
          '<"col-md-2"<"me-3"l>>' +
          '<"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>' +
          '>t' +
          '<"row mx-2"' +
          '<"col-sm-12 col-md-6"i>' +
          '<"col-sm-12 col-md-6"p>' +
          '>',
      language: {
          sLengthMenu: '_MENU_',
          search: '',
          searchPlaceholder: 'Search..'
      },
      buttons: [

          {
              text: '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Ask for holiday</span>',
              className: 'add-new btn btn-primary waves-effect waves-light',
              attr: {
                  'data-bs-toggle': 'offcanvas',
                  'data-bs-target': '#offcanvasAddHoliday'
              }
          }
      ],
      responsive: {
          details: {
              display: $.fn.dataTable.Responsive.display.modal({
                  header: function(row) {
                      var data = row.data();
                      return 'Details of ' + data['name'];
                  }
              }),
              type: 'column',
              renderer: function(api, rowIdx, columns) {
                  var data = $.map(columns, function(col, i) {
                      return col.title !== ''
                          ? '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                            '<td>' + col.title + ':</td> ' +
                            '<td>' + col.data + '</td>' +
                            '</tr>'
                          : '';
                  }).join('');

                  return data ? $('<table class="table"/><tbody />').append(data) : false;
              }
          }
      }
  });
}

// Handle department filter change
$('#filter-department').change(function () {
  dt_user.draw(); // Redraw datatable on department filter change
});
// Handle download record button click
$(document).on('click', '.download-record', function () {
  var filePath = $(this).data('file');

  // Perform file download
  window.location.href = '/download?url=' + filePath;
});

  // Delete Record
  $(document).on('click', '.delete-record', function () {
    var user_id = $(this).data('id'),
      dtrModal = $('.dtr-bs-modal.show');

    // hide responsive modal in small screen
    if (dtrModal.length) {
      dtrModal.modal('hide');
    }

    // sweetalert for confirmation of delete
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then(function (result) {
      if (result.value) {
        // delete the data
        $.ajax({
          type: 'DELETE',
          url: `${baseUrl}MedicalFile-list/${user_id}`,
          success: function () {
            dt_user.draw();
          },
          error: function (error) {
            console.log(error);
          }
        });

        // success sweetalert
        Swal.fire({
          icon: 'success',
          title: 'Deleted!',
          text: 'The Medical file has been deleted!',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        Swal.fire({
          title: 'Cancelled',
          text: 'The Medical file is not deleted!',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      }
    });
  });
  //show record
  $(document).on('click', '.view-record', function(event) {
    event.preventDefault(); // Prevent the default link behavior

    var patientId = $(this).data('id');

    // Make an AJAX request to the show route
    $.ajax({
        url: '/MedicalFile-list/' + patientId, // Adjust the URL according to your route setup
        method: 'GET',
        success: function(response) {
            // Handle the successful response, for example, redirecting to the show page
            window.location.href = '/MedicalFile-list/' + patientId;
        },
        error: function(xhr, status, error) {
            // Handle any errors, for example, displaying an error message
            console.error(xhr.responseText);
        }
    });
});

  // edit record
  $(document).on('click', '.edit-record', function () {
    var user_id = $(this).data('id');
    var reason = $(this).data('reason');
    var date_start = $(this).data('start');
    console.log(date_start);
    var date_end = $(this).data('end');
    var status = $(this).data('status');
      $('#holiday_id').val(user_id);
      $('#edit-holiday-reason').val(reason);
      $('#edit-holiday-start-date').val(date_start);
      $('#edit-holiday-end-date').val(date_end);
      $('#edit-holiday-status').val(status);

  });

  // changing the title
  $('.add-new').on('click', function () {
    $('#user_id').val(''); //reseting input field

  });

  // Filter form control to default size
  // ? setTimeout used for multilingual table initialization
  setTimeout(() => {
    $('.dataTables_filter .form-control').removeClass('form-control-sm');
    $('.dataTables_length .form-select').removeClass('form-select-sm');
  }, 300);

// Validating form and updating holiday data
const addNewHolidayForm = document.getElementById('addNewHolidayForm');

// Holiday form validation
const fv = FormValidation.formValidation(addNewHolidayForm, {
  fields: {
    reason: {
      validators: {
        notEmpty: {
          message: 'Please enter a reason'
        }
      }
    },
    start_date: {
      validators: {
        notEmpty: {
          message: 'Please enter the start date'
        },
        date: {
          format: 'YYYY-MM-DD',
          message: 'Please enter a valid date'
        }
      }
    },
    end_date: {
      validators: {
        notEmpty: {
          message: 'Please enter the end date'
        },
        date: {
          format: 'YYYY-MM-DD',
          message: 'Please enter a valid date'
        }
      }
    }
  },
  plugins: {
    trigger: new FormValidation.plugins.Trigger(),
    bootstrap5: new FormValidation.plugins.Bootstrap5({
      // Use this for enabling/changing valid/invalid class
      eleValidClass: '',
      rowSelector: function (field, ele) {
        // field is the field name & ele is the field element
        return '.mb-3';
      }
    }),
    submitButton: new FormValidation.plugins.SubmitButton(),
    autoFocus: new FormValidation.plugins.AutoFocus()
  }
}).on('core.form.valid', function () {
  // Create a new FormData object
  var formData = new FormData($('#addNewHolidayForm')[0]);

  // Append additional data if needed
  formData.append('id', $('#holiday_id').val());
  formData.append('reason', $('#add-holiday-reason').val());
  formData.append('start_date', $('#add-holiday-start-date').val());
  formData.append('end_date', $('#add-holiday-end-date').val());

  // Adding or updating holiday when form successfully validates
  $.ajax({
    data: formData,
    url: `${baseUrl}holidays`, // Update URL to match your endpoint
    type: 'POST',
    contentType: false, // Set contentType to false when sending FormData
    processData: false, // Set processData to false to prevent jQuery from automatically processing the data
    success: function (status) {
      dt_user.draw();
      offCanvasForm.offcanvas('hide');

      // Sweetalert
      Swal.fire({
        icon: 'success',
        title: `Successfully ${status}!`,
        text: `Holiday ${status} successfully.`,
        customClass: {
          confirmButton: 'btn btn-success'
        }
      });
    },
    error: function (err) {
      offCanvasForm.offcanvas('hide');
      Swal.fire({
        title: 'Error!',
        text: err.responseText || 'An error occurred while processing your request.',
        icon: 'error',
        customClass: {
          confirmButton: 'btn btn-danger'
        }
      });
    }
  });
});


  // clearing form data when offcanvas hidden
  offCanvasForm.on('hidden.bs.offcanvas', function () {
    fv.resetForm(true);
  });

  const phoneMaskList = document.querySelectorAll('.phone-mask');

  // Phone Number
  if (phoneMaskList) {
    phoneMaskList.forEach(function (phoneMask) {
      new Cleave(phoneMask, {
        phone: true,
        phoneRegionCode: 'US'
      });
    });
  }
});
