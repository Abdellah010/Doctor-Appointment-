<?php

namespace App\Enums;

enum DoctorStatus: string
{
    case Pending = 'pending';
    case Verified = 'verified';
    case Rejected = 'rejected';
}
