<?php

namespace Nahoy\ApiPlatform\QueueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Serializer\Annotation\Groups;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * Class BaseJob
 *
 * @ORM\MappedSuperclass()
 */
class BaseJob
{
    /** State if job is inserted, but not yet ready to be started. */
    const STATE_NEW = 'new';

    /**
     * State if job is inserted, and might be started.
     *
     * It is important to note that this does not automatically mean that all
     * jobs of this state can actually be started, but you have to check
     * isStartable() to be absolutely sure.
     *
     * In contrast to NEW, jobs of this state at least might be started,
     * while jobs of state NEW never are allowed to be started.
     */
    const STATE_PENDING = 'pending';

    /** State if job was never started, and will never be started. */
    const STATE_CANCELED = 'canceled';

    /** State if job was started and has not exited, yet. */
    const STATE_RUNNING = 'running';

    /** State if job exists with a successful exit code. */
    const STATE_FINISHED = 'finished';

    /** State if job exits with a non-successful exit code. */
    const STATE_FAILED = 'failed';

    /** State if job exceeds its configured maximum runtime. */
    const STATE_TERMINATED = 'terminated';

    /**
     * State if an error occurs in the runner command.
     *
     * The runner command is the command that actually launches the individual
     * jobs. If instead an error occurs in the job command, this will result
     * in a state of FAILED.
     */
    const STATE_INCOMPLETE = 'incomplete';

    /**
     * State if an error occurs in the runner command.
     *
     * The runner command is the command that actually launches the individual
     * jobs. If instead an error occurs in the job command, this will result
     * in a state of FAILED.
     */
    const DEFAULT_QUEUE = 'default';
    const MAX_QUEUE_LENGTH = 50;

    const PRIORITY_LOW = -5;
    const PRIORITY_DEFAULT = 0;
    const PRIORITY_HIGH = 5;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy = "AUTO")
     * @ORM\Column(type = "bigint", options = {"unsigned": true})
     * @Groups("job")
     */
    protected $id;

    /**
     * @ORM\Column(type = "string", length = 15)
     * @Groups("job")
     */
    protected $status;

    /**
     * @ORM\Column(type = "string", length = Job::MAX_QUEUE_LENGTH)
     * @Groups("job")
     */
    protected $queue;

    /**
     * @ORM\Column(type = "smallint")
     * @Groups("job")
     */
    protected $priority = 0;

    /**
     * @ORM\Column(type = "datetime", name="createdAt")
     * @Groups("job")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type = "datetime", name="startedAt", nullable = true)
     * @Groups("job")
     */
    protected $startedAt;

    /**
     * @ORM\Column(type = "datetime", name="checkedAt", nullable = true)
     * @Groups("job")
     */
    protected $checkedAt;

    /**
     * @ORM\Column(type = "datetime", name="closedAt", nullable = true)
     * @Groups("job")
     */
    protected $closedAt;

    /**
     * @ORM\Column(type = "string", name="workerName", length = 50, nullable = true)
     * @Groups("job")
     */
    protected $workerName;

    /**
     * @ORM\Column(type = "string")
     * @Groups("job")
     */
    protected $command;

    /**
     * @ORM\Column(type = "json_array")
     * @Groups("job")
     */
    protected $args;

    /**
     * @ORM\Column(type = "smallint", name="exitCode", nullable = true, options = {"unsigned": true})
     * @Groups("job")
     */
    protected $exitCode;

    /**
     * @ORM\Column(type = "smallint", nullable = true, options = {"unsigned": true})
     * @Groups("job")
     */
    protected $runtime;

    /**
     * @ORM\Column(type = "bigint", name="memoryUsage", nullable = true, options = {"unsigned": true})
     * @Groups("job")
     */
    protected $memoryUsage;

    /**
     * @ORM\Column(type = "bigint", name="memoryUsageReal", nullable = true, options = {"unsigned": true})
     * @Groups("job")
     */
    protected $memoryUsageReal;

    /**
     * get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Job
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set queue
     *
     * @param string $queue
     *
     * @return Job
     */
    public function setQueue($queue)
    {
        $this->queue = $queue;
    }

    /**
     * get queue
     *
     * @return string
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * set workerName
     *
     * @param string $workerName
     *
     * @return Job
     */
    public function setWorkerName($workerName)
    {
        $this->workerName = $workerName;
    }

    /**
     * get workerName
     *
     * @return string
     */
    public function getWorkerName()
    {
        return $this->workerName;
    }

    /**
     * set priority
     *
     * @param integer $priority
     *
     * @return Job
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority * -1;
    }

    /**
     * get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * get startedAt
     *
     * @return \DateTime
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * get checkedAt
     *
     * @return \DateTime
     */
    public function getCheckedAt()
    {
        return $this->checkedAt;
    }

    /**
     * get closedAt
     *
     * @return \DateTime
     */
    public function getClosedAt()
    {
        return $this->closedAt;
    }

    /**
     * set command
     *
     * @param string $command
     *
     * @return Job
     */
    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * get command
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * get args
     *
     * @return mixed
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * set runtime
     *
     * @param integer $time
     *
     * @return Job
     */
    public function setRuntime($time)
    {
        $this->runtime = (integer) $time;
    }

    /**
     * get runtime
     *
     * @return integer
     */
    public function getRuntime()
    {
        return $this->runtime;
    }

    /**
     * set memoryUsage
     *
     * @param integer $memoryUsage
     *
     * @return Job
     */
    public function setMemoryUsage($memoryUsage)
    {
        $this->memoryUsage = $memoryUsage;
    }

    /**
     * get memoryUsage
     *
     * @return integer
     */
    public function getMemoryUsage()
    {
        return $this->memoryUsage;
    }

    /**
     * set memoryUsageReal
     *
     * @param integer $memoryUsageReal
     *
     * @return Job
     */
    public function setMemoryUsageReal($memoryUsageReal)
    {
        $this->memoryUsageReal = $memoryUsageReal;
    }

    /**
     * get memoryUsageReal
     *
     * @return integer
     */
    public function getMemoryUsageReal()
    {
        return $this->memoryUsageReal;
    }

    /**
     * Set exitCode
     *
     * @param integer $exitCode
     *
     * @return Job
     */
    public function setExitCode($exitCode)
    {
        $this->exitCode = $exitCode;
    }

    /**
     * get exitCode
     *
     * @return integer
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('Job(id = %s, command = "%s")', $this->id, $this->command);
    }
}
