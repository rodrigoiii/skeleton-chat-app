<?php

namespace App\Debugbars;

use Tracy\IBarPanel;

class TestDebugbar implements IBarPanel
{
    public function getTab()
    {
        return '
            <span>
                <span class="tracy-label">Test</span>
            </span>
        ';
    }

    public function getPanel()
    {
        return '
            <div class="tracy-inner">
                <div class="tracy-inner-container">
                    Hello World
                </div>
            </div>
        ';
    }
}
