<?php

namespace FocalStrategy\ReportGenerator;

use MyCLabs\Enum\Enum;

/**
 * @method static ReportType HTML()
 * @method static ReportType PLAIN()
 */
class ReportType extends Enum
{
    const HTML = 'html';
    const PLAIN = 'plain';
}
