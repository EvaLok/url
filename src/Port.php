<?php
/**
 * This file is part of the League.url library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/thephpleague/url/
 * @version 4.0.0
 * @package League.url
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace League\Url;

use InvalidArgumentException;
use League\Url\Interfaces;
use League\Url\Utilities;

/**
 * Value object representing a URL port component.
 *
 * @package League.url
 * @since 1.0.0
 */
class Port extends Component implements Interfaces\Port
{
    use Utilities\StandardPort;

    /**
     * New Instance
     *
     * @param int $data
     */
    public function __construct($data = null)
    {
        if (! is_null($data)) {
            $this->data = $this->validate($data);
        }
    }

    /**
     * Validate the port
     *
     * @param int $data
     */
    protected function validate($data)
    {
        $data = filter_var($data, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]);
        if (false === $data) {
            throw new InvalidArgumentException('The submitted port is invalid');
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getUriComponent()
    {
        $data = $this->__toString();
        if (empty($data)) {
            return $data;
        }
        return ':'.$data;
    }

    /**
     * {@inheritdoc}
     */
    public function toInt()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getStandardSchemes()
    {
        return $this->getStandardSchemesFromPort($this->data);
    }
}
