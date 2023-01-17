<?php

declare(strict_types=1);

namespace Oneduo\RecaptchaEnterprise\Services;

use Carbon\CarbonInterval;
use Google\Cloud\RecaptchaEnterprise\V1\Assessment;
use Google\Cloud\RecaptchaEnterprise\V1\Event;
use Google\Cloud\RecaptchaEnterprise\V1\RecaptchaEnterpriseServiceClient as RecaptchaClient;
use Google\Cloud\RecaptchaEnterprise\V1\TokenProperties;
use Illuminate\Support\Carbon;
use Oneduo\RecaptchaEnterprise\Contracts\RecaptchaEnterpriseContract;
use Oneduo\RecaptchaEnterprise\Exceptions\InvalidTokenException;
use Oneduo\RecaptchaEnterprise\Exceptions\MissingPropertiesException;

class RecaptchaEnterpriseService implements RecaptchaEnterpriseContract
{
    public readonly RecaptchaClient $client;

    public readonly Assessment $response;

    public ?float $score;

    public ?TokenProperties $properties;

    public function __construct()
    {
        $this->client = app(RecaptchaClient::class, [
            'options' => [
                'credentials' => static::credentials(),
            ],
        ]);
    }

    private function projectName(): string
    {
        return RecaptchaClient::projectName(data_get(static::credentials(), 'project_id'));
    }

    private function siteKey(): string
    {
        return config('recaptcha-enterprise.site_key');
    }

    public static function credentials(): array
    {
        $useCredentials = config('recaptcha-enterprise.use_credentials');

        return data_get(config('recaptcha-enterprise.credentials'), $useCredentials, []);
    }

    /**
     * @throws MissingPropertiesException
     * @throws InvalidTokenException
     * @throws \Google\ApiCore\ApiException
     */
    public function handle(string $token): static
    {
        $event = $this->event($token);

        $assessment = $this->assessment($event);

        $this->response = $this->client->createAssessment($this->projectName(), $assessment);

        // The SDK documentation recommends closing the connection after each request.
        $this->close();

        $this->properties = $this->response->getTokenProperties();

        if ($this->properties === null) {
            throw MissingPropertiesException::make($assessment);
        }

        // throw an error if the token is invalid
        if (! $this->properties->getValid()) {
            throw InvalidTokenException::make($this->properties->getInvalidReason());
        }

        $this->score = $this->response->getRiskAnalysis()?->getScore();

        return $this;
    }

    protected function event(string $token): Event
    {
        return (new Event())
            ->setSiteKey($this->siteKey())
            ->setToken($token);
    }

    protected function assessment(Event $event): Assessment
    {
        return (new Assessment())
            ->setEvent($event);
    }

    public function hasValidScore(): bool
    {
        $threshold = config('recaptcha-enterprise.score_threshold');

        // if no threshold is set, we assume it passes
        if ($threshold === null) {
            return true;
        }

        // if no score is set, we assume it fails
        if ($this->score === null) {
            return false;
        }

        // check if the score is higher than the threshold
        return $this->score >= $threshold;
    }

    public function hasValidAction(string $action): bool
    {
        return $this->properties->getAction() === $action;
    }

    public function hasValidTimestamp(CarbonInterval $interval): bool
    {
        $timestamp = $this->properties->getCreateTime()?->getSeconds();

        return Carbon::parse($timestamp)->lessThanOrEqualTo(now()->sub($interval));
    }

    public function isValid(?string $action = null, ?CarbonInterval $interval = null): bool
    {
        $valid = $this->hasValidScore();

        if ($action) {
            $valid = $valid && $this->hasValidAction($action);
        }

        if ($interval) {
            $valid = $valid && $this->hasValidTimestamp($interval);
        }

        return $valid;
    }

    public function close(): void
    {
        $this->client->close();
    }
}
