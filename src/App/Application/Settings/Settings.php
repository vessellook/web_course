<?php
declare(strict_types=1);

namespace App\Application\Settings;

class Settings implements SettingsInterface
{
    /**
     * @var array
     */
    private $settings;

    /**
     * Settings constructor.
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key = '')
    {
        if (!isset($this->settings[$key])) {
            if ($key === 'create-database-sql') {
                $this->settings[$key] = file_get_contents($this->settings['recreate-database-path']);
            }
        }
        return (empty($key)) ? $this->settings : $this->settings[$key];
    }
}
