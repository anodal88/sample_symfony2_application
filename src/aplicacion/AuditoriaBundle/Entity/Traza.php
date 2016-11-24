<?php

//namespace Gedmo\Loggable\Entity;
namespace aplicacion\AuditoriaBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Gedmo\Loggable\Entity\LogEntry;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;

/**
 * Gedmo\Loggable\Entity\LogEntry
 *
 * @ORM\Table(
 *     name="Traza",
 *  indexes={
 *      @index(name="log_class_lookup_idx", columns={"object_class"}),
 *      @index(name="log_date_lookup_idx", columns={"logged_at"}),
 *      @index(name="log_user_lookup_idx", columns={"username"}),
 *      @index(name="log_version_lookup_idx", columns={"object_id", "object_class", "version"})
 *  }
 * )
 * @ORM\Entity(repositoryClass="Gedmo\Loggable\Entity\Repository\LogEntryRepository")
 */
class Traza extends AbstractLogEntry
{
    /**
     * All required columns are mapped through inherited superclass
     */
}
