<?php

namespace App\Enums;

enum BusinessRegistrationStatus: string
{
  case APPROVED = 'approved';
  case REJECTED = 'rejected';
  case PENDING = 'pending';

  public function label(): string
  {
    return match ($this) {
      self::APPROVED => 'Approved',
      self::REJECTED => 'Rejected',
      self::PENDING => 'Pending',
    };
  }

  public function color(): string
  {
    return match ($this) {
      self::APPROVED => 'green',
      self::REJECTED => 'red',
      self::PENDING => 'yellow',
    };
  }

  public static function fromString(string $status): self
  {
    return match ($status) {
      'approved' => self::APPROVED,
      'rejected' => self::REJECTED,
      default => self::PENDING,
    };
  }
}
