/**
 * App Calendar
 */

/**
 * ! If both start and end dates are same Full calendar will nullify the end date value.
 * ! Full calendar will end the event on a day before at 12:00:00AM thus, event won't extend to the end date.
 * ! We are getting events from a separate file named app-calendar-events.js. You can add or remove events from there.
 *
 **/

'use strict';

let direction = 'ltr';

if (isRtl) {
  direction = 'rtl';
}

document.addEventListener('DOMContentLoaded', function () {
  (function () {
    const calendarEl = document.getElementById('calendar'),
      appCalendarSidebar = document.querySelector('.app-calendar-sidebar'),
      addEventSidebar = document.getElementById('addEventSidebar'),
      appOverlay = document.querySelector('.app-overlay'),
      calendarsColor = {
        Consultation: 'primary',
        Procedure: 'success',
        Examination: 'danger',
        'Follow-up': 'warning',
        Other: 'info'
      },
      offcanvasTitle = document.querySelector('.offcanvas-title'),
      btnToggleSidebar = document.querySelector('.btn-toggle-sidebar'),
      btnSubmit = document.querySelector('button[type="submit"]'),
      btnDeleteEvent = document.querySelector('.btn-delete-event'),
      btnCancel = document.querySelector('.btn-cancel'),
      eventTitle = document.querySelector('#eventTitle'),
      eventStartDate = document.querySelector('#eventStartDate'),
      eventEndDate = document.querySelector('#eventEndDate'),
      // eventUrl = document.querySelector('#eventURL'),
      eventLabel = $('#eventLabel'), // ! Using jquery vars due to select2 jQuery dependency
      eventdoctors = $('#eventDoctors'), // ! Using jquery vars due to select2 jQuery dependency
      eventpatients = $('#eventPatients'), // ! Using jquery vars due to select2 jQuery dependency
      // eventLocation = document.querySelector('#eventLocation'),
      eventDescription = document.querySelector('#eventDescription'),
      // allDaySwitch = document.querySelector('.allDay-switch'),
      selectAll = document.querySelector('.select-all'),
      filterInput = [].slice.call(document.querySelectorAll('.input-filter')),
      inlineCalendar = document.querySelector('.inline-calendar');
      // Initialize Select2 for multiple selection
$('.select-filter-patients').select2({
  placeholder: 'Select patients',
  allowClear: true, // Optional: Add an option to clear the selection
  closeOnSelect: false // Optional: Keep the dropdown open after selection
});


    let eventToUpdate,
      currentEvents = events, // Assign app-calendar-events.js file events (assume events from API) to currentEvents (browser store/object) to manage and update calender events
      isFormValid = false,
      inlineCalInstance;

    // Init event Offcanvas
    const bsAddEventSidebar = new bootstrap.Offcanvas(addEventSidebar);

    //! TODO: Update Event label and guest code to JS once select removes jQuery dependency
    // Event Label (select2)
    if (eventLabel.length) {
      function renderBadges(option) {
        if (!option.id) {
          return option.text;
        }
        var $badge =
          "<span class='badge badge-dot bg-" + $(option.element).data('label') + " me-2'> " + '</span>' + option.text;

        return $badge;
      }
      eventLabel.wrap('<div class="position-relative"></div>').select2({
         placeholder: 'Select value',
        dropdownParent: eventLabel.parent(),
        templateResult: renderBadges,
        templateSelection: renderBadges,
        minimumResultsForSearch: -1,
        escapeMarkup: function (es) {
          return es;
        }
      });
    }

    // Event doctors (select2)
    // if (eventdoctors.length) {
    //   // function renderGuestAvatar(option) {
    //   //   if (!option.id) {
    //   //     return option.text;
    //   //   }
    //   //   var $avatar =
    //   //     "<div class='d-flex flex-wrap align-items-center'>" +
    //   //     "<div class='avatar avatar-xs me-2'>" +
    //   //     "<img src='" +
    //   //     assetsPath +
    //   //     'img/avatars/' +
    //   //     $(option.element).data('avatar') +
    //   //     "' alt='avatar' class='rounded-circle' />" +
    //   //     '</div>' +
    //   //     option.text +
    //   //     '</div>';

    //   //   return $avatar;
    //   // }
    //   eventdoctors.wrap('<div class="position-relative"></div>').select2({
    //      placeholder: 'Select value',
    //     dropdownParent: eventdoctors.parent(),
    //     closeOnSelect: false,
    //     // templateResult: renderGuestAvatar,
    //     // templateSelection: renderGuestAvatar,
    //     escapeMarkup: function (es) {
    //       return es;
    //     }
    //   });
    // }
    if (eventpatients.length) {

      eventpatients.wrap('<div class="position-relative"></div>').select2({
        // placeholder: 'Select value',
        dropdownParent: eventpatients.parent(),
        closeOnSelect: false,
        // templateResult: renderGuestAvatar,
        // templateSelection: renderGuestAvatar,
        escapeMarkup: function (es) {
          return es;
        }
      });
    }

    // Event start (flatpicker)
    if (eventStartDate) {
      var start = eventStartDate.flatpickr({
        enableTime: true,
        altFormat: 'Y-m-dTH:i:S',
        onReady: function (selectedDates, dateStr, instance) {
          if (instance.isMobile) {
            instance.mobileInput.setAttribute('step', null);
          }
        }
      });
    }

    // Event end (flatpicker)
    if (eventEndDate) {
      var end = eventEndDate.flatpickr({
        enableTime: true,
        altFormat: 'Y-m-dTH:i:S',
        onReady: function (selectedDates, dateStr, instance) {
          if (instance.isMobile) {
            instance.mobileInput.setAttribute('step', null);
          }
        }
      });
    }

    // Inline sidebar calendar (flatpicker)
    if (inlineCalendar) {
      inlineCalInstance = inlineCalendar.flatpickr({
        monthSelectorType: 'static',
        inline: true
      });
    }

    // Event click function
    function eventClick(info) {
      eventToUpdate = info.event;
      if (eventToUpdate.url) {
        info.jsEvent.preventDefault();
        window.open(eventToUpdate.url, '_blank');
      }
      bsAddEventSidebar.show();
      // For update event set offcanvas title text: Update Event
      if (offcanvasTitle) {
        offcanvasTitle.innerHTML = 'Update Event';
      }
      btnSubmit.innerHTML = 'Update';
      btnSubmit.classList.add('btn-update-event');
      btnSubmit.classList.remove('btn-add-event');
      btnDeleteEvent.classList.remove('d-none');

      eventTitle.value = eventToUpdate.title;
      start.setDate(eventToUpdate.start, true, 'Y-m-d');
      // eventToUpdate.allDay === true ? (allDaySwitch.checked = true) : (allDaySwitch.checked = false);
      eventToUpdate.end !== null
        ? end.setDate(eventToUpdate.end, true, 'Y-m-d')
        : end.setDate(eventToUpdate.start, true, 'Y-m-d');
      eventLabel.val(eventToUpdate.extendedProps.calendar).trigger('change');
      // eventToUpdate.extendedProps.location !== undefined
      //   ? (eventLocation.value = eventToUpdate.extendedProps.location)
      //   : null;
      eventToUpdate.extendedProps.doctor_id !== undefined
        ? eventdoctors.val(eventToUpdate.extendedProps.doctor_id).trigger('change')
        : null;
        eventToUpdate.extendedProps.doctor_id !== undefined
        ? eventpatients.val(eventToUpdate.extendedProps.patient_id).trigger('change')
        : null;
      eventToUpdate.extendedProps.description !== undefined
        ? (eventDescription.value = eventToUpdate.extendedProps.description)
        : null;

      // // Call removeEvent function
      // btnDeleteEvent.addEventListener('click', e => {
      //   removeEvent(parseInt(eventToUpdate.id));
      //   // eventToUpdate.remove();
      //   bsAddEventSidebar.hide();
      // });
    }

    // Modify sidebar toggler
    function modifyToggler() {
      const fcSidebarToggleButton = document.querySelector('.fc-sidebarToggle-button');
      fcSidebarToggleButton.classList.remove('fc-button-primary');
      fcSidebarToggleButton.classList.add('d-lg-none', 'd-inline-block', 'ps-0');
      while (fcSidebarToggleButton.firstChild) {
        fcSidebarToggleButton.firstChild.remove();
      }
      fcSidebarToggleButton.setAttribute('data-bs-toggle', 'sidebar');
      fcSidebarToggleButton.setAttribute('data-overlay', '');
      fcSidebarToggleButton.setAttribute('data-target', '#app-calendar-sidebar');
      fcSidebarToggleButton.insertAdjacentHTML('beforeend', '<i class="ti ti-menu-2 ti-sm text-heading"></i>');
    }

    // Filter events by calender
    function selectedCalendars() {
      let selected = [],
        filterInputChecked = [].slice.call(document.querySelectorAll('.input-filter:checked'));

      filterInputChecked.forEach(item => {
        selected.push(item.getAttribute('data-value'));
      });

      return selected;
    }
    $('#filterPatients').on('change', function() {
      // Get selected patient IDs
      var selectedPatients = $(this).val();

      // Refetch events with selected patient IDs
      calendar.refetchEvents();
    });
    $('#filterDoctors').on('change', function() {
      // Get selected patient IDs
      var selectedPatients = $(this).val();

      // Refetch events with selected patient IDs
      calendar.refetchEvents();
    });

    // --------------------------------------------------------------------------------------------------
    // AXIOS: fetchEvents
    // * This will be called by fullCalendar to fetch events. Also this can be used to refetch events.
    // --------------------------------------------------------------------------------------------------
    function fetchEvents(info, successCallback) {
      // Get selected filters and patient IDs
      let selectedFilters = selectedCalendars();
      let selectedPatients = $('#filterPatients').val();
      let selectedDoctors = $('#filterDoctors').val();

      // If selectedFilters or selectedPatients is empty, return an empty array
      if (selectedFilters.length === 0 || !selectedPatients) {
        return successCallback([]);
      }
      if (selectedFilters.length === 0 || !selectedDoctors) {
        return successCallback([]);
      }

      // Fetch events from the API endpoint based on selected filters and patient IDs
      fetch('/calendar/events?filters=' + JSON.stringify(selectedFilters) + '&patientIds=' + JSON.stringify(selectedPatients) +'&doctorsIds=' + JSON.stringify(selectedDoctors) )
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          // Prepare events data in the correct format
          let events = data.map(event => ({
            id: event.id,
            title: event.title,
            start: event.start,
            end: event.end,
            extendedProps: {
              calendar: event.extendedProps.calendar,
              doctor_id: event.extendedProps.doctor_id,
              patient_id: event.extendedProps.patient_id,
              description: event.extendedProps.description
            }
          }));
          successCallback(events);
        })
        .catch(error => {
          console.error('Error fetching calendar events:', error);
        });
    }




    // Init FullCalendar
    // ------------------------------------------------
    let calendar = new Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      events: fetchEvents,
      plugins: [dayGridPlugin, interactionPlugin, listPlugin, timegridPlugin],
      editable: true,
      dragScroll: true,
      dayMaxEvents: 2,
      eventResizableFromStart: true,
      customButtons: {
        sidebarToggle: {
          text: 'Sidebar'
        }
      },
      headerToolbar: {
        start: 'sidebarToggle, prev,next, title',
        end: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
      },
      direction: direction,
      initialDate: new Date(),
      navLinks: true, // can click day/week names to navigate views
      eventClassNames: function ({ event: calendarEvent }) {
        const colorName = calendarsColor[calendarEvent._def.extendedProps.calendar];
        // Background Color
        return ['fc-event-' + colorName];
      },
      dateClick: function (info) {
        let date = moment(info.date).format('YYYY-MM-DD');
        resetValues();
        bsAddEventSidebar.show();

        // For new event set offcanvas title text: Add Event
        if (offcanvasTitle) {
          offcanvasTitle.innerHTML = 'Add Event';
        }
        btnSubmit.innerHTML = 'Add';
        btnSubmit.classList.remove('btn-update-event');
        btnSubmit.classList.add('btn-add-event');
        btnDeleteEvent.classList.add('d-none');
        eventStartDate.value = date;
        eventEndDate.value = date;
      },
      eventClick: function (info) {
        eventClick(info);
      },
      datesSet: function () {
        modifyToggler();
      },
      viewDidMount: function () {
        modifyToggler();
      }
    });

    // Render calendar
    calendar.render();
    // Modify sidebar toggler
    modifyToggler();

    const eventForm = document.getElementById('eventForm');
    const fv = FormValidation.formValidation(eventForm, {
      fields: {
        eventTitle: {
          validators: {
            notEmpty: {
              message: 'Please enter event title '
            }
          }
        },
        eventStartDate: {
          validators: {
            notEmpty: {
              message: 'Please enter start date '
            }
          }
        },
        eventEndDate: {
          validators: {
            notEmpty: {
              message: 'Please enter end date '
            }
          }
        },
        eventDescription: {
          validators: {
            notEmpty: {
              message: 'Please enter description '
            }
          }
        },
        eventLabel: {
          validators: {
            notEmpty: {
              message: 'Please enter Label '
            }
          }
        },
        eventdoctors: {
          validators: {
            notEmpty: {
              message: 'Please enter doctor '
            }
          }
        },
        eventpatients: {
          validators: {
            notEmpty: {
              message: 'Please enter patient '
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
    })
      .on('core.form.valid', function () {
        // Jump to the next step when all fields in the current step are valid
        isFormValid = true;
      })
      .on('core.form.invalid', function () {
        // if fields are invalid
        isFormValid = false;
      });

    // Sidebar Toggle Btn
    if (btnToggleSidebar) {
      btnToggleSidebar.addEventListener('click', e => {
        btnCancel.classList.remove('d-none');
      });
    }

// Add Event
// ------------------------------------------------
function addEvent(eventData) {
  var formData = new FormData();
  formData.append('eventTitle', eventData.title);
  formData.append('eventStartDate', eventData.start);
  formData.append('eventEndDate', eventData.end);
  formData.append('eventLabel', eventData.extendedProps.calendar);
  formData.append('eventDoctors', eventData.extendedProps.doctors);
  formData.append('eventPatients', eventData.extendedProps.patients);
  formData.append('eventDescription', eventData.extendedProps.description);

  $.ajax({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type: "POST",
    url: baseUrl + "calendar/add-event",
    data: formData,
    dataType: "json",
    cache: false,
    contentType: false,
    processData: false,
    success: function(response) {
      // Sweetalert
      Swal.fire({
        icon: 'success',
        title: `Successfully ${response.message}!`,
        text: `Event ${response.message} Successfully.`,
        customClass: {
          confirmButton: 'btn btn-success'
        }
      });
      bsAddEventSidebar.hide();
      calendar.refetchEvents();
    },
    error: function(xhr, status, error) {
      // Handle error response
      if (xhr.status === 422) {
        var errors = xhr.responseJSON.errors;
        // Display errors in the form
        for (var key in errors) {
          if (errors.hasOwnProperty(key)) {
            var errorMessage = errors[key][0]; // Get the first error message for the field
            // Display or highlight the error message in the form
            // For example, you can display it next to the corresponding input field
            $('#' + key).addClass('is-invalid');
            $('#' + key + '-error').text(errorMessage); // Assuming you have a span with id key-error for error display
          }
        }

        Swal.fire({
          title: 'Error',
          text: errorMessage,
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      } else if (xhr.status === 403) {
        Swal.fire({
          title: 'Forbidden',
          text: 'You do not have permission to add this event.',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-danger'
          }
        });
      } else {
        console.error(xhr.responseText);
        // You may want to notify the user about other types of errors
        Swal.fire({
          title: 'Error',
          text: 'An unexpected error occurred.',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-danger'
          }
        });
      }
    }
  });
}



// Update Event
// ------------------------------------------------
function updateEvent(eventData) {
  eventData.id = parseInt(eventData.id);
  var formData = new FormData();

  formData.append('eventTitle', eventData.title);
  formData.append('eventStartDate', eventData.start);
  formData.append('eventEndDate', eventData.end);
  formData.append('eventLabel', eventData.extendedProps.calendar);
  formData.append('eventDoctors', eventData.extendedProps.doctors);
  formData.append('eventPatients', eventData.extendedProps.patients);
  formData.append('eventDescription', eventData.extendedProps.description);

  $.ajax({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type: "POST", // Change to POST
    url: baseUrl + "calendar/update-event/" + eventData.id,
    data: formData,
    dataType: "json",
    cache: false,
    contentType: false,
    processData: false,
    success: function(response) {
      // Sweetalert
      Swal.fire({
        icon: 'success',
        title: `Successfully ${response.message}!`,
        text: `Event ${response.message} Successfully.`,
        customClass: {
          confirmButton: 'btn btn-success'
        }
      });
      bsAddEventSidebar.hide();
      calendar.refetchEvents();
    },
    error: function(xhr, status, error) {
      // Handle error response
      if (xhr.status === 422) {
        var errors = xhr.responseJSON.errors;
        // Display errors in the form
        for (var key in errors) {
          if (errors.hasOwnProperty(key)) {
            var errorMessage = errors[key][0]; // Get the first error message for the field
            // Display or highlight the error message in the form
            // For example, you can display it next to the corresponding input field
            $('#' + key).addClass('is-invalid');
            $('#' + key + '-error').text(errorMessage); // Assuming you have a span with id key-error for error display
          }
        }

        Swal.fire({
          title: 'Error',
          text: errorMessage,
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-success'
          }
        });
      } else if (xhr.status === 403) {
        Swal.fire({
          title: 'Forbidden',
          text: 'You do not have permission to update this event.',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-danger'
          }
        });
      } else {
        console.error(xhr.responseText);
        // You may want to notify the user about other types of errors
        Swal.fire({
          title: 'Error',
          text: 'An unexpected error occurred.',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-danger'
          }
        });
      }
    }
  });
}



    // Remove Event
    // ------------------------------------------------

    function removeEvent(eventId) {
      // ? Delete existing event data to current events object and refetch it to display on calender
      // ? You can write below code to AJAX call success response
      currentEvents = currentEvents.filter(function (event) {
        return event.id != eventId;
      });
      calendar.refetchEvents();

      // ? To delete event directly to calender (won't update currentEvents object)
      // removeEventInCalendar(eventId);
    }

    // (Update Event In Calendar (UI Only)
    // ------------------------------------------------
    const updateEventInCalendar = (updatedEventData, propsToUpdate, extendedPropsToUpdate) => {
      const existingEvent = calendar.getEventById(updatedEventData.id);

      // --- Set event properties except date related ----- //
      // ? Docs: https://fullcalendar.io/docs/Event-setProp
      // dateRelatedProps => ['start', 'end', 'allDay']
      // eslint-disable-next-line no-plusplus
      for (var index = 0; index < propsToUpdate.length; index++) {
        var propName = propsToUpdate[index];
        existingEvent.setProp(propName, updatedEventData[propName]);
      }

      // --- Set date related props ----- //
      // ? Docs: https://fullcalendar.io/docs/Event-setDates
      // existingEvent.setDates(updatedEventData.start, updatedEventData.end, {
      //   allDay: updatedEventData.allDay
      // });

      // --- Set event's extendedProps ----- //
      // ? Docs: https://fullcalendar.io/docs/Event-setExtendedProp
      // eslint-disable-next-line no-plusplus
      for (var index = 0; index < extendedPropsToUpdate.length; index++) {
        var propName = extendedPropsToUpdate[index];
        existingEvent.setExtendedProp(propName, updatedEventData.extendedProps[propName]);
      }
    };

    // Remove Event In Calendar (UI Only)
    // ------------------------------------------------
    function removeEventInCalendar(eventId) {
      calendar.getEventById(eventId).remove();
    }

    // Add new event
    // ------------------------------------------------
    btnSubmit.addEventListener('click', e => {
      // Reset error messages

      if (btnSubmit.classList.contains('btn-add-event')) {
        if (isFormValid) {
          let newEvent = {
            id: calendar.getEvents().length + 1,
            title: eventTitle.value,
            start: eventStartDate.value,
            end: eventEndDate.value,
            // startStr: eventStartDate.value,
            // endStr: eventEndDate.value,
            // display: 'block',
            extendedProps: {
              // location: eventLocation.value,
              doctors: eventdoctors.val(),
              patients: eventpatients.val(),
              calendar: eventLabel.val(),
              description: eventDescription.value
            }
          };
          // if (eventUrl.value) {
          //   newEvent.url = eventUrl.value;
          // }
          // if (allDaySwitch.checked) {
          //   newEvent.allDay = true;
          // }
          addEvent(newEvent);
          // bsAddEventSidebar.hide();
        }
      } else {
        // Update event
        // ------------------------------------------------
        if (isFormValid) {
          let eventData = {
            id: eventToUpdate.id,
           title: eventTitle.value,
            start: eventStartDate.value,
            end: eventEndDate.value,
            // startStr: eventStartDate.value,
            // endStr: eventEndDate.value,
            // display: 'block',
            extendedProps: {
              // location: eventLocation.value,
              doctors: eventdoctors.val(),
              patients: eventpatients.val(),
              calendar: eventLabel.val(),
              description: eventDescription.value
            }
          };

          updateEvent(eventData);
          // bsAddEventSidebar.hide();
        }
      }
    });

    btnDeleteEvent.addEventListener('click', e => {
      var id = parseInt(eventToUpdate.id);
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
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'DELETE',
            url: `${baseUrl}calendar/${id}`,
            success: function () {
              bsAddEventSidebar.hide();
              calendar.refetchEvents();
              // success sweetalert
              Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: 'The event has been deleted!',
                customClass: {
                  confirmButton: 'btn btn-success'
                }
              });
              bsAddEventSidebar.hide();
              calendar.refetchEvents();
            },
            error: function (error) {
              if (error.status === 403) {
                Swal.fire({
                  icon: 'error',
                  title: 'Forbidden',
                  text: 'You do not have permission to delete this event.',
                  customClass: {
                    confirmButton: 'btn btn-danger'
                  }
                });
              } else {
                console.log(error);
              }
            }
          });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
          Swal.fire({
            title: 'Cancelled',
            text: 'The event is not deleted!',
            icon: 'error',
            customClass: {
              confirmButton: 'btn btn-success'
            }
          });
        }
      });
    });

    // Reset event form inputs values
    // ------------------------------------------------
    function resetValues() {
      eventEndDate.value = '';
      // eventUrl.value = '';
      eventStartDate.value = '';
      eventTitle.value = '';
      // eventLocation.value = '';
      // allDaySwitch.checked = false;
       eventdoctors.val(userId).trigger('change');
      eventpatients.val('').trigger('change');
      eventDescription.value = '';
          // Reset error messages
    $('.error-message').text(''); // Clear the text content of all error message spans
    $('.form-control').removeClass('is-invalid');
    $('.form-select').removeClass('is-invalid');
    }

    // When modal hides reset input values
    addEventSidebar.addEventListener('hidden.bs.offcanvas', function () {
      resetValues();
    });

    // Hide left sidebar if the right sidebar is open
    btnToggleSidebar.addEventListener('click', e => {
      if (offcanvasTitle) {
        offcanvasTitle.innerHTML = 'Add Event';
      }
      btnSubmit.innerHTML = 'Add';
      btnSubmit.classList.remove('btn-update-event');
      btnSubmit.classList.add('btn-add-event');
      btnDeleteEvent.classList.add('d-none');
      appCalendarSidebar.classList.remove('show');
      appOverlay.classList.remove('show');
    });

    // Calender filter functionality
    // ------------------------------------------------
    if (selectAll) {
      selectAll.addEventListener('click', e => {
        if (e.currentTarget.checked) {
          document.querySelectorAll('.input-filter').forEach(c => (c.checked = 1));
        } else {
          document.querySelectorAll('.input-filter').forEach(c => (c.checked = 0));
        }
        calendar.refetchEvents();
      });
    }

    if (filterInput) {
      filterInput.forEach(item => {
        item.addEventListener('click', () => {
          document.querySelectorAll('.input-filter:checked').length < document.querySelectorAll('.input-filter').length
            ? (selectAll.checked = false)
            : (selectAll.checked = true);
          calendar.refetchEvents();
        });
      });
    }

    // Jump to date on sidebar(inline) calendar change
    inlineCalInstance.config.onChange.push(function (date) {
      calendar.changeView(calendar.view.type, moment(date[0]).format('YYYY-MM-DD'));
      modifyToggler();
      appCalendarSidebar.classList.remove('show');
      appOverlay.classList.remove('show');
    });
  })();
});
