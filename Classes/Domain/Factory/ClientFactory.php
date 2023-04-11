<?php
declare(strict_types=1);

namespace Flowpack\OpenSearch\Domain\Factory;

/*
 * This file is part of the Flowpack.OpenSearch package.
 *
 * (c) Contributors of the Flowpack Team - flowpack.org
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Flowpack\OpenSearch\Domain\Model\Client;
use Flowpack\OpenSearch\Domain\Model\Client\ClientConfiguration;
use Flowpack\OpenSearch\Exception as OpenSearchException;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Error\Exception as FlowErrorException;

/**
 * Client factory
 * @Flow\Scope("singleton")
 */
class ClientFactory
{
    /**
     * @var array
     */
    protected $settings;

    /**
     * @param array $settings
     */
    public function injectSettings(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param string $bundle
     * @param string $clientClassName
     * @return Client
     * @throws OpenSearchException
     */
    public function create($bundle = null, $clientClassName = Client::class)
    {
        if ($bundle === null) {
            $bundle = 'default';
        }

        if (!isset($this->settings['clients'][$bundle]) || !is_array($this->settings['clients'][$bundle])) {
            throw new OpenSearchException('The inquired client settings bundle "' . $bundle . '" is not present in setting "Flowpack.OpenSearch.clients".', 1338890487);
        }
        $clientsSettings = $this->settings['clients'][$bundle];

        $clientConfigurations = $this->buildClientConfigurations($clientsSettings);

        $client = new $clientClassName();
        $client->setClientConfigurations($clientConfigurations);
        $client->setBundle($bundle);

        return $client;
    }

    /**
     * @param array $clientsSettings
     * @return array
     * @throws OpenSearchException
     */
    protected function buildClientConfigurations(array $clientsSettings)
    {
        $clientConfigurations = [];
        $clientConfiguration = new ClientConfiguration();
        foreach ($clientsSettings as $clientSettings) {
            $configuration = clone $clientConfiguration;
            foreach ($clientSettings as $settingKey => $settingValue) {
                $setterMethodName = 'set' . ucfirst($settingKey);
                try {
                    $configuration->$setterMethodName($settingValue);
                } catch (FlowErrorException $exception) {
                    $exceptionMessage = 'Setting key "' . $settingKey . '" as client configuration value is not allowed. Refer to the Settings.yaml.example for the supported keys.';
                    throw new OpenSearchException($exceptionMessage, 1338886877, $exception);
                }
            }
            $clientConfigurations[] = $configuration;
        }

        return $clientConfigurations;
    }
}
