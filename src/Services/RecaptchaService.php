<?php

declare(strict_types=1);

namespace Oneduo\RecaptchaEnterprise\Services;

use Carbon\CarbonInterval;
use Google\Cloud\RecaptchaEnterprise\V1\Assessment;
use Google\Cloud\RecaptchaEnterprise\V1\Event;
use Google\Cloud\RecaptchaEnterprise\V1\Client\RecaptchaEnterpriseServiceClient as RecaptchaClient;
use Google\Cloud\RecaptchaEnterprise\V1\TokenProperties;
use Illuminate\Support\Carbon;
use Oneduo\RecaptchaEnterprise\Contracts\RecaptchaContract;
use Oneduo\RecaptchaEnterprise\Exceptions\InvalidTokenException;
use Oneduo\RecaptchaEnterprise\Exceptions\MissingPropertiesException;

/**
 * @codeCoverageIgnore Handled by a mock
 */
class RecaptchaService implements RecaptchaContract
{
    public RecaptchaClient $client;

    public Assessment $assessment;

    public ?TokenProperties $properties;

    public ?float $score;

    public function __construct()
    {
        $this->client = app(RecaptchaClient::class, [
            'options' => [
                'credentials' => static::credentials(),
            ],
        ]);
    }

    protected static function credentials(): array
    {
        $useCredentials = config('recaptcha-enterprise.use_credentials');

        return data_get(config('recaptcha-enterprise.credentials'), $useCredentials, []);
    }

    protected function projectName(): string
    {
        return RecaptchaClient::projectName(data_get(static::credentials(), 'project_id'));
    }

    protected function siteKey(): string
    {
        return config('recaptcha-enterprise.site_key');
    }

    /**
     * Assess the token against Google reCAPTCHA Enterprise
     *
     * @throws MissingPropertiesException
     * @throws InvalidTokenException
     * @throws \Google\ApiCore\ApiException
     */
    public function assess(string $token): static
    {
        $this->initAssessmentForEvent($this->event($token));

        // we run the assessment through the reCAPTCHA Enterprise API client
        $this->assessment = $this->client->createAssessment($this->projectName(), $this->assessment);

        // The SDK documentation recommends closing the connection after each request.
        $this->close();

        $this->properties = $this->assessment->getTokenProperties();

        // throw an error if no properties are returned
        if ($this->properties === null) {
            throw MissingPropertiesException::forAssessment($this->assessment);
        }

        // throw an error if the token is invalid
        if (! $this->properties->getValid()) {
            throw InvalidTokenException::forReason($this->properties->getInvalidReason());
        }

        // set the score
        $this->score = $this->assessment->getRiskAnalysis()?->getScore();

        return $this;
    }

    protected function event(string $token): Event
    {
        return app(Event::class)
            ->setSiteKey($this->siteKey())
            ->setToken($token);
    }

    protected function initAssessmentForEvent(Event $event): void
    {
        $this->assessment = app(Assessment::class)->setEvent($event);
    }

    public function validateScore(): bool
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

    public function validateAction(string $action): bool
    {
        return $this->properties->getAction() === $action;
    }

    public function validateCreationTime(CarbonInterval $interval): bool
    {
        $timestamp = $this->properties->getCreateTime()?->getSeconds();

        return Carbon::parse($timestamp)->lessThanOrEqualTo(now()->sub($interval));
    }

    public function isValid(?string $action = null, ?CarbonInterval $interval = null): bool
    {
        $valid = $this->validateScore();

        if ($action) {
            $valid = $valid && $this->validateAction($action);
        }

        if ($interval) {
            $valid = $valid && $this->validateCreationTime($interval);
        }

        return $valid;
    }

    protected function close(): void
    {
        $this->client->close();
    }
}
