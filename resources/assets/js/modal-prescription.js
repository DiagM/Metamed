/**
 * Edit Prescription
 */

'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
    // Initialize select2 if you have any select elements
    const select2 = $('.select2');
    if (select2.length) {
        select2.each(function () {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>').select2({
                placeholder: 'Select value',
                dropdownParent: $this.parent()
            });
        });
    }

    // Handle adding new medication fields
    document.getElementById('addMedicationButton').addEventListener('click', function() {
        var medicationFields = document.getElementById('medicationFields');
        var newMedicationSet = document.createElement('div');
        newMedicationSet.classList.add('row', 'medication-set', 'mb-3');
        newMedicationSet.innerHTML = `
            <div class="col-12 col-md-6">
                <label class="form-label" for="prescriptionMedication">Medication</label>
                <input type="text" name="prescriptionMedication[]" class="form-control" placeholder="Medication Name" />
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label" for="prescriptionDosage">Dosage</label>
                <input type="text" name="prescriptionDosage[]" class="form-control" placeholder="Dosage (e.g., 500mg)" />
            </div>
            <div class="col-12">
                <label class="form-label" for="prescriptionInstructions">Instructions</label>
                <textarea name="prescriptionInstructions[]" class="form-control" rows="3" placeholder="Instructions for the patient"></textarea>
            </div>
        `;
        medicationFields.appendChild(newMedicationSet);
    });

    // Edit prescription form validation
    const fv = FormValidation.formValidation(document.getElementById('editPrescriptionForm'), {
        fields: {
            prescriptionPatientName: {
                validators: {
                    notEmpty: {
                        message: 'Please enter the patient name'
                    }
                }
            },
            'prescriptionMedication[]': {
                validators: {
                    notEmpty: {
                        message: 'Please enter the medication name'
                    }
                }
            },
            'prescriptionDosage[]': {
                validators: {
                    notEmpty: {
                        message: 'Please enter the dosage'
                    }
                }
            },
            'prescriptionInstructions[]': {
                validators: {
                    notEmpty: {
                        message: 'Please enter the instructions'
                    }
                }
            }
        },
        plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap5: new FormValidation.plugins.Bootstrap5({
                rowSelector: '.col-12'
            }),
            submitButton: new FormValidation.plugins.SubmitButton(),
            autoFocus: new FormValidation.plugins.AutoFocus()
        }
    }).on('core.form.valid', function() {
      // Form is valid, let's submit it
      const form = document.getElementById('editPrescriptionForm');
      const formData = new FormData(form);

      $.ajax({
          url: `${baseUrl}MedicalFile/prescription`, // Adjust the URL to match your Laravel route
          method: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          xhrFields: {
              responseType: 'blob' // Set the response type to blob
          },
          success: function(response, status, xhr) {
              // Check content type of response
              var contentType = xhr.getResponseHeader('Content-Type');

              // Handle different content types
              if (contentType.toLowerCase().indexOf("application/pdf") !== -1) {
                  var filename = "";
                  var disposition = xhr.getResponseHeader('Content-Disposition');
                  if (disposition && disposition.indexOf('attachment') !== -1) {
                      var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                      var matches = filenameRegex.exec(disposition);
                      if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
                  }

                  var blob = new Blob([response], { type: 'application/pdf' });

                  if (typeof window.navigator.msSaveBlob !== 'undefined') {
                      // IE workaround
                      window.navigator.msSaveBlob(blob, filename);
                  } else {
                      var URL = window.URL || window.webkitURL;
                      var downloadUrl = URL.createObjectURL(blob);

                      // Create a link and trigger download
                      var a = document.createElement("a");
                      if (typeof a.download === 'undefined') {
                          window.location = downloadUrl;
                      } else {
                          a.href = downloadUrl;
                          a.download = filename;
                          document.body.appendChild(a);
                          a.click();
                      }

                      // Cleanup
                      setTimeout(function() {
                          URL.revokeObjectURL(downloadUrl);
                          document.body.removeChild(a);
                      }, 100);
                  }

                  // Optionally close the modal after successful download
                  $('#editPrescription').modal('hide');
                  //clear modal
                  $('#editPrescriptionForm').find('input[type="text"], textarea').not('#prescriptionPatientId, #prescriptionPatientName').val('');
                  $('#editPrescriptionForm').find('select').val('').trigger('change'); // Clear select2 fields
              } else {
                  console.error('Error: Expected application/pdf content type but received:', contentType);
                  alert('An error occurred while generating the prescription.');
              }
          },
          error: function(error) {
              console.error('Error generating prescription', error);
              alert('An error occurred while generating the prescription.');
          }
      });
  });

});
