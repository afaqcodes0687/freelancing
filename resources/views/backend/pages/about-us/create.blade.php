@extends('backend.layout.master')
@section('site_title', __('Create About Us Page'))

@section('content')
    <div class="dashboard__content">
        <div class="dashboard__content__header">
            <h4 class="dashboard__content__title">{{ __('Create About Us Page') }}</h4>
            <div class="dashboard__content__header__right">
                <a href="{{ route('admin.about-us.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>
        </div>

        <div class="dashboard__card">
            <div class="dashboard__card__body">
                <form action="{{ route('admin.about-us.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- CEO Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">{{ __('CEO Section') }}</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ceo_name">{{ __('CEO Name') }}</label>
                                <input type="text" class="form-control" id="ceo_name" name="ceo_name" 
                                       value="{{ old('ceo_name') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ceo_title">{{ __('CEO Title') }}</label>
                                <input type="text" class="form-control" id="ceo_title" name="ceo_title" 
                                       value="{{ old('ceo_title') }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="ceo_description">{{ __('CEO Description') }}</label>
                                <textarea class="form-control" id="ceo_description" name="ceo_description" rows="4">{{ old('ceo_description') }}</textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="ceo_image">{{ __('CEO Image') }}</label>
                                <input type="file" class="form-control" id="ceo_image" name="ceo_image" accept="image/*">
                                <small class="text-muted">{{ __('Recommended size: 400x400px') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Main About Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">{{ __('Main About Section') }}</h5>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="main_title">{{ __('Main Title') }}</label>
                                <input type="text" class="form-control" id="main_title" name="main_title" 
                                       value="{{ old('main_title') }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="main_description">{{ __('Main Description') }}</label>
                                <textarea class="form-control" id="main_description" name="main_description" rows="4">{{ old('main_description') }}</textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="opportunity_text">{{ __('Opportunity Text') }}</label>
                                <textarea class="form-control" id="opportunity_text" name="opportunity_text" rows="4">{{ old('opportunity_text') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">{{ __('Statistics') }}</h5>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="clients_count">{{ __('Clients Count') }}</label>
                                <input type="text" class="form-control" id="clients_count" name="clients_count" 
                                       value="{{ old('clients_count') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="freelancers_count">{{ __('Freelancers Count') }}</label>
                                <input type="text" class="form-control" id="freelancers_count" name="freelancers_count" 
                                       value="{{ old('freelancers_count') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="orders_count">{{ __('Orders Count') }}</label>
                                <input type="text" class="form-control" id="orders_count" name="orders_count" 
                                       value="{{ old('orders_count') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="jobs_handled">{{ __('Jobs Handled') }}</label>
                                <input type="text" class="form-control" id="jobs_handled" name="jobs_handled" 
                                       value="{{ old('jobs_handled') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="earned_amount">{{ __('Earned Amount') }}</label>
                                <input type="text" class="form-control" id="earned_amount" name="earned_amount" 
                                       value="{{ old('earned_amount') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="awards_count">{{ __('Awards Count') }}</label>
                                <input type="text" class="form-control" id="awards_count" name="awards_count" 
                                       value="{{ old('awards_count') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Video Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">{{ __('Video Section') }}</h5>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="video_title">{{ __('Video Title') }}</label>
                                <input type="text" class="form-control" id="video_title" name="video_title" 
                                       value="{{ old('video_title') }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="video_description">{{ __('Video Description') }}</label>
                                <textarea class="form-control" id="video_description" name="video_description" rows="3">{{ old('video_description') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="video_url">{{ __('Video URL') }}</label>
                                <input type="url" class="form-control" id="video_url" name="video_url" 
                                       value="{{ old('video_url') }}" placeholder="https://www.youtube.com/embed/...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="video_thumbnail">{{ __('Video Thumbnail') }}</label>
                                <input type="file" class="form-control" id="video_thumbnail" name="video_thumbnail" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <!-- What We Do Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">{{ __('What We Do Section') }}</h5>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="what_we_do_title">{{ __('What We Do Title') }}</label>
                                <input type="text" class="form-control" id="what_we_do_title" name="what_we_do_title" 
                                       value="{{ old('what_we_do_title') }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="what_we_do_description">{{ __('What We Do Description') }}</label>
                                <textarea class="form-control" id="what_we_do_description" name="what_we_do_description" rows="3">{{ old('what_we_do_description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Team Members -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">{{ __('Team Members') }}</h5>
                            <button type="button" class="btn btn-sm btn-primary mb-3" onclick="addTeamMember()">
                                <i class="fas fa-plus"></i> {{ __('Add Team Member') }}
                            </button>
                            <div id="team_members_container">
                                <div class="row team-member-item">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ __('Name') }}</label>
                                            <input type="text" class="form-control" name="team_member_names[]" placeholder="{{ __('Name') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ __('Position') }}</label>
                                            <input type="text" class="form-control" name="team_member_positions[]" placeholder="{{ __('Position') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ __('Image') }}</label>
                                            <input type="file" class="form-control" name="team_member_images[]" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label>&nbsp;</label><br>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeTeamMember(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Certifications -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">{{ __('Certifications') }}</h5>
                            <button type="button" class="btn btn-sm btn-primary mb-3" onclick="addCertification()">
                                <i class="fas fa-plus"></i> {{ __('Add Certification') }}
                            </button>
                            <div id="certifications_container">
                                <div class="row certification-item">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ __('Title') }}</label>
                                            <input type="text" class="form-control" name="certification_titles[]" placeholder="{{ __('Title') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ __('Link') }}</label>
                                            <input type="url" class="form-control" name="certification_links[]" placeholder="{{ __('Link') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ __('Image') }}</label>
                                            <input type="file" class="form-control" name="certification_images[]" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label>&nbsp;</label><br>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeCertification(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Meta Data -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">{{ __('Meta Data') }}</h5>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="meta_title">{{ __('Meta Title') }}</label>
                                <input type="text" class="form-control" id="meta_title" name="meta_title" 
                                       value="{{ old('meta_title') }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="meta_description">{{ __('Meta Description') }}</label>
                                <textarea class="form-control" id="meta_description" name="meta_description" rows="3">{{ old('meta_description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('Create About Us Page') }}
                        </button>
                        <a href="{{ route('admin.about-us.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function addTeamMember() {
            const container = document.getElementById('team_members_container');
            const newItem = document.createElement('div');
            newItem.className = 'row team-member-item mt-3';
            newItem.innerHTML = `
                <div class="col-md-4">
                    <div class="form-group">
                        <label>${'{__("Name")}'}</label>
                        <input type="text" class="form-control" name="team_member_names[]" placeholder="${'{__("Name")}'">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>${'{__("Position")}'}</label>
                        <input type="text" class="form-control" name="team_member_positions[]" placeholder="${'{__("Position")}'">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>${'{__("Image")}'}</label>
                        <input type="file" class="form-control" name="team_member_images[]" accept="image/*">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label>&nbsp;</label><br>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeTeamMember(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newItem);
        }

        function removeTeamMember(button) {
            button.closest('.team-member-item').remove();
        }

        function addCertification() {
            const container = document.getElementById('certifications_container');
            const newItem = document.createElement('div');
            newItem.className = 'row certification-item mt-3';
            newItem.innerHTML = `
                <div class="col-md-3">
                    <div class="form-group">
                        <label>${'{__("Title")}'}</label>
                        <input type="text" class="form-control" name="certification_titles[]" placeholder="${'{__("Title")}'">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>${'{__("Link")}'}</label>
                        <input type="url" class="form-control" name="certification_links[]" placeholder="${'{__("Link")}'">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>${'{__("Image")}'}</label>
                        <input type="file" class="form-control" name="certification_images[]" accept="image/*">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label>&nbsp;</label><br>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeCertification(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newItem);
        }

        function removeCertification(button) {
            button.closest('.certification-item').remove();
        }
    </script>
@endsection
