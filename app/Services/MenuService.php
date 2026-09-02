<?php

namespace App\Services;

use App\Models\MenuItem;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class MenuService
{
    public function save(array $data, ?UploadedFile $photo, ?MenuItem $item = null): void
    {
        unset($data['photo']);
        $newPath = $photo ? 'storage/'.$photo->store('menu', 'public') : null;
        $oldPath = null;
        try {
            DB::transaction(function () use ($item, $data, $newPath, &$oldPath) {
                $locked = $item ? MenuItem::whereKey($item->id)->lockForUpdate()->firstOrFail() : new MenuItem;
                $oldPath = $locked->image_path;
                $locked->fill($data);
                if ($newPath) {
                    $locked->image_path = $newPath;
                }
                $locked->save();
            }, 5);
        } catch (Throwable $exception) {
            $this->deleteUpload($newPath);
            if ($exception instanceof QueryException && $exception->getCode() === '23000') {
                throw ValidationException::withMessages(['menu_category_id' => 'Kategori tidak tersedia lagi. Pilih kategori yang ada.']);
            }
            throw $exception;
        }
        if ($newPath && $oldPath !== $newPath) {
            $this->deleteUpload($oldPath);
        }
    }

    public function delete(MenuItem $item): void
    {
        $oldPath = DB::transaction(function () use ($item) {
            $locked = MenuItem::whereKey($item->id)->lockForUpdate()->firstOrFail();
            $path = $locked->image_path;
            $locked->delete();

            return $path;
        }, 5);
        $this->deleteUpload($oldPath);
    }

    private function deleteUpload(?string $path): void
    {
        if ($path && str_starts_with($path, 'storage/menu/')) {
            Storage::disk('public')->delete(substr($path, 8));
        }
    }
}
