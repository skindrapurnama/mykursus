<?php

namespace App\Filament\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait RestrictsToMentorCourses
{
    public static function canCreate(): bool
    {
        return static::isAdminUser();
    }

    public static function canEdit($record): bool
    {
        return static::isAdminUser();
    }

    public static function canDelete($record): bool
    {
        return static::isAdminUser();
    }

    public static function canDeleteAny(): bool
    {
        return static::isAdminUser();
    }

    protected static function isAdminUser(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->isAdmin();
    }

    protected static function isMentorUser(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->isInstructor();
    }

    protected static function scopeToMentorCourses(Builder $query, string $courseRelation = 'course'): Builder
    {
        if (! static::isMentorUser()) {
            return $query;
        }

        return $query->whereHas("{$courseRelation}.mentors", function (Builder $q): void {
            $q->where('mentors.user_id', auth()->id());
        });
    }
}
