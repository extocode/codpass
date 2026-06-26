<?php

declare(strict_types=1);
/**
 * sysPass
 *
 * @author    nuxsmin
 * @link      https://syspass.org
 * @copyright 2012-2019, Rubén Domínguez nuxsmin@$syspass.org
 *
 * This file is part of sysPass.
 *
 * sysPass is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * sysPass is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 *  along with sysPass.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace SP\Storage\File;

use SP\Util\Util;

/**
 * Class FileHandler
 *
 * @package SP\Storage\File;
 */
final class FileHandler
{
    public const CHUNK_LENGTH = 8192;

    public const CHUNK_FACTOR = 3;

    /**
     * @var string
     */
    protected $file;

    /**
     * @var resource
     */
    protected $handle;

    private bool $locked = false;

    /**
     * FileHandler constructor.
     */
    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * Writes data into file
     *
     * @param mixed $data
     *
     * @throws FileException
     */
    public function write($data): self
    {
        if (!is_resource($this->handle)) {
            $this->open('wb');
        }

        if (@fwrite($this->handle, (string) $data) === false) {
            throw new FileException(sprintf(__('Unable to read/write the file (%s)'), $this->file));
        }

        return $this;
    }

    /**
     * Opens the file
     *
     * @param string $mode
     *
     * @param bool   $lock
     *
     * @return resource
     * @throws FileException
     */
    public function open($mode = 'r', $lock = false)
    {
        $this->handle = @fopen($this->file, $mode);

        if ($lock && $this->locked === false) {
            $this->lock();
        }

        if ($this->handle === false) {
            throw new FileException(sprintf(__('Unable to open the file (%s)'), $this->file));
        }

        return $this->handle;
    }

    /**
     * Lock the file
     *
     * @param int $mode
     *
     * @throws FileException
     */
    private function lock($mode = LOCK_EX): void
    {
        // Non-blocking with bounded retry: a blocking flock() can deadlock a
        // process against its own un-released lock on the same file. Fail loudly
        // after a few seconds instead of hanging forever.
        for ($attempt = 0; !($this->locked = flock($this->handle, $mode | LOCK_NB)); $attempt++) {
            if ($attempt >= 50) {
                throw new FileException(sprintf(__('Unable to obtain a lock (%s)'), $this->file));
            }

            usleep(100_000); // 100ms, ~5s total
        }

        logger(sprintf('File locked: %s', $this->file));
    }

    /**
     * Reads data from file into a string
     *
     * @return string Data read from file
     * @throws FileException
     */
    public function readToString(): string
    {
        if (($data = file_get_contents($this->file)) === false) {
            throw new FileException(sprintf(__('Unable to read from file (%s)'), $this->file));
        }

        return $data;
    }

    /**
     * Reads data from file into an array
     *
     * @throws FileException
     */
    public function readToArray(): array
    {
        if (($data = @file($this->file, FILE_SKIP_EMPTY_LINES)) === false) {
            throw new FileException(sprintf(__('Unable to read from file (%s)'), $this->file));
        }

        return $data;
    }

    /**
     * Reads data from file into a string
     *
     * @param string $data Data to write into file
     *
     * @throws FileException
     */
    public function save($data): self
    {
        if (file_put_contents($this->file, $data, LOCK_EX) === false) {
            throw new FileException(sprintf(__('Unable to read/write the file (%s)'), $this->file));
        }

        return $this;
    }

    /**
     * Reads data from file
     *
     * @return string Data read from file
     * @throws FileException
     */
    public function read(): string
    {
        if (!is_resource($this->handle)) {
            $this->open('rb');
        }

        $data = '';

        while (!feof($this->handle)) {
            $data .= fread($this->handle, self::CHUNK_LENGTH);
        }

        $this->close();

        return $data;
    }

    /**
     * Closes the file
     *
     * @throws FileException
     */
    public function close(): self
    {
        if ($this->locked) {
            $this->unlock();
        }

        if (!is_resource($this->handle) || @fclose($this->handle) === false) {
            throw new FileException(sprintf(__('Unable to close the file (%s)'), $this->file));
        }

        return $this;
    }

    /**
     * Unlock the file
     */
    private function unlock(): void
    {
        $this->locked = !flock($this->handle, LOCK_UN);
    }

    /**
     * @param callable $chunker
     * @param float    $rate
     *
     * @throws FileException
     */
    public function readChunked(?callable $chunker = null, ?float $rate = null): void
    {
        $maxRate = Util::getMaxDownloadChunk() / self::CHUNK_FACTOR;

        if ($rate === null || $rate > $maxRate) {
            $rate = $maxRate;
        }

        if (!is_resource($this->handle)) {
            $this->open('rb');
        }

        // Clean any existing output buffers to prevent corruption
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        while (!feof($this->handle)) {
            if ($chunker !== null) {
                $chunker(fread($this->handle, (int) round($rate)));
            } else {
                print fread($this->handle, (int) round($rate));
                flush();
            }
        }

        $this->close();
    }

    /**
     * Checks if the file is writable
     *
     * @throws FileException
     */
    public function checkIsWritable(): self
    {
        if (!is_writable($this->file) && @touch($this->file) === false) {
            throw new FileException(sprintf(__('Unable to write in file (%s)'), $this->file));
        }

        return $this;
    }

    /**
     * Checks if the file exists
     *
     * @throws FileException
     */
    public function checkFileExists(): self
    {
        if (!file_exists($this->file)) {
            throw new FileException(sprintf(__('File not found (%s)'), $this->file));
        }

        return $this;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @param bool $isExceptionOnZero
     *
     * @throws FileException
     */
    public function getFileSize($isExceptionOnZero = false): int
    {
        $size = filesize($this->file);

        if ($size === false || ($isExceptionOnZero === true && $size === 0)) {
            throw new FileException(sprintf(__('Unable to read/write file (%s)'), $this->file));
        }

        return $size;
    }

    /**
     * Clears the stat cache for the given file
     */
    public function clearCache(): self
    {
        clearstatcache(true, $this->file);

        return $this;
    }

    /**
     * Deletes a file
     *
     * @throws FileException
     */
    public function delete(): self
    {
        if (@unlink($this->file) === false) {
            throw new FileException(sprintf(__('Unable to delete file (%s)'), $this->file));
        }

        return $this;
    }

    /**
     * Returns the content type in MIME format
     *
     * @throws FileException
     */
    public function getFileType(): string
    {
        $this->checkIsReadable();

        return mime_content_type($this->file);
    }

    /**
     * Checks if the file is readable
     *
     * @throws FileException
     */
    public function checkIsReadable(): self
    {
        if (!is_readable($this->file)) {
            throw new FileException(sprintf(__('Unable to read/write file (%s)'), $this->file));
        }

        return $this;
    }

    /**
     * @throws FileException
     */
    public function getFileTime(): int
    {
        $this->checkIsReadable();

        return filemtime($this->file) ?: 0;
    }
}
