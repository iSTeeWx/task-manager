<?php

namespace App\Entity;

enum Status: string {
    case TODO = "todo";
    case IN_PROGRESS = "in_progress";
    case DONE = "done";
}