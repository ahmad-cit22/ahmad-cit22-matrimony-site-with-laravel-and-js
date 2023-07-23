@extends('admin.layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Edit Member') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.member.update') }}" method="POST" id="add-member-form" enctype="multipart/form-data">
                        @csrf
                        <input name="id" type="hidden" value="{{ $member->id }}">
                        <div class="mb-3">
                            <label class="form-label">Name*</label>
                            <input type="text" class="form-control @error('member_name')is-invalid @enderror" name="member_name" placeholder="Enter Member's Name" value="{{ $member->member_name }}" required>
                            @error('member_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="img">
                                <img class="mt-1" id="image-preview" src="{{ static_asset('uploads/team_members/' . $member->member_image) }}" alt="{{ $member->member_image }}" width="100">
                            </div>
                            <label class="form-label">Image <span class="text-muted">(300x300)<sup>*</sup></span></label>
                            <input type="file" class="form-control @error('member_image')is-invalid @enderror" name="member_image" onchange="document.getElementById('image-preview').src = window.URL.createObjectURL(this.files[0])" accept=".jpg, .png, jpeg, .gif, .webp">
                            @error('member_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Designation*</label>
                            <input type="text" class="form-control @error('designation')is-invalid @enderror" name="designation" placeholder="Enter Member's Designation" value="{{ $member->designation }}" required>
                            @error('designation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                            <div class="mb-3">
                            <label class="form-label">Sorting Position*</label>
                            <input type="number" class="form-control @error('sorting_position')is-invalid @enderror" name="sorting_position" placeholder="Enter Sorting Position" value="{{ $member->sorting_position }}" required>
                            @error('sorting_position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="error mb-3"></div>
                        <button class="mt-2 btn btn-primary btn-sm">Update Now</button>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @section('script')
        
    @endsection
