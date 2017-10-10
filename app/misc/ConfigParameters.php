<?php
declare(strict_types=1);

namespace App\Misc;


class ConfigParameters {

    /** @var string */
    private $projectTitle;

    /** @var string */
    private $mailFrom;

    /** @var string */
    private $mailAdmin;


    /**
     * configParameters constructor.
     * @param string $projectTitle
     * @param string $mailFrom
     * @param string $mailAdmin
     */
    public function __construct($projectTitle, $mailFrom, $mailAdmin)
    {
        $this->projectTitle = $projectTitle;
        $this->mailFrom = $mailFrom;
        $this->mailAdmin = $mailAdmin;
    }

    /**
     * @return string
     */
    public function getProjectTitle()
    {
        return $this->projectTitle;
    }

    /**
     * @return string
     */
    public function getMailFrom()
    {
        return $this->mailFrom;
    }

    /**
     * @return string
     */
    public function getMailAdmin()
    {
        return $this->mailAdmin;
    }

}