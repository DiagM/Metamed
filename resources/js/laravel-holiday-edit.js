
const editHolidayForm = document.getElementById('editHolidayForm');

// Holiday form validation
const fvv = FormValidation.formValidation(editHolidayForm, {
  fields: {
    status: {
      validators: {
        notEmpty: {
          message: 'Please select a status'
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
  var formData = new FormData($('#editHolidayForm')[0]);
  // Append additional data if needed
  formData.append('id', $('#holiday_id').val());
  formData.append('reason', $('#edit-holiday-reason').val());
  formData.append('start_date', $('#edit-holiday-start-date').val());
  formData.append('end_date', $('#edit-holiday-end-date').val());
  formData.append('status', $('#edit-holiday-status').val());
  console.log($('#edit-holiday-status').val());
  // Adding or updating holiday when form successfully validates
  $.ajax({
    data: formData,
    url: `${baseUrl}changestatus`, // Update URL to match your endpoint
    type: 'POST',
    contentType: false, // Set contentType to false when sending FormData
    processData: false, // Set processData to false to prevent jQuery from automatically processing the data
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
        $('#offcanvasEditHoliday').offcanvas('hide');
        // Optionally clear form fields or perform other actions
      } else {
        console.error('Error: Expected application/pdf content type but received:', contentType);
        alert('An error occurred while downloading the file.');
      }
    },
    error: function (err) {
      $('#offcanvasEditHoliday').offcanvas('hide');
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
