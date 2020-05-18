<?php

namespace Devorto\Logger;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use UnexpectedValueException;

/**
 * Class MicrosoftTeams
 *
 * @package Devorto\Logger
 */
class MicrosoftTeams implements LoggerInterface
{
    /**
     * @var string
     */
    protected $webHookUrl;

    /**
     * @var string
     */
    protected $applicationTitle;

    /**
     * @var string
     */
    protected $applicationUrl;

    /**
     * Slack constructor.
     *
     * @param string $webHookUrl
     * @param string $applicationTitle
     * @param string $applicationUrl
     */
    public function __construct(string $webHookUrl, string $applicationTitle, string $applicationUrl = '')
    {
        if (!filter_var($webHookUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Provided $webHookUrl is not a valid URL.');
        }
        $this->webHookUrl = $webHookUrl;

        if (empty($applicationTitle)) {
            throw new InvalidArgumentException('Provided $applicationTitle cannot be empty.');
        }
        $this->applicationTitle = $applicationTitle;

        if (!empty($applicationUrl) && !filter_var($applicationUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Provided $applicationUrl is not a valid URL.');
        }
        $this->applicationUrl = $applicationUrl;
    }

    /**
     * Formats and sends the data to Microsoft Teams.
     *
     * @param string $themeColor
     * @param string $level
     * @param string $message
     */
    protected function send(string $themeColor, string $level, string $message): void
    {
        $text = sprintf('%s: %s', ucfirst($level), $this->applicationTitle);
        if (!empty($this->applicationUrl)) {
            $text .= sprintf(' (%s)', $this->applicationUrl);
        }

        $data = [
            'themeColor' => $themeColor,
            'title' => $text,
            'text' => nl2br($message)
        ];

        $ch = curl_init($this->webHookUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $result = curl_exec($ch);
        curl_close($ch);

        if ($result !== '1') {
            throw new UnexpectedValueException(
                sprintf('Response of Microsoft Teams webhook returned an unexpected value "%s".', $result)
            );
        }
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function emergency($message, array $context = [])
    {
        if (empty($context['theme_color'])) {
            $context['theme_color'] = 'FF0000';
        }

        $this->send($context['theme_color'], LogLevel::EMERGENCY, $message);
    }

    /**
     * Action must be taken immediately.
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function alert($message, array $context = [])
    {
        if (empty($context['theme_color'])) {
            $context['theme_color'] = 'FF0000';
        }

        $this->send($context['theme_color'], LogLevel::ALERT, $message);
    }

    /**
     * Critical conditions.
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function critical($message, array $context = [])
    {
        if (empty($context['theme_color'])) {
            $context['theme_color'] = 'FF0000';
        }

        $this->send($context['theme_color'], LogLevel::CRITICAL, $message);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function error($message, array $context = [])
    {
        if (empty($context['theme_color'])) {
            $context['theme_color'] = 'FF0000';
        }

        $this->send($context['theme_color'], LogLevel::ERROR, $message);
    }

    /**
     * Exceptional occurrences that are not errors.
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function warning($message, array $context = [])
    {
        if (empty($context['theme_color'])) {
            $context['theme_color'] = 'FFFF00';
        }

        $this->send($context['theme_color'], LogLevel::WARNING, $message);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function notice($message, array $context = [])
    {
        if (empty($context['theme_color'])) {
            $context['theme_color'] = '000080';
        }

        $this->send($context['theme_color'], LogLevel::NOTICE, $message);
    }

    /**
     * Interesting events.
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function info($message, array $context = [])
    {
        if (empty($context['theme_color'])) {
            $context['theme_color'] = '000080';
        }

        $this->send($context['theme_color'], LogLevel::INFO, $message);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function debug($message, array $context = [])
    {
        if (empty($context['theme_color'])) {
            $context['theme_color'] = '000080';
        }

        $this->send($context['theme_color'], LogLevel::DEBUG, $message);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        switch ($level) {
            case LogLevel::EMERGENCY:
                $this->emergency($message, $context);
                break;
            case LogLevel::ALERT:
                $this->alert($message, $context);
                break;
            case LogLevel::CRITICAL:
                $this->critical($message, $context);
                break;
            case LogLevel::ERROR:
                $this->error($message, $context);
                break;
            case LogLevel::WARNING:
                $this->warning($message, $context);
                break;
            case LogLevel::NOTICE:
                $this->notice($message, $context);
                break;
            case LogLevel::INFO:
                $this->info($message, $context);
                break;
            case LogLevel::DEBUG:
                $this->debug($message, $context);
                break;
            default:
                throw new InvalidArgumentException('Provided $level is not supported.');
        }
    }
}
