<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h5 class="mb-0 fw-bold">Update Profile</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= site_url('admin/profile') ?>" method="post">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">First Name</label>
                            <input type="text" name="first_name" class="form-control" value="<?= html_escape($user['first_name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="<?= html_escape($user['last_name'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?= html_escape($user['email'] ?? '') ?>">
                        <div class="form-text">Optional. Used for notifications.</div>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold mb-3">Change Password</h6>
                    <div class="mb-3">
                        <label class="form-label fw-bold">New Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save me-2"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
