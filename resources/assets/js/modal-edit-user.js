/**
 * Edit User
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
  // Select2 initialization
  $('.select2').each(function () {
    var $this = $(this);
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select value',
      dropdownParent: $this.parent()
    });
  });

  // Avatar generation
  var userData = window.userData || {};
  var userName = userData.name || '';
  var states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
  var stateNum = Math.floor(Math.random() * states.length);
  var state = states[stateNum];

  var initials = userName.match(/\b\w/g) || [];
  initials = ((initials.shift() || '') + (initials.pop() || '')).toUpperCase();
  var avatarHtml = '<span class="avatar-initial rounded-circle bg-label-' + state + '" style="display: inline-block; width: 100px; height: 100px; line-height: 100px; text-align: center; font-size: 24px;">' + initials + '</span>';

  // Append avatar to the DOM
  var avatarWrapper = document.querySelector('.avatar-wrapper');
  if (avatarWrapper) {
    avatarWrapper.innerHTML = avatarHtml;
  }

  // Cleave.js initialization for Tax ID and Phone Number
  var modalEditUserTaxID = document.querySelector('.modal-edit-tax-id');
  var modalEditUserPhone = document.querySelector('.phone-number-mask');

  if (modalEditUserTaxID) {
    new Cleave(modalEditUserTaxID, {
      prefix: 'TIN',
      blocks: [3, 3, 3, 4],
      uppercase: true
    });
  }

  if (modalEditUserPhone) {
    new Cleave(modalEditUserPhone, {
      phone: true,
      phoneRegionCode: 'US'
    });
  }

  // Form validation using FormValidation library
  FormValidation.formValidation(document.getElementById('editUserForm'), {
    fields: {
      'name': {
        validators: {
          notEmpty: {
            message: 'Please enter your full name'
          },
          regexp: {
            regexp: /^[a-zA-Z\s]+$/,
            message: 'The name can only consist of alphabetical characters and spaces'
          }
        }
      },
      'email': {
        validators: {
          notEmpty: {
            message: 'Please enter your email address'
          },
          emailAddress: {
            message: 'The input is not a valid email address'
          }
        }
      },
      'userContact': {
        validators: {
          notEmpty: {
            message: 'Please enter your contact number'
          },
          phone: {
            message: 'The input is not a valid phone number',
            country: 'US' // Assuming US phone format
          }
        }
      },
      'license_number': {
        validators: {
          notEmpty: {
            message: 'Please enter your license number'
          }
        }
      },
      'date_of_birth': {
        validators: {
          notEmpty: {
            message: 'Please enter your date of birth'
          },
          date: {
            format: 'YYYY-MM-DD',
            message: 'The input is not a valid date (YYYY-MM-DD format)'
          }
        }
      },
      'gender': {
        validators: {
          notEmpty: {
            message: 'Please select your gender'
          }
        }
      },
      'address': {
        validators: {
          notEmpty: {
            message: 'Please enter your address'
          }
        }
      },
      'height': {
        validators: {
          notEmpty: {
            message: 'Please enter your height'
          },
          numeric: {
            message: 'The input must be a number'
          }
        }
      },
      'weight': {
        validators: {
          notEmpty: {
            message: 'Please enter your weight'
          },
          numeric: {
            message: 'The input must be a number'
          }
        }
      },
      'blood_type': {
        validators: {
          notEmpty: {
            message: 'Please select your blood type'
          }
        }
      },
      'medical_notes': {
        validators: {
          notEmpty: {
            message: 'Please enter your medical notes'
          }
        }
      },
      'allergies': {
        validators: {
          notEmpty: {
            message: 'Please enter your allergies and reactions'
          }
        }
      }
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        rowSelector: '.row'
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  }).on('core.form.valid', function() {
    // When the form is valid, proceed with AJAX submission
    const form = document.getElementById('editUserForm');
    const formData = new FormData(form);

    $.ajax({
      url: `${baseUrl}patient-list`,
        method: 'POST',  // Assuming you're using POST method
        data: formData,
        processData: false,
        contentType: false,
        success: function(status) {
            // Handle success response
        // sweetalert
        Swal.fire({
          icon: 'success',
          title: `Successfully ${status}!`,
          text: `Patient ${status} Successfully.`,
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });            // Optionally, close the modal or perform other actions
            $('#editUser').modal('hide');
        },
        error: function(error) {
            // Handle error
            console.error('Error submitting form data:', error);
            alert('An error occurred while submitting the form.');
        }
    });
});

});
