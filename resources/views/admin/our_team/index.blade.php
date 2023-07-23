@extends('admin.layouts.app')
@section('content')
    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header row gutters-5">
                    <div class="col text-center text-md-left">
                        <h5 class="mb-md-0 h6">{{ translate('Member List') }}</h5>
                    </div>
                </div>
                <div class="card-body">
                    <table class="datatable table table-bordered aiz-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ translate('Member Image') }}</th>
                                <th data-breakpoints="md">{{ translate('Member Name') }}</th>
                                <th data-breakpoints="md">{{ translate('Designation') }}</th>
                                <th class="text-right" width="20%">{{ translate('Options') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($members as $key => $member)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><img class="" src="{{ static_asset('uploads/team_members/' . $member->member_image) }}" alt="{{ $member->member_image }}" width="100"></td>
                                    <td>{{ $member->member_name }}</td>
                                    <td>{{ $member->designation }}</td>
                                    <td>
                                        <a href="{{ route('admin.edit.member', $member->id) }}" class="btn btn-primary btn-sm"><i class="lar la-edit"></i></a>
                                        <button class="btn btn-danger btn-sm" id="delete-member" onclick="delete_member()" value="{{ $member->id }}"><i class="lar la-trash-alt"></i></button>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">No Members Found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 fs-5">Add New Member</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.member.add') }}" method="POST" id="add-member-form" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Name*</label>
                            <input type="text" class="form-control @error('member_name')is-invalid @enderror" name="member_name" placeholder="Enter Member's Name" value="{{ old('member_name') }}" required>
                            @error('member_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image <span class="text-muted">(300x300)<sup>*</sup></span></label>
                            <input type="file" class="form-control @error('member_image')is-invalid @enderror" name="member_image">
                            @error('member_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Designation*</label>
                            <input type="text" class="form-control @error('designation')is-invalid @enderror" name="designation" placeholder="Enter Member's Designation" value="{{ old('designation') }}" required>
                            @error('designation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sorting Position*</label>
                            <input type="number" class="form-control @error('sorting_position')is-invalid @enderror" name="sorting_position" placeholder="Enter Sorting Position" value="{{ old('sorting_position') }}" required>
                            @error('sorting_position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="error mb-3"></div>
                        <button class="mt-2 btn btn-primary btn-sm">Add Now</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Blog Edit Modal -->
    <div class="modal fade" id="editBlog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content p-3" style="width: 150%">
                <form id="edit-blog-form" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">Edit Blog</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h3>...</h3>
                        <div class="error"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <style>
        .slot-day {
            display: none;
        }

        form .error {
            font-size: .9em;
            color: #dc3545;
            display: none;
        }
    </style>
@endsection

@section('script')
    @if (session('addSuccess'))
        <script>
            Swal.fire(
                'Done',
                "{{ session('addSuccess') }}",
                'success',
            )
        </script>
    @endif

    @if (session('updateSuccess'))
        <script>
            Swal.fire(
                'Done',
                "{{ session('updateSuccess') }}",
                'success',
            )
        </script>
    @endif

    @if (session('dltSuccess'))
        <script>
            Swal.fire(
                'Done',
                "{{ session('dltSuccess') }}",
                'success',
            )
        </script>
    @endif

    <script>
        function delete_member() {
            let id = document.querySelector("#delete-member").value;

            let url = "{{ route('admin.member.delete', ':id') }}";
            url = url.replace(':id', id);
            delete_warning(url);
        };
    </script>
@endsection
