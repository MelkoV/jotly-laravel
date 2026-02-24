<?php

declare(strict_types=1);

namespace App\Enums;

enum ListAccess: int
{
    /** Приватный список. По умолчанию. */
    case Private = 1;
    /** Участники, имеющие доступ к списку, могут редактировать. Не распространяется на владельца. */
    case CanEdit = 2;
    /** Владелец списка может приглашать новых участников (по email). */
    case Invite = 4;
    /** Доступ к списку по прямой ссылке. */
    case Link = 8;
}
