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
 * Class Memcached
 * Storage implementation using memcached.
 * @package PHPProm\Storage
 */
class Memcached extends AbstractStorage {

    /**
     * @var \Memcached
     * The memcached connection.
     */
    protected $memcached;

    /**
     * @var string
     * The global key prefix.
     */
    protected $prefix;

    /**
     * Memcached constructor.
     *
     * @param $host
     * the connection host
     * @param int $port
     * the connection port, default 11211
     * @param string $prefix
     * the global key prefix to use, default 'PHPProm:'
     */
    public function __construct($host, $port = 11211, $prefix = 'PHPProm:') {
        parent::__construct();
        $this->memcached = new \Memcached();
        $this->memcached->addServer($host, $port);
        $this->prefix = $prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function storeMeasurement($metric, $key, $value) {
        $this->memcached->set($this->prefix.$metric.':'.$key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function incrementMeasurement($metric, $key) {
        // Increment doesn't work on older versions, see
        // https://github.com/php-memcached-dev/php-memcached/issues/133
        $value = $this->memcached->get($this->prefix.$metric.':'.$key);
        if ($value === false) {
            $value = 0;
        }
        $value++;
        $this->storeMeasurement($metric, $key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMeasurements($metric, array $keys, $defaultValue = 'Nan') {
        $measurements = [];
        foreach ($keys as $key) {
            $measurements[$key] = $defaultValue;
        }
        $prefixedKeys = array_map(function($key) use ($metric) {
            return $this->prefix.$metric.':'.$key;
        }, $keys);
        foreach ($this->memcached->getMulti($prefixedKeys) as $key => $value) {
            $unprefixedKey                = substr($key, strlen($this->prefix) + strlen($metric) + 1);
            $measurements[$unprefixedKey] = $value !== false ? (float)$value : $defaultValue;
        }
        return $measurements;
    }
}
