<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Sebastian Krupinski <krupinski01@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Dido\Http;

use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Http\ICallbackResponse;
use OCP\AppFramework\Http\IOutput;

class GeneratedResponse extends Response implements ICallbackResponse
{
    protected $generator;

    public function __construct($generator, $contentType, $statusCode = Http::STATUS_OK)
    {
        parent::__construct();

        $this->generator = $generator;
        
        $this->setStatus($statusCode);
        $this->cacheFor(0);
        $this->addHeader('Content-Type', $contentType);

    }

	public function callback(IOutput $output) {

        foreach ($this->generator as $chunk) {
            print($chunk);
            flush();
        }

	}

}
