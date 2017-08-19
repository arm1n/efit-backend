<?php

namespace AppBundle\Command;

use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * A command console that creates users and stores them in the database.
 *
 * $ php bin/console app:add-user
 * $ php bin/console app:add-user -vv (verbose mode)
 *
 * @subpackage Command
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class AddUserCommand extends ContainerAwareCommand
{
    const MAX_ATTEMPTS = 5;

    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:add-user')
            ->setDescription('Creates users and stores them in the database')
            ->addArgument('username', InputArgument::OPTIONAL, 'The username of the new user')
            ->setHelp(<<<'HELP'
The <info>%command.name%</info> command creates new users and saves them in the database:

  <info>php %command.full_name%</info> <comment>username</comment>

If you omit required arguments, the command will ask you to provide the missing values:

  # command will ask you for username
  <info>php %command.full_name%</info>
HELP
            );
    }

    /**
     * This method is executed before the interact() and the execute() methods.
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * This method is executed after initialize() and before execute(). Its purpose
     * is to check if some of the options/arguments are missing and interactively
     * ask the user for those values.
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        if (null !== $username) {
            return;
        }

        $output->writeln([
            '',
            'Add User Command Interactive Wizard',
            '-----------------------------------',
            '',
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console app:add-user username',
            '',
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
            '',
        ]);

        $console = $this->getHelper('question');

        $username = $input->getArgument('username');
        if ($username === null) {
            $question = new Question(' > <info>Username</info>: ');
            $question->setValidator(function ($answer) {
                if (empty($answer)) {
                    throw new \RuntimeException('The username cannot be empty');
                }

                return $answer;
            });
            $question->setMaxAttempts(self::MAX_ATTEMPTS);

            $username = $console->ask($input, $output, $question);
            $input->setArgument('username', $username);
        } else {
            $output->writeln(' > <info>Username</info>: '.$username);
        }
    }

    /**
     * This method is executed after interact() and initialize(). It usually
     * contains the logic to execute to complete this command task.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = microtime(true);
        $username = $input->getArgument('username');

        // make sure to validate the user data is correct
        $this->validateUserData($username);

        // create the user and encode its password
        $user = new User();
        $user->setUsername($username);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln([
            '',
            sprintf('[OK] %s was successfully created: %s', 'User', $user->getUsername())
        ]);

        if ($output->isVerbose()) {
            $finishTime = microtime(true);
            $elapsedTime = $finishTime - $startTime;

            $output->writeln(sprintf('[INFO] New user database id: %d / Elapsed time: %.2f ms', $user->getId(), $elapsedTime * 1000));
        }
    }

    /**
     * @internal
     */
    private function validateUserData($username)
    {
        $repository = $this->entityManager->getRepository(User::class);
        $user = $repository->findOneBy(['username' => $username]);

        if ($user !== null) {
            throw new \RuntimeException(sprintf('There is already a user registered with the "%s" username.', $username));
        }
    }
}
