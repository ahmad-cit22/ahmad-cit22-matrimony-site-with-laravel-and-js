@extends('frontend.layouts.member_panel')
@section('panel_content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Add New Image to Gallery') }} <span class="fs-6 text-primary">(Remaining Gallery Image Upload: {{ $remaining_image }})</span>
            </h5>
        </div>
        <div class="card-body">
            <form id="image-upload-form" action="{{ route('gallery-image.store') }}" method="POST">
                @csrf
                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="signinSrEmail">{{ translate('Image') }}</label>
                    <div class="col-md-9">
                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium text-white">{{ translate('Browse') }}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="gallery_image" class="selected-files" required>
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-9">
                        <p>(Accepted Image Formats: <span class="text-primary">JPG, JPEG, PNG</span>)</p>
                        <p>(**To upload files, click on 'browse/choose file', then select an image from previously uploaded image )</p>
                    </div>
                </div>
                <div class="form-group row text-right">
                    <div class="col-md-11">
                        <button type="button" class="btn btn-primary" onclick="image_upload_warning()">{{ translate('Confirm') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('modal')
    @include('modals.confirm_modal')
@endsection




@section('script')
    <script>
        // Express Interest
        function image_upload_warning() {
            var remaining_image = {{ $remaining_image }};
            $('.confirm_modal').modal('show');
            $("#confirm_modal_title").html("{{ translate('Confirm Image Upload!') }}");
            $("#confirm_modal_content").html("<p class='fs-14'>{{ translate('Remaining Image Upload') }}: " +
                remaining_image +
                "</p><p class='text-danger'>{{ translate('**N.B. Uploading An Image Will Cost 1 From Your Remaining Gallery Image Uploads**') }}</p>"
            );
        }

        $("#confirm_button").click(function() {
            $('#image-upload-form').submit();
        });
    </script>
@endsection
