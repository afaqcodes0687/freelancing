@extends('backend.layout.master')
@section('site_title', __('Edit About Us Page'))

@section('content')
    <div class="dashboard__content">
        <div class="dashboard__content__header ms-5 mt-3">
            <h4 class="dashboard__content__title">{{ __('Edit About Us Page') }}</h4>
            <div class="dashboard__content__header__right">
                <a href="{{ route('admin.about-us.index') }}" class="btn btn-secondary mt-2">
                    <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>
        </div>

        <div class="container-fluid px-4">
            <div class="dashboard__card">
                <div class="dashboard__card__body p-4">
                    <form action="{{ route('admin.about-us.update', $aboutUs->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                    <!-- CEO Section -->
                    <div class="section-card mb-4">
                        <div class="section-header">
                            <h5 class="section-title">
                                <i class="fas fa-user-tie mr-2"></i>{{ __('CEO Section') }}
                            </h5>
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ceo_name">{{ __('CEO Name') }}</label>
                                        <input type="text" class="form-control" id="ceo_name" name="ceo_name" 
                                               value="{{ old('ceo_name', $aboutUs->ceo_name) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ceo_title">{{ __('CEO Title') }}</label>
                                        <input type="text" class="form-control" id="ceo_title" name="ceo_title" 
                                               value="{{ old('ceo_title', $aboutUs->ceo_title) }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="ceo_description">{{ __('CEO Description') }}</label>
                                        <textarea class="form-control" id="ceo_description" name="ceo_description" rows="4">{{ old('ceo_description', $aboutUs->ceo_description) }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ceo_image">{{ __('CEO Image') }}</label>
                                        <input type="file" class="form-control" id="ceo_image" name="ceo_image" accept="image/*">
                                        @if($aboutUs->ceo_image)
                                            <br>
                                            <img src="{{ asset('assets/frontend/img/' . $aboutUs->ceo_image) }}" 
                                                 class="img-thumbnail" style="max-width: 150px;">
                                            <br>
                                            <small class="text-muted">{{ __('Current image') }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="section-card mb-4">
                        <div class="section-header">
                            <h5 class="section-title">
                                <i class="fas fa-info-circle mr-2"></i>{{ __('Main Content') }}
                            </h5>
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="main_title">{{ __('Main Title') }}</label>
                                        <input type="text" class="form-control" id="main_title" name="main_title" 
                                               value="{{ old('main_title', $aboutUs->main_title) }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="main_description">{{ __('Main Description') }}</label>
                                        <textarea class="form-control" id="main_description" name="main_description" rows="4">{{ old('main_description', $aboutUs->main_description) }}</textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="opportunity_text">{{ __('Opportunity Text') }}</label>
                                        <textarea class="form-control" id="opportunity_text" name="opportunity_text" rows="4">{{ old('opportunity_text', $aboutUs->opportunity_text) }}</textarea>
                                    </div>
                                </div>
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
                                       value="{{ old('clients_count', $aboutUs->clients_count) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="freelancers_count">{{ __('Freelancers Count') }}</label>
                                <input type="text" class="form-control" id="freelancers_count" name="freelancers_count" 
                                       value="{{ old('freelancers_count', $aboutUs->freelancers_count) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="orders_count">{{ __('Orders Count') }}</label>
                                <input type="text" class="form-control" id="orders_count" name="orders_count" 
                                       value="{{ old('orders_count', $aboutUs->orders_count) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="jobs_handled">{{ __('Jobs Handled') }}</label>
                                <input type="text" class="form-control" id="jobs_handled" name="jobs_handled" 
                                       value="{{ old('jobs_handled', $aboutUs->jobs_handled) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="earned_amount">{{ __('Earned Amount') }}</label>
                                <input type="text" class="form-control" id="earned_amount" name="earned_amount" 
                                       value="{{ old('earned_amount', $aboutUs->earned_amount) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="awards_count">{{ __('Awards Count') }}</label>
                                <input type="text" class="form-control" id="awards_count" name="awards_count" 
                                       value="{{ old('awards_count', $aboutUs->awards_count) }}">
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
                                       value="{{ old('video_title', $aboutUs->video_title) }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="video_description">{{ __('Video Description') }}</label>
                                <textarea class="form-control" id="video_description" name="video_description" rows="3">{{ old('video_description', $aboutUs->video_description) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="video_url">{{ __('Video URL') }}</label>
                                <input type="url" class="form-control" id="video_url" name="video_url" 
                                       value="{{ old('video_url', $aboutUs->video_url) }}" placeholder="https://www.youtube.com/embed/...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="video_thumbnail">{{ __('Video Thumbnail') }}</label>
                                <input type="file" class="form-control" id="video_thumbnail" name="video_thumbnail" accept="image/*">
                                @if($aboutUs->video_thumbnail)
                                    <br>
                                    <img src="{{ asset('assets/frontend/img/' . $aboutUs->video_thumbnail) }}" 
                                         class="img-thumbnail" style="max-width: 150px;">
                                    <br>
                                    <small class="text-muted">{{ __('Current thumbnail') }}</small>
                                @endif
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
                                       value="{{ old('what_we_do_title', $aboutUs->what_we_do_title) }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="what_we_do_description">{{ __('What We Do Description') }}</label>
                                <textarea class="form-control" id="what_we_do_description" name="what_we_do_description" rows="3">{{ old('what_we_do_description', $aboutUs->what_we_do_description) }}</textarea>
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
                                @if($aboutUs->team_members)
                                    @foreach(json_decode($aboutUs->team_members, true) as $key => $member)
                                        <div class="row team-member-item @if($key > 0) mt-3 @endif">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>{{ __('Name') }}</label>
                                                    <input type="text" class="form-control" name="team_member_names[]" 
                                                           value="{{ $member['name'] }}" placeholder="{{ __('Name') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>{{ __('Position') }}</label>
                                                    <input type="text" class="form-control" name="team_member_positions[]" 
                                                           value="{{ $member['position'] }}" placeholder="{{ __('Position') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>{{ __('Image') }}</label>
                                                    <input type="file" class="form-control" name="team_member_images[]" accept="image/*">
                                                    @if($member['image'])
                                                        <br>
                                                        <img src="{{ asset('assets/frontend/img/' . $member['image']) }}" 
                                                             class="img-thumbnail" style="max-width: 80px;">
                                                        <br>
                                                        <small class="text-muted">{{ __('Current image') }}</small>
                                                    @endif
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
                                    @endforeach
                                @else
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
                                @endif
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
                                @if($aboutUs->certifications)
                                    @foreach(json_decode($aboutUs->certifications, true) as $key => $cert)
                                        <div class="row certification-item @if($key > 0) mt-3 @endif">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>{{ __('Title') }}</label>
                                                    <input type="text" class="form-control" name="certification_titles[]" 
                                                           value="{{ $cert['title'] }}" placeholder="{{ __('Title') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>{{ __('Link') }}</label>
                                                    <input type="url" class="form-control" name="certification_links[]" 
                                                           value="{{ $cert['link'] }}" placeholder="{{ __('Link') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>{{ __('Image') }}</label>
                                                    <input type="file" class="form-control" name="certification_images[]" accept="image/*">
                                                    @if($cert['image'])
                                                        <br>
                                                        <img src="{{ asset('assets/frontend/img/' . $cert['image']) }}" 
                                                             class="img-thumbnail" style="max-width: 80px;">
                                                        <br>
                                                        <small class="text-muted">{{ __('Current image') }}</small>
                                                    @endif
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
                                    @endforeach
                                @else
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
                                @endif
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
                                       value="{{ old('meta_title', $aboutUs->meta_title) }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="meta_description">{{ __('Meta Description') }}</label>
                                <textarea class="form-control" id="meta_description" name="meta_description" rows="3">{{ old('meta_description', $aboutUs->meta_description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('Update About Us Page') }}
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
            `;
            container.appendChild(newItem);
        }

        function removeTeamMember(button) {
            if (confirm('{{ __("Are you sure you want to remove this team member?") }}')) {
                const row = button.closest('.team-member-item');
                // Clear all input values to ensure they're not submitted
                row.querySelectorAll('input').forEach(input => {
                    if (input.type === 'text') {
                        input.value = '';
                    } else if (input.type === 'file') {
                        input.value = '';
                    }
                });
                // Hide the row
                row.style.display = 'none';
                // Add a hidden input to mark this row for deletion
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'deleted_team_members[]';
                deleteInput.value = 'deleted';
                row.appendChild(deleteInput);
            }
        }

        function deleteTeamMemberImage(button, memberName) {
            if (confirm('{{ __("Are you sure you want to delete this team member\'s image?") }}')) {
                // Add hidden input to mark image for deletion
                const row = button.closest('.team-member-item');
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'delete_team_member_images[]';
                deleteInput.value = memberName;
                row.appendChild(deleteInput);
                
                // Remove image preview
                const img = row.querySelector('img');
                if (img) {
                    img.style.display = 'none';
                }
                
                // Remove the delete button
                button.style.display = 'none';
            }
        }

        function addCertification() {
            const container = document.getElementById('certifications_container');
            const newItem = document.createElement('div');
            newItem.className = 'row certification-item mt-3';
            newItem.innerHTML = `
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
            `;
            container.appendChild(newItem);
        }

        function removeCertification(button) {
            if (confirm('{{ __("Are you sure you want to remove this certification?") }}')) {
                const row = button.closest('.certification-item');
                // Clear all input values to ensure they're not submitted
                row.querySelectorAll('input').forEach(input => {
                    if (input.type === 'text' || input.type === 'url') {
                        input.value = '';
                    } else if (input.type === 'file') {
                        input.value = '';
                    }
                });
                // Hide the row
                row.style.display = 'none';
                // Add a hidden input to mark this row for deletion
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'deleted_certifications[]';
                deleteInput.value = 'deleted';
                row.appendChild(deleteInput);
            }
        }

        function deleteCertificationImage(button, certTitle) {
            if (confirm('{{ __("Are you sure you want to delete this certification image?") }}')) {
                // Add hidden input to mark image for deletion
                const row = button.closest('.certification-item');
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'delete_certification_images[]';
                deleteInput.value = certTitle;
                row.appendChild(deleteInput);
                
                // Remove image preview
                const img = row.querySelector('img');
                if (img) {
                    img.style.display = 'none';
                }
                
                // Remove the delete button
                button.style.display = 'none';
            }
        }
    </script>

    <style>
        .container-fluid {
            max-width: 100%;
            padding: 0 20px;
        }
        
        .section-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .section-header {
            background: linear-gradient(135deg, #309400 0%, #309400 100%);
            color: white;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .section-title {
            margin: 0;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .section-body {
            padding: 2rem;
        }
        
        .dashboard__card {
            border: none;
            box-shadow: none;
            background: transparent;
        }
        
        .dashboard__card__body {
            padding: 0;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #309400;
            box-shadow: 0 0 0 0.2rem rgba(48, 148, 0, 0.25);
        }
        
        .btn-primary {
            background: #309400;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #257300;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(48, 148, 0, 0.3);
        }
        
        .btn-success {
            background: #309400;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover {
            background: #257300;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(48, 148, 0, 0.3);
        }
        
        .btn-outline-primary {
            color: #309400;
            border: 2px solid #309400;
            background: transparent;
        }
        
        .btn-outline-primary:hover {
            background: #309400;
            border-color: #309400;
            color: white;
        }
        
        .img-thumbnail {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            margin-top: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .container-fluid {
                padding: 0 10px;
            }
            
            .section-body {
                padding: 1rem;
            }
            
            .section-header {
                padding: 0.75rem 1rem;
            }
        }
    </style>
@endsection
