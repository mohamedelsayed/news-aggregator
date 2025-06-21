<?php

namespace App;

enum DataSource: string
{
    case NEWS_API = 'NewsAPI';
    case GUARDIAN = 'Guardian';
    case NEW_YORK_TIMES = 'NewYorkTimes';
}
