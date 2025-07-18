<?php

/*
 * This file is part of Chrome PHP.
 *
 * (c) Soufiane Ghzal <sghzal@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HeadlessChromium\PageUtils;

use HeadlessChromium\Exception\ScreenshotFailed;

class PageScreenshot extends AbstractBinaryInput
{
    /**
     * {@inheritdoc}
     *
     * @internal
     */
    protected function getException(string $message): ScreenshotFailed
    {
        return new ScreenshotFailed(
            \sprintf('Cannot make a screenshot. Reason : %s', $message)
        );
    }
}
