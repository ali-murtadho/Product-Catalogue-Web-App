<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StoreSettingController extends Controller
{
    /**
     * Show the form for editing the store settings.
     */
    public function edit(): View
    {
        $setting = StoreSetting::instance();

        if (!$setting) {
            $setting = StoreSetting::create([
                'id' => 1,
                'store_name' => 'Toko Default',
                'wa_numbers' => ['6281234567890'],
                'wa_template' => null,
                'address' => null,
                'social_links' => ['instagram' => '', 'tiktok' => '', 'facebook' => ''],
                'logo_path' => null,
            ]);
        }

        return view('admin.settings.edit', compact('setting'));
    }

    /**
     * Update the store settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'wa_numbers' => ['required', 'array', 'min:1'],
            'wa_numbers.*' => ['required', 'string', 'regex:/^62\d{8,13}$/'],
            'wa_template' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'social_links.instagram' => ['nullable', 'string', 'max:255'],
            'social_links.tiktok' => ['nullable', 'string', 'max:255'],
            'social_links.facebook' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'wa_numbers.*.regex' => 'Nomor WhatsApp harus dimulai dengan 62 dan terdiri dari 10-15 digit angka.',
            'wa_numbers.*.required' => 'Nomor WhatsApp tidak boleh kosong.',
        ]);

        $setting = StoreSetting::instance();

        if (!$setting) {
            $setting = new StoreSetting();
            $setting->id = 1;
        }

        $setting->store_name = $validated['store_name'];
        $setting->wa_numbers = array_values(array_filter($validated['wa_numbers']));
        $setting->wa_template = $validated['wa_template'];
        $setting->address = $validated['address'];
        $setting->social_links = [
            'instagram' => $validated['social_links']['instagram'] ?? '',
            'tiktok' => $validated['social_links']['tiktok'] ?? '',
            'facebook' => $validated['social_links']['facebook'] ?? '',
        ];

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            $setting->logo_path = $this->compressAndStoreImage($request->file('logo'), 'logos');
        }

        $setting->save();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan toko berhasil diperbarui.');
    }

    /**
     * Compress and store an uploaded image using GD library.
     * Resizes to max 1200px width and compresses quality to 80%.
     *
     * @param UploadedFile $file
     * @param string $directory Subdirectory within public disk
     * @return string The stored file path relative to public disk
     */
    private function compressAndStoreImage(UploadedFile $file, string $directory): string
    {
        $mime = $file->getMimeType();
        $filename = uniqid($directory . '_') . '.' . $file->getClientOriginalExtension();
        $path = $directory . '/' . $filename;
        $absolutePath = Storage::disk('public')->path($path);

        // Ensure directory exists
        $dir = dirname($absolutePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Create image resource based on mime type
        $sourceImage = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($file->getPathname()),
            'image/png' => @imagecreatefrompng($file->getPathname()),
            'image/webp' => @imagecreatefromwebp($file->getPathname()),
            default => @imagecreatefromjpeg($file->getPathname()),
        };

        if (!$sourceImage) {
            // Fallback: store without compression
            $file->storeAs($directory, $filename, 'public');
            return $path;
        }

        $originalWidth = imagesx($sourceImage);
        $originalHeight = imagesy($sourceImage);
        $maxWidth = 1200;

        // Resize if wider than max width
        if ($originalWidth > $maxWidth) {
            $ratio = $maxWidth / $originalWidth;
            $newWidth = $maxWidth;
            $newHeight = (int) round($originalHeight * $ratio);

            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG and WebP
            if ($mime === 'image/png' || $mime === 'image/webp') {
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);
                $transparent = imagecolorallocatealpha($resizedImage, 0, 0, 0, 127);
                imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $transparent);
            }

            imagecopyresampled(
                $resizedImage,
                $sourceImage,
                0, 0, 0, 0,
                $newWidth, $newHeight,
                $originalWidth, $originalHeight
            );

            imagedestroy($sourceImage);
            $sourceImage = $resizedImage;
        }

        // Save compressed image
        match ($mime) {
            'image/jpeg' => imagejpeg($sourceImage, $absolutePath, 80),
            'image/png' => imagepng($sourceImage, $absolutePath, 8),
            'image/webp' => imagewebp($sourceImage, $absolutePath, 80),
            default => imagejpeg($sourceImage, $absolutePath, 80),
        };

        imagedestroy($sourceImage);

        return $path;
    }
}
