@php
    $profile ??= null;
    $skills ??= collect();
    $cities ??= collect();
    $selectedSkillIds ??= [];
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    {{-- Profile Photo --}}
    <div class="col-md-12 mb-3">
        <label class="form-label">{{ t('Profile Photo') }}</label>
        @if($profile && $profile->photo_url)
            <div class="mb-2">
                <img src="{{ $profile->photo_url }}" alt="Current photo" class="rounded" style="max-height: 150px;">
            </div>
        @endif
        <input type="file" name="photo" class="form-control" accept="image/*">
        <small class="text-muted">{{ t('Upload a professional photo. Max 2MB.') }}</small>
    </div>

    {{-- Title --}}
    <div class="col-md-12 mb-3">
        <label for="title" class="form-label required">{{ t('Profile Title') }} <span class="text-danger">*</span></label>
        <input type="text"
               name="title"
               id="title"
               class="form-control @error('title') is-invalid @enderror"
               value="{{ old('title', $profile->title ?? '') }}"
               placeholder="{{ t('e.g., Experienced House Maid, Professional Gardener') }}"
               required>
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Bio --}}
    <div class="col-md-12 mb-3">
        <label for="bio" class="form-label">{{ t('About Me') }}</label>
        <textarea name="bio"
                  id="bio"
                  class="form-control @error('bio') is-invalid @enderror"
                  rows="4"
                  placeholder="{{ t('Describe your experience, what services you offer, and why employers should hire you...') }}"
        >{{ old('bio', $profile->bio ?? '') }}</textarea>
        @error('bio')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Skills Selection --}}
    <div class="col-md-12 mb-3">
        <label class="form-label">{{ t('Skills & Services') }}</label>
        <div class="row">
            @foreach($skills as $skill)
                <div class="col-md-4 col-6 mb-2">
                    <div class="form-check">
                        <input type="checkbox"
                               name="skills[]"
                               value="{{ $skill->id }}"
                               id="skill_{{ $skill->id }}"
                               class="form-check-input"
                               {{ in_array($skill->id, old('skills', $selectedSkillIds)) ? 'checked' : '' }}>
                        <label class="form-check-label" for="skill_{{ $skill->id }}">
                            <i class="{{ $skill->icon ?? 'fa-solid fa-check' }}"></i>
                            {{ $skill->name }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Custom Skills --}}
    <div class="col-md-12 mb-3">
        <label for="custom_skills" class="form-label">{{ t('Other Skills') }}</label>
        <input type="text"
               name="custom_skills"
               id="custom_skills"
               class="form-control @error('custom_skills') is-invalid @enderror"
               value="{{ old('custom_skills', $profile->custom_skills ?? '') }}"
               placeholder="{{ t('Enter additional skills separated by commas') }}">
        <small class="text-muted">{{ t('e.g., Baking, Pet Care, Event Decoration') }}</small>
        @error('custom_skills')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Location --}}
    <div class="col-md-6 mb-3">
        <label for="city_id" class="form-label">{{ t('City') }}</label>
        <select name="city_id" id="city_id" class="form-control @error('city_id') is-invalid @enderror">
            <option value="">{{ t('Select City') }}</option>
            @foreach($cities as $city)
                <option value="{{ $city->id }}"
                    {{ old('city_id', $profile->city_id ?? '') == $city->id ? 'selected' : '' }}>
                    {{ $city->name }}
                </option>
            @endforeach
        </select>
        @error('city_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="district" class="form-label">{{ t('Area/District') }}</label>
        <input type="text"
               name="district"
               id="district"
               class="form-control @error('district') is-invalid @enderror"
               value="{{ old('district', $profile->district ?? '') }}"
               placeholder="{{ t('e.g., Area 47, Chilomoni') }}">
        @error('district')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Experience & Rate --}}
    <div class="col-md-4 mb-3">
        <label for="experience_years" class="form-label">{{ t('Years of Experience') }}</label>
        <input type="number"
               name="experience_years"
               id="experience_years"
               class="form-control @error('experience_years') is-invalid @enderror"
               value="{{ old('experience_years', $profile->experience_years ?? '') }}"
               min="0"
               max="50">
        @error('experience_years')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="hourly_rate" class="form-label">{{ t('Expected Hourly Rate') }}</label>
        <div class="input-group">
            <span class="input-group-text">MWK</span>
            <input type="number"
                   name="hourly_rate"
                   id="hourly_rate"
                   class="form-control @error('hourly_rate') is-invalid @enderror"
                   value="{{ old('hourly_rate', $profile->hourly_rate ?? '') }}"
                   min="0"
                   step="0.01"
                   placeholder="{{ t('Optional') }}">
        </div>
        @error('hourly_rate')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="gender" class="form-label">{{ t('Gender') }}</label>
        <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror">
            <option value="">{{ t('Prefer not to say') }}</option>
            <option value="male" {{ old('gender', $profile->gender ?? '') == 'male' ? 'selected' : '' }}>{{ t('Male') }}</option>
            <option value="female" {{ old('gender', $profile->gender ?? '') == 'female' ? 'selected' : '' }}>{{ t('Female') }}</option>
            <option value="other" {{ old('gender', $profile->gender ?? '') == 'other' ? 'selected' : '' }}>{{ t('Other') }}</option>
        </select>
        @error('gender')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Availability Status --}}
    <div class="col-md-6 mb-3">
        <label for="availability_status" class="form-label required">{{ t('Availability') }} <span class="text-danger">*</span></label>
        <select name="availability_status" id="availability_status" class="form-control @error('availability_status') is-invalid @enderror" required>
            <option value="available" {{ old('availability_status', $profile->availability_status ?? 'available') == 'available' ? 'selected' : '' }}>
                {{ t('Available') }} - {{ t('Ready to work') }}
            </option>
            <option value="busy" {{ old('availability_status', $profile->availability_status ?? '') == 'busy' ? 'selected' : '' }}>
                {{ t('Busy') }} - {{ t('Currently employed but open to offers') }}
            </option>
            <option value="not_available" {{ old('availability_status', $profile->availability_status ?? '') == 'not_available' ? 'selected' : '' }}>
                {{ t('Not Available') }} - {{ t('Not looking for work right now') }}
            </option>
        </select>
        @error('availability_status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Visibility Toggle --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">{{ t('Profile Visibility') }}</label>
        <div class="form-check form-switch mt-2">
            <input type="checkbox"
                   name="is_public"
                   id="is_public"
                   class="form-check-input"
                   value="1"
                   {{ old('is_public', $profile->is_public ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_public">
                {{ t('Make my profile visible to employers') }}
            </label>
        </div>
        <small class="text-muted">
            {{ t('By enabling this, verified employers can see your profile and contact you about job opportunities.') }}
        </small>
    </div>
</div>

<hr>

<h5 class="mb-3"><i class="fa-solid fa-address-book"></i> {{ t('Contact Information') }}</h5>
<p class="text-muted mb-3">
    <i class="fa-solid fa-shield-halved"></i>
    {{ t('Your contact details are hidden from the public. Only verified employers with active subscriptions can see them.') }}
</p>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="phone" class="form-label">{{ t('Phone Number') }}</label>
        <input type="tel"
               name="phone"
               id="phone"
               class="form-control @error('phone') is-invalid @enderror"
               value="{{ old('phone', $profile->phone ?? '') }}"
               placeholder="+265 xxx xxx xxx">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="email" class="form-label">{{ t('Email Address') }}</label>
        <input type="email"
               name="email"
               id="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $profile->email ?? '') }}"
               placeholder="your@email.com">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="whatsapp" class="form-label">{{ t('WhatsApp Number') }}</label>
        <input type="tel"
               name="whatsapp"
               id="whatsapp"
               class="form-control @error('whatsapp') is-invalid @enderror"
               value="{{ old('whatsapp', $profile->whatsapp ?? '') }}"
               placeholder="+265 xxx xxx xxx">
        @error('whatsapp')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
