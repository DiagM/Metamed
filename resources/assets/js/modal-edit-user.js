/**
 * Edit User
 */

'use strict';

// Select2 (jquery)
$(function () {
  const select2 = $('.select2');

  // Select2 Country
  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Select value',
        dropdownParent: $this.parent()
      });
    });
  }
});

document.addEventListener('DOMContentLoaded', function (e) {
    // Avatar script
    var $name = window.userData.name;
    var stateNum = Math.floor(Math.random() * 6);
    var states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
    var $state = states[stateNum];

    var $initials = $name.match(/\b\w/g) || [];
    $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
    var $output = '<span class="avatar-initial rounded-circle bg-label-' + $state + '" style="display: inline-block; width: 100px; height: 100px; line-height: 100px; text-align: center; font-size: 24px;">' + $initials + '</span>';


    // Append the generated avatar badge to the DOM
    var avatarWrapper = document.querySelector('.avatar-wrapper');
    if (avatarWrapper) {
      avatarWrapper.innerHTML = $output;
    }
  (function () {
    // variables
    const modalEditUserTaxID = document.querySelector('.modal-edit-tax-id');
    const modalEditUserPhone = document.querySelector('.phone-number-mask');

    // Prefix
    if (modalEditUserTaxID) {
      new Cleave(modalEditUserTaxID, {
        prefix: 'TIN',
        blocks: [3, 3, 3, 4],
        uppercase: true
      });
    }

    // Phone Number Input Mask
    if (modalEditUserPhone) {
      new Cleave(modalEditUserPhone, {
        phone: true,
        phoneRegionCode: 'US'
      });
    }

    // Edit user form validation
    FormValidation.formValidation(document.getElementById('editUserForm'), {
      fields: {
        modalEditUserFirstName: {
          validators: {
            notEmpty: {
              message: 'Please enter your first name'
            },
            regexp: {
              regexp: /^[a-zA-Zs]+$/,
              message: 'The first name can only consist of alphabetical'
            }
          }
        },
        modalEditUserLastName: {
          validators: {
            notEmpty: {
              message: 'Please enter your last name'
            },
            regexp: {
              regexp: /^[a-zA-Zs]+$/,
              message: 'The last name can only consist of alphabetical'
            }
          }
        },
        modalEditUserName: {
          validators: {
            notEmpty: {
              message: 'Please enter your username'
            },
            stringLength: {
              min: 6,
              max: 30,
              message: 'The name must be more than 6 and less than 30 characters long'
            },
            regexp: {
              regexp: /^[a-zA-Z0-9 ]+$/,
              message: 'The name can only consist of alphabetical, number and space'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          // Use this for enabling/changing valid/invalid class
          // eleInvalidClass: '',
          eleValidClass: '',
          rowSelector: '.col-12'
        }),
        submitButton: new FormValidation.plugins.SubmitButton(),
        // Submit the form when all fields are valid
        // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
        autoFocus: new FormValidation.plugins.AutoFocus()
      }
    });
  })();
});
