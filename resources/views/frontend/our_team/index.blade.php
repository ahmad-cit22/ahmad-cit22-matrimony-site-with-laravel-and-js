@extends('frontend.layouts.app')
@section('content')
    <section class="pt-6 pb-4 bg-white text-center">
        <div class="container">
            <h1 class="fw-600 text-dark">{{ translate('Our Team') }}</h1>
        </div>
    </section>
    <section class="pt-5 pb-4 bg-white">
        <div class="container">
            <div class="row">
                @forelse ($members as $key => $member)
                    <div class="col-6 col-lg-3">
                        <div class="card mb-3 shadow-2">
                            <img src="{{ static_asset('uploads/team_members/' . $member->member_image) }}" class="img-fluid" width="300">
                            <div class="p-3">
                                <h4 class="text-dark">{{ $member->member_name }}</h4>
                                <div class="mb-3">
                                    <span class="opacity-80">{{ $member->designation }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div>
                        <p>No Members Found!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection

@section('modal')
    @include('modals.login_modal')
    @include('modals.package_update_alert_modal')
@endsection

@section('script')
    <script type="text/javascript">
        // Login alert
        function loginModal() {
            $('#LoginModal').modal();
        }

        // Package update alert
        function package_update_alert() {
            $('.package_update_alert_modal').modal('show');
        }
    </script>
@endsection
