<!-- Edit User Modal -->
<div class="modal fade" id="editUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Edit Profile</h3>
                </div>
                <form id="editUserForm" class="row g-3" method="POST" onsubmit="return false">
                    <input type="hidden" id="edit-user-id" name="id" value="{{ $user->id }}">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="edit-user-fullname">Full Name</label>
                        <input type="text" class="form-control" id="edit-user-fullname" placeholder="John Doe"
                            name="name" aria-label="John Doe" value="{{ $user->name }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="edit-user-email">Email</label>
                        <input type="email" id="edit-user-email" name="email" class="form-control"
                            placeholder="example@domain.com" value="{{ $user->email }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="edit-user-contact">Contact</label>
                        <input type="text" id="edit-user-contact" class="form-control phone-mask"
                            placeholder="+1 (609) 988-44-11" aria-label="john.doe@example.com" name="contact"
                            value="{{ $user->contact }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="edit-user-license_number">License number</label>
                        <input type="text" id="edit-user-license_number" name="license_number" class="form-control"
                            placeholder="AD576" aria-label="jdoe1" value="{{ $user->license_number }}" disabled />
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="edit-user-date_of_birth">Date of Birth</label>
                        <input type="date" id="edit-user-date_of_birth" name="date_of_birth" class="form-control"
                            value="{{ $user->date_of_birth }}" />
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="edit-user-address">Address</label>
                        <textarea id="edit-user-address" name="address" class="form-control" rows="3">{{ $user->address }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="edit-user-password">New Password</label>
                        <input type="password" id="edit-user-password" name="password" class="form-control"
                            placeholder="Enter new password" />
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="edit-user-password-confirm">Confirm New Password</label>
                        <input type="password" id="edit-user-password-confirm" name="password_confirmation"
                            class="form-control" placeholder="Confirm new password" />
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
<!--/ Edit User Modal -->
