/**
 * Edit User
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
    // ajax setup
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
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

  // Cleave.js initialization for Phone Number mask
  var modalEditUserPhone = document.querySelector('.phone-mask');

  // if (modalEditUserPhone) {
  //   new Cleave(modalEditUserPhone, {
  //     phone: true,
  //     phoneRegionCode: 'US'
  //   });
  // }

  // Form validation using FormValidation library
  FormValidation.formValidation(document.getElementById('editUserForm'), {
    fields: {
      'name': {
        validators: {
          notEmpty: {
            message: 'Please enter the full name'
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
            message: 'Please enter the email address'
          },
          emailAddress: {
            message: 'The input is not a valid email address'
          }
        }
      },
      'contact': {
        validators: {
          notEmpty: {
            message: 'Please enter the contact number'
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
            enabled: false,
            message: 'Please enter the license number'
          }
        }
      },
      'date_of_birth': {
        validators: {
          notEmpty: {
            message: 'Please enter the date of birth'
          },
          date: {
            format: 'YYYY-MM-DD',
            message: 'The input is not a valid date (YYYY-MM-DD format)'
          }
        }
      },
      'address': {
        validators: {
          notEmpty: {
            message: 'Please enter the address'
          }
        }
      },
      'password': {
        validators: {
          notEmpty: {
            message: 'Please enter the new password'
          },
          stringLength: {
            min: 6,
            message: 'The password must be at least 6 characters'
          }
        }
      },
      'password_confirmation': {
        validators: {
          notEmpty: {
            message: 'Please confirm the new password'
          },
          identical: {
            compare: function() {
              return document.querySelector('#edit-user-password').value;
            },
            message: 'The passwords do not match'
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
      url: `${baseUrl}pages/profile-user/${$('#edit-user-id').val()}`,
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        // Handle success response
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: 'User profile updated successfully',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
        $('#editUser').modal('hide'); // Optionally close the modal
      },
      error: function(error) {
        // Handle error
        console.error('Error submitting form data:', error);
        alert('An error occurred while submitting the form.');
      }
    });
  });

});
