<?php

namespace AppBundle\Command;

use AppBundle\Entity\Admin;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * A command console that creates admins and stores them in the database.
 *
 * $ php bin/console app:add-admin
 * $ php bin/console app:add-admin -vv (verbose mode)
 *
 * @subpackage Command
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class AddAdminCommand extends ContainerAwareCommand
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
            ->setName('app:add-admin')
            ->setDescription('Creates admins and stores them in the database')
            ->addArgument('username', InputArgument::OPTIONAL, 'The username of the new admin')
            ->addArgument('password', InputArgument::OPTIONAL, 'The plain password of the new admin')
            ->addOption('roles', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'The roles of the new admin')
            ->setHelp(<<<'HELP'
The <info>%command.name%</info> command creates new users and saves them in the database:

  <info>php %command.full_name%</info> <comment>username</comment> <comment>[password]</comment>

By default the command creates regular admins. To create users with other roles, add the <comment>--role</comment> option:

  <info>php %command.full_name%</info> username password <comment>--role=ROLE_SUPER_ADMIN</comment>

If you omit required arguments, the command will ask you to provide the missing values:

  # command will ask you for username and password
  <info>php %command.full_name%</info>

  # command will ask you for password
  <info>php %command.full_name%</info> username
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
        $password = $input->getArgument('password');
        if ($username!==null && $password!==null) {
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

        if ($password === null) {
            $question = new Question(' > <info>Password</info> (your type will be hidden): ');
            $question->setValidator([$this, 'passwordValidator']);
            $question->setHidden(true);
            $question->setMaxAttempts(self::MAX_ATTEMPTS);

            $password = $console->ask($input, $output, $question);
            $input->setArgument('password', $password);
        } else {
            $output->writeln(' > <info>Password</info>: '.str_repeat('*', strlen($password)));
        }
    }

    /**
     * This method is executed after interact() and initialize(). It usually
     * contains the logic to execute to complete this command task.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = microtime(true);

        $roles = $input->getOption('roles');
        $username = $input->getArgument('username');
        $plainPassword = $input->getArgument('password');

        // make sure to validate the user data is correct
        $this->validateUserData($username, $plainPassword);

        // create the user and encode its password
        $admin = new Admin();
        $admin->setRoles($roles);
        $admin->setUsername($username);

        $encoder = $this->getContainer()->get('security.password_encoder');
        $encodedPassword = $encoder->encodePassword($admin, $plainPassword);
        $admin->setPassword($encodedPassword);

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $output->writeln([
            '',
            sprintf('[OK] %s was successfully created: %s', 'User', $admin->getUsername())
        ]);

        if ($output->isVerbose()) {
            $finishTime = microtime(true);
            $elapsedTime = $finishTime - $startTime;

            $output->writeln(sprintf('[INFO] New admin database id: %d / Elapsed time: %.2f ms', $admin->getId(), $elapsedTime * 1000));
        }
    }

    /**
     * @internal
     */
    private function validateUserData($username, $plainPassword)
    {
        $repository = $this->entityManager->getRepository(Admin::class);
        $admin = $repository->findOneBy(['username' => $username]);

        if ($admin !== null) {
            throw new \RuntimeException(sprintf('There is already an admin registered with the "%s" username.', $username));
        }

        $this->passwordValidator($plainPassword);
    }

    /**
     * @internal
     */
    public function passwordValidator($plainPassword)
    {
        if (empty($plainPassword)) {
            throw new \Exception('The password can not be empty for roles ROLE_ADMIN and ROLE_SUPER_ADMIN.');
        }

        if (strlen(trim($plainPassword)) < 4) {
            throw new \Exception('The password must be at least 4 characters long.');
        }

        return $plainPassword;
    }
}
