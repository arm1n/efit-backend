<?php

namespace AppBundle\Normalizer; 

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizer for DateTime objects via FOSRest bundle.
 *
 * @subpackage Normalizer
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class DateTimeNormalizer implements NormalizerInterface
{
    /**
     * Normalizes date time object into timestamp.
     * 
     * @param \DateTime $date
     * @param string $format
     * @param array  $context
     * @return array
     */
    public function normalize($date, $format = null, array $context = array())
    {
        //return $date->getTimestamp();
        return $date->format(\DateTime::ISO8601);
    }

    /**
     * Checks if the given class is a DateTime.
     *
     * @param mixed  $data   Data to normalize.
     * @param string $format The format being (de-)serialized from or into.
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof \DateTime;
    }
}
