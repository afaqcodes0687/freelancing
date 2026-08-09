<x-validation.error />
<table class="DataTable_activation">
    <thead>
    <tr>
        <th>{{__('ID')}}</th>
        <th>{{__('Project Title')}}</th>
        <th>{{__('Image')}}</th>
        <th>{{__('Status (change by admin)')}}</th>
        <th>{{__('Action')}}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($all_projects as $project)
        <tr>
            <td>{{ $project->id }}</td>
            <td>
                {{ $project->title }} <br>
                @if($project->project_approve_request === 0) <small class="badge bg-danger">{{ __('Request for activate') }}</small> @endif
            </td>
                       <td>
                @php
                    $raw = $project->image;

                    // Normalize to array: if already array, keep; if valid JSON string, decode; else wrap as array
                    $files = is_array($raw)
                        ? $raw
                        : (json_decode($raw) !== null && json_last_error() === JSON_ERROR_NONE
                            ? json_decode($raw, true)
                            : [$raw]);

                    $isCloud = cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']);
                @endphp


                    @if(!empty($files))
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($files as $file)
                                @php
                                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                    $fileUrl = $isCloud
                                        ? render_frontend_cloud_image_if_module_exists('project/' . $file, load_from: $project->load_from)
                                        : asset('assets/uploads/project/' . $file);
                                @endphp

                                @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg']))
                                    <img src="{{ $fileUrl }}" width="80" height="80" alt="Image" style="object-fit:cover; border-radius:5px;">
                                @elseif(in_array($ext, ['mp4', 'mov', 'webm', 'mkv']))
                                    <video width="80" height="80" controls style="border-radius:5px;">
                                        <source src="{{ $fileUrl }}" type="video/{{ $ext }}">
                                        Your browser does not support the video tag.
                                    </video>
                                @elseif($ext === 'pdf')
                                    <a href="{{ $fileUrl }}" target="_blank" title="PDF">
                                        <img src="{{ asset('assets/icons/pdf-icon.png') }}" width="50" alt="PDF">
                                    </a>
                                @elseif(in_array($ext, ['doc', 'docx']))
                                    <a href="{{ $fileUrl }}" target="_blank" title="Word Doc">
                                        <img src="{{ asset('assets/icons/word-icon.png') }}" width="50" alt="Word">
                                    </a>
                                @elseif(in_array($ext, ['xls', 'xlsx', 'csv']))
                                    <a href="{{ $fileUrl }}" target="_blank" title="Excel">
                                        <img src="{{ asset('assets/icons/excel-icon.png') }}" width="50" alt="Excel">
                                    </a>
                                @else
                                    <a href="{{ $fileUrl }}" target="_blank">
                                        <i class="fa fa-file"></i> {{ $file }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <span class="text-danger">No uploaded files</span>
                    @endif
                </td>
            <td>
                <x-status.table.active-inactive :status="$project->status"/>
            </td>
            <td>
                <x-status.table.select-action :title="__('Select Action')"/>
                <ul class="dropdown-menu status_dropdown__list">
                    @can('project-details')
                    <li class="status_dropdown__item">
                        <a href="{{ route('admin.project.details',$project->id) }}" class="btn dropdown-item status_dropdown__list__link">{{ __('Project Details') }}</a>
                    </li>
                    @endcan
                    @can('project-edit')
                        <li class="status_dropdown__item">
                            <a href="{{ route('admin.project.edit', $project->id) }}" class="btn dropdown-item status_dropdown__list__link">
                                {{ __('Edit Project') }}
                            </a>
                        </li>
                        @endcan

                    @can('project-delete')
                    <li class="status_dropdown__item">
                        <x-popup.delete-popup :title="__('Delete Project')" :url="route('admin.project.delete',$project->id)"/>
                    </li>
                    @endcan
                    @can('project-status-change')
                    <li class="status_dropdown__item">
                        @if($project->project_approve_request === 0 || $project->project_approve_request === 2)
                            <x-status.table.status-change :title="__('Activate Project')" :url="route('admin.project.status.change',$project->id)"/>
                        @else
                            <x-status.table.status-change :title="__('Inactivate Project')" :url="route('admin.project.status.change',$project->id)"/>
                        @endif
                    </li>
                    @endcan
                </ul>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<x-pagination.laravel-paginate :allData="$all_projects"/>
