<?php

namespace App\Support;

final class Roles
{
    public const STUDENT = 'student';
    public const MENTOR = 'mentor';
    public const ADMIN = 'admin';

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::STUDENT,
            self::MENTOR,
            self::ADMIN,
        ];
    }
}
