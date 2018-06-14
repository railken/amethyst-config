<?php

namespace Railken\LaraOre\Config;

use Railken\Laravel\Manager\Contracts\AgentContract;
use Railken\Laravel\Manager\ModelManager;
use Railken\Laravel\Manager\Tokens;

class ConfigManager extends ModelManager
{
    /**
     * Class name entity.
     *
     * @var string
     */
    public $entity = Config::class;

    /**
     * List of all attributes.
     *
     * @var array
     */
    protected $attributes = [
        Attributes\Id\IdAttribute::class,
        Attributes\Key\KeyAttribute::class,
        Attributes\Value\ValueAttribute::class,
        Attributes\CreatedAt\CreatedAtAttribute::class,
        Attributes\UpdatedAt\UpdatedAtAttribute::class,
    ];

    /**
     * List of all exceptions.
     *
     * @var array
     */
    protected $exceptions = [
        Tokens::NOT_AUTHORIZED => Exceptions\ConfigNotAuthorizedException::class,
    ];

    /**
     * Construct.
     *
     * @param AgentContract $agent
     */
    public function __construct(AgentContract $agent = null)
    {
        $this->setRepository(new ConfigRepository($this));
        $this->setSerializer(new ConfigSerializer($this));
        $this->setValidator(new ConfigValidator($this));
        $this->setAuthorizer(new ConfigAuthorizer($this));

        parent::__construct($agent);
    }

    /**
     * Load configs.
     *
     * @return void
     */
    public function loadConfig()
    {
        $configs = $this->getRepository()->findToLoad();

        $configs = $configs->mapWithKeys(function ($config, $key) {
            return [$config->resolveKey($config->key) => $config->value];
        })->toArray();

        config($configs);
    }
}
