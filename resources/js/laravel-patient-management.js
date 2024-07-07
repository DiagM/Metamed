/**
 * Page User List
 */

'use strict';

// Datatable (jquery)
$(function () {
  // Variable declaration for table
  var dt_user_table = $('.datatables-users'),
    select2 = $('.select2'),
    userView = baseUrl + 'app/user/view/account',
    offCanvasForm = $('#offcanvasAddUser');

  // if (select2.length) {
  //   var $this = select2;
  //   $this.wrap('<div class="position-relative"></div>').select2({
  //     placeholder: 'Select Department',
  //     dropdownParent: $this.parent()
  //   });
  // }

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
        url: baseUrl + 'patient-list',
            data: function (d) {
                d.blood_type = $('#filter-blood-type').val(); // Include blood_type filter
                d.doctor_name = $('#filter-doctor-name').val(); // Include doctor_name filter
                d.department_name = $('#filter-department-name').val(); // Include department_name filter
                d.hospital_name = $('#filter-hospital-name').val(); // Include hospital_name filter

            }

      },
      columns: [
        // columns according to JSON
        { data: '' },
        { data: 'id' },
        { data: 'name' },
        { data: 'email' },
        { data: 'license_number' },
        { data: 'contact' },
        { data: 'blood_type' },
        { data: 'action' }
      ],
      columnDefs: [
        {
          // For Responsive
          className: 'control',
          searchable: false,
          orderable: false,
          responsivePriority: 2,
          targets: 0,
          render: function (data, type, full, meta) {
            return '';
          }
        },
        {
          searchable: false,
          orderable: false,
          targets: 1,
          render: function (data, type, full, meta) {
            return `<span>${full.fake_id}</span>`;
          }
        },
        {
          // User full name
          targets: 2,
          responsivePriority: 4,
          render: function (data, type, full, meta) {
            var $name = full['name'];

            // For Avatar badge
            var stateNum = Math.floor(Math.random() * 6);
            var states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
            var $state = states[stateNum],
              $name = full['name'],
              $initials = $name.match(/\b\w/g) || [],
              $output;
            $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
            $output = '<span class="avatar-initial rounded-circle bg-label-' + $state + '">' + $initials + '</span>';

            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center user-name">' +
              '<div class="avatar-wrapper">' +
              '<div class="avatar avatar-sm me-3">' +
              $output +
              '</div>' +
              '</div>' +
              '<div class="d-flex flex-column">' +
              '<a href="' +
              userView +
              '" class="text-body text-truncate"><span class="fw-medium">' +
              $name +
              '</span></a>' +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // User email
          targets: 3,
          render: function (data, type, full, meta) {
            var $email = full['email'];

            return '<span class="user-email">' + $email + '</span>';
          }
        },
        {
          //license_number
          targets: 4,
          render: function (data, type, full, meta) {
            var $license_number = full['license_number'];

            return '<span class="license_number">' + $license_number + '</span>';
          }
        },

        {
          //contact
          targets: 5,
          render: function (data, type, full, meta) {
            var $contact = full['contact'];

            return '<span class="contact">' + $contact + '</span>';
          }
        },{
                  // Blood Type
                  targets: 6,
                  render: function (data, type, full, meta) {
                    var $blood_type = full['blood_type'];
                    return '<span class="blood_type">' + $blood_type + '</span>';
                  }
                },
        {
          // Actions
          targets: -1,
          title: 'Actions',
          searchable: false,
          orderable: false,
          render: function (data, type, full, meta) {

            return (
              '<div class="d-inline-block text-nowrap">' +
              `<button class="btn btn-sm btn-icon edit-record" data-id="${full['id']}" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="ti ti-edit"></i></button>` +
              `<button class="btn btn-sm btn-icon delete-record" data-id="${full['id']}"><i class="ti ti-trash"></i></button>` +
              '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button>' +
              '<div class="dropdown-menu dropdown-menu-end m-0">' +
              '<a href="javascript:;" class="dropdown-item view-record" data-id="' + full['id'] + '">View</a>' +

              '<a href="javascript:;" class="dropdown-item">Suspend</a>' +
              '</div>' +
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
      // Buttons with Dropdown
      buttons: [
        {
          extend: 'collection',
          className: 'btn btn-label-primary dropdown-toggle mx-3 waves-effect waves-light',
          text: '<i class="ti ti-logout rotate-n90 me-2"></i>Export',
          buttons: [
            {
              extend: 'print',
              title: 'Users',
              text: '<i class="ti ti-printer me-2" ></i>Print',
              className: 'dropdown-item',
              exportOptions: {
                columns: [2, 3],
                // prevent avatar to be print
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('user-name')) {
                        result = result + item.lastChild.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              },
              customize: function (win) {
                //customize print view for dark
                $(win.document.body)
                  .css('color', config.colors.headingColor)
                  .css('border-color', config.colors.borderColor)
                  .css('background-color', config.colors.body);
                $(win.document.body)
                  .find('table')
                  .addClass('compact')
                  .css('color', 'inherit')
                  .css('border-color', 'inherit')
                  .css('background-color', 'inherit');
              }
            },
            {
              extend: 'csv',
              title: 'Users',
              text: '<i class="ti ti-file-text me-2" ></i>Csv',
              className: 'dropdown-item',
              exportOptions: {
                columns: [2, 3],
                // prevent avatar to be print
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList.contains('user-name')) {
                        result = result + item.lastChild.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              }
            },
            {
              extend: 'excel',
              title: 'Users',
              text: '<i class="ti ti-file-spreadsheet me-2"></i>Excel',
              className: 'dropdown-item',
              exportOptions: {
                columns: [2, 3],
                // prevent avatar to be display
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList.contains('user-name')) {
                        result = result + item.lastChild.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              }
            },
            {
              extend: 'pdf',
              title: 'Users',
              text: '<i class="ti ti-file-text me-2"></i>Pdf',
              className: 'dropdown-item',
              exportOptions: {
                columns: [2, 3],
                // prevent avatar to be display
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList.contains('user-name')) {
                        result = result + item.lastChild.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              }
            },
            {
              extend: 'copy',
              title: 'Users',
              text: '<i class="ti ti-copy me-1" ></i>Copy',
              className: 'dropdown-item',
              exportOptions: {
                columns: [2, 3],
                // prevent avatar to be copy
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList.contains('user-name')) {
                        result = result + item.lastChild.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              }
            }
          ]
        },
        {
          text: '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add New Patient</span>',
          className: 'add-new btn btn-primary waves-effect waves-light',
          attr: {
            'data-bs-toggle': 'offcanvas',
            'data-bs-target': '#offcanvasAddUser'
          }
        }
      ],
      // For responsive popup
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
              var data = row.data();
              return 'Details of ' + data['name'];
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            var data = $.map(columns, function (col, i) {
              return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                ? '<tr data-dt-row="' +
                    col.rowIndex +
                    '" data-dt-column="' +
                    col.columnIndex +
                    '">' +
                    '<td>' +
                    col.title +
                    ':' +
                    '</td> ' +
                    '<td>' +
                    col.data +
                    '</td>' +
                    '</tr>'
                : '';
            }).join('');

            return data ? $('<table class="table"/><tbody />').append(data) : false;
          }
        }
      }
    });
        // Event listener for blood type filter change
        $('#filter-blood-type').change(function () {
          dt_user.draw();
      });

      // Event listener for doctor name filter change
      $('#filter-doctor-name').change(function () {
        dt_user.draw();
    });
  }
