<?php

/*
 * This file is part of the PHPProm package.
 *
 * (c) Philip Lehmann-Böhm <philip@philiplb.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPProm\Storage;

/**
 * Class AbstractStorage
 * The parent class of all storage implementations.
 * @package PHPProm\Storage
 */
abstract class AbstractStorage {

    /**
     * @var array
     * Holds the available metrics.
     */
    protected $availableMetrics;

    /**
     * AbstractStorage constructor.
     */
    public function __construct() {
        $this->availableMetrics = [];
    }

    /**
     * Adds a metric to the available ones.
     *
     * @param string $metric
     * the metric itself as delivered by Prometheus
     * @param string $label
     * the name of the one Prometheus label to categorize the values
     * @param string $help
     * a helping text for the metric
     * @param string $type
     * the Prometheus type of the metric
     * @param string $defaultValue
     * the default value which the metric gets if there is no value stored
     */
    public function addAvailableMetric($metric, $label, $help, $type, $defaultValue) {
        $this->availableMetrics[] = [
            'metric' => $metric,
            'label' => $label,
            'help' => $help,
            'type' => $type,
            'defaultValue' => $defaultValue
        ];
    }

    /**
     * Gets all available metrics in an array.
     *
     * @return array
     * the available metrics
     */
    public function getAvailableMetrics() {
        return $this->availableMetrics;
    }

    /**
     * Stores a measurement.
     *
     * @param string $metric
     * the name of the metric
     * @param string $key
     * the key
     * @param float $value
     * the value
     * @return void
     */
    abstract public function storeMeasurement($metric, $key, $value);

    /**
     * Increments a measurement, starting with 1 if it doesn't exist yet.
     * @param string $metric
     * the name of the metric
     * @param string $key
     * the key
     * @return void
     */
    abstract public function incrementMeasurement($metric, $key);

    /**
     * Gets all measurements.
     *
     * @param string $metric
     * the name of the metric
     * @param array $keys
     * the keys to retrieve
     * @param string $defaultValue
     * the default value a key gets if there is no value for it in the storage
     * @return array
     * the map with the keys and values
     */
    abstract public function getMeasurements($metric, array $keys, $defaultValue = 'Nan');

}
