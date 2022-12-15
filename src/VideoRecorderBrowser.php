<?php
declare(strict_types = 1);

namespace VCR\VCRBundle;

use Symfony\Bundle\FrameworkBundle\KernelBrowser as BaseBrowser;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\BrowserKit\History;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use VCR\Videorecorder;

/**
 * Special simulating client for browser requests with vcr video recorder.
 */
class VideoRecorderBrowser extends BaseBrowser
{
    /**
     * @var bool
     */
    protected $vcrEnabled = false;

    /**
     * @var null|string
     */
    protected $vcrCassetteBasePath;

    /**
     * @var null|string
     */
    protected $vcrCassetteName;

    /**
     * @var callable|null
     */
    protected $vcrConfigureCallback;

    /**
     * VideoRecorderClient constructor.
     *
     * @param KernelInterface $kernel
     * @param array $server
     * @param null|History $history
     * @param null|CookieJar $cookieJar
     */
    public function __construct(
        KernelInterface $kernel,
        array $server = [],
        History $history = null,
        CookieJar $cookieJar = null
    ) {
        parent::__construct($kernel, $server, $history, $cookieJar);
        $kernel->boot();
        if ($this->getContainer()->getParameter('vcr.enabled')) {
            $this->vcrEnabled = true;
        }
    }

    /**
     * @return null|string
     */
    public function getVideoRecorderCassetteBasePath(): ?string
    {
        return $this->vcrCassetteBasePath;
    }

    /**
     * @param null|string $vcrCassetteBasePath
     *
     * @return void
     */
    public function setVideoRecorderCassetteBasePath(?string $vcrCassetteBasePath)
    {
        if (! empty($vcrCassetteBasePath)) {
            $vcrCassetteBasePath = trim($vcrCassetteBasePath, "\t\n\r\0\x0B" . DIRECTORY_SEPARATOR);
        } else {
            $vcrCassetteBasePath = null;
        }

        $this->vcrCassetteBasePath = $vcrCassetteBasePath;
    }

    /**
     * Enable the video recorder for this client (& all its performed requests).
     *
     * @param null|string $cassetteName The cassette name to insert
     * @param bool $insertDefaultCassette Insert the default cassette if non passed
     *
     * @return void
     */
    public function enableVideoRecorder(?string $cassetteName = null, bool $insertDefaultCassette = true): void
    {
        if ($this->isVideoRecorderEnabled()) {
            return;
        }

        $this->getVideoRecorder()->turnOn();
        $this->vcrEnabled = true;
        if (! empty($cassetteName)) {
            $this->insertVideoRecorderCassette($cassetteName);
        } elseif ($insertDefaultCassette) {
            $this->insertDefaultVideoRecorderCassette();
        }
    }

    /**
     * Disable the video recorder for this client (&all its performed requests).
     *
     * @return void
     */
    public function disableVideoRecorder(): void
    {
        $this->ejectVideoRecorderCassette(false);
        $this->getVideoRecorder()->turnOff();
        $this->vcrEnabled = false;
    }

    /**
     * Is video recorder enabled?
     *
     * @return bool
     */
    public function isVideoRecorderEnabled(): bool
    {
        return $this->vcrEnabled;
    }

    /**
     * Get the current video recorder.
     *
     * @return Videorecorder
     */
    public function getVideoRecorder(): Videorecorder
    {
        $container = $this->getContainer();
        if (null === $container) {
            throw new \LogicException('Kernel seems not to be booted, due container is not available.');
        }

        return $container->get('vcr.recorder');
    }

    /**
     * @param string $cassetteName
     * @param bool $force True if force
     *
     * @return void
     */
    public function insertVideoRecorderCassette(string $cassetteName, bool $force = false)
    {
        if (! $this->isVideoRecorderEnabled()) {
            throw new \LogicException('VideoRecorder seems not be enabled. Enable it first, then insert a cassette.');
        }

        if ($this->vcrCassetteName !== $cassetteName || $force) {
            $resolvedCassetteName = [$this->vcrCassetteBasePath, $cassetteName];
            $resolvedCassetteName = \array_filter($resolvedCassetteName);
            $resolvedCassetteName = \implode(DIRECTORY_SEPARATOR, $resolvedCassetteName);
            $this->getVideoRecorder()->insertCassette($resolvedCassetteName);
            $this->vcrCassetteName = $cassetteName;
        }
    }

    /**
     * Ejects the current inserted video recorder cassette.
     *
     * @param bool $reInsertDefaultCassette
     *
     * @return void
     */
    public function ejectVideoRecorderCassette(bool $reInsertDefaultCassette = true)
    {
        $this->getVideoRecorder()->eject();
        $this->vcrCassetteName = null;
        if ($reInsertDefaultCassette) {
            $this->insertDefaultVideoRecorderCassette();
        }
    }

    /**
     * The default video recorder cassette name.
     *
     * @return string The default video recorder cassette name
     */
    protected function getDefaultVideoRecorderCassetteName(): string
    {
        $container = $this->getContainer();
        if (null === $container) {
            throw new \LogicException('Kernel seems not to be bootet, due container is not available.');
        }

        return $container->getParameter('vcr.cassette.name');
    }

    /**
     * Insert the default video recorder cassette.
     *
     * @return void
     */
    protected function insertDefaultVideoRecorderCassette(): void
    {
        $cassetteName = $this->getDefaultVideoRecorderCassetteName();
        $this->getVideoRecorder()->insertCassette($cassetteName);
    }

    /**
     * Set a callable is called after container has been set up but before the request is performed to configure the
     * video recorder used for this request.
     *
     * @param callable|null $callback
     *
     * @return void
     */
    public function setVideoRecorderConfigureCallback(callable $callback = null): void
    {
        $this->vcrConfigureCallback = $callback;
    }

    /**
     * Remove existing video recorder configure callback.
     *
     * @return void
     */
    public function removeVideoRecorderConfigureCallback(): void
    {
        $this->vcrConfigureCallback = null;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    protected function doRequest($request): Response
    {
        // this is a little bit hacky because the we need the parent properties, but they are private :-(
        $propertyAccessor = function ($property) {
            $funcArgs = \func_get_args();
            if (2 === count($funcArgs)) {
                $this->$property = \func_get_arg(1);
            } else {
                return $this->$property;
            }
        };
        $propertyAccessor = $propertyAccessor->bindTo($this, BaseBrowser::class);

        $shouldReboot = $propertyAccessor('reboot');
        $hasPerformedRequest = $propertyAccessor('hasPerformedRequest');
        if ($this->isVideoRecorderEnabled() && $hasPerformedRequest && $shouldReboot) {
            $this->disableReboot();

            $kernel = $this->getKernel();
            $kernel->shutdown();
            $kernel->boot();

            $this->enableVideoRecorder();
            $this->insertVideoRecorderCassette($this->vcrCassetteName, true);
        }

        if ($this->vcrConfigureCallback instanceof \Closure) {
            $vcrCallable = \Closure::bind($this->vcrConfigureCallback, $this);
            \call_user_func($vcrCallable, $this->getVideoRecorder(), $this->getContainer());
        }

        $response = parent::doRequest($request);

        if ($this->isVideoRecorderEnabled() && $hasPerformedRequest && $shouldReboot) {
            $this->enableReboot();
        }

        return $response;
    }
}
