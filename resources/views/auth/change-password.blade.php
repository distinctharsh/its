@extends('layouts.layout')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Card for the form -->
            <div class="card shadow-lg border-light">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Change Password</h3>
                </div>
                <div class="card-body">
                    <!-- Display Validation Errors -->
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form id="change-password-form">
                        @csrf

                        <!-- Current Password -->
                        <div class="form-group position-relative mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            <span class="eye-icon position-absolute" id="toggleCurrentPassword" style="right: 10px; top: 35px; cursor: pointer;">
                                <i class="fa fa-eye-slash"></i>
                            </span>
                        </div>

                        <!-- New Password -->
                        <div class="form-group position-relative mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <span class="eye-icon position-absolute" id="toggleNewPassword" style="right: 10px; top: 35px; cursor: pointer;">
                                <i class="fa fa-eye-slash"></i>
                            </span>
                        </div>

                        <!-- Confirm New Password -->
                        <div class="form-group position-relative mb-4">
                            <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                            <span class="eye-icon position-absolute" id="toggleConfirmPassword" style="right: 10px; top: 35px; cursor: pointer;">
                                <i class="fa fa-eye-slash"></i>
                            </span>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary w-100">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(document).ready(function() {

        // Handle form submission via AJAX
        $('#change-password-form').on('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            var formData = $(this).serialize(); // Serialize form data

            $.ajax({
                url: '{{ route('changePassword') }}', // Your route for handling the password change
                method: 'POST',
                data: formData,
                success: function(response) {
                    // Show success Fancy Alert if response is successful
                    FancyAlerts.show({
                        msg: response.message, // The success message from the response
                        type: 'success',
                    });

                    // Log the user out after success (client-side)
                    setTimeout(function() {
                        window.location.href = "{{ route('loadLogin') }}"; // Redirect to login page
                    }, 3000); // Redirect after 3 seconds to allow reading the success message
                },
                error: function(xhr, status, error) {
                    // Show error Fancy Alert if there is an error
                    var response = xhr.responseJSON;
                    FancyAlerts.show({
                        msg: response.message || 'There was an error processing your request.',
                        type: 'error',
                    });
                }
            });
        });

        // Password visibility toggle logic
        function togglePasswordVisibility(inputId, iconId) {
            const passwordField = document.getElementById(inputId);
            const icon = document.getElementById(iconId).querySelector('i');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }

        // Event listeners for toggling password visibility
        const currentPasswordToggle = document.getElementById('toggleCurrentPassword');
        if (currentPasswordToggle) {
            currentPasswordToggle.addEventListener('click', function() {
                togglePasswordVisibility('current_password', 'toggleCurrentPassword');
            });
        }

        const newPasswordToggle = document.getElementById('toggleNewPassword');
        if (newPasswordToggle) {
            newPasswordToggle.addEventListener('click', function() {
                togglePasswordVisibility('new_password', 'toggleNewPassword');
            });
        }

        const confirmPasswordToggle = document.getElementById('toggleConfirmPassword');
        if (confirmPasswordToggle) {
            confirmPasswordToggle.addEventListener('click', function() {
                togglePasswordVisibility('new_password_confirmation', 'toggleConfirmPassword');
            });
        }
    });
</script>
@endpush
