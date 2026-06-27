<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function currentAdmin()
    {
        return auth()->user();
    }

    protected function currentPropertyId(): ?int
    {
        $user = $this->currentAdmin();
        return $user && ! $user->isSuperAdmin() ? $user->property_id : null;
    }

    protected function ensureVisibleToAdmin($model): void
    {
        $admin = $this->currentAdmin();
        if (! $admin || $admin->isSuperAdmin()) {
            return;
        }

        $propertyId = $model->property_id
            ?? optional($model->unit ?? null)->property_id
            ?? optional($model->user ?? null)->property_id;

        abort_unless((int) $propertyId === (int) $admin->property_id, 403, 'Data ini bukan milik kos Anda.');
    }
}
