<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AppInstallation;
use App\Models\AppVersion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AppUpdateAdminController extends Controller
{
    public function index(): View
    {
        $versions = AppVersion::query()
            ->orderByDesc('release_date')
            ->orderByDesc('id')
            ->paginate(15, ['*'], 'versions_page');

        $recentInstallations = AppInstallation::query()
            ->with('user:id,first_name,last_name,email')
            ->latestFirst()
            ->limit(10)
            ->get();

        $stats = [
            'total_versions' => AppVersion::count(),
            'android_live_version' => optional(AppVersion::latestForPlatform(AppVersion::PLATFORM_ANDROID)->first())->version,
            'ios_live_version' => optional(AppVersion::latestForPlatform(AppVersion::PLATFORM_IOS)->first())->version,
            'installations_7_days' => AppInstallation::recent(7)->count(),
            'installations_30_days' => AppInstallation::recent(30)->count(),
        ];

        return view('backend.pages.app-update.index', [
            'versions' => $versions,
            'recentInstallations' => $recentInstallations,
            'stats' => $stats,
            'platforms' => AppVersion::platforms(),
        ]);
    }

    public function installations(Request $request): View
    {
        $query = AppInstallation::query()
            ->with('user:id,first_name,last_name,email')
            ->latestFirst();

        if ($request->filled('platform')) {
            $query->byPlatform($request->string('platform'));
        }

        if ($request->filled('version')) {
            $query->byVersion($request->string('version'));
        }

        if ($request->filled('device_id')) {
            $query->where('device_id', 'like', '%' . $request->string('device_id') . '%');
        }

        $installations = $query->paginate(25)->withQueryString();

        $versionOptions = AppInstallation::query()
            ->select('version')
            ->distinct()
            ->orderByDesc('version')
            ->pluck('version');

        return view('backend.pages.app-update.installations', [
            'installations' => $installations,
            'platforms' => AppVersion::platforms(),
            'versionOptions' => $versionOptions,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($data) {
            $version = AppVersion::create($data);
            $this->syncActiveVersion($version, (bool) $data['is_active']);
        });

        return back()->with(toastr_success(__('App version created successfully.')));
    }

    public function edit(int $id): View
    {
        return view('backend.pages.app-update.edit', [
            'version' => AppVersion::findOrFail($id),
            'platforms' => AppVersion::platforms(),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $version = AppVersion::findOrFail($id);
        $data = $this->validatedData($request, $version->id);

        DB::transaction(function () use ($version, $data) {
            $version->update($data);
            $this->syncActiveVersion($version->fresh(), (bool) $data['is_active']);
        });

        return redirect()->route('admin.app.update.index')->with(toastr_success(__('App version updated successfully.')));
    }

    public function activate(int $id): RedirectResponse
    {
        $version = AppVersion::findOrFail($id);

        DB::transaction(function () use ($version) {
            AppVersion::query()
                ->where('platform', $version->platform)
                ->update(['is_active' => false]);

            $version->update(['is_active' => true]);
        });

        return back()->with(toastr_success(__('Selected version is now live for :platform.', ['platform' => ucfirst($version->platform)])));
    }

    public function destroy(int $id): RedirectResponse
    {
        $version = AppVersion::findOrFail($id);

        if ($version->is_active) {
            return back()->with(toastr_error(__('Please activate another version before deleting the live version.')));
        }

        $version->delete();

        return back()->with(toastr_success(__('App version deleted successfully.')));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $request->validate([
            'app_update_enabled' => 'required|in:0,1',
            'app_update_disabled_message' => 'nullable|string|max:500',
            'app_update_default_message' => 'nullable|string|max:500',
            'app_update_support_text' => 'nullable|string|max:500',
            'app_update_android_fallback_url' => 'nullable|url|max:500',
            'app_update_ios_fallback_url' => 'nullable|url|max:500',
            'app_update_android_message' => 'nullable|string|max:500',
            'app_update_ios_message' => 'nullable|string|max:500',
        ]);

        foreach ($request->only([
            'app_update_enabled',
            'app_update_disabled_message',
            'app_update_default_message',
            'app_update_support_text',
            'app_update_android_fallback_url',
            'app_update_ios_fallback_url',
            'app_update_android_message',
            'app_update_ios_message',
        ]) as $key => $value) {
            update_static_option($key, $value);
        }

        return back()->with(toastr_success(__('App update settings saved successfully.')));
    }

    private function validatedData(Request $request, ?int $ignoreId = null): array
    {
        $request->validate([
            'version_name' => 'required|string|max:191',
            'platform' => 'required|in:android,ios',
            'release_notes' => 'nullable|string',
            'download_url' => 'nullable|url|max:500',
            'file_size' => 'nullable|integer|min:0',
            'min_supported_version' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
            'force_update' => 'nullable|boolean',
            'checksum' => 'nullable|string|max:255',
            'signature' => 'nullable|string|max:255',
            'release_date' => 'required|date',
            'version' => [
                'required',
                'string',
                'max:20',
                Rule::unique('app_versions', 'version')
                    ->ignore($ignoreId)
                    ->where(fn ($query) => $query->where('platform', $request->platform)),
            ],
        ]);

        return [
            'version' => trim((string) $request->version),
            'version_name' => trim((string) $request->version_name),
            'platform' => (string) $request->platform,
            'release_notes' => $this->normalizeReleaseNotes($request->release_notes),
            'download_url' => $request->download_url ?: '',
            'file_size' => (int) ($request->file_size ?: 0),
            'min_supported_version' => $request->min_supported_version ?: null,
            'is_active' => $request->boolean('is_active'),
            'force_update' => $request->boolean('force_update'),
            'checksum' => $request->checksum ?: null,
            'signature' => $request->signature ?: null,
            'release_date' => $request->release_date,
        ];
    }

    private function normalizeReleaseNotes(?string $releaseNotes): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $releaseNotes))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    private function syncActiveVersion(AppVersion $version, bool $shouldActivate): void
    {
        if (!$shouldActivate) {
            return;
        }

        AppVersion::query()
            ->where('platform', $version->platform)
            ->where('id', '!=', $version->id)
            ->update(['is_active' => false]);

        $version->update(['is_active' => true]);
    }
}