// Handle department filter change
$('#filter-department-name').change(function () {
  dt_user.draw(); // Redraw datatable on department filter change
});

$('#filter-hospital-name').change(function () {
  dt_user.draw(); // Redraw datatable on department filter change
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
          url: `${baseUrl}patient-list/${user_id}`,
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
          text: 'The user has been deleted!',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        Swal.fire({
          title: 'Cancelled',
          text: 'The User is not deleted!',
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
        url: '/patient-list/' + patientId, // Adjust the URL according to the route setup
        method: 'GET',
        success: function(response) {
            // Handle the successful response, for example, redirecting to the show page
            window.location.href = '/patient-list/' + patientId;
        },
        error: function(xhr, status, error) {
            // Handle any errors, for example, displaying an error message
            console.error(xhr.responseText);
        }
    });
});

// Edit record
$(document).on('click', '.edit-record', function() {
  var user_id = $(this).data('id');

  // Close any visible offcanvas modal
  var dtrModal = $('.dtr-bs-modal.show');
  if (dtrModal.length) {
    dtrModal.modal('hide');
  }

  // Change the title of offcanvas
  $('#offcanvasAddUserLabel').html('Edit Patient');

  // Fetch data using AJAX
  $.get(`${baseUrl}patient-list/${user_id}/edit`, function(data) {
    $('#user_id').val(data.id);
    $('#add-user-fullname').val(data.name);
    $('#add-user-email').val(data.email);
    $('#add-user-contact').val(data.contact);
    $('#add-user-license_number').val(data.license_number);
    $('#add-user-date_of_birth').val(data.date_of_birth);
    $('#add-user-gender').val(data.gender);
    $('#add-user-address').val(data.address);
    $('#add-user-height').val(data.height);
    $('#add-user-weight').val(data.weight);
    $('#add-user-blood_type').val(data.blood_type);
    $('#add-user-medical_notes').val(data.medical_notes);
    $('#add-user-allergies').val(data.allergies);
  });
});


  // changing the title
  $('.add-new').on('click', function () {
    $('#user_id').val(''); //reseting input field
    $('#offcanvasAddUserLabel').html('Add Patient');
    $('#department').val("");
  });

  // Filter form control to default size
  // ? setTimeout used for multilingual table initialization
  setTimeout(() => {
    $('.dataTables_filter .form-control').removeClass('form-control-sm');
    $('.dataTables_length .form-select').removeClass('form-select-sm');
  }, 300);

  // validating form and updating user's data
  const addNewUserForm = document.getElementById('addNewUserForm');

  // user form validation
  const fv = FormValidation.formValidation(addNewUserForm, {
    fields: {
      name: {
        validators: {
          notEmpty: {
            message: 'Please enter fullname'
          }
        }
      },
      email: {
        validators: {
          notEmpty: {
            message: 'Please enter the email'
          },
          emailAddress: {
            message: 'The value is not a valid email address'
          }
        }
      },
      userContact: {
        validators: {
          notEmpty: {
            message: 'Please enter the contact'
          }
        }
      },
      license_number: {
        validators: {
          notEmpty: {
            message: 'Please enter the license_number'
          }
        }
      },
      date_of_birth: {
        validators: {
          notEmpty: {
            message: 'Please enter the date_of_birth'
          }
        }
      },
      address: {
        validators: {
          notEmpty: {
            message: 'Please enter the address'
          }
        }
      },
      height: {
        validators: {
          notEmpty: {
            message: 'Please enter the height'
          },
          numeric: {
            message: 'The value must be a number'
          }

        }
      },
      weight: {
        validators: {
          notEmpty: {
            message: 'Please enter the weight'
          },
          numeric: {
            message: 'The value must be a number'
          }
        }
      },
      blood_type: {
        validators: {
          notEmpty: {
            message: 'Please enter the blood type'
          },
          regexp: {
            regexp: /^(A|B|AB|O)[+-]$/,
            message: 'Please enter a valid blood type (e.g., O+, A-)'
          }
        }
      },
      medical_notes: {
        validators: {
          notEmpty: {
            message: 'Please enter the medical notes'
          }
        }
      },
      allergies: {
        validators: {
          notEmpty: {
            message: 'Please enter the allergies'
          }
        }
      },


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
      // Submit the form when all fields are valid
      // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  }).on('core.form.valid', function () {
    // adding or updating user when form successfully validate
    $.ajax({
      data: $('#addNewUserForm').serialize(),
      url: `${baseUrl}patient-list`,
      type: 'POST',
      success: function (status) {
        dt_user.draw();
        offCanvasForm.offcanvas('hide');

        // sweetalert
        Swal.fire({
          icon: 'success',
          title: `Successfully ${status}!`,
          text: `User ${status} Successfully.`,
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      },
      error: function (err) {
        offCanvasForm.offcanvas('hide');
        if (err.responseJSON && err.responseJSON.message) {
            Swal.fire({
                title: 'Duplicate Entry!',
                text: err.responseJSON.message,
                icon: 'error',
                customClass: {
                    confirmButton: 'btn btn-success'
                }
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: 'An unexpected error occurred.',
                icon: 'error',
                customClass: {
                    confirmButton: 'btn btn-success'
                }
            });
        }
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
