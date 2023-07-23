@extends('frontend.layouts.app')

@section('content')
    <!-- How It Works Section -->
    @if (get_setting('show_how_it_works_section') == 'on' && get_setting('how_it_works_steps_titles') != null)
        <section class="py-8 bg-white">
            <div class="container">
                <div class="row">
                    <div class="col-lg-10 col-xl-8 col-xxl-6 mx-auto">
                        <div class="text-center section-title mb-5">
                            <h2 class="fw-600 mb-3">{{ get_setting('how_it_works_title') }}</h2>
                            <p class="fw-400 fs-16 opacity-60">{{ get_setting('how_it_works_sub_title') }}</p>
                        </div>
                    </div>
                </div>
                <div class="row gutters-10">
                    @php
                        $how_it_works_steps_titles = json_decode(get_setting('how_it_works_steps_titles'));
                        $step = 1;
                    @endphp
                    @foreach ($how_it_works_steps_titles as $key => $how_it_works_steps_title)
                        <div class="col-lg">
                            <div class="border p-3 mb-3">
                                <div class=" row align-items-center">
                                    <div class="col-7">
                                        <div class="text-primary fw-600 h1">{{ $step++ }}</div>
                                        <div class="text-secondary fs-20 mb-2 fw-600">{{ $how_it_works_steps_title }}
                                        </div>
                                        <div class="fs-15 opacity-60">
                                            {{ json_decode(get_setting('how_it_works_steps_sub_titles'), true)[$key] }}
                                        </div>
                                    </div>
                                    <div class="mt-3 col-5 text-right">
                                        <img src="{{ uploaded_asset(json_decode(get_setting('how_it_works_steps_icons'), true)[$key]) }}" class="img-fluid h-80px">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
