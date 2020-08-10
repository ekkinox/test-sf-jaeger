<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace App\Action;

use Jaeger\Config;
use OpenTracing\GlobalTracer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class JaegerAction
{
    /** @var Environment */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke(Request $request): Response
    {
        $config = new Config(
            [
                'local_agent' => [
                    'reporting_host' => 'demo_lti1p3_jaeger',
                    'reporting_port' => 5775,
                ],
                'sampler' => [
                    'type' => \Jaeger\SAMPLER_TYPE_CONST,
                    'param' => true,
                ],
                'logging' => true,
            ],
            'your-app-name'
        );
        $config->initializeTracer();

        $tracer = GlobalTracer::get();

        $scope = $tracer->startActiveSpan('TestSpan', []);

        $scope->close();

        $tracer->flush();

        return new Response($this->twig->render('base.html.twig'));
    }
}
