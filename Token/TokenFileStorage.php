<?php

namespace FL\GmailBundle\Token;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * Class TokenFileStorage
 *
 * Deals with local persistence of the files associated to
 * the auth code and the authentication token. It will both get
 * these items and save them to the file system.
 *
 * @package FL\GmailBundle\Token
 */
class TokenFileStorage implements TokenStorageInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Path to the access token JSON file.
     * @var string
     */
    private $accessTokenFilePath;

    /**
     * Path to the code file path.
     * @var string
     */
    private $authCodeFilePath;

    /**
     * TokenFileStorage constructor.
     * @param Filesystem $filesystem
     * @param string $accessTokenFilePath
     * @param string $authCodeFilePath
     */
    public function __construct(
        Filesystem $filesystem,
        string $accessTokenFilePath,
        string $authCodeFilePath
    ) {
        $this->filesystem = $filesystem;
        $this->accessTokenFilePath = $accessTokenFilePath;
        $this->authCodeFilePath = $authCodeFilePath;
    }

    /**
     * Store the access token.
     * @param array $accessToken
     */
    public function persistAccessToken(array $accessToken)
    {
        $this->filesystem->dumpFile($this->accessTokenFilePath, json_encode($accessToken));
    }

    /**
     * Returns a json string access token if it exists, null otherwise.
     * @return string|null
     */
    public function getAccessToken()
    {
        if ($this->filesystem->exists($this->accessTokenFilePath)) {
            return json_decode($this->getContent($this->accessTokenFilePath));
        }
    }

    /**
     * Store the auth code.
     * @param string $authCode
     */
    public function persistAuthCode(string $authCode)
    {
        $this->filesystem->dumpFile($this->authCodeFilePath, $authCode);
    }

    /**
     * Returns an auth code if it exists, null otherwise.
     * @return string|null
     */
    public function getAuthCode()
    {
        if ($this->filesystem->exists($this->authCodeFilePath)) {
            return $this->getContent($this->authCodeFilePath);
        }
    }

    /**
     * Delete the auth code.
     * @param void
     * @return void
     */
    public function deleteAuthCode()
    {
        if ($this->filesystem->exists($this->authCodeFilePath)) {
            $this->filesystem->remove($this->authCodeFilePath);
        }
    }

    /**
     * Validate path and return file contents.
     * @param string $path
     * @return string
     */
    public function getContent(string $path)
    {
        if (!$this->filesystem->exists($path)) {
            throw new FileNotFoundException();
        }

        if (is_dir($path)) {
            throw new FileException("Path indicates a directory. File expected.");
        }

        return file_get_contents($path);
    }
}
