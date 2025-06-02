@extends('layouts.auth-layout')

@section('content')


<div class="card">
    <h5 class="card-header text-center custom-card-header ">Inspector Tracking Software</h5>
    <div class="card-body">


        @if(\Session::has('error'))
        <div class="alert alert-danger text-center">
            {{ \Session::get('error') }}
        </div>
        @endif

        <form action="{{ route('userLogin') }}" method="post">
            @csrf

            <div class="row m-3 flex-column">
                <label for="username"><i class="fa-regular fa-user"></i> Username</label>
                <div class="">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter your Username" required>
                </div>
            </div>

            <div class="row m-3 flex-column">
                <label for="password"><i class="fa-solid fa-lock"></i> Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your Password" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn" onclick="togglePasswordVisibility()" tabindex="-1">
                        <i class="fa-solid fa-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>
            </div>




            <!-- <div class="row mt-4 mb-3 flex-column align-items-center">
                <div class="col-md-8 text-center d-flex justify-content-center  pl-0">
                    <div class="">
                        <label for="captcha" class="mt-2 mr-3 ">Captcha        &nbsp;</label>
                    </div>
                    <div class="">

                        <img id="captchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image rounded mr-3 border" style="border-color: #ddd;">
                    </div>
                    <div class="">
                        &nbsp;
                        <i class="fa-solid fa-arrows-rotate text-primary position-absolute end-0 mt-2" style="cursor: pointer; font-size: 1.25rem;" onclick="refreshCaptcha()"></i>
                    </div>

                </div>
            </div> -->

            <!-- <div class="col-md-12 d-flex justify-content-center">
                <input type="text" name="captcha" class="form-control code-text" placeholder="Enter the code" required minlength="6" maxlength="6">
            </div> -->
    </div>

    <div class="row justify-content-center">
        <div class="col-md-4 mb-4 d-flex justify-content-center align-items-center">
            <button type="submit" class="btn btn-block ">Log In</button>
        </div>
    </div>

    </form>
</div>


@endsection



@push('scripts')
<script>
    function refreshCaptcha() {
        document.getElementById('captchaImage').src = "{{ route('captcha') }}?" + Date.now();
    }
</script>
@endpush