<?php

namespace App\Services\Requestor;


use App\Services\Application\RequesterType;
use PHPOpenSourceSaver\JWTAuth\JWT;

class JWTRequest
{
    private static $instance;

    private function __construct(
        public readonly RequesterType $requesterType,
        public readonly int           $agentId,
    )
    {
    }

    /**
     * @throws JWTException
     */
    public static function load(): self
    {
        $jwt = app(JWT::class);
        if (is_null($jwt->getToken())) {
            throw new JWTException('Authentication token not found', 401);
        }

        if (!self::$instance) {
            self::$instance = new self(RequesterType::AGENT, $jwt->getClaim('sub'));
        }
        return self::$instance;
    }

    public static function getRequesterType(): RequesterType
    {
        return self::$instance->requesterType;
    }

    public static function getAgentId(): int
    {
        return self::$instance->agentId;
    }
}
