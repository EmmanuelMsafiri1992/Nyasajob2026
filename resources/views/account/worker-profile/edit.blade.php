@extends('layouts.master')

@section('content')
    @includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])
    <div class="main-container">
        <div class="container">
            <div class="row">

                @if (session()->has('flash_notification'))
                    <div class="col-xl-12">
                        <div class="row">
                            <div class="col-xl-12">
                                @include('flash::message')
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-md-3 page-sidebar">
                    @includeFirst([config('larapen.core.customizedViewPath') . 'account.inc.sidebar', 'account.inc.sidebar'])
                </div>

                <div class="col-md-9 page-content">
                    <div class="inner-box">
                        <h2 class="title-2">
                            <i class="fa-solid fa-user-pen"></i> {{ t('Edit Worker Profile') }}
                        </h2>

                        <form method="POST" action="{{ route('account.worker-profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            @include('account.worker-profile._form', ['profile' => $profile, 'selectedSkillIds' => $selectedSkillIds ?? []])

                            <div class="row mt-4">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa-solid fa-save"></i> {{ t('Update Profile') }}
                                    </button>
                                    <a href="{{ route('account.worker-profile') }}" class="btn btn-secondary">
                                        <i class="fa-solid fa-times"></i> {{ t('Cancel') }}
                                    </a>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
