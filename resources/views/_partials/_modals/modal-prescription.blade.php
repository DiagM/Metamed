<!-- Edit Prescription Modal -->
<div class="modal fade" id="editPrescription" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-prescription">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Prescription</h3>
                    <p class="text-muted">Update the details of the prescription.</p>
                </div>
                <form id="editPrescriptionForm" class="row g-3" method="POST" onsubmit="return false">
                    <input type="text" id="prescriptionPatientId" name="prescriptionPatientId"
                        value="{{ $user->id }}" hidden>
                    <div class="col-12">
                        <label class="form-label" for="prescriptionPatientName">Patient Name</label>
                        <input type="text" id="prescriptionPatientName" name="prescriptionPatientName"
                            class="form-control" placeholder="Patient Name" value="{{ $user->name }}" readonly />
                    </div>
                    <div id="medicationFields">
                        <div class="row medication-set mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="prescriptionMedication">Medication</label>
                                <input type="text" id="prescriptionMedication" name="prescriptionMedication[]"
                                    class="form-control" placeholder="Medication Name" />
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="prescriptionDosage">Dosage</label>
                                <input type="text" id="prescriptionDosage" name="prescriptionDosage[]"
                                    class="form-control" placeholder="Dosage (e.g., 500mg)" />
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="prescriptionInstructions">Instructions</label>
                                <textarea id="prescriptionInstructions" name="prescriptionInstructions[]" class="form-control" rows="3"
                                    placeholder="Instructions for the patient"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 text-center mb-3">
                        <button type="button" class="btn btn-secondary" id="addMedicationButton">Add More
                            Medication</button>
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                            aria-label="Close">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--/ Edit Prescription Modal -->
